<?php

namespace App\Http\Controllers\Logistics;

use App\Http\Controllers\Controller;
use App\Models\Logistics\LogisticsCenter;
use App\Models\Logistics\PickupRequest;
use App\Models\Logistics\ServiceArea;
use App\Models\Logistics\Shipment;
use App\Models\Rider\RiderProfile;
use App\Services\Fulfillment\ShipmentWorkflowService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DispatchController extends Controller
{
    public function index(Request $request): View
    {
        return $this->assignments($request);
    }

    public function parcels(Request $request): View
    {
        $center = $this->center($request);
        $status = strtoupper((string) $request->query('status', ''));

        $shipments = Shipment::query()
            ->when($center, fn ($query) => $query->where(function ($q) use ($center) {
                $q->where('logistics_center_id', $center->id)
                    ->orWhere(fn ($unassigned) => $unassigned->whereNull('logistics_center_id')
                        ->whereHas('serviceArea', fn ($area) => $area->where('logistics_center_id', $center->id)));
            }))
            ->when($status !== '', fn ($query) => $query->where('current_status', $status))
            ->with(['sellerOrder.sellerProfile.user', 'sellerOrder.order.address', 'serviceArea', 'riderAssignments.riderProfile.user'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('Logistics.parcels.index', compact('shipments', 'status', 'center'));
    }

    public function pickups(Request $request): View
    {
        $center = $this->center($request);
        $tracking = trim((string) $request->query('tracking', ''));

        $pickups = PickupRequest::query()
            ->whereIn('status', ['PENDING', 'APPROVED'])
            ->whereHas('shipment', function ($query) use ($center, $tracking) {
                if ($center) {
                    $query->where(function ($q) use ($center) {
                        $q->where('logistics_center_id', $center->id)
                            ->orWhere(fn ($unassigned) => $unassigned->whereNull('logistics_center_id')
                                ->whereHas('serviceArea', fn ($area) => $area->where('logistics_center_id', $center->id)));
                    });
                }
                if ($tracking !== '') {
                    $query->where('tracking_number', $tracking);
                }
            })
            ->with(['shipment.sellerOrder.sellerProfile.user', 'shipment.sellerOrder.order.address', 'shipment.riderAssignments.riderProfile.user'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $riders = RiderProfile::query()
            ->where('status', 'ACTIVE')
            ->when($center, fn ($query) => $query->where('logistics_center_id', $center->id))
            ->with('user')
            ->orderBy('id')
            ->get();

        return view('Logistics.pickups.index', compact('pickups', 'riders', 'center'));
    }

    public function receive(Request $request): View
    {
        $center = $this->center($request);
        $tracking = trim((string) $request->query('tracking', ''));

        $shipments = Shipment::query()
            ->where('current_status', 'PICKED_UP')
            ->when($tracking !== '', fn ($query) => $query->where('tracking_number', $tracking))
            ->when($center, fn ($query) => $query->where(function ($q) use ($center) {
                $q->where('logistics_center_id', $center->id)
                    ->orWhere(fn ($unassigned) => $unassigned->whereNull('logistics_center_id')
                        ->whereHas('serviceArea', fn ($area) => $area->where('logistics_center_id', $center->id)));
            }))
            ->with(['sellerOrder.sellerProfile.user', 'sellerOrder.order.address'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('Logistics.parcels.receive', compact('shipments', 'center'));
    }

    public function sorting(Request $request): View
    {
        $center = $this->center($request);
        $tracking = trim((string) $request->query('tracking', ''));

        $shipments = Shipment::query()
            ->whereIn('current_status', ['AT_SORTING_CENTER', 'SORTED'])
            ->when($tracking !== '', fn ($query) => $query->where('tracking_number', $tracking))
            ->when($center, fn ($query) => $query->where('logistics_center_id', $center->id))
            ->with(['sellerOrder.order.address', 'serviceArea'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $areas = ServiceArea::query()
            ->when($center, fn ($query) => $query->where('logistics_center_id', $center->id))
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('Logistics.sorting.index', compact('shipments', 'areas', 'center'));
    }

    public function assignments(Request $request): View
    {
        $center = $this->center($request);
        $tracking = trim((string) $request->query('tracking', ''));

        $shipments = Shipment::query()
            ->whereIn('current_status', ['SORTED', 'ASSIGNED_TO_RIDER', 'OUT_FOR_DELIVERY', 'DELIVERY_FAILED'])
            ->when($tracking !== '', fn ($query) => $query->where('tracking_number', $tracking))
            ->when($center, fn ($query) => $query->where('logistics_center_id', $center->id))
            ->with(['sellerOrder.order.address', 'serviceArea', 'riderAssignments.riderProfile.user'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $riders = RiderProfile::query()
            ->where('status', 'ACTIVE')
            ->when($center, fn ($query) => $query->where('logistics_center_id', $center->id))
            ->with(['user', 'areaAssignments.serviceArea'])
            ->orderBy('id')
            ->get();

        return view('Logistics.assignments.index', compact('shipments', 'riders', 'center'));
    }

    public function tracking(Request $request): View
    {
        $shipment = null;
        $tracking = trim((string) $request->query('tracking'));

        if ($tracking !== '') {
            $center = $this->center($request);
            abort_unless($center, 403);
            $shipment = Shipment::query()
                ->where('tracking_number', $tracking)
                ->where(function ($query) use ($center) {
                    $query->where('logistics_center_id', $center->id)
                        ->orWhere(fn ($unassigned) => $unassigned->whereNull('logistics_center_id')
                            ->whereHas('serviceArea', fn ($area) => $area->where('logistics_center_id', $center->id)));
                })
                ->with(['sellerOrder.items', 'sellerOrder.order.address', 'events.actor', 'scans', 'deliveryAttempts'])
                ->first();
        }

        return view('Logistics.tracking.index', compact('shipment', 'tracking'));
    }

    public function show(Shipment $shipment): View
    {
        $center = request()->user()?->logisticsCenter;
        abort_unless($center, 403);
        $this->ensureShipmentBelongsToCenter($shipment, $center);
        $shipment->load(['sellerOrder.items', 'sellerOrder.sellerProfile.user', 'sellerOrder.order.address', 'sellerOrder.order.buyer', 'serviceArea', 'riderAssignments.riderProfile.user', 'events.actor', 'scans', 'deliveryAttempts']);

        return view('Logistics.parcels.show', compact('shipment'));
    }

    public function assign(Request $request, Shipment $shipment, ShipmentWorkflowService $workflow): RedirectResponse
    {
        $center = $this->center($request);
        abort_unless($center, 403);
        $this->ensureShipmentBelongsToCenter($shipment, $center);
        $data = $request->validate([
            'rider_profile_id' => ['required', 'integer', 'exists:rider_profiles,id'],
            'assignment_type' => ['required', 'string', 'in:PICKUP,DELIVERY'],
        ]);

        $rider = RiderProfile::findOrFail($data['rider_profile_id']);
        abort_unless((int) $rider->logistics_center_id === (int) $center->id, 403);
        $workflow->assignRider($shipment, $rider, $data['assignment_type'], $request->user());

        return back()->with('status', 'Rider assignment created.');
    }

    public function assignPickup(Request $request, Shipment $shipment, ShipmentWorkflowService $workflow): RedirectResponse
    {
        $center = $this->center($request);
        abort_unless($center, 403);
        $this->ensureShipmentBelongsToCenter($shipment, $center);
        $data = $request->validate([
            'rider_profile_id' => ['required', 'integer', 'exists:rider_profiles,id'],
        ]);

        $pickup = $shipment->pickupRequests()->whereIn('status', ['PENDING', 'APPROVED'])->latest()->firstOrFail();
        if ($pickup->status === 'PENDING') {
            $workflow->approvePickup($pickup, $request->user());
        }

        $rider = RiderProfile::findOrFail($data['rider_profile_id']);
        abort_unless((int) $rider->logistics_center_id === (int) $center->id, 403);
        $workflow->assignRider($shipment, $rider, 'PICKUP', $request->user());

        return back()->with('status', 'Pickup rider assigned.');
    }

    public function approvePickup(Request $request, PickupRequest $pickupRequest, ShipmentWorkflowService $workflow): RedirectResponse
    {
        $center = $this->center($request);
        abort_unless($center, 403);
        $this->ensureShipmentBelongsToCenter($pickupRequest->shipment, $center);
        $workflow->approvePickup($pickupRequest, $request->user(), $request->input('notes'));

        return back()->with('status', 'Pickup request approved.');
    }

    public function rejectPickup(Request $request, PickupRequest $pickupRequest, ShipmentWorkflowService $workflow): RedirectResponse
    {
        $center = $this->center($request);
        abort_unless($center, 403);
        $this->ensureShipmentBelongsToCenter($pickupRequest->shipment, $center);
        $data = $request->validate(['rejection_reason' => ['required', 'string', 'max:1000']]);
        $workflow->rejectPickup($pickupRequest, $request->user(), $data['rejection_reason']);

        return back()->with('status', 'Pickup request rejected.');
    }

    public function confirmReceive(Request $request, Shipment $shipment, ShipmentWorkflowService $workflow): RedirectResponse
    {
        $center = $this->center($request);
        abort_unless($center, 403);
        $this->ensureShipmentBelongsToCenter($shipment, $center);

        $data = $request->validate([
            'scanned_code' => ['required', 'string', 'max:150'],
            'scan_method' => ['nullable', 'string', 'in:QR,BARCODE,MANUAL'],
        ]);

        $workflow->receiveAtCenter($shipment, $center, $request->user(), $data['scanned_code'], $data['scan_method'] ?? 'MANUAL');

        return back()->with('status', 'Parcel received at sorting center.');
    }

    public function sortParcel(Request $request, Shipment $shipment, ShipmentWorkflowService $workflow): RedirectResponse
    {
        $center = $this->center($request);
        abort_unless($center, 403);
        $this->ensureShipmentBelongsToCenter($shipment, $center);

        $data = $request->validate([
            'service_area_id' => ['nullable', 'integer', 'exists:service_areas,id'],
        ]);

        $area = isset($data['service_area_id'])
            ? ServiceArea::query()->where('logistics_center_id', $center->id)->where('is_active', true)->findOrFail($data['service_area_id'])
            : null;
        $workflow->sortShipment($shipment, $center, $request->user(), $area);

        return back()->with('status', 'Parcel sorted successfully.');
    }

    public function waybill(Request $request, string $tracking): View
    {
        $center = $this->center($request);
        abort_unless($center, 403);
        $shipment = Shipment::query()
            ->where('tracking_number', $tracking)
            ->where('logistics_center_id', $center->id)
            ->with(['sellerOrder.items', 'sellerOrder.sellerProfile.user', 'sellerOrder.sellerProfile.businessAddress', 'sellerOrder.order.address', 'waybills'])
            ->firstOrFail();

        return view('Logistics.waybill', compact('shipment'));
    }

    private function center(Request $request): mixed
    {
        return $request->user()->logisticsCenter;
    }

    private function ensureShipmentBelongsToCenter(Shipment $shipment, LogisticsCenter $center): void
    {
        $shipment->loadMissing('serviceArea');
        $isAssignedToCenter = (int) $shipment->logistics_center_id === (int) $center->id;
        $areaBelongsToCenter = ! $shipment->logistics_center_id
            && (int) $shipment->serviceArea?->logistics_center_id === (int) $center->id;

        abort_unless($isAssignedToCenter || $areaBelongsToCenter, 403);
    }
}
