<?php

namespace App\Http\Controllers\Logistics;

use App\Http\Controllers\Controller;
use App\Models\Auth\RegistrationApplication;
use App\Models\Logistics\ServiceArea;
use App\Models\Logistics\ServiceAreaLocation;
use App\Models\Logistics\Shipment;
use App\Models\Rider\RiderAreaAssignment;
use App\Models\Rider\RiderApplicationData;
use App\Models\Rider\RiderProfile;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class LogisticsPortalController extends Controller
{
    public function dashboard(Request $request): View
    {
        $center = $request->user()->logisticsCenter;
        abort_unless($center, 403);

        return view('Logistics.dashboard.index', [
            'center' => $center,
            'stats' => [
                'shipments' => Shipment::where('logistics_center_id', $center->id)->count(),
                'for_receive' => Shipment::where('logistics_center_id', $center->id)->whereIn('current_status', ['PICKED_UP', 'READY_FOR_PICKUP'])->count(),
                'for_sorting' => Shipment::where('logistics_center_id', $center->id)->where('current_status', 'AT_SORTING_CENTER')->count(),
                'for_dispatch' => Shipment::where('logistics_center_id', $center->id)->where('current_status', 'SORTED')->count(),
                'active_riders' => $center->riders()->where('status', 'ACTIVE')->count(),
            ],
            'recentShipments' => Shipment::where('logistics_center_id', $center->id)->with(['sellerOrder.order.address'])->latest()->limit(10)->get(),
        ]);
    }

    public function riderApplications(Request $request): View
    {
        $center = $request->user()->logisticsCenter;
        abort_unless($center, 403);

        $applications = RegistrationApplication::query()
            ->whereHas('user', fn ($query) => $query->where('account_type', 'RIDER'))
            ->whereHas('riderData', fn ($query) => $query->where('target_logistics_center_id', $center->id))
            ->with(['user', 'documents', 'riderData'])
            ->latest()
            ->paginate(15);

        return view('Logistics.riders.application.index', compact('applications', 'center'));
    }

    public function riderApplicationShow(Request $request, RegistrationApplication $application): View
    {
        $center = $request->user()->logisticsCenter;
        abort_unless($center, 403);

        $application->load(['user', 'documents', 'riderData']);
        abort_unless((int) $application->riderData?->target_logistics_center_id === (int) $center->id, 403);

        return view('Logistics.riders.application.show', compact('application', 'center'));
    }

    public function approveRider(Request $request, RegistrationApplication $application): RedirectResponse
    {
        $center = $request->user()->logisticsCenter;
        abort_unless($center, 403);

        $application->load(['user', 'riderData']);
        abort_unless((int) $application->riderData?->target_logistics_center_id === (int) $center->id, 403);

        DB::transaction(function () use ($application, $center, $request): void {
            $application->update([
                'status' => 'APPROVED',
                'reviewed_by_user_id' => $request->user()->id,
                'reviewed_at' => now(),
                'decision_notes' => $request->input('decision_notes'),
                'rejection_reason' => null,
            ]);

            $application->user->update(['status' => 'ACTIVE']);

            RiderProfile::updateOrCreate(
                ['user_id' => $application->user_id],
                [
                    'logistics_center_id' => $center->id,
                    'vehicle_type' => $application->riderData->vehicle_type,
                    'plate_number' => $application->riderData->plate_number,
                    'drivers_license_number' => $application->riderData->drivers_license_number,
                    'status' => 'ACTIVE',
                    'approved_by_user_id' => $request->user()->id,
                    'approved_at' => now(),
                ],
            );
        });

        return redirect()->route('logistics.riders.applications')->with('status', 'Rider approved and activated.');
    }

    public function rejectRider(Request $request, RegistrationApplication $application): RedirectResponse
    {
        $center = $request->user()->logisticsCenter;
        abort_unless($center, 403);

        $data = $request->validate(['rejection_reason' => ['required', 'string', 'max:1000']]);
        $application->load('riderData');
        abort_unless((int) $application->riderData?->target_logistics_center_id === (int) $center->id, 403);

        $application->update([
            'status' => 'REJECTED',
            'reviewed_by_user_id' => $request->user()->id,
            'reviewed_at' => now(),
            'rejection_reason' => $data['rejection_reason'],
        ]);

        $application->user?->update(['status' => 'DEACTIVATED']);

        return back()->with('status', 'Rider application rejected.');
    }

    public function riders(Request $request): View
    {
        $center = $request->user()->logisticsCenter;
        abort_unless($center, 403);

        $riders = RiderProfile::query()
            ->where('logistics_center_id', $center->id)
            ->with(['user', 'areaAssignments.serviceArea'])
            ->latest()
            ->paginate(15);

        return view('Logistics.riders.index', compact('riders', 'center'));
    }

    public function riderShow(Request $request, RiderProfile $rider): View
    {
        $center = $request->user()->logisticsCenter;
        abort_unless($center && (int) $rider->logistics_center_id === (int) $center->id, 403);

        $rider->load(['user', 'areaAssignments.serviceArea', 'assignments.shipment.sellerOrder.order.address', 'earnings']);

        return view('Logistics.riders.show', compact('rider', 'center'));
    }

    public function activateRider(Request $request, RiderProfile $rider): RedirectResponse
    {
        $center = $request->user()->logisticsCenter;
        abort_unless($center && (int) $rider->logistics_center_id === (int) $center->id, 403);

        $rider->update(['status' => 'ACTIVE']);
        $rider->user?->update(['status' => 'ACTIVE']);

        return back()->with('status', 'Rider activated.');
    }

    public function deactivateRider(Request $request, RiderProfile $rider): RedirectResponse
    {
        $center = $request->user()->logisticsCenter;
        abort_unless($center && (int) $rider->logistics_center_id === (int) $center->id, 403);

        $rider->update(['status' => 'SUSPENDED']);
        $rider->user?->update(['status' => 'SUSPENDED']);

        return back()->with('status', 'Rider suspended.');
    }

    public function deliveryAreas(Request $request): View
    {
        $center = $request->user()->logisticsCenter;
        abort_unless($center, 403);

        $areas = ServiceArea::query()
            ->where('logistics_center_id', $center->id)
            ->with(['locations', 'riderAssignments.riderProfile.user'])
            ->orderBy('name')
            ->get();

        $riders = RiderProfile::where('logistics_center_id', $center->id)->where('status', 'ACTIVE')->with('user')->get();

        return view('Logistics.delivery-areas.index', compact('areas', 'riders', 'center'));
    }

    public function saveDeliveryArea(Request $request): RedirectResponse
    {
        $center = $request->user()->logisticsCenter;
        abort_unless($center, 403);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'code' => ['nullable', 'string', 'max:50'],
            'province_code' => ['required', 'string', 'max:50'],
            'province_name' => ['required', 'string', 'max:150'],
            'municipality_code' => ['required', 'string', 'max:50'],
            'municipality_name' => ['required', 'string', 'max:150'],
            'barangay_code' => ['required', 'string', 'max:50'],
            'barangay_name' => ['required', 'string', 'max:150'],
            'rider_profile_id' => ['nullable', 'integer', 'exists:rider_profiles,id'],
        ]);

        DB::transaction(function () use ($data, $center, $request): void {
            $area = ServiceArea::firstOrCreate(
                ['logistics_center_id' => $center->id, 'code' => $data['code'] ?: Str::slug($data['name'])],
                ['name' => $data['name'], 'is_active' => true],
            );

            $area->update(['name' => $data['name'], 'is_active' => true]);

            ServiceAreaLocation::updateOrCreate(
                ['service_area_id' => $area->id, 'barangay_code' => $data['barangay_code']],
                [
                    'province_code' => $data['province_code'],
                    'province_name' => $data['province_name'],
                    'municipality_code' => $data['municipality_code'],
                    'municipality_name' => $data['municipality_name'],
                    'barangay_name' => $data['barangay_name'],
                ],
            );

            if (! empty($data['rider_profile_id'])) {
                RiderAreaAssignment::updateOrCreate(
                    ['rider_profile_id' => $data['rider_profile_id'], 'service_area_id' => $area->id, 'is_active' => true],
                    ['assigned_by_user_id' => $request->user()->id, 'assigned_at' => now(), 'ended_at' => null],
                );
            }
        });

        return back()->with('status', 'Service area saved.');
    }

    public function toggleDeliveryArea(Request $request, ServiceArea $area): RedirectResponse
    {
        $center = $request->user()->logisticsCenter;
        abort_unless($center && (int) $area->logistics_center_id === (int) $center->id, 403);

        $area->update(['is_active' => ! $area->is_active]);

        return back()->with('status', 'Service area updated.');
    }

    public function reports(Request $request): View
    {
        $center = $request->user()->logisticsCenter;
        abort_unless($center, 403);

        return view('Logistics.reports.index', [
            'center' => $center,
            'shipmentsByStatus' => Shipment::where('logistics_center_id', $center->id)->selectRaw('current_status, COUNT(*) as total')->groupBy('current_status')->pluck('total', 'current_status'),
            'riderCount' => RiderProfile::where('logistics_center_id', $center->id)->count(),
        ]);
    }

    public function profile(Request $request): View
    {
        $center = $request->user()->logisticsCenter?->load('address');
        abort_unless($center, 403);

        return view('Logistics.profile.index', compact('center'));
    }

    public function messages(): View
    {
        return view('Logistics.messages.index', ['conversations' => collect()]);
    }

    public function sendMessage(): RedirectResponse
    {
        return back()->with('status', 'Messaging is handled in Phase 5.');
    }
}
