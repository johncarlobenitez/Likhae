<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Seller\SellerOrder;
use App\Services\Fulfillment\ShipmentWorkflowService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SellerOrderController extends Controller
{
    public function index(Request $request): View
    {
        $seller = $request->user()->sellerProfile;
        abort_unless($seller, 403);

        $status = strtoupper((string) $request->query('status', ''));

        $orders = SellerOrder::query()
            ->where('seller_profile_id', $seller->id)
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->with(['order.buyer', 'order.address', 'items', 'shipment.events', 'shipment.pickupRequests', 'shipment.riderAssignments.riderProfile.user'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $counts = SellerOrder::query()
            ->where('seller_profile_id', $seller->id)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('Seller.orders', [
            'seller' => $seller,
            'orders' => $orders,
            'counts' => $counts,
            'activeStatus' => $status,
        ]);
    }

    public function transition(Request $request, SellerOrder $sellerOrder, ShipmentWorkflowService $workflow): RedirectResponse
    {
        $seller = $request->user()->sellerProfile;
        abort_unless($seller && (int) $sellerOrder->seller_profile_id === (int) $seller->id, 403);

        $data = $request->validate([
            'action' => ['required', 'string', 'in:confirm,prepare,ready,cancel'],
        ]);

        $workflow->sellerTransition($sellerOrder, $data['action'], $request->user());

        return back()->with('status', 'Seller order updated successfully.');
    }
}
