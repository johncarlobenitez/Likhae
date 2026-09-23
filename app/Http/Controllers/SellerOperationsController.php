<?php

namespace App\Http\Controllers;

use App\Models\LedgerEntry;
use App\Models\Payout;
use App\Models\Seller;
use App\Models\SellerOrder;
use App\Models\Shipment;
use App\Services\LedgerService;
use App\Services\ShipmentCodeService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SellerOperationsController extends Controller
{
    public function dashboard(Request $request): View
    {
        $seller = $this->seller($request);
        $orders = $this->orders($seller)->get();
        $products = $seller->products()->with('variants')->get();
        $revenueOrders = $orders->where('status', 'completed');
        $chart = collect(range(6, 0))->map(function (int $offset) use ($revenueOrders): array {
            $day = now()->subDays($offset);
            return ['label' => $day->format('D'), 'value' => $revenueOrders->filter(fn ($order) => $order->created_at?->isSameDay($day))->sum('subtotal_minor') / 100];
        });
        $max = max(1, (float) $chart->max('value'));

        return view('Seller.dashboard', [
            'seller' => $seller->owner,
            'dashboardStats' => [
                'today_sales' => $revenueOrders->filter(fn ($order) => $order->created_at?->isToday())->sum('subtotal_minor') / 100,
                'orders' => $orders->count(), 'revenue' => $revenueOrders->sum('subtotal_minor') / 100,
                'products_sold' => $revenueOrders->sum(fn ($order) => $order->items->sum('quantity')),
                'pending_shipment' => $orders->whereIn('status', ['accepted', 'packed', 'ready_to_ship'])->count(),
                'inventory_alerts' => $products->filter(fn ($product) => $product->variants->sum('stock') <= 5)->count(),
            ],
            'salesChart' => $chart->map(fn ($point) => $point + ['height' => max(8, (int) round($point['value'] / $max * 100))]),
            'orderStatusCounts' => [
                'placed' => $orders->where('status', 'pending')->count(),
                'preparing' => $orders->whereIn('status', ['accepted', 'packed'])->count(),
                'ready-for-pickup' => $orders->where('status', 'ready_to_ship')->count(),
                'shipping' => $orders->whereIn('status', ['shipped', 'delivered'])->count(),
                'completed' => $orders->where('status', 'completed')->count(),
                'returns' => $orders->where('status', 'refunded')->count(),
            ],
            'sellerOrders' => $orders->take(5)->map(fn ($order) => $this->orderRow($order)),
            'inventoryAlerts' => $products->sortBy(fn ($product) => $product->variants->sum('stock'))->take(3)->map(fn ($product) => ['id' => $product->id, 'name' => $product->name, 'stock' => $product->variants->sum('stock')]),
        ]);
    }

    public function logistics(Request $request): View
    {
        $seller = $this->seller($request);
        $orders = $this->orders($seller)->get();
        return view('Seller.orders', [
            'pageMode' => 'logistics', 'mode' => $request->input('view', 'couriers'),
            'status' => $request->input('status', 'all'), 'selectedOrder' => $request->input('order'),
            'sellerOrders' => $orders->map(fn ($order) => $this->orderRow($order)),
            'deliveries' => $orders->pluck('shipment')->filter(), 'riders' => collect(),
        ]);
    }

    public function waybill(Request $request, SellerOrder $sellerOrder, ShipmentCodeService $codes): View
    {
        $seller = $this->seller($request);
        abort_unless($sellerOrder->seller_id === $seller->id, 403);
        $sellerOrder->load(['order.buyer', 'items', 'shipment', 'seller.pickupAddress']);
        abort_unless($sellerOrder->shipment, 404, 'Shipment has not been created yet.');

        return view('Seller.waybill', [
            'sellerOrder' => $sellerOrder, 'shipment' => $sellerOrder->shipment,
            'seller' => $seller, 'snapshot' => $sellerOrder->order->shipping_address_snapshot ?? [],
            'qrSvg' => $codes->qr($sellerOrder->shipment->tracking_code),
            'barcodeSvg' => $codes->barcode($sellerOrder->shipment->tracking_code),
        ]);
    }

    public function finance(Request $request): View
    {
        $seller = $this->seller($request);
        $entries = LedgerEntry::where('account_type', 'seller')->where('account_id', $seller->id)->latest()->get();
        $orders = $this->orders($seller)->get();
        $gross = $orders->where('status', 'completed')->sum('subtotal_minor') / 100;
        $commission = $orders->where('status', 'completed')->sum('commission_minor') / 100;
        $trend = collect(range(29, 0))->map(function (int $offset) use ($orders): array {
            $day = now()->subDays($offset);
            return ['date' => $day, 'value' => $orders->where('status', 'completed')->filter(fn ($order) => $order->created_at?->isSameDay($day))->sum('subtotal_minor') / 100];
        });
        return view('Seller.finance', [
            'tab' => $request->input('tab', 'sales'), 'financeStats' => ['gross' => $gross, 'commission' => $commission, 'net' => $gross - $commission, 'pending' => $seller->balanceMinor() / 100],
            'commissionRate' => $seller->commission_bps / 10000, 'transactions' => $entries,
            'payouts' => Payout::where('seller_id', $seller->id)->latest()->get(), 'financeTrend' => $trend,
        ]);
    }

    public function requestPayout(Request $request, LedgerService $ledger): RedirectResponse
    {
        $seller = $this->seller($request);
        $data = $request->validate(['amount_minor' => ['required', 'integer', 'min:1']]);
        $ledger->requestPayout($seller, (int) $data['amount_minor'], $request->user());
        return back()->with('status', 'Payout request submitted.');
    }

    public function exportStatement(Request $request): StreamedResponse
    {
        $seller = $this->seller($request);
        $entries = LedgerEntry::where('account_type', 'seller')->where('account_id', $seller->id)->orderBy('id')->get();
        return $this->csv('seller-ledger-'.now()->format('Ymd-His').'.csv', ['Date', 'Type', 'Amount (centavos)', 'Note'], $entries->map(fn ($entry) => [$entry->created_at, $entry->type, $entry->amount_minor, $entry->note]));
    }

    public function reports(Request $request): View
    {
        [$seller, $from, $to, $orders] = $this->reportData($request);
        $grossMinor = $orders->whereNotIn('status', ['cancelled', 'refunded'])->sum('subtotal_minor');
        $commissionMinor = $orders->whereNotIn('status', ['cancelled', 'refunded'])->sum('commission_minor');
        return view('Seller.reports', ['report' => $request->input('report', 'sales'), 'reportFrom' => $from, 'reportTo' => $to,
            'reportSummary' => ['orders' => $orders->count(), 'gross' => $grossMinor / 100, 'commission' => $commissionMinor / 100, 'net' => ($grossMinor - $commissionMinor) / 100, 'completed' => $orders->where('status', 'completed')->count(), 'cancelled' => $orders->where('status', 'cancelled')->count()]]);
    }

    public function downloadReport(Request $request): StreamedResponse
    {
        $request->validate(['report' => ['required', 'in:sales,profit,orders,products'], 'from' => ['required', 'date'], 'to' => ['required', 'date', 'after_or_equal:from']]);
        [, , , $orders] = $this->reportData($request);
        return $this->csv('seller-report-'.now()->format('Ymd-His').'.csv', ['Order', 'Buyer', 'Status', 'Total (centavos)', 'Created'], $orders->map(fn ($order) => [$order->order->reference.'-'.$order->id, $order->order->buyer?->name, $order->status, $order->subtotal_minor + $order->shipping_fee_minor, $order->created_at]));
    }

    public function exportOrders(Request $request): StreamedResponse
    {
        $orders = $this->orders($this->seller($request))->get();
        return $this->csv('seller-orders-'.now()->format('Ymd-His').'.csv', ['Order', 'Buyer', 'Status', 'Total (centavos)', 'Tracking'], $orders->map(fn ($order) => [$order->order->reference.'-'.$order->id, $order->order->buyer?->name, $order->status, $order->subtotal_minor + $order->shipping_fee_minor, $order->shipment?->tracking_code]));
    }

    private function seller(Request $request): Seller
    {
        return $request->user()->sellers()->where('status', 'approved')->firstOrFail();
    }

    private function orders(Seller $seller)
    {
        return SellerOrder::with(['order.buyer', 'order.payments', 'items.product.images', 'shipment.provider', 'shipment.rider.user'])->where('seller_id', $seller->id)->latest();
    }

    private function reportData(Request $request): array
    {
        $seller = $this->seller($request);
        $from = Carbon::parse($request->input('from', now()->subDays(30)->toDateString()))->startOfDay();
        $to = Carbon::parse($request->input('to', now()->toDateString()))->endOfDay();
        return [$seller, $from, $to, $this->orders($seller)->whereBetween('seller_orders.created_at', [$from, $to])->get()];
    }

    private function orderRow(SellerOrder $order): array
    {
        $first = $order->items->first();
        return ['id' => $order->order->reference.'-'.$order->id, 'db_id' => $order->id, 'buyer_id' => $order->order->buyer_id, 'buyer' => $order->order->buyer?->name, 'product' => $first?->product_name ?? 'Order items', 'variant' => $first?->variant_name ?? 'Standard', 'quantity' => $order->items->sum('quantity'), 'total' => ($order->subtotal_minor + $order->shipping_fee_minor) / 100, 'payment' => str($order->order->payment_method)->headline(), 'status_key' => match ($order->status) {'pending'=>'placed','accepted','packed'=>'preparing','ready_to_ship'=>'ready-for-pickup','shipped','delivered'=>'shipping','refunded'=>'returns',default=>$order->status}, 'status' => str($order->status)->headline(), 'date' => $order->created_at?->format('M d, Y'), 'shipping' => $order->shipment?->provider?->name, 'shipping_address' => collect($order->order->shipping_address_snapshot)->only(['line1','barangay','city','province'])->filter()->implode(', '), 'delivery' => $order->shipment];
    }

    private function csv(string $name, array $header, Collection $rows): StreamedResponse
    {
        return response()->streamDownload(function () use ($header, $rows): void { $out = fopen('php://output', 'w'); fputcsv($out, $header); foreach ($rows as $row) fputcsv($out, $row); fclose($out); }, $name, ['Content-Type' => 'text/csv']);
    }
}
