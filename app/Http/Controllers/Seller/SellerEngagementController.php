<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;

use App\Models\Seller\Message;
use App\Models\Seller\ProductReview;
use App\Models\Seller\Seller;
use App\Models\Seller\SellerCampaign;
use App\Models\Seller\SellerOrder;
use App\Models\User;
use App\Models\Seller\WorkspaceNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SellerEngagementController extends Controller
{
    public function messages(Request $request): View
    {
        $shop = $this->shop($request); $owner = $shop->owner;
        $buyerIds = SellerOrder::where('seller_id', $shop->id)->join('orders','orders.id','=','seller_orders.order_id')->pluck('orders.buyer_id')
            ->merge(Message::where('sender_id',$owner->id)->orWhere('recipient_id',$owner->id)->get()->flatMap(fn ($message) => [$message->sender_id,$message->recipient_id]))
            ->reject(fn ($id) => (int)$id === $owner->id)->unique();
        $buyers = User::role('buyer')->where('status','active')->whereIn('id',$buyerIds)->orderBy('name')->get();
        $buyer = $buyers->firstWhere('id',(int)$request->input('buyer')) ?? $buyers->first();
        $messages = $buyer ? $this->thread($owner->id,$buyer->id)->get() : collect();
        if ($buyer) Message::where('sender_id',$buyer->id)->where('recipient_id',$owner->id)->whereNull('read_at')->update(['read_at'=>now()]);
        $rows = $buyers->map(function ($buyer) use ($owner) { $last=$this->thread($owner->id,$buyer->id)->latest('id')->first(); return ['buyer'=>$buyer,'last'=>$last,'unread'=>Message::where('sender_id',$buyer->id)->where('recipient_id',$owner->id)->whereNull('read_at')->count()]; })->sortByDesc(fn ($row) => $row['last']?->id ?? 0)->values();
        return view('Seller.messages',['mode'=>'messages','seller'=>$owner,'conversationRows'=>$rows,'conversationBuyer'=>$buyer,'chatMessages'=>$messages]);
    }

    public function sendMessage(Request $request): RedirectResponse|JsonResponse
    {
        $shop=$this->shop($request); $owner=$shop->owner;
        $data=$request->validate(['recipient_id'=>['required','exists:users,id'],'order_id'=>['nullable','exists:orders,id'],'body'=>['required','string','max:2000']]);
        $buyer=User::whereKey($data['recipient_id'])->role('buyer')->firstOrFail();
        if (!empty($data['order_id'])) abort_unless(SellerOrder::where('seller_id',$shop->id)->where('order_id',$data['order_id'])->whereHas('order',fn($q)=>$q->where('buyer_id',$buyer->id))->exists(),403);
        $message=Message::create(['sender_id'=>$owner->id,'recipient_id'=>$buyer->id,'order_id'=>$data['order_id']??null,'body'=>$data['body']]);
        WorkspaceNotification::create(['user_id'=>$buyer->id,'type'=>'message','title'=>'New seller message','body'=>'Message from '.$shop->name,'action_url'=>route('buyer.messages',[],false)]);
        $payload=$this->payload($message,$owner->id);
        return $request->expectsJson()?response()->json(['message'=>$payload],201):redirect()->route('seller.messages',['buyer'=>$buyer->id])->with('status','Message sent.');
    }

    public function stream(Request $request): StreamedResponse
    {
        $shop=$this->shop($request); $owner=$shop->owner; $buyer=User::whereKey((int)$request->query('buyer_id'))->role('buyer')->firstOrFail();
        abort_unless(SellerOrder::where('seller_id',$shop->id)->whereHas('order',fn($q)=>$q->where('buyer_id',$buyer->id))->exists() || $this->thread($owner->id,$buyer->id)->exists(),403);
        $after=max(0,(int)$request->query('after'));
        return response()->stream(function () use ($owner,$buyer,$after): void { $rows=$this->thread($owner->id,$buyer->id)->where('id','>',$after)->limit(50)->get(); foreach($rows as $message){echo "event: message\n".'data: '.json_encode($this->payload($message,$owner->id))."\n\n";} if($rows->isEmpty()) echo "event: heartbeat\n".'data: '.json_encode(['after'=>$after])."\n\n"; },200,['Content-Type'=>'text/event-stream','Cache-Control'=>'no-cache, no-transform','X-Accel-Buffering'=>'no']);
    }

    public function reviews(Request $request): View
    {
        $shop=$this->shop($request); $reviews=ProductReview::with(['buyer','product'])->where('seller_id',$shop->id)->latest()->get();
        return view('Seller.messages',['mode'=>'reviews','reviews'=>$reviews,'reviewStats'=>['average'=>round((float)$reviews->avg('rating'),1),'count'=>$reviews->count(),'new'=>$reviews->where('created_at','>=',now()->subDays(7))->count(),'awaiting'=>$reviews->whereNull('reply')->count(),'with_photos'=>$reviews->count()?round($reviews->where('has_photo',true)->count()/$reviews->count()*100):0,'positive'=>$reviews->count()?round($reviews->where('rating','>=',4)->count()/$reviews->count()*100):0],'ratingBars'=>collect(range(5,1))->mapWithKeys(fn($rating)=>[$rating=>$reviews->count()?(int)round($reviews->where('rating',$rating)->count()/$reviews->count()*100):0])]);
    }

    public function replyReview(Request $request, ProductReview $review): RedirectResponse { $shop=$this->shop($request); abort_unless($review->seller_id===$shop->id,403); $data=$request->validate(['reply'=>['required','string','max:1200']]); $review->update(['reply'=>$data['reply'],'replied_at'=>now()]); return back()->with('status','Review reply published.'); }
    public function exportReviews(Request $request): StreamedResponse { $shop=$this->shop($request); $rows=ProductReview::with(['buyer','product'])->where('seller_id',$shop->id)->latest()->get(); return response()->streamDownload(function()use($rows){$out=fopen('php://output','w');fputcsv($out,['Buyer','Product','Rating','Review','Reply','Date']);foreach($rows as $row)fputcsv($out,[$row->buyer?->name,$row->product?->name,$row->rating,$row->body,$row->reply,$row->created_at]);fclose($out);},'seller-reviews-'.now()->format('Ymd-His').'.csv'); }
    public function marketing(Request $request): View { $shop=$this->shop($request); return view('Seller.marketing',['tab'=>$request->input('tab','discounts'),'campaigns'=>SellerCampaign::where('seller_id',$shop->id)->latest()->get()]); }
    public function storeCampaign(Request $request): RedirectResponse { $shop=$this->shop($request); $data=$request->validate(['type'=>['required',Rule::in(['discount','voucher','promotion'])],'name'=>['required','string','max:120'],'code'=>['nullable','string','max:40'],'discount_type'=>['required',Rule::in(['percent','fixed'])],'discount_value'=>['required','numeric','min:0'],'minimum_spend'=>['nullable','numeric','min:0'],'usage_limit'=>['nullable','integer','min:1'],'starts_at'=>['nullable','date'],'ends_at'=>['nullable','date','after_or_equal:starts_at']]); SellerCampaign::create(['seller_id'=>$shop->id,'type'=>$data['type'],'name'=>$data['name'],'code'=>$data['code']??null,'discount_type'=>$data['discount_type'],'discount_bps'=>$data['discount_type']==='percent'?(int)round($data['discount_value']*100):null,'discount_minor'=>$data['discount_type']==='fixed'?(int)round($data['discount_value']*100):null,'minimum_spend_minor'=>(int)round(($data['minimum_spend']??0)*100),'usage_limit'=>$data['usage_limit']??null,'starts_at'=>$data['starts_at']??null,'ends_at'=>$data['ends_at']??null,'status'=>'active']); return back()->with('status','Campaign created successfully.'); }
    public function toggleCampaign(Request $request, SellerCampaign $campaign): RedirectResponse { $shop=$this->shop($request); abort_unless($campaign->seller_id===$shop->id,403); $campaign->update(['status'=>$campaign->status==='active'?'paused':'active']); return back()->with('status','Campaign status updated.'); }
    public function notifications(Request $request): View { $owner=$this->shop($request)->owner; $rows=WorkspaceNotification::where('user_id',$owner->id)->latest()->paginate(30); $all=WorkspaceNotification::where('user_id',$owner->id); return view('Seller.notifications',['notifications'=>$rows,'notificationCounts'=>['all'=>(clone $all)->count(),'orders'=>(clone $all)->where('type','orders')->count(),'inventory'=>(clone $all)->where('type','inventory')->count(),'finance'=>(clone $all)->where('type','finance')->count(),'system'=>(clone $all)->where('type','system')->count()]]); }
    public function readAll(Request $request): RedirectResponse { $owner=$this->shop($request)->owner; WorkspaceNotification::where('user_id',$owner->id)->whereNull('read_at')->update(['read_at'=>now()]); return back()->with('status','Notifications marked as read.'); }
    private function shop(Request $request): Seller { return $request->user()->sellers()->where('status','approved')->with('owner')->firstOrFail(); }
    private function thread(int $sellerUserId,int $buyerId){ return Message::with(['sender','recipient','order'])->where(fn($q)=>$q->where(fn($x)=>$x->where('sender_id',$sellerUserId)->where('recipient_id',$buyerId))->orWhere(fn($x)=>$x->where('sender_id',$buyerId)->where('recipient_id',$sellerUserId)))->orderBy('id'); }
    private function payload(Message $message,int $viewer): array { return ['id'=>$message->id,'sender_id'=>$message->sender_id,'recipient_id'=>$message->recipient_id,'order_id'=>$message->order_id,'body'=>$message->body,'from_me'=>$message->sender_id===$viewer,'created_at'=>$message->created_at?->toIso8601String(),'time'=>$message->created_at?->diffForHumans()]; }
}
