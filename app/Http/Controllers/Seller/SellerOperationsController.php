<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Admin\CommissionTransaction;
use App\Models\Seller\SellerOrder;
use App\Models\Seller\Voucher;
use App\Services\Fulfillment\ShipmentWorkflowService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\View\View;

class SellerOperationsController extends Controller
{
    public function dashboard(Request $request): View
    {
        $seller = $request->user()->sellerProfile;
        abort_unless($seller, 403);

        $orders = SellerOrder::query()->where('seller_profile_id', $seller->id);

        $stats = [
            'orders' => (clone $orders)->count(),
            'placed' => (clone $orders)->where('status', 'PLACED')->count(),
            'ready' => (clone $orders)->where('status', 'READY_FOR_PICKUP')->count(),
            'completed' => (clone $orders)->where('status', 'COMPLETED')->count(),
            'sales' => (float) (clone $orders)->where('status', 'COMPLETED')->sum('grand_total'),
        ];

        $recentOrders = (clone $orders)
            ->with(['order.buyer', 'items', 'shipment'])
            ->latest()
            ->limit(8)
            ->get();

        $completedSales = (clone $orders)
            ->where('status', 'COMPLETED')
            ->where('updated_at', '>=', today()->subDays(6)->startOfDay())
            ->get(['grand_total', 'updated_at']);

        $dailySales = collect(range(6, 0))->mapWithKeys(function (int $daysAgo) use ($completedSales): array {
            $date = today()->subDays($daysAgo);

            return [$date->toDateString() => (float) $completedSales
                ->filter(fn (SellerOrder $order): bool => $order->updated_at->isSameDay($date))
                ->sum('grand_total')];
        });
        $maximumDailySales = max(1, (float) $dailySales->max());
        $salesChart = $dailySales->map(fn (float $value, string $date): array => [
            'label' => \Illuminate\Support\Carbon::parse($date)->format('D'),
            'value' => $value,
            'height' => $value > 0 ? max(8, (int) round(($value / $maximumDailySales) * 100)) : 2,
        ])->values();

        $sellerOrders = $recentOrders->map(function (SellerOrder $order): array {
            $statusKey = match ($order->status) {
                'PLACED' => 'placed',
                'CONFIRMED' => 'confirmed',
                'PREPARING' => 'preparing',
                'READY_FOR_PICKUP' => 'ready-for-pickup',
                'PICKED_UP', 'SHIPPED' => 'shipping',
                'COMPLETED' => 'completed',
                'CANCELLED' => 'cancelled',
                default => strtolower(str_replace('_', '-', $order->status)),
            };

            return [
                'id' => $order->seller_order_number,
                'db_id' => $order->id,
                'buyer' => $order->order?->buyer?->name ?? 'Buyer',
                'product' => $order->items->pluck('product_name')->filter()->join(', '),
                'total' => (float) $order->grand_total,
                'status' => str($order->status)->replace('_', ' ')->title()->toString(),
                'status_key' => $statusKey,
            ];
        });

        $inventoryAlerts = $seller->products()
            ->withSum('variants', 'stock')
            ->get()
            ->map(fn ($product): array => [
                'name' => $product->name,
                'stock' => (int) ($product->variants_sum_stock ?? 0),
            ])
            ->filter(fn (array $product): bool => $product['stock'] <= 5)
            ->sortBy('stock')
            ->take(6)
            ->values();

        return view('Seller.dashboard', [
            'seller' => $request->user(),
            'sellerProfile' => $seller,
            'dashboardStats' => [
                'today_sales' => (float) (clone $orders)->whereDate('created_at', today())->sum('grand_total'),
                'orders' => $stats['orders'],
                'revenue' => $stats['sales'],
                'products_sold' => $seller->products()->count(),
                'pending_shipment' => $stats['ready'],
                'inventory_alerts' => $seller->products()->whereHas('variants', fn ($query) => $query->where('stock', '<=', 5))->count(),
            ],
            'orderStatusCounts' => [
                'placed' => $stats['placed'],
                'confirmed' => (clone $orders)->where('status', 'CONFIRMED')->count(),
                'preparing' => (clone $orders)->where('status', 'PREPARING')->count(),
                'ready-for-pickup' => $stats['ready'],
                'shipping' => (clone $orders)->whereIn('status', ['PICKED_UP', 'SHIPPED'])->count(),
            ],
            'salesChart' => $salesChart,
            'sellerOrders' => $sellerOrders,
            'inventoryAlerts' => $inventoryAlerts,
        ]);
    }

    public function logistics(Request $request): View
    {
        $seller = $request->user()->sellerProfile;
        abort_unless($seller, 403);

        $orders = SellerOrder::query()
            ->where('seller_profile_id', $seller->id)
            ->with(['order.address', 'items', 'shipment.events', 'shipment.pickupRequests', 'shipment.riderAssignments.riderProfile.user'])
            ->whereHas('shipment')
            ->latest()
            ->paginate(15);

        return view('Seller.logistics', [
            'seller' => $seller,
            'orders' => $orders,
        ]);
    }

