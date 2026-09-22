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
        'pickup_assigned' => ['pickup_accepted'], 'pickup_accepted' => ['picked_up'],
        'picked_up' => ['in_transit_to_hub'], 'in_transit_to_hub' => ['received_at_hub'],
        'received_at_hub' => ['sorting'], 'sorting' => ['ready_for_delivery'],
        'delivery_assigned' => ['delivery_accepted'], 'delivery_accepted' => ['delivery_collected'],
        'delivery_collected' => ['out_for_delivery'],
        'out_for_delivery' => ['delivered', 'failed'], 'failed' => ['assigned', 'returned'],
        'delivered' => [], 'returned' => [],
    ];
    protected $fillable = ['seller_order_id', 'logistics_provider_id', 'rider_id', 'pickup_rider_id', 'delivery_rider_id', 'tracking_code', 'status', 'fee_minor', 'cod_amount_minor', 'cod_collected', 'attempts'];

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

    public function pickupRider(): BelongsTo { return $this->belongsTo(Rider::class, 'pickup_rider_id'); }
    public function deliveryRider(): BelongsTo { return $this->belongsTo(Rider::class, 'delivery_rider_id'); }

    public function events(): HasMany
    {
        return $this->hasMany(DeliveryEvent::class)->orderBy('occurred_at');
    }

    public function assignPickupTo(Rider $rider, User $actor): self
    {
        return DB::transaction(function () use ($rider, $actor) {
            $shipment = self::query()->lockForUpdate()->findOrFail($this->id);
            if ($actor->hasRole('logistics')) {
                abort_unless($actor->logisticsProvider?->id === $shipment->logistics_provider_id, 403);
            }
            abort_unless($rider->is_active && $rider->logistics_provider_id === $shipment->logistics_provider_id, 403);
            if ($shipment->status === 'pickup_assigned' && $shipment->pickup_rider_id === $rider->id) return $shipment;
            if (! in_array($shipment->status, ['unassigned', 'pickup_assigned'], true)) throw new ConflictHttpException('Pickup can only be assigned before collection.');
            $shipment->pickup_rider_id = $rider->id;
            $shipment->rider_id = $rider->id;
            $shipment->status = 'pickup_assigned';
            $shipment->save();
            $shipment->events()->firstOrCreate(
                ['status' => 'pickup_assigned', 'attempt' => 1],
                ['user_id' => $actor->id, 'note' => 'Pickup assigned to '.$rider->user->name, 'occurred_at' => now()],
            );
            return $shipment->refresh();
        });
    }

    public function assignDeliveryTo(Rider $rider, User $actor): self
    {
        return DB::transaction(function () use ($rider, $actor) {
            $shipment = self::query()->lockForUpdate()->findOrFail($this->id);
            abort_unless($actor->logisticsProvider?->id === $shipment->logistics_provider_id, 403);
            abort_unless($rider->is_active && $rider->logistics_provider_id === $shipment->logistics_provider_id, 403);
            abort_unless($shipment->status === 'ready_for_delivery', 409, 'Parcel must be received and sorted before delivery assignment.');
            $shipment->delivery_rider_id = $rider->id;
            $shipment->rider_id = $rider->id;
            $shipment->status = 'delivery_assigned';
            $shipment->save();
            $shipment->events()->create(['status' => 'delivery_assigned', 'attempt' => 1, 'user_id' => $actor->id, 'note' => 'Delivery assigned to '.$rider->user->name, 'occurred_at' => now()]);
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
                $pickupStates = ['pickup_assigned', 'pickup_accepted', 'picked_up', 'in_transit_to_hub'];
                $expectedRider = in_array($shipment->status, $pickupStates, true) ? $shipment->pickup_rider_id : $shipment->delivery_rider_id;
                abort_unless($actor->rider?->id === $expectedRider && $actor->rider->is_active, 403);
            } elseif ($actor->hasRole('logistics')) {
                abort_unless($actor->logisticsProvider?->id === $shipment->logistics_provider_id, 403);
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
            if ($status === 'delivery_collected' && $shipment->sellerOrder->status === 'ready_to_ship') $shipment->sellerOrder->transitionTo('shipped', $actor);
            if ($status === 'delivered' && $shipment->sellerOrder->status === 'shipped') {
                $shipment->sellerOrder->transitionTo('delivered', $actor);
                if ($shipment->cod_amount_minor > 0) {
                    $shipment->sellerOrder->order->payments()->update(['status' => 'paid']);
                    $shipment->sellerOrder->order->update(['payment_status' => 'paid']);
                }
            }
            return $shipment->refresh();
        });
    }
}
