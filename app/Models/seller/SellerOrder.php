<?php

namespace App\Models\Seller;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class SellerOrder extends Model
{
    public const TRANSITIONS = [
        'pending' => ['accepted', 'cancelled'],
        'accepted' => ['packed', 'cancelled'],
        'packed' => ['ready_to_ship', 'cancelled'],
        'ready_to_ship' => ['shipped'],
        'shipped' => ['delivered'],
        'delivered' => ['completed', 'refunded'],
        'completed' => ['refunded'],
        'cancelled' => [],
        'refunded' => [],
    ];
    protected $fillable = ['order_id', 'seller_id', 'logistics_provider_id', 'subtotal_minor', 'shipping_fee_minor', 'commission_minor', 'status', 'delivered_at', 'note'];

    protected function casts(): array
    {
        return ['delivered_at' => 'datetime'];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class);
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(LogisticsProvider::class, 'logistics_provider_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function shipment(): HasOne
    {
        return $this->hasOne(Shipment::class);
    }

    public function returnRequest(): HasOne
    {
        return $this->hasOne(ReturnRequest::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(SellerOrderEvent::class)->latest();
    }

    public function transitionTo(string $status, ?User $actor = null, ?string $note = null): self
    {
        return DB::transaction(function () use ($status, $actor, $note) {
            $order = self::query()->lockForUpdate()->findOrFail($this->id);
            if ($order->status === $status) {
                return $order;
            }
            if (! in_array($status, self::TRANSITIONS[$order->status] ?? [], true)) {
                throw new ConflictHttpException("Illegal seller order transition: {$order->status} to {$status}.");
            }
            $from = $order->status;
            $changes = ['status' => $status];
            if ($status === 'delivered') $changes['delivered_at'] = now();
            $order->update($changes);
            $order->events()->create(['from_status' => $from, 'to_status' => $status, 'user_id' => $actor?->id, 'note' => $note]);

            if ($status === 'cancelled') {
                foreach ($order->items as $item) {
                    $item->productVariant?->increment('stock', $item->quantity);
                }
            }

            if ($status === 'ready_to_ship') {
                if (! $order->logistics_provider_id) {
                    throw new ConflictHttpException('A logistics provider must be selected before shipment creation.');
                }
                $shipment = $order->shipment()->firstOrCreate([], [
                    'logistics_provider_id' => $order->logistics_provider_id,
                    'tracking_code' => 'LKH-'.now()->format('ymd').'-'.Str::upper(Str::random(10)),
                    'status' => 'unassigned',
                    'fee_minor' => $order->shipping_fee_minor,
                    'cod_amount_minor' => in_array($order->order->payment_method, ['cod', 'cash_on_delivery'], true)
                        ? $order->subtotal_minor + $order->shipping_fee_minor
                        : 0,
                ]);
                $shipment->events()->firstOrCreate(
                    ['status' => 'unassigned', 'attempt' => 1],
                    ['user_id' => $actor?->id, 'note' => 'Shipment created and awaiting rider assignment.', 'occurred_at' => now()],
                );
            }
            return $order->refresh();
        });
    }
}
