<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class Shipment extends Model
{
    public const TRANSITIONS = [
        'unassigned' => ['assigned'], 'assigned' => ['picked_up'],
        'picked_up' => ['in_transit'], 'in_transit' => ['out_for_delivery'],
        'out_for_delivery' => ['delivered', 'failed'], 'failed' => ['assigned', 'returned'],
        'delivered' => [], 'returned' => [],
    ];
    protected $fillable = ['seller_order_id', 'logistics_provider_id', 'rider_id', 'tracking_code', 'status', 'fee_minor', 'cod_amount_minor', 'cod_collected', 'attempts'];

    protected function casts(): array
    {
        return ['cod_collected' => 'boolean'];
    }

    public function sellerOrder(): BelongsTo
    {
        return $this->belongsTo(SellerOrder::class);
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(LogisticsProvider::class, 'logistics_provider_id');
    }

    public function rider(): BelongsTo
    {
        return $this->belongsTo(Rider::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(DeliveryEvent::class)->orderBy('occurred_at');
    }

    public function assignTo(Rider $rider, User $actor): self
    {
        return DB::transaction(function () use ($rider, $actor) {
            $shipment = self::query()->lockForUpdate()->findOrFail($this->id);
            if ($actor->hasRole('logistics')) {
                abort_unless($actor->logisticsProvider?->id === $shipment->logistics_provider_id, 403);
            }
            abort_unless($rider->is_active && $rider->user->status === 'active' && ! $rider->user->isSuspended()
                && $rider->logistics_provider_id === $shipment->logistics_provider_id, 403);
            if ($shipment->status === 'assigned' && $shipment->rider_id === $rider->id) return $shipment;
            if (! in_array($shipment->status, ['unassigned', 'assigned', 'failed'], true)) {
                throw new ConflictHttpException('Shipment cannot be assigned or reassigned after pickup.');
            }
            $shipment->rider_id = $rider->id;
            $shipment->status = 'assigned';
            $shipment->save();
            $shipment->events()->firstOrCreate(
                ['status' => 'assigned', 'attempt' => max(1, $shipment->attempts + 1)],
                ['user_id' => $actor->id, 'note' => 'Assigned to '.$rider->user->name, 'occurred_at' => now()],
            );
            return $shipment->refresh();
        });
    }

    public function transitionTo(string $status, User $actor, ?string $note = null, ?string $photoPath = null, ?string $receiverName = null): self
    {
        return DB::transaction(function () use ($status, $actor, $note, $photoPath, $receiverName) {
            $shipment = self::query()->with('sellerOrder.order')->lockForUpdate()->findOrFail($this->id);
            if ($shipment->status === $status) return $shipment;
            if (! in_array($status, self::TRANSITIONS[$shipment->status] ?? [], true)) {
                throw new ConflictHttpException("Illegal shipment transition: {$shipment->status} to {$status}.");
            }
            if ($actor->hasRole('rider')) {
                abort_unless($actor->rider?->id === $shipment->rider_id && $actor->rider->is_active, 403);
            }
            if ($status === 'delivered' && (! $photoPath || ! trim((string) $receiverName))) {
                throw new ConflictHttpException('Receiver name and proof photo are required.');
            }
            if ($status === 'failed' && ! trim((string) $note)) {
                throw new ConflictHttpException('A failure reason is required.');
            }
            $attempt = max(1, $shipment->attempts + 1);
            if ($status === 'failed') $shipment->attempts = $attempt;
            $finalStatus = $status === 'failed' && $attempt >= 3 ? 'returned' : $status;
            $shipment->status = $finalStatus;
            if ($status === 'delivered' && $shipment->cod_amount_minor > 0) $shipment->cod_collected = true;
            $shipment->save();
            $shipment->events()->firstOrCreate(
                ['status' => $status, 'attempt' => $attempt],
                ['user_id' => $actor->id, 'note' => $note, 'receiver_name' => $receiverName, 'photo_path' => $photoPath, 'occurred_at' => now()],
            );
            if ($finalStatus === 'returned') {
                $shipment->events()->firstOrCreate(
                    ['status' => 'returned', 'attempt' => $attempt],
                    ['user_id' => $actor->id, 'note' => $note, 'occurred_at' => now()],
                );
            }
            if ($status === 'picked_up' && $shipment->sellerOrder->status === 'ready_to_ship') $shipment->sellerOrder->transitionTo('shipped', $actor);
            if ($status === 'delivered' && $shipment->sellerOrder->status === 'shipped') {
                $shipment->sellerOrder->transitionTo('delivered', $actor);
                if ($shipment->cod_amount_minor > 0) {
                    $parent = Order::whereKey($shipment->sellerOrder->order_id)->lockForUpdate()->firstOrFail();
                    $uncollected = $parent->sellerOrders()->whereNotIn('status', ['cancelled', 'refunded'])
                        ->whereDoesntHave('shipment', fn ($query) => $query->where('cod_collected', true))->exists();
                    if (! $uncollected && $parent->payment_status === 'pending') {
                        $parent->payments()->update(['status' => 'paid']);
                        $parent->update(['payment_status' => 'paid']);
                    }
                }
            }
            return $shipment->refresh();
        });
    }
}
