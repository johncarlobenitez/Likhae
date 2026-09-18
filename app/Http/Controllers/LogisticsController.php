<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\Message;
use App\Models\ParcelAssignment;
use App\Models\User;
use App\Models\WorkspaceNotification;
use App\Services\ParcelWorkflow;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class LogisticsController extends Controller
{
    public function __construct(private readonly ParcelWorkflow $workflow) {}

    public function pickupRequests(): View
    {
        $deliveries = $this->deliveryQuery()
            ->whereIn('status', ['awaiting_pickup_assignment', 'pickup_assigned', 'pickup_accepted', 'picked_up'])
            ->latest()
            ->get();
        $riders = $this->riderQuery()->where('status', 'active')->get();

        $pickupRows = $deliveries->map(function (Delivery $delivery) use ($riders) {
            $seller = $delivery->order?->seller;
            $ranked = $riders->map(function (User $rider) use ($seller) {
                $locationScore = ($seller?->barangay && $rider->barangay === $seller->barangay ? 3 : 0)
                    + ($seller?->municipality && $rider->municipality === $seller->municipality ? 2 : 0)
                    + ($seller?->province && $rider->province === $seller->province ? 1 : 0);
                $workload = ParcelAssignment::where('rider_id', $rider->id)->whereIn('status', ['assigned', 'accepted', 'picked_up'])->count();
                return ['id' => $rider->id, 'name' => $rider->name, 'area' => $rider->municipality ?: 'Area not recorded', 'workload' => $workload, 'score' => $locationScore];
            })->sortBy([['score', 'desc'], ['workload', 'asc'], ['name', 'asc']])->values();

            return ['parcel' => $this->parcelRow($delivery), 'delivery' => $delivery, 'riders' => $ranked];
        });

        return view('logistics.pickups.index', compact('pickupRows'));
    }

    public function assignPickupRider(Request $request, Delivery $delivery): RedirectResponse
    {
        $this->authorizeDelivery($delivery);
        abort_unless($delivery->status === 'awaiting_pickup_assignment', 422, 'This parcel is not awaiting a pickup rider.');
        $validated = $request->validate(['rider_id' => ['required', 'integer', 'exists:users,id']]);
        $rider = $this->riderQuery()->whereKey($validated['rider_id'])->where('status', 'active')->firstOrFail();

        DB::transaction(function () use ($delivery, $rider, $request) {
            $this->workflow->assign($delivery, $rider, 'seller_pickup', $request->user());
            $this->workflow->transition($delivery, 'pickup_assigned', $request->user(), "Pickup assigned to {$rider->name}.", ['pickup_rider_id' => $rider->id, 'pickup_assigned_at' => now()]);
            WorkspaceNotification::create(['user_id' => $rider->id, 'type' => 'pickup', 'title' => 'New seller pickup', 'body' => "Parcel {$delivery->tracking_number} has been assigned to you.", 'action_url' => route('rider.pickups.show', $delivery)]);
        });

        return back()->with('status', "Pickup assigned to {$rider->name}.");
    }

    public function dashboard(Request $request): View
    {
        $deliveries = $this->deliveryQuery()->latest()->get();
        $today = now()->startOfDay();
        $activeRiders = $this->riderQuery()->where('status', 'active')->count();

        $logisticsStats = [
            [
                'label' => 'Received Today',
                'value' => $deliveries->filter(fn (Delivery $delivery) => $delivery->updated_at?->gte($today))->count(),
                'description' => 'Incoming parcels',
                'change' => $deliveries->whereIn('status', ['requested', 'received'])->count().' incoming',
                'type' => 'primary',
                'icon' => 'box',
            ],
            [
                'label' => 'Sorting Queue',
                'value' => $deliveries->whereIn('status', ['received', 'scanned', 'at_sorting_center'])->count(),
                'description' => 'Waiting for sorting',
                'change' => $deliveries->where('status', 'requested')->count().' pickup requests',
                'type' => 'warning',
                'icon' => 'sort',
            ],
            [
                'label' => 'Awaiting Rider',
                'value' => $deliveries->whereIn('status', ['sorted', 'awaiting_rider'])->count(),
                'description' => 'Ready for assignment',
                'change' => $activeRiders.' riders active',
                'type' => 'info',
                'icon' => 'rider',
            ],
            [
                'label' => 'On the Road',
                'value' => $deliveries->where('status', 'out_for_delivery')->count(),
                'description' => 'Out for delivery',
                'change' => $deliveries->where('status', 'delivered')->count().' delivered',
                'type' => 'success',
                'icon' => 'truck',
            ],
        ];

        $logisticsRecentParcels = $deliveries->take(5)->map(fn (Delivery $delivery) => $this->parcelRow($delivery))->values()->all();
        $logisticsAreas = $this->areaRows($deliveries);
        $logisticsActivity = $this->activityRows($deliveries);

        return view('logistics.dashboard.index', compact('logisticsStats', 'logisticsRecentParcels', 'logisticsAreas', 'logisticsActivity'));
    }

    public function parcels(Request $request): View
    {
        $deliveries = $this->deliveryQuery()
            ->when($request->filled('tracking'), fn ($query) => $query->where('tracking_number', 'like', '%'.$request->input('tracking').'%'))
            ->latest()
            ->get();

        $logisticsParcels = $deliveries->map(fn (Delivery $delivery) => $this->parcelRow($delivery))->values()->all();
        $logisticsOverview = [
            ['label' => 'All Parcels', 'value' => $deliveries->count(), 'description' => 'Total parcel records', 'tone' => 'primary', 'icon' => 'parcel'],
            ['label' => 'Waiting Sorting', 'value' => $deliveries->whereIn('status', ['received', 'scanned', 'at_sorting_center'])->count(), 'description' => 'Pending sorting', 'tone' => 'warning', 'icon' => 'sorting'],
            ['label' => 'Awaiting Rider', 'value' => $deliveries->whereIn('status', ['sorted', 'awaiting_rider'])->count(), 'description' => 'Ready for assignment', 'tone' => 'primary', 'icon' => 'rider'],
            ['label' => 'On the Road', 'value' => $deliveries->where('status', 'out_for_delivery')->count(), 'description' => 'Active deliveries', 'tone' => 'primary', 'icon' => 'truck'],
            ['label' => 'Delivered', 'value' => $deliveries->where('status', 'delivered')->count(), 'description' => 'Successfully delivered', 'tone' => 'success', 'icon' => 'check'],
        ];

        return view('logistics.parcels.index', compact('logisticsParcels', 'logisticsOverview'));
    }

    public function parcelShow(Delivery $delivery): View
    {
        $delivery->load(['order.buyer', 'order.seller', 'order.items.product', 'rider']);

        return view('logistics.parcels.show', [
            'id' => $delivery->id,
            'delivery' => $delivery,
            'logisticsParcel' => $this->parcelRow($delivery),
        ]);
    }

    public function receive(Request $request): View
    {
        $tracking = trim((string) $request->query('tracking', ''));
        $delivery = $tracking === '' ? null : $this->deliveryQuery()
            ->where(function ($query) use ($tracking) {
                $query->where('tracking_number', $tracking)
                    ->orWhereHas('order', fn ($orderQuery) => $orderQuery->where('order_number', $tracking));
            })
            ->first();
        $logisticsReceiveParcel = $delivery ? $this->parcelRow($delivery) : null;

        return view('logistics.parcels.receive', compact('tracking', 'delivery', 'logisticsReceiveParcel'));
    }

    public function tracking(Request $request): View
    {
        $tracking = trim((string) $request->query('tracking', ''));
        $delivery = null;

        if ($tracking !== '') {
            $delivery = $this->deliveryQuery()
                ->where(function ($query) use ($tracking) {
                    $query->where('tracking_number', $tracking)
                        ->orWhereHas('order', fn ($orderQuery) => $orderQuery->where('order_number', $tracking));
                })
                ->first();
        }

        $recentDeliveries = $this->deliveryQuery()->latest()->take(10)->get()
            ->map(fn (Delivery $delivery) => $this->parcelRow($delivery))
            ->values();

        return view('logistics.tracking.index', [
            'tracking' => $tracking,
            'delivery' => $delivery,
            'parcel' => $delivery ? $this->parcelRow($delivery) : null,
            'recentDeliveries' => $recentDeliveries,
        ]);
    }

    public function scanner(): RedirectResponse
    {
        return redirect()->route('logistics.parcels.receive');
    }

    public function scan(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'tracking' => ['required', 'string', 'max:120'],
        ]);
        $tracking = trim($validated['tracking']);
        $delivery = $this->deliveryQuery()
            ->where('tracking_number', $tracking)
            ->orWhereHas('order', fn ($query) => $query->where('order_number', $tracking))
            ->first();

        if (! $delivery) {
            return back()->withErrors(['tracking' => 'No parcel was found for that tracking or order number.'])->withInput();
        }

        return redirect()->route('logistics.parcels.show', $delivery);
    }

    public function waybill(string $tracking): View
    {
        $delivery = $this->deliveryQuery()
            ->where('tracking_number', $tracking)
            ->orWhereHas('order', fn ($query) => $query->where('order_number', $tracking))
            ->firstOrFail();

        return view('logistics.waybill', [
            'tracking' => $tracking,
            'delivery' => $delivery,
            'parcel' => $this->parcelRow($delivery),
        ]);
    }

    public function confirmReceived(Request $request, Delivery $delivery): RedirectResponse
    {
        $this->authorizeDelivery($delivery);
        $validated = $request->validate(['tracking' => ['required', 'string', 'max:120']]);
        abort_unless(hash_equals((string) $delivery->tracking_number, trim($validated['tracking'])), 422, 'The scanned tracking number does not match this parcel.');

        if ($delivery->status === 'at_sorting_center') {
            return redirect()->route('logistics.parcels.receive', ['tracking' => $delivery->tracking_number])->with('status', 'This parcel has already been received.');
        }

        DB::transaction(function () use ($request, $delivery) {
            $delivery = Delivery::whereKey($delivery->id)->lockForUpdate()->firstOrFail();

            $allowed = $delivery->handover_method === 'seller_dropoff' ? ['awaiting_dropoff'] : ['picked_up'];
            abort_unless(in_array($delivery->status, $allowed, true), 422, 'The parcel is not ready to be received by this sorting center.');

            $assignment = $delivery->assignments()->where('assignment_type', 'seller_pickup')->first();
            if ($assignment) $assignment->update(['status' => 'completed', 'completed_at' => now()]);
            $this->workflow->scan($delivery, 'sorting_center_received', $request->user(), $assignment);
            $delivery = $this->workflow->transition($delivery, 'at_sorting_center', $request->user(), 'Parcel scanned and received by logistics.', ['arrived_at_sorting_center_at' => now(), 'received_at' => now(), 'received_by' => $request->user()->id]);

            $delivery->order()->update(['status' => 'shipping']);

            WorkspaceNotification::create([
                'user_id' => $delivery->order->seller_id,
                'type' => 'logistics',
                'title' => 'Parcel received at sorting center',
                'body' => "Order {$delivery->order->order_number} has reached the sorting center.",
                'action_url' => route('seller.logistics', ['order' => $delivery->order->order_number]),
            ]);
        });

        return redirect()->route('logistics.sorting')->with('status', 'Parcel received at sorting center and added to the sorting queue.');
    }

    public function riderApplications(): View
    {
        $applications = $this->riderQuery()->latest()->get();
        $logisticsRiderApplications = $applications->map(fn (User $rider) => $this->riderRow($rider))->values()->all();
        $logisticsRiderSummary = [
            ['label' => 'Total Applications', 'value' => $applications->count(), 'description' => 'All rider registrations', 'tone' => 'primary', 'icon' => 'applications'],
            ['label' => 'Pending', 'value' => $applications->where('status', 'pending')->count(), 'description' => 'Awaiting verification', 'tone' => 'warning', 'icon' => 'clock'],
            ['label' => 'Approved', 'value' => $applications->where('status', 'active')->count(), 'description' => 'Verified rider accounts', 'tone' => 'success', 'icon' => 'check'],
            ['label' => 'Rejected', 'value' => $applications->where('status', 'rejected')->count(), 'description' => 'Applications declined', 'tone' => 'danger', 'icon' => 'x'],
        ];

        return view('logistics.riders.application.index', compact('logisticsRiderApplications', 'logisticsRiderSummary'));
    }

    public function sorting(Request $request): View
    {
        $deliveries = $this->deliveryQuery()
            ->whereIn('status', ['received', 'scanned', 'at_sorting_center'])
            ->latest()
            ->get();

        $logisticsParcels = $deliveries->map(fn (Delivery $delivery) => $this->parcelRow($delivery))->values()->all();
        $logisticsOverview = [
            ['label' => 'Sorting Queue', 'value' => $deliveries->count(), 'class' => 'bg-warning-soft text-warning'],
            ['label' => 'Sorted Today', 'value' => $this->deliveryQuery()->where('status', 'sorted')->whereDate('updated_at', today())->count(), 'class' => 'bg-success-soft text-success'],
            ['label' => 'Awaiting Rider', 'value' => $this->deliveryQuery()->whereIn('status', ['sorted', 'awaiting_rider'])->count(), 'class' => 'bg-info-soft text-info'],
            ['label' => 'Unassigned Area', 'value' => $deliveries->filter(fn (Delivery $delivery) => blank($delivery->order?->buyer?->municipality))->count(), 'class' => 'bg-primary-soft text-primary'],
        ];

        $tracking = trim((string) $request->query('tracking', ''));
        $selectedDelivery = $tracking === '' ? null : $deliveries->first(fn (Delivery $delivery) => $delivery->tracking_number === $tracking || $delivery->order?->order_number === $tracking);

        return view('logistics.sorting.index', compact('logisticsParcels', 'logisticsOverview', 'tracking', 'selectedDelivery'));
    }

    public function markSorted(Request $request, Delivery $delivery): RedirectResponse
    {
        $this->authorizeDelivery($delivery);
        $validated = $request->validate(['tracking' => ['required', 'string', 'max:120']]);
        abort_unless(hash_equals((string) $delivery->tracking_number, trim($validated['tracking'])), 422, 'The scanned tracking number does not match this parcel.');

        abort_unless(in_array($delivery->status, ['received', 'scanned', 'at_sorting_center'], true), 422, 'Only parcels received at the sorting center can be sorted.');

        DB::transaction(function () use ($delivery) {
            $delivery = Delivery::whereKey($delivery->id)->lockForUpdate()->firstOrFail();
            $buyer = $delivery->order?->buyer;
            $area = collect([$buyer?->barangay, $buyer?->municipality, $buyer?->province])->filter()->implode(', ');
            $this->workflow->scan($delivery, 'sorting_scan', request()->user());
            $delivery = $this->workflow->transition($delivery, 'sorted', request()->user(), 'Parcel sorted by delivery destination.', ['sorted_at' => now(), 'delivery_area' => $area ?: null]);

            WorkspaceNotification::create([
                'user_id' => $delivery->order->seller_id,
                'type' => 'logistics',
                'title' => 'Parcel sorted',
                'body' => "Order {$delivery->order->order_number} has been sorted by destination.",
                'action_url' => route('seller.logistics', ['order' => $delivery->order->order_number]),
            ]);
        });

        return back()->with('status', 'Parcel marked as sorted and ready for rider assignment.');
    }

    public function assignments(Request $request): View
    {
        $waiting = $this->deliveryQuery()
            ->whereIn('status', ['sorted', 'awaiting_rider'])
            ->latest()
            ->get();
        $activeRiders = $this->riderQuery()->where('status', 'active')->orderBy('name')->get();
        $outForDelivery = $this->deliveryQuery()->where('status', 'out_for_delivery')->count();
        $completedToday = $this->deliveryQuery()->where('status', 'delivered')->whereDate('delivered_at', today())->count();

        $logisticsAssignmentStats = [
            ['label' => 'Waiting Assignment', 'value' => $waiting->count(), 'class' => 'bg-primary-soft text-primary'],
            ['label' => 'Available Riders', 'value' => $activeRiders->count(), 'class' => 'bg-info-soft text-info'],
            ['label' => 'Out for Delivery', 'value' => $outForDelivery, 'class' => 'bg-warning-soft text-warning'],
            ['label' => 'Completed Today', 'value' => $completedToday, 'class' => 'bg-success-soft text-success'],
        ];

        $logisticsAssignmentParcels = $waiting->map(fn (Delivery $delivery) => $this->parcelRow($delivery))->values()->all();
        $logisticsAssignmentRiders = $activeRiders->map(fn (User $rider) => [
            'id' => $rider->id,
            'name' => $rider->name,
            'area' => $rider->municipality ?: 'Unassigned',
            'active' => ParcelAssignment::where('rider_id', $rider->id)->where('assignment_type', 'final_delivery')->whereIn('status', ['assigned', 'accepted', 'picked_up'])->count(),
        ])->values()->all();

        $tracking = trim((string) $request->query('tracking', ''));
        $releaseDelivery = $tracking === '' ? null : $this->deliveryQuery()
            ->where('status', 'assigned_to_rider')
            ->where(fn ($query) => $query->where('tracking_number', $tracking)->orWhereHas('order', fn ($order) => $order->where('order_number', $tracking)))
            ->first();
        $releaseAssignment = $releaseDelivery?->assignments->firstWhere('assignment_type', 'final_delivery');
        $releaseAuthorized = $releaseDelivery?->scanEvents()->where('scan_type', 'delivery_release_scan')->exists() ?? false;

        return view('logistics.assignments.index', compact('logisticsAssignmentStats', 'logisticsAssignmentParcels', 'logisticsAssignmentRiders', 'tracking', 'releaseDelivery', 'releaseAssignment', 'releaseAuthorized'));
    }

    public function assignRider(Request $request, Delivery $delivery): RedirectResponse
    {
        $this->authorizeDelivery($delivery);

        $validated = $request->validate([
            'rider_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        $rider = $this->riderQuery()->whereKey($validated['rider_id'])->where('status', 'active')->firstOrFail();

        abort_unless(in_array($delivery->status, ['sorted', 'awaiting_rider'], true), 422, 'Only sorted parcels can be assigned to riders.');

        DB::transaction(function () use ($delivery, $rider) {
            $delivery = Delivery::whereKey($delivery->id)->lockForUpdate()->firstOrFail();
            $this->workflow->assign($delivery, $rider, 'final_delivery', request()->user());
            $delivery = $this->workflow->transition($delivery, 'assigned_to_rider', request()->user(), "Final delivery assigned to {$rider->name}.", ['rider_id' => $rider->id, 'assigned_at' => now()]);

            WorkspaceNotification::create([
                'user_id' => $rider->id,
                'type' => 'delivery',
                'title' => 'New delivery assignment',
                'body' => "Parcel {$delivery->tracking_number} has been assigned to you.",
                'action_url' => route('rider.deliveries.show', $delivery->id),
            ]);
        });

        return back()->with('status', "Parcel assigned to {$rider->name}.");
    }

    public function releaseToRider(Request $request, Delivery $delivery): RedirectResponse
    {
        $this->authorizeDelivery($delivery);
        $validated = $request->validate(['tracking' => ['required', 'string', 'max:120']]);
        abort_unless(hash_equals((string) $delivery->tracking_number, trim($validated['tracking'])), 422, 'The scanned tracking number does not match this parcel.');
        abort_unless($delivery->status === 'assigned_to_rider', 422, 'Only parcels assigned to a final-delivery rider can be released.');
        $assignment = $delivery->assignments()->where('assignment_type', 'final_delivery')->first();
        abort_unless($assignment, 422, 'This parcel has no final-delivery assignment.');
        if ($assignment->status === 'assigned') {
            return redirect()->route('logistics.assignments', ['tracking' => $delivery->tracking_number])
                ->withErrors(['release' => 'The assigned rider must accept the delivery assignment before Logistics can authorize release.']);
        }
        abort_unless($assignment->status === 'accepted', 422, 'This final-delivery assignment is not eligible for release.');

        if ($delivery->scanEvents()->where('scan_type', 'delivery_release_scan')->where('assignment_id', $assignment->id)->exists()) {
            return redirect()->route('logistics.assignments', ['tracking' => $delivery->tracking_number])->with('status', 'This parcel was already authorized for release.');
        }

        $this->workflow->scan($delivery, 'delivery_release_scan', $request->user(), $assignment);

        return redirect()->route('logistics.assignments', ['tracking' => $delivery->tracking_number])->with('status', 'Parcel verified and authorized for release to '.$assignment->rider->name.'.');
    }

    public function riders(): View
    {
        $riders = $this->riderQuery()->latest()->get();
        $logisticsRiders = $riders->map(fn (User $rider) => [
            'id' => $rider->id,
            'code' => 'RID-'.str_pad((string) $rider->id, 4, '0', STR_PAD_LEFT),
            'name' => $rider->name,
            'area' => $rider->municipality ?: 'Unassigned',
            'status' => $this->riderStatusLabel($rider),
            'parcels' => Delivery::where('rider_id', $rider->id)->whereIn('status', ['assigned', 'out_for_delivery'])->count(),
        ])->values()->all();
        $logisticsRiderStats = [
            ['label' => 'Total Riders', 'value' => $riders->count(), 'tone' => 'primary'],
            ['label' => 'Available', 'value' => $riders->where('status', 'active')->count(), 'tone' => 'success'],
            ['label' => 'Delivering', 'value' => collect($logisticsRiders)->where('parcels', '>', 0)->count(), 'tone' => 'info'],
            ['label' => 'Inactive', 'value' => $riders->whereNotIn('status', ['active', 'pending'])->count(), 'tone' => 'warning'],
        ];

        return view('logistics.riders.index', compact('logisticsRiders', 'logisticsRiderStats'));
    }

    public function deliveryAreas(): View
    {
        $deliveries = $this->deliveryQuery()->latest()->get();
        $riders = $this->riderQuery()->latest()->get();
        $areas = $deliveries
            ->map(function (Delivery $delivery) use ($riders) {
                $destination = $delivery->address ?: $delivery->order?->shipping_address ?: $this->addressFor($delivery->order?->buyer);
                $municipality = $delivery->order?->buyer?->municipality ?: $this->destinationPart($destination, 1) ?: 'Unassigned';
                $province = $delivery->order?->buyer?->province ?: $this->destinationPart($destination, 2) ?: 'Not recorded';

                return [
                    'province' => $province,
                    'municipality' => $municipality,
                    'barangay' => $delivery->order?->buyer?->barangay ?: 'Not recorded',
                    'status' => $municipality === 'Unassigned' ? 'Needs address review' : 'Active',
                    'riders' => $riders->where('municipality', $municipality)->where('status', 'active')->count(),
                    'parcels' => 1,
                ];
            })
            ->groupBy(fn (array $area) => $area['province'].'|'.$area['municipality'].'|'.$area['barangay'])
            ->map(function ($group) {
                $first = $group->first();
                $first['parcels'] = $group->sum('parcels');

                return $first;
            })
            ->values();

        return view('logistics.delivery-areas.index', [
            'areas' => $areas,
            'areaStats' => [
                'total' => $areas->count(),
                'active' => $areas->where('status', 'Active')->count(),
                'riders' => $riders->where('status', 'active')->count(),
                'unassigned' => $areas->where('municipality', 'Unassigned')->count(),
            ],
        ]);
    }

    public function messages(Request $request): View
    {
        $user = $request->user();
        $contacts = User::whereIn('role', ['seller', 'buyer', 'rider', 'courier'])
            ->where('status', 'active')
            ->whereKeyNot($user->id)
            ->orderBy('name')
            ->get();
        $selected = $request->integer('contact') ? $contacts->firstWhere('id', $request->integer('contact')) : $contacts->first();
        $messages = $selected
            ? Message::with('sender')
                ->where(function ($query) use ($user, $selected) {
                    $query->where('sender_id', $user->id)->where('recipient_id', $selected->id);
                })
                ->orWhere(function ($query) use ($user, $selected) {
                    $query->where('sender_id', $selected->id)->where('recipient_id', $user->id);
                })
                ->orderBy('created_at')
                ->get()
            : collect();

        return view('logistics.messages.index', compact('contacts', 'selected', 'messages'));
    }

    public function reports(Request $request): View
    {
        $deliveries = $this->deliveryQuery()->latest()->get();
        $delivered = $deliveries->where('status', 'delivered')->count();
        $failed = $deliveries->where('status', 'delivery_failed')->count();
        $total = max($deliveries->count(), 1);

        return view('logistics.reports.index', [
            'summaryCards' => [
                ['label' => 'Parcels Received', 'value' => $deliveries->whereIn('status', ['at_sorting_center', 'sorted', 'assigned', 'out_for_delivery', 'delivered'])->count(), 'change' => 'From delivery records', 'tone' => 'primary', 'icon' => 'package'],
                ['label' => 'Delivered', 'value' => $delivered, 'change' => round(($delivered / $total) * 100, 1).'% success', 'tone' => 'success', 'icon' => 'check'],
                ['label' => 'Out for Delivery', 'value' => $deliveries->where('status', 'out_for_delivery')->count(), 'change' => 'Active now', 'tone' => 'primary', 'icon' => 'truck'],
                ['label' => 'Failed Delivery', 'value' => $failed, 'change' => 'Recorded failures', 'tone' => 'warning', 'icon' => 'alert'],
            ],
            'parcelSummary' => $deliveries->groupBy(fn (Delivery $delivery) => $delivery->updated_at?->format('M d, Y') ?: 'No date')->map(fn ($group, $date) => [
                $date,
                $group->whereIn('status', ['at_sorting_center', 'sorted', 'assigned', 'out_for_delivery', 'delivered'])->count(),
                $group->whereIn('status', ['sorted', 'assigned', 'out_for_delivery', 'delivered'])->count(),
                $group->where('status', 'out_for_delivery')->count(),
                $group->where('status', 'delivered')->count(),
                $group->where('status', 'delivery_failed')->count(),
            ])->values()->take(10),
            'deliveryOverview' => $deliveries->groupBy(fn (Delivery $delivery) => $delivery->updated_at?->format('M d') ?: 'No date')->map(fn ($group, $date) => ['label' => $date, 'value' => $group->count()])->values()->take(7),
            'successRate' => round(($delivered / $total) * 100, 1),
            'pendingCount' => $deliveries->whereNotIn('status', ['delivered', 'delivery_failed'])->count(),
            'riders' => $this->riderQuery()->where('status', 'active')->get()->map(fn (User $rider) => [
                $rider->name,
                $rider->municipality ?: 'Unassigned',
                Delivery::where('rider_id', $rider->id)->count(),
                Delivery::where('rider_id', $rider->id)->where('status', 'delivered')->count(),
                'No rating data',
                '',
            ]),
            'areas' => $this->areaRows($deliveries),
            'codTotal' => $deliveries->filter(fn (Delivery $delivery) => $delivery->order?->payment_method === 'cod')->sum(fn (Delivery $delivery) => (float) $delivery->order?->total_amount),
        ]);
    }

    public function profile(Request $request): View
    {
        return view('logistics.profile.index', ['accountUser' => $request->user()]);
    }

    public function riderShow(User $user): View
    {
        abort_unless(in_array($user->role, ['courier', 'rider'], true), 404);

        return view('logistics.riders.show', ['rider' => $this->riderRow($user)]);
    }

    public function approveRider(Request $request, User $user): RedirectResponse
    {
        return $this->reviewRider($request, $user, 'active');
    }

    public function rejectRider(Request $request, User $user): RedirectResponse
    {
        return $this->reviewRider($request, $user, 'rejected');
    }

    public function index(string $mode = 'logistics'): View
    {
        $deliveries = Delivery::with('rider')->latest()->get();
        $centerId = $this->currentCenterId();
        $riders = User::where('role', 'courier')
            ->when($centerId, fn ($query) => $query->where('logistics_center_id', $centerId))
            ->get();
        $currentUser = $this->currentUser();
        $messages = $currentUser ? Message::with('sender')->where('recipient_id', $currentUser->id)->latest()->get() : collect();
        $notifications = $currentUser ? WorkspaceNotification::where('user_id', $currentUser->id)->latest()->get() : collect();
        $contacts = User::whereIn('role', ['courier', 'seller'])->where('status', 'active')->orderBy('name')->get();

        $view = $mode === 'logistics' ? 'Logistics.dashboard' : 'Logistics.screen';

        return view($view, [
            'mode' => $mode,
            'deliveries' => $deliveries,
            'riders' => $riders,
            'stats' => [
                'incoming' => Delivery::whereIn('status', ['received', 'scanned'])->count(),
                'awaiting_sort' => Delivery::where('status', 'scanned')->count(),
                'ready' => Delivery::where('status', 'sorted')->count(),
                'active_riders' => (clone $riders)->whereIn('status', ['active', 'approved'])->count(),
            ],
            'messages' => $messages,
            'notifications' => $notifications,
            'contacts' => $contacts,
        ]);
    }

    public function sendMessage(Request $request): RedirectResponse
    {
        $sender = $this->currentUser();
        abort_unless($sender, 403);
        $validated = $request->validate([
            'recipient_id' => ['required', 'exists:users,id'],
            'body' => ['required', 'string', 'max:2000'],
        ]);
        $recipient = User::whereKey($validated['recipient_id'])->whereIn('role', ['courier', 'rider', 'seller', 'buyer'])->where('status', 'active')->firstOrFail();
        Message::create(['sender_id' => $sender->id, 'recipient_id' => $recipient->id, 'body' => $validated['body']]);
        $url = match ($recipient->role) {
            'seller' => route('seller.messages'),
            'buyer' => route('buyer.messages'),
            default => route('rider.dashboard'),
        };
        WorkspaceNotification::create(['user_id' => $recipient->id, 'type' => 'message', 'title' => 'New logistics message', 'body' => "Message from {$sender->name}", 'action_url' => $url]);
        return back()->with('status', "Message sent to {$recipient->name}.");
    }

    public function updateRider(Request $request, User $user): RedirectResponse
    {
        abort_unless($user->role === 'courier', 404);
        $centerId = $this->currentCenterId();
        abort_unless(! $centerId || $user->logistics_center_id === $centerId, 403);

        $validated = $request->validate([
            'status' => ['required', 'in:approved,rejected,active,inactive'],
        ]);

        $user->update(['status' => $validated['status']]);

        return back()->with('status', "Rider {$user->name} is now {$validated['status']}.");
    }

    private function currentCenterId(): ?int
    {
        return request()->user()?->id;
    }

    private function currentUser(): ?User
    {
        return request()->user();
    }

    private function deliveryQuery()
    {
        return Delivery::query()->with(['order.buyer', 'order.seller', 'rider', 'pickupRider', 'assignments.rider', 'statusHistory']);
    }

    private function riderQuery()
    {
        return User::whereIn('role', ['courier', 'rider']);
    }

    private function authorizeDelivery(Delivery $delivery): void
    {
        abort_unless($delivery->order()->exists(), 404);
        abort_unless($delivery->logistics_center_id === null || $delivery->logistics_center_id === $this->currentCenterId(), 403);
    }

    private function reviewRider(Request $request, User $user, string $status): RedirectResponse
    {
        abort_unless(in_array($user->role, ['courier', 'rider'], true), 404);

        $updated = User::whereKey($user->id)
            ->where('status', 'pending')
            ->update([
                'status' => $status,
                'reviewed_by' => $request->user()->id,
                'reviewed_at' => now(),
                'rejection_reason' => $status === 'rejected' ? 'Rejected by logistics center.' : null,
            ]);

        abort_unless($updated, 422, 'This rider application is no longer pending.');

        return back()->with('success', "Rider application {$this->statusLabel($status)}.");
    }

    private function parcelRow(Delivery $delivery): array
    {
        $order = $delivery->order;
        $buyer = $order?->buyer;
        $seller = $order?->seller;
        $destination = $delivery->address ?: $order?->shipping_address ?: $this->addressFor($buyer);
        $municipality = $buyer?->municipality ?: $this->destinationPart($destination, 1);
        $statusKey = $this->parcelStatusKey($delivery->status);
        $items = $order?->items?->map(fn ($item) => [
            'name' => $item->product?->name ?: $item->product_name ?: 'Order item',
            'variation' => $item->variant ?: 'Standard',
            'quantity' => (int) $item->quantity,
            'price' => (float) $item->price,
            'image' => $this->productImageUrl($item->product?->image_path),
        ])->values()->all() ?: [];
        $firstImage = collect($items)->firstWhere('image')['image'] ?? asset('images/product-placeholder.svg');

        return [
            'id' => $delivery->id,
            'tracking' => $delivery->tracking_number ?: ($order?->order_number ?? 'DEL-'.$delivery->id),
            'order' => $order?->order_number ?? 'Order #'.$delivery->order_id,
            'buyer' => $buyer?->name ?: 'Buyer',
            'seller' => $seller?->business_name ?: $seller?->store_name ?: $seller?->name ?: 'Seller',
            'contact' => $buyer?->contact_number ?: 'Not provided',
            'destination' => $destination ?: 'No delivery address',
            'address' => $destination ?: 'No delivery address',
            'area' => $municipality ? 'Area: '.$municipality : 'Unassigned',
            'rider' => $delivery->rider?->name ?: 'Not assigned',
            'payment' => $order?->payment_method ? Str::headline($order->payment_method) : 'Not recorded',
            'value' => (float) ($order?->total_amount ?? 0),
            'status' => $this->statusLabel($delivery->status),
            'status_key' => $statusKey,
            'status_type' => $this->statusTone($statusKey),
            'condition' => 'Not recorded',
            'received_from' => $delivery->handover_method === 'seller_dropoff' ? 'Seller drop-off' : ($delivery->pickupRider?->name ?: 'Pickup rider pending'),
            'received_at' => $delivery->received_at?->format('F d, Y g:i A') ?? 'Pending',
            'notes' => $delivery->pickup_note ?: 'No logistics notes recorded.',
            'items' => $items,
            'image' => $firstImage,
            'received' => $delivery->updated_at?->format('M d, Y g:i A') ?? 'Not received',
            'time' => $delivery->updated_at?->format('g:i A') ?? 'Pending',
        ];
    }

    private function productImageUrl(?string $path): string
    {
        if (! $path) {
            return asset('images/product-placeholder.svg');
        }

        return Str::startsWith($path, ['http://', 'https://']) ? $path : Storage::url($path);
    }

    private function riderRow(User $rider): array
    {
        return [
            'id' => $rider->id,
            'name' => $rider->name,
            'email' => $rider->email,
            'vehicle' => $rider->vehicle_type ? Str::headline($rider->vehicle_type) : 'Not provided',
            'plate' => $rider->plate_number ?: 'Not provided',
            'status' => $this->statusLabel($rider->status),
            'area' => $rider->municipality ?: 'Unassigned',
            'submitted' => $rider->created_at?->format('M d, Y') ?? 'Unknown',
            'contact' => $rider->contact_number ?: 'Not provided',
        ];
    }

    private function riderStatusLabel(User $rider): string
    {
        if ($rider->status !== 'active') {
            return $this->statusLabel($rider->status);
        }

        return Delivery::where('rider_id', $rider->id)->whereIn('status', ['assigned', 'out_for_delivery'])->exists()
            ? 'Delivering'
            : 'Available';
    }

    private function areaRows($deliveries): array
    {
        $riders = $this->riderQuery()->where('status', 'active')->get()->groupBy(fn (User $rider) => $rider->municipality ?: 'Unassigned');

        return $riders->map(function ($group, string $municipality) {
            return [
                'code' => Str::upper(Str::substr($municipality, 0, 1)) ?: 'U',
                'area' => $municipality === 'Unassigned' ? 'Unassigned Area' : 'Area: '.$municipality,
                'municipality' => $municipality,
                'available' => $group->count(),
                'total' => $group->count(),
            ];
        })->values()->take(4)->all() ?: [[
            'code' => 'U',
            'area' => 'Unassigned Area',
            'municipality' => 'No active riders',
            'available' => 0,
            'total' => 1,
        ]];
    }

    private function activityRows($deliveries): array
    {
        return $deliveries->take(4)->map(fn (Delivery $delivery) => [
            'title' => $this->statusLabel($delivery->status),
            'description' => ($delivery->tracking_number ?: 'Delivery #'.$delivery->id).' for '.($delivery->order?->buyer?->name ?: 'buyer'),
            'time' => $delivery->updated_at?->diffForHumans() ?? 'Pending',
            'type' => $this->statusTone($this->parcelStatusKey($delivery->status)),
        ])->values()->all();
    }

    private function parcelStatusKey(?string $status): string
    {
        return match ($status) {
            'requested', 'received', 'scanned', 'at_sorting_center' => 'waiting_sorting',
            'sorted', 'awaiting_rider' => 'awaiting_rider',
            'assigned' => 'assigned',
            'out_for_delivery' => 'out_for_delivery',
            'delivered' => 'delivered',
            default => $status ?: 'waiting_sorting',
        };
    }

    private function statusTone(string $statusKey): string
    {
        return match ($statusKey) {
            'waiting_sorting' => 'warning',
            'delivered' => 'success',
            default => 'info',
        };
    }

    private function statusLabel(?string $status): string
    {
        return match ($status) {
            'active' => 'Approved',
            'pending' => 'Pending Approval',
            'rejected' => 'Rejected',
            null, '' => 'Pending',
            default => Str::headline($status),
        };
    }

    private function addressFor(?User $user): string
    {
        if (! $user) {
            return '';
        }

        return collect([$user->house_number, $user->street, $user->barangay, $user->municipality, $user->province])->filter()->implode(', ');
    }

    private function destinationPart(?string $destination, int $offset): ?string
    {
        $parts = collect(explode(',', (string) $destination))->map(fn ($part) => trim($part))->filter()->values();

        return $parts[$offset] ?? $parts->last();
    }
}
