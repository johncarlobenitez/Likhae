<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RiderShipmentController extends Controller
{
    public function index(Request $request): View
    {
        $rider = $request->user()->rider()->where('is_active', true)->firstOrFail();
        $shipments = Shipment::with(['sellerOrder.order', 'sellerOrder.seller.pickupAddress', 'events'])
            ->where('rider_id', $rider->id)
            ->when($request->filled('tracking'), fn ($query) => $query->where('tracking_code', trim((string) $request->query('tracking'))))
            ->latest()
            ->get();

        $shipmentStats = [
            'assigned' => $shipments->where('status', 'assigned')->count(),
            'picked_up' => $shipments->where('status', 'picked_up')->count(),
            'in_transit' => $shipments->where('status', 'in_transit')->count(),
            'out_for_delivery' => $shipments->where('status', 'out_for_delivery')->count(),
            'delivered' => $shipments->where('status', 'delivered')->count(),
        ];

        return view('rider.shipments', compact('shipments', 'shipmentStats'));
    }

    public function transition(Request $request, Shipment $shipment): RedirectResponse
    {
        $rider = $request->user()->rider()->where('is_active',true)->firstOrFail();
        abort_unless($shipment->rider_id === $rider->id,403);
        $data = $request->validate([
            'status'=>['required',Rule::in(['picked_up','in_transit','out_for_delivery','delivered','failed'])],
            'note'=>['required_if:status,failed','nullable','string','max:1000'], 'receiver_name'=>['required_if:status,delivered','nullable','string','max:255'],
            'proof'=>['required_if:status,delivered','nullable','image','mimes:jpg,jpeg,png,webp','max:10240'],
        ]);
        $photo = $request->file('proof')?->store('delivery-proofs','local');
        $shipment->transitionTo($data['status'],$request->user(),$data['note']??null,$photo,$data['receiver_name']??null);
        return back()->with('status','Parcel moved to '.str($data['status'])->headline().'.');
    }
}
