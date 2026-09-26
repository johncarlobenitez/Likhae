<?php

namespace App\Http\Controllers\Logistics;

use App\Http\Controllers\Controller;
use App\Models\Auth\RegistrationApplication;
use App\Models\Logistics\ServiceArea;
use App\Models\Logistics\ServiceAreaLocation;
use App\Models\Logistics\Shipment;
use App\Models\Rider\RiderAreaAssignment;
use App\Models\Rider\RiderProfile;
use App\Models\User;
use App\Services\Communication\ConversationService;
use App\Services\RegistrationWorkflowService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class LogisticsPortalController extends Controller
{
    public function __construct(private readonly RegistrationWorkflowService $registrationWorkflow) {}

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

        $this->registrationWorkflow->approveRider(
            $application,
            $request->user(),
            $request->input('decision_notes'),
            $request,
        );

        return redirect()->route('logistics.riders.applications')->with('status', 'Rider approved and activated.');
    }

    public function rejectRider(Request $request, RegistrationApplication $application): RedirectResponse
    {
        $center = $request->user()->logisticsCenter;
        abort_unless($center, 403);

        $data = $request->validate(['rejection_reason' => ['required', 'string', 'max:1000']]);
        $application->load('riderData');
        abort_unless((int) $application->riderData?->target_logistics_center_id === (int) $center->id, 403);

        $this->registrationWorkflow->reject(
            $application,
            $request->user(),
            $data['rejection_reason'],
            $request,
        );

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

        if (! empty($data['rider_profile_id'])) {
            abort_unless(
                RiderProfile::query()->whereKey($data['rider_profile_id'])->where('logistics_center_id', $center->id)->where('status', 'ACTIVE')->exists(),
                403,
            );
        }

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

    public function exportReport(Request $request)
    {
        $center = $request->user()->logisticsCenter;
        abort_unless($center, 403);
        $rows = Shipment::query()->where('logistics_center_id', $center->id)->latest()->get();
        $csv = "Tracking,Status,Destination,Updated\n".$rows->map(fn ($shipment) => implode(',', [
            $shipment->tracking_number,
            $shipment->current_status,
            '"'.str_replace('"', '""', collect([$shipment->destination_barangay_name, $shipment->destination_municipality_name, $shipment->destination_province_name])->filter()->implode(', ')).'"',
            $shipment->updated_at?->toDateTimeString(),
        ]))->implode("\n");

        return response($csv, 200, ['Content-Type' => 'text/csv', 'Content-Disposition' => 'attachment; filename="logistics-report.csv"']);
    }

    public function profile(Request $request): View
    {
        $center = $request->user()->logisticsCenter?->load('address');
        abort_unless($center, 403);

        return view('Logistics.profile.index', ['center' => $center, 'user' => $request->user()]);
    }

    public function updateAccount(Request $request): RedirectResponse
    {
        abort_unless($request->user()->logisticsCenter, 403);
        $user = $request->user();
        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'contact_number' => ['required', 'string', 'max:30', Rule::unique('users', 'contact_number')->ignore($user->id)],
        ]);

        $user->update($data);

        return back()->with('status', 'Account profile updated.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        abort_unless($request->user()->logisticsCenter, 403);
        $data = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $request->user()->update(['password' => Hash::make($data['password'])]);

        return back()->with('status', 'Account password updated.');
    }

    public function messages(Request $request, ConversationService $conversationService): View
    {
        return view('Logistics.messages.index', [
            'conversations' => $conversationService->listFor($request->user()),
            'contacts' => User::query()->where('status', 'ACTIVE')->whereKeyNot($request->user()->id)->orderBy('first_name')->get(),
        ]);
    }

    public function sendMessage(Request $request, ConversationService $conversationService): RedirectResponse
    {
        $data = $request->validate([
            'recipient_user_id' => ['required', 'integer', 'exists:users,id'],
            'body' => ['required', 'string', 'max:5000'],
        ]);
        $conversationService->send($request->user(), (int) $data['recipient_user_id'], $data['body']);

        return back()->with('status', 'Message sent.');
    }
}
