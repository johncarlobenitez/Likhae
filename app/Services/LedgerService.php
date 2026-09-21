<?php

namespace App\Services;

use App\Models\LedgerEntry;
use App\Models\Payout;
use App\Models\ReturnRequest;
use App\Models\Seller;
use App\Models\SellerOrder;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LedgerService
{
    public function complete(SellerOrder $sellerOrder, ?User $actor = null): void
    {
        DB::transaction(function () use ($sellerOrder, $actor) {
            $order = SellerOrder::query()->with('shipment')->lockForUpdate()->findOrFail($sellerOrder->id);
            if ($order->status !== 'completed') $order->transitionTo('completed', $actor);
            $this->entry('seller', $order->seller_id, 'sale', $order->subtotal_minor - $order->commission_minor, $order, $actor);
            $this->entry('platform', 0, 'commission', $order->commission_minor, $order, $actor);
            if ($order->shipment) $this->entry('courier', $order->shipment->logistics_provider_id, 'shipping_fee', $order->shipping_fee_minor, $order, $actor);
        });
    }

    public function requestPayout(Seller $seller, int $amountMinor, User $actor): Payout
    {
        return DB::transaction(function () use ($seller, $amountMinor, $actor) {
            $locked = Seller::query()->lockForUpdate()->findOrFail($seller->id);
            $balance = (int) LedgerEntry::where('account_type', 'seller')->where('account_id', $locked->id)->sum('amount_minor');
            if ($amountMinor < 1 || $amountMinor > $balance) throw ValidationException::withMessages(['amount' => 'Payout exceeds the available balance.']);
            $payout = Payout::create(['seller_id' => $locked->id, 'amount_minor' => $amountMinor, 'status' => 'pending']);
            $this->entry('seller', $locked->id, 'payout_reserved', -$amountMinor, $payout, $actor);
            return $payout;
        });
    }

    public function decidePayout(Payout $payout, string $status, User $admin, ?string $reference = null): Payout
    {
        return DB::transaction(function () use ($payout, $status, $admin, $reference) {
            $locked = Payout::query()->lockForUpdate()->findOrFail($payout->id);
            if ($locked->status !== 'pending') return $locked;
            if (! in_array($status, ['paid', 'rejected'], true)) throw ValidationException::withMessages(['status' => 'Invalid payout decision.']);
            $locked->update(['status' => $status, 'reference' => $reference, 'decided_by' => $admin->id, 'decided_at' => now()]);
            if ($status === 'rejected') {
                $reversal = $this->entry('seller', $locked->seller_id, 'payout_reversal', $locked->amount_minor, $locked, $admin);
                $locked->update(['reversal_ledger_entry_id' => $reversal->id]);
            }
            return $locked->refresh();
        });
    }

    public function refund(ReturnRequest $return, User $actor): ReturnRequest
    {
        return DB::transaction(function () use ($return, $actor) {
            $request = ReturnRequest::query()->with(['sellerOrder.items.productVariant', 'sellerOrder.order.payments', 'sellerOrder.order.sellerOrders'])->lockForUpdate()->findOrFail($return->id);
            if ($request->status === 'refunded') return $request;
            if (! in_array($request->status, ['approved', 'disputed'], true)) throw ValidationException::withMessages(['return' => 'This return cannot be refunded.']);
            $order = $request->sellerOrder;
            if (! $request->stock_restored_at) {
                foreach ($order->items as $item) $item->productVariant?->increment('stock', $item->quantity);
                $request->stock_restored_at = now();
            }
            $this->entry('seller', $order->seller_id, 'refund_reversal', -($order->subtotal_minor - $order->commission_minor), $order, $actor);
            $this->entry('platform', 0, 'commission_reversal', -$order->commission_minor, $order, $actor);
            $this->entry('buyer', $request->buyer_id, 'refund_credit', $order->subtotal_minor, $request, $actor);
            $order->transitionTo('refunded', $actor);
            $request->status = 'refunded';
            $request->resolved_by = $actor->id;
            if (in_array($order->order->payment_method, ['cod','cash_on_delivery'], true)) $request->cod_repayment_status = 'pending';
            $request->save();
            $order->order->payments()->update(['status' => $order->order->sellerOrders()->where('status', '!=', 'refunded')->exists() ? 'partially_refunded' : 'refunded']);
            return $request->refresh();
        });
    }

    private function entry(string $accountType, int $accountId, string $type, int $amount, object $reference, ?User $actor): LedgerEntry
    {
        return LedgerEntry::firstOrCreate([
            'account_type' => $accountType, 'account_id' => $accountId, 'type' => $type,
            'reference_type' => $reference::class, 'reference_id' => $reference->id,
        ], ['amount_minor' => $amount, 'created_by' => $actor?->id]);
    }
}
