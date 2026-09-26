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
            ->with(['order.buyer', 'shipment'])
            ->latest()
            ->limit(8)
            ->get();

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
                'ready' => $stats['ready'],
            ],
            'recentOrders' => $recentOrders,
        ]);
    }

    public function logistics(Request $request): View
    {
        $seller = $request->user()->sellerProfile;
        abort_unless($seller, 403);

        $orders = SellerOrder::query()
            ->where('seller_profile_id', $seller->id)
            ->with(['order.address', 'shipment.pickupRequests', 'shipment.riderAssignments.riderProfile.user'])
            ->whereIn('status', ['CONFIRMED', 'PREPARING', 'READY_FOR_PICKUP', 'PICKED_UP'])
            ->latest()
            ->paginate(15);

        return view('Seller.orders', [
            'seller' => $seller,
            'orders' => $orders,
            'counts' => collect(),
            'activeStatus' => 'LOGISTICS',
        ]);
    }

    public function waybill(Request $request, SellerOrder $sellerOrder, ShipmentWorkflowService $workflow): View
    {
        $seller = $request->user()->sellerProfile;
        abort_unless($seller && (int) $sellerOrder->seller_profile_id === (int) $seller->id, 403);

        $shipment = $workflow->ensureShipment($sellerOrder);
        $shipment->loadMissing(['sellerOrder.items', 'sellerOrder.order.address', 'waybills', 'events']);

        return view('Seller.waybill', [
            'sellerOrder' => $sellerOrder->fresh(['items', 'sellerProfile.user']),
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
        return $this->finance($request);
    }

    public function downloadReport(Request $request)
    {
        return $this->exportOrders($request);
    }
}
