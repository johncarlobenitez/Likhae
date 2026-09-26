<?php

namespace App\Http\Controllers\Rider;

use App\Http\Controllers\Controller;
use App\Models\Rider\RiderAssignment;
use App\Services\Fulfillment\ShipmentWorkflowService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RiderShipmentController extends Controller
{
    public function index(Request $request): View
    {
        $rider = $request->user()->riderProfile;
        abort_unless($rider, 403);

        $assignments = RiderAssignment::query()
            ->where('rider_profile_id', $rider->id)
            ->with(['shipment.sellerOrder.order.address', 'shipment.sellerOrder.sellerProfile.user', 'shipment.sellerOrder.sellerProfile.businessAddress'])
            ->latest()
            ->paginate(15);

        return view('Rider.shipments', compact('assignments', 'rider'));
    }

    public function transition(Request $request, RiderAssignment $assignment, ShipmentWorkflowService $workflow): RedirectResponse
    {
        $rider = $request->user()->riderProfile;
        abort_unless($rider && (int) $assignment->rider_profile_id === (int) $rider->id, 403);

        $data = $request->validate([
            'action' => ['required', 'string', 'in:accept,start,pickup_complete,delivery_success,delivery_failed,reject'],
            'scan_method' => ['nullable', 'string', 'in:QR,BARCODE,MANUAL'],
            'scanned_code' => ['required_if:action,pickup_complete', 'nullable', 'string', 'max:150'],
            'failure_reason' => ['required_if:action,delivery_failed', 'nullable', 'string', 'max:1000'],
            'attempt_status' => ['nullable', 'string', 'in:FAILED,RESCHEDULED,RETURNED'],
            'next_attempt_at' => ['required_if:attempt_status,RESCHEDULED', 'nullable', 'date', 'after:now'],
            'reason' => ['nullable', 'string', 'max:1000'],
            'proof_path' => ['nullable', 'string', 'max:500'],
        ]);

        $workflow->riderTransition($assignment, $data['action'], $request->user(), $data);

        return back()->with('status', 'Rider assignment updated.');
    }
}
