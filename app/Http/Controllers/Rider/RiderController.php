<?php

namespace App\Http\Controllers\Rider;

use App\Http\Controllers\Controller;
use App\Models\Rider\RiderAssignment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RiderController extends Controller
{
    public function dashboard(Request $request): View
    {
        $rider = $this->rider($request);

        $assignments = RiderAssignment::query()->where('rider_profile_id', $rider->id);

        return view('Rider.dashboard', [
            'rider' => $rider,
            'stats' => [
                'pickup_pending' => (clone $assignments)->pickup()->whereIn('status', ['ASSIGNED', 'ACCEPTED'])->count(),
                'delivery_pending' => (clone $assignments)->delivery()->whereIn('status', ['ASSIGNED', 'ACCEPTED'])->count(),
                'in_progress' => (clone $assignments)->where('status', 'IN_PROGRESS')->count(),
                'completed' => (clone $assignments)->where('status', 'COMPLETED')->count(),
                'earnings' => (float) $rider->earnings()->sum('amount'),
            ],
            'todayAssignments' => (clone $assignments)->with(['shipment.sellerOrder.order.address'])->latest()->limit(8)->get(),
        ]);
    }

    public function shipments(Request $request): View
    {
        $rider = $this->rider($request);

        $assignments = RiderAssignment::query()
            ->where('rider_profile_id', $rider->id)
            ->with(['shipment.sellerOrder.order.address', 'shipment.sellerOrder.sellerProfile.user'])
            ->latest()
            ->paginate(15);

        return view('Rider.shipments', compact('assignments', 'rider'));
    }

    public function pickups(Request $request): View
    {
        $rider = $this->rider($request);

        $assignments = RiderAssignment::query()
            ->where('rider_profile_id', $rider->id)
            ->pickup()
            ->whereIn('status', ['ASSIGNED', 'ACCEPTED', 'IN_PROGRESS'])
            ->with(['shipment.sellerOrder.order.address', 'shipment.sellerOrder.sellerProfile.user'])
            ->latest()
            ->paginate(15);

        return view('Rider.pickups.index', compact('assignments', 'rider'));
    }

    public function pickupShow(Request $request, RiderAssignment $assignment): View
    {
        $rider = $this->rider($request);
        abort_unless((int) $assignment->rider_profile_id === (int) $rider->id && $assignment->assignment_type === 'PICKUP', 403);

        $assignment->load(['shipment.sellerOrder.items', 'shipment.sellerOrder.order.address', 'shipment.sellerOrder.sellerProfile.user', 'shipment.events', 'shipment.scans']);

        return view('Rider.pickups.show', compact('assignment', 'rider'));
    }

    public function deliveries(Request $request): View
    {
        $rider = $this->rider($request);

        $assignments = RiderAssignment::query()
            ->where('rider_profile_id', $rider->id)
            ->delivery()
            ->whereIn('status', ['ASSIGNED', 'ACCEPTED', 'IN_PROGRESS'])
            ->with(['shipment.sellerOrder.order.address', 'shipment.sellerOrder.sellerProfile.user'])
            ->latest()
            ->paginate(15);

        return view('Rider.deliveries.index', compact('assignments', 'rider'));
    }

    public function deliveryShow(Request $request, RiderAssignment $assignment): View
    {
        $rider = $this->rider($request);
        abort_unless((int) $assignment->rider_profile_id === (int) $rider->id && $assignment->assignment_type === 'DELIVERY', 403);

        $assignment->load(['shipment.sellerOrder.items', 'shipment.sellerOrder.order.address', 'shipment.sellerOrder.sellerProfile.user', 'shipment.events', 'shipment.deliveryAttempts']);

        return view('Rider.deliveries.show', compact('assignment', 'rider'));
    }

    public function earnings(Request $request): View
    {
        $rider = $this->rider($request);

        $earnings = $rider->earnings()->with('riderAssignment.shipment')->latest()->paginate(15);

        return view('Rider.earnings.index', compact('rider', 'earnings'));
    }

    public function history(Request $request): View
    {
        $rider = $this->rider($request);

        $assignments = RiderAssignment::query()
            ->where('rider_profile_id', $rider->id)
            ->whereIn('status', ['COMPLETED', 'REJECTED', 'CANCELLED'])
            ->with(['shipment.sellerOrder.order.address'])
            ->latest()
            ->paginate(15);

        return view('Rider.history.index', compact('rider', 'assignments'));
    }

    public function profile(Request $request): View
    {
        $rider = $this->rider($request)->load(['user', 'logisticsCenter', 'areaAssignments.serviceArea.locations']);

        return view('Rider.profile.index', compact('rider'));
    }

    public function messages(): View
    {
        return view('Rider.messages', ['conversations' => collect()]);
    }

    public function sendMessage(): RedirectResponse
    {
        return back()->with('status', 'Messaging is handled in Phase 5.');
    }

    private function rider(Request $request)
    {
        $rider = $request->user()->riderProfile;
        abort_unless($rider, 403);

        return $rider;
    }
}