    public function waybill(Request $request, SellerOrder $sellerOrder, ShipmentWorkflowService $workflow): View
    {
        $seller = $request->user()->sellerProfile;
        abort_unless($seller && (int) $sellerOrder->seller_profile_id === (int) $seller->id, 403);

        $shipment = $workflow->ensureShipment($sellerOrder);
        $shipment->loadMissing(['sellerOrder.items', 'sellerOrder.order.address', 'waybills', 'events']);

        return view('Seller.waybill', [
            'sellerOrder' => $sellerOrder->fresh(['items', 'sellerProfile.user', 'sellerProfile.businessAddress', 'order.address']),
            'shipment' => $shipment,
            'waybill' => $shipment->waybills()->latest('format_version')->first(),
        ]);
    }

    public function exportOrders(Request $request)
    {
        $seller = $request->user()->sellerProfile;
        abort_unless($seller, 403);

        $rows = SellerOrder::query()
            ->where('seller_profile_id', $seller->id)
            ->with('shipment')
            ->latest()
            ->get()
            ->map(fn (SellerOrder $order): array => [
                $order->seller_order_number,
                $order->status,
                number_format((float) $order->grand_total, 2, '.', ''),
                $order->shipment?->tracking_number,
                optional($order->created_at)->toDateTimeString(),
            ]);

        $csv = "seller_order,status,total,tracking,created_at\n";
        foreach ($rows as $row) {
            $csv .= implode(',', array_map(fn ($value) => '"'.str_replace('"', '""', (string) $value).'"', $row))."\n";
        }

        return Response::make($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="seller-orders.csv"',
        ]);
    }

    public function finance(Request $request): View
    {
        $seller = $request->user()->sellerProfile;
        abort_unless($seller, 403);

        $orders = SellerOrder::query()->where('seller_profile_id', $seller->id);
        $commissions = CommissionTransaction::query()->whereHas('sellerOrder', fn ($query) => $query->where('seller_profile_id', $seller->id));

        return view('Seller.finance', [
            'seller' => $seller,
            'grossSales' => (float) (clone $orders)->where('status', 'COMPLETED')->sum('grand_total'),
            'pendingSales' => (float) (clone $orders)->whereNotIn('status', ['COMPLETED', 'CANCELLED'])->sum('grand_total'),
            'commissionDue' => (float) (clone $commissions)->where('status', 'PENDING')->sum('commission_amount'),
            'commissions' => (clone $commissions)->with('sellerOrder')->latest()->paginate(15),
        ]);
    }


    public function exportStatement(Request $request)
    {
        return $this->exportOrders($request);
    }

    public function reports(Request $request): View
    {
        $seller = $request->user()->sellerProfile;
        abort_unless($seller, 403);
        $data = $request->validate([
            'report' => ['nullable', 'string', 'in:sales,profit,orders,products'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);
        $from = \Illuminate\Support\Carbon::parse($data['from'] ?? now()->startOfMonth())->startOfDay();
        $to = \Illuminate\Support\Carbon::parse($data['to'] ?? now())->endOfDay();
        $orders = SellerOrder::query()->where('seller_profile_id', $seller->id)->whereBetween('created_at', [$from, $to]);
        $gross = (float) (clone $orders)->sum('grand_total');
        $commission = (float) CommissionTransaction::query()->whereHas('sellerOrder', fn ($query) => $query->where('seller_profile_id', $seller->id))->whereBetween('created_at', [$from, $to])->sum('commission_amount');

        return view('Seller.reports', [
            'report' => $data['report'] ?? 'sales',
            'reportFrom' => $from,
            'reportTo' => $to,
            'reportSummary' => [
                'orders' => (clone $orders)->count(),
                'completed' => (clone $orders)->where('status', 'COMPLETED')->count(),
                'cancelled' => (clone $orders)->where('status', 'CANCELLED')->count(),
                'gross' => $gross,
                'commission' => $commission,
                'net' => $gross - $commission,
            ],
        ]);
    }

    public function downloadReport(Request $request)
    {
        $seller = $request->user()->sellerProfile;
        abort_unless($seller, 403);
        $from = \Illuminate\Support\Carbon::parse($request->query('from', now()->startOfMonth()))->startOfDay();
        $to = \Illuminate\Support\Carbon::parse($request->query('to', now()))->endOfDay();
        $rows = SellerOrder::query()->where('seller_profile_id', $seller->id)->whereBetween('created_at', [$from, $to])->latest()->get();
        $csv = "Order,Status,Gross,Created\n".$rows->map(fn ($order) => implode(',', [$order->seller_order_number, $order->status, $order->grand_total, $order->created_at?->toDateTimeString()]))->implode("\n");

        return Response::make($csv, 200, ['Content-Type' => 'text/csv', 'Content-Disposition' => 'attachment; filename="seller-report.csv"']);
    }
}
