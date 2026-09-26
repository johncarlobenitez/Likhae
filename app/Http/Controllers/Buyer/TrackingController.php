<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Logistics\DeliveryAttempt;
use App\Models\Logistics\Shipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class TrackingController extends Controller
{
    public function show(Request $request, string $tracking): View
    {
        $shipment = Shipment::query()
            ->where('tracking_number', $tracking)
            ->with([
                'sellerOrder.order.buyer',
                'sellerOrder.order.address',
                'sellerOrder.items',
                'events.actor',
                'scans',
                'deliveryAttempts',
            ])
            ->firstOrFail();

        $user = $request->user();

        if ($user !== null && $user->account_type === 'BUYER') {
            abort_unless((int) $shipment->sellerOrder->order->buyer_user_id === (int) $user->id, 403);
        }

        return view('Buyer.tracking.show', compact('shipment'));
    }

    public function proof(Request $request, int|string $event): BinaryFileResponse
    {
        $attempt = DeliveryAttempt::query()
            ->with(['shipment.sellerOrder.order', 'riderAssignment.riderProfile.user'])
            ->findOrFail($event);

        $user = $request->user();
        abort_unless($user !== null, 403);

        $isBuyer = $user->account_type === 'BUYER'
            && (int) $attempt->shipment->sellerOrder->order->buyer_user_id === (int) $user->id;
        $isAssignedRider = $user->account_type === 'RIDER'
            && (int) optional(optional($attempt->riderAssignment)->riderProfile)->user_id === (int) $user->id;
        $isStaff = in_array($user->account_type, ['ADMIN', 'LOGISTICS'], true);

        abort_unless($isBuyer || $isAssignedRider || $isStaff, 403);
        abort_unless(filled($attempt->proof_path), 404, 'No proof file is attached to this delivery attempt.');

        $path = (string) $attempt->proof_path;
        $disk = Storage::disk('public');
        abort_unless($disk->exists($path), 404, 'Proof file was not found.');

        return response()->file($disk->path($path));
    }
}
