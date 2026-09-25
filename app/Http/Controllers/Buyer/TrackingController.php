<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;

use App\Models\Logistics\DeliveryEvent;
use App\Models\Logistics\Shipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class TrackingController extends Controller
{
    public function show(string $trackingCode): View
    {
        $shipment = Shipment::with(['provider', 'events:id,shipment_id,status,occurred_at', 'sellerOrder.order'])
            ->where('tracking_code', $trackingCode)->firstOrFail();
        return view('Buyer.tracking.show', [
            'tracking' => $shipment->tracking_code,
            'courier' => $shipment->provider?->name,
            'city' => data_get($shipment->sellerOrder->order->shipping_address_snapshot, 'city'),
            'status' => $shipment->status,
            'events' => $shipment->events,
        ]);
    }

    public function proof(Request $request, DeliveryEvent $event)
    {
        Gate::authorize('viewProof', $event->shipment);
        abort_unless($event->photo_path && Storage::disk('local')->exists($event->photo_path), 404);
        return Storage::disk('local')->response($event->photo_path, null, ['Cache-Control' => 'private, no-store']);
    }
}
