<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Rider;
use App\Models\ServiceArea;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class LogisticsPortalController extends Controller
{
    public function dashboard(Request $request): View
    {
        $provider = $request->user()->logisticsProvider()->where('status', 'approved')->firstOrFail();
        $shipmentQuery = Shipment::query()->where('logistics_provider_id', $provider->id);
        $shipments = (clone $shipmentQuery)->with(['rider.user', 'sellerOrder.order', 'sellerOrder.seller.pickupAddress'])
            ->latest()
            ->limit(8)
            ->get();

        $stats = [
            ['label' => 'Parcels', 'value' => (string) (clone $shipmentQuery)->count(), 'description' => 'All tracked shipments', 'change' => 'Live'],
            ['label' => 'Assigned', 'value' => (string) (clone $shipmentQuery)->whereIn('status', ['pickup_assigned', 'pickup_accepted', 'picked_up', 'in_transit_to_hub', 'delivery_assigned', 'delivery_accepted', 'delivery_collected', 'out_for_delivery'])->count(), 'description' => 'Active rider assignments', 'change' => 'Current'],
            ['label' => 'Delivered', 'value' => (string) (clone $shipmentQuery)->where('status', 'delivered')->count(), 'description' => 'Completed deliveries', 'change' => 'Database'],
            ['label' => 'Riders', 'value' => (string) $provider->riders()->where('is_active', true)->count(), 'description' => 'Active riders', 'change' => 'Ready'],
        ];

        $recentParcels = $shipments->map(fn (Shipment $shipment) => [
            'id' => $shipment->id,
            'tracking' => $shipment->tracking_code,
            'buyer' => $shipment->sellerOrder?->order?->buyer?->name ?? 'Buyer',
            'destination' => $shipment->sellerOrder?->order?->shipping_address_snapshot['city'] ?? 'Unknown city',
            'area' => $shipment->sellerOrder?->order?->shipping_address_snapshot['province'] ?? 'Unknown province',
            'status' => str($shipment->status)->headline()->toString(),
            'time' => $shipment->updated_at?->diffForHumans() ?? 'Recently',
        ])->all();

        $areas = $provider->serviceAreas()->orderBy('province')->orderBy('city')->get()->map(fn ($area) => [
            'area' => $area->province,
            'municipality' => $area->city,
            'available' => $provider->riders()->where('is_active', true)->count(),
            'total' => max(1, $provider->riders()->count()),
        ])->all();

        return view('Logistics.dashboard.index', [
            'logisticsStats' => $stats,
            'logisticsRecentParcels' => $recentParcels,
            'logisticsAreas' => $areas,
            'logisticsActivity' => [],
        ]);
    }

    public function deliveryAreas(Request $request): View
    {
        $provider=$request->user()->logisticsProvider()->where('status','approved')->firstOrFail();
        $areas=$provider->serviceAreas()->orderBy('province')->orderBy('city')->get()->map(fn($area)=>['province'=>$area->province,'municipality'=>$area->city,'barangay'=>'All served barangays','status'=>'Active','riders'=>$provider->riders()->where('is_active',true)->count(),'parcels'=>Shipment::where('logistics_provider_id',$provider->id)->whereHas('sellerOrder.order',fn($q)=>$q->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(shipping_address_snapshot, '$.city')) = ?",[$area->city]))->count()]);
        return view('logistics.delivery-areas.index',['areas'=>$areas,'areaStats'=>['total'=>$areas->count(),'active'=>$areas->count(),'riders'=>$provider->riders()->where('is_active',true)->count(),'unassigned'=>0]]);
    }

    public function messages(Request $request): View
    {
        $user=$request->user(); $contacts=User::anyRole(['seller','buyer','rider'])->where('status','active')->whereKeyNot($user->id)->orderBy('name')->get();
        $selected=$request->integer('contact')?$contacts->firstWhere('id',$request->integer('contact')):$contacts->first();
        $messages=$selected?Message::with('sender')->where(fn($q)=>$q->where(fn($x)=>$x->where('sender_id',$user->id)->where('recipient_id',$selected->id))->orWhere(fn($x)=>$x->where('sender_id',$selected->id)->where('recipient_id',$user->id)))->oldest()->get():collect();
        return view('logistics.messages.index',compact('contacts','selected','messages'));
    }

    public function sendMessage(Request $request): RedirectResponse
    {
        $data=$request->validate(['recipient_id'=>['required','exists:users,id'],'body'=>['required','string','max:2000']]);
        Message::create(['sender_id'=>$request->user()->id,'recipient_id'=>$data['recipient_id'],'body'=>$data['body']]);
        return back()->with('status','Message sent.');
    }

    public function reports(Request $request): View
    {
        $provider=$request->user()->logisticsProvider()->where('status','approved')->firstOrFail();
        $shipments=Shipment::with(['rider.user','sellerOrder.order'])->where('logistics_provider_id',$provider->id)->latest()->get();
        $delivered=$shipments->where('status','delivered')->count();$failed=$shipments->where('status','failed')->count();$total=max(1,$shipments->count());
        return view('logistics.reports.index',[
            'summaryCards'=>[['label'=>'Parcels Received','value'=>$shipments->count(),'change'=>'Canonical shipments','tone'=>'primary','icon'=>'package'],['label'=>'Delivered','value'=>$delivered,'change'=>round($delivered/$total*100,1).'% success','tone'=>'success','icon'=>'check'],['label'=>'Out for Delivery','value'=>$shipments->where('status','out_for_delivery')->count(),'change'=>'Active now','tone'=>'primary','icon'=>'truck'],['label'=>'Failed Delivery','value'=>$failed,'change'=>'Recorded attempts','tone'=>'warning','icon'=>'alert']],
            'parcelSummary'=>$shipments->groupBy(fn($shipment)=>$shipment->updated_at?->format('M d, Y')??'No date')->map(fn($group,$date)=>[$date,$group->count(),$group->whereIn('status',['assigned','picked_up','in_transit','out_for_delivery','delivered'])->count(),$group->where('status','out_for_delivery')->count(),$group->where('status','delivered')->count(),$group->where('status','failed')->count()])->values()->take(10),
            'deliveryOverview'=>$shipments->groupBy(fn($shipment)=>$shipment->updated_at?->format('M d')??'No date')->map(fn($group,$date)=>['label'=>$date,'value'=>$group->count()])->values()->take(7),
            'successRate'=>round($delivered/$total*100,1),'pendingCount'=>$shipments->whereNotIn('status',['delivered','returned'])->count(),
            'riders'=>$provider->riders()->with('user')->get()->map(fn($rider)=>[$rider->user->name,$rider->vehicle_type?:'No vehicle',Shipment::where('rider_id',$rider->id)->count(),Shipment::where('rider_id',$rider->id)->where('status','delivered')->count(),'No rating data','']),
            'areas'=>$provider->serviceAreas()->get()->map(fn($area)=>['area'=>$area->province,'municipality'=>$area->city,'available'=>$provider->riders()->where('is_active',true)->count()]),
            'codTotal'=>$shipments->where('cod_collected',true)->sum('cod_amount_minor')/100,
        ]);
    }

    public function riderApplications(Request $request): View
    {
        $provider = $request->user()->logisticsProvider()->where('status', 'approved')->firstOrFail();
        $riders = $provider->riders()->with('user')->get();
        $applicants = $riders->map(fn ($rider) => [
            'id' => $rider->id,
            'name' => $rider->user->name,
            'email' => $rider->user->email,
            'status' => $rider->is_active ? 'Approved' : ($rider->user->status === 'rejected' ? 'Rejected' : 'Pending Approval'),
            'vehicle' => $rider->vehicle_type ?? 'Not provided',
            'plate' => $rider->plate_no ?? 'Not provided',
            'area' => 'Service area',
            'submitted' => $rider->created_at?->diffForHumans() ?? 'Recently',
        ]);

        return view('logistics.riders.application.index', [
            'logisticsRiderApplications' => $applicants,
            'logisticsRiderSummary' => [
                ['label' => 'Total Applications', 'value' => $riders->count(), 'description' => 'All rider registrations', 'tone' => 'primary', 'icon' => 'applications'],
                ['label' => 'Pending', 'value' => $riders->filter(fn ($rider) => ! $rider->is_active && $rider->user->status !== 'rejected')->count(), 'description' => 'Awaiting verification', 'tone' => 'warning', 'icon' => 'clock'],
                ['label' => 'Approved', 'value' => $riders->where('is_active', true)->count(), 'description' => 'Verified rider accounts', 'tone' => 'success', 'icon' => 'check'],
                ['label' => 'Rejected', 'value' => $riders->filter(fn ($rider) => ! $rider->is_active && $rider->user->status === 'rejected')->count(), 'description' => 'Applications declined', 'tone' => 'danger', 'icon' => 'x'],
            ],
        ]);
    }

    public function riderApplicationShow(Request $request, Rider $rider): View
    {
        $provider = $request->user()->logisticsProvider()->where('status', 'approved')->firstOrFail();
        abort_unless($rider->logistics_provider_id === $provider->id, 403);
        $rider->load('user');

        return view('logistics.riders.application.show', [
            'rider' => [
                'id' => $rider->id,
                'name' => $rider->user->name,
                'email' => $rider->user->email,
                'contact' => $rider->user->contact_number,
                'address' => 'Not recorded',
                'vehicle' => $rider->vehicle_type ?? 'Not provided',
                'plate' => $rider->plate_no ?? 'Not provided',
                'area' => 'Service area',
                'submitted' => $rider->created_at?->diffForHumans() ?? 'Recently',
                'status' => $rider->is_active ? 'Approved' : ($rider->user->status === 'rejected' ? 'Rejected' : 'Pending Approval'),
            ],
        ]);
    }

    public function approveRider(Request $request, Rider $rider): RedirectResponse
    {
        $provider = $request->user()->logisticsProvider()->where('status', 'approved')->firstOrFail();
        abort_unless($rider->logistics_provider_id === $provider->id, 403);
        $rider->getConnection()->transaction(function () use ($rider): void {
            $rider->update(['is_active' => true]);
            $rider->user()->update(['status' => 'active']);
        });

        return back()->with('success', 'Rider approved.');
    }

    public function rejectRider(Request $request, Rider $rider): RedirectResponse
    {
        $provider = $request->user()->logisticsProvider()->where('status', 'approved')->firstOrFail();
        abort_unless($rider->logistics_provider_id === $provider->id, 403);
        $rider->getConnection()->transaction(function () use ($rider): void {
            $rider->update(['is_active' => false]);
            $rider->user()->update(['status' => 'rejected']);
        });

        return back()->with('success', 'Rider application rejected.');
    }

    public function profile(Request $request): View
    {
        $user = $request->user()->load(['logisticsProvider', 'addresses']);

        return view('logistics.profile.index', [
            'accountUser' => $user,
            'provider' => $user->logisticsProvider,
            'address' => $user->addresses->firstWhere('is_default', true) ?? $user->addresses->first(),
        ]);
    }
    public function riderShow(Request $request, Rider $rider): View { $provider=$request->user()->logisticsProvider()->where('status','approved')->firstOrFail(); abort_unless($rider->logistics_provider_id===$provider->id,403); $rider->load('user'); return view('logistics.riders.show',['rider'=>['id'=>$rider->id,'name'=>$rider->user->name,'email'=>$rider->user->email,'contact'=>$rider->user->contact_number,'area'=>$rider->vehicle_type?:'Unassigned','status'=>$rider->is_active?'Active':'Inactive','parcels'=>Shipment::where('rider_id',$rider->id)->whereNotIn('status',['delivered','returned'])->count()]]); }
}
