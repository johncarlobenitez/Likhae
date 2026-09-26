<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Admin\Notification;
use App\Models\Buyer\Review;
use App\Models\Seller\Voucher;
use App\Services\Communication\ConversationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SellerEngagementController extends Controller
{
    public function messages(Request $request, ConversationService $conversationService): View
    {
        return view('Seller.messages', [
            'conversations' => $conversationService->listFor($request->user()),
            'contacts' => \App\Models\User::query()->where('status', 'ACTIVE')->whereKeyNot($request->user()->id)->orderBy('first_name')->get(),
        ]);
    }

    public function stream(Request $request, ConversationService $conversationService): JsonResponse
    {
        return response()->json([
            'success' => true,
            'conversations' => $conversationService->listFor($request->user())->map(fn ($conversation) => [
                'id' => $conversation->id,
                'latest' => $conversation->latestMessage?->body,
                'updated_at' => optional($conversation->updated_at)->toIso8601String(),
            ]),
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

    public function reviews(Request $request): View
    {
        $seller = $request->user()->sellerProfile;
        abort_unless($seller, 403);

        $reviews = Review::query()
            ->whereHas('orderItem.sellerOrder', fn ($query) => $query->where('seller_profile_id', $seller->id))
            ->with(['orderItem', 'buyer'])
            ->latest()
            ->paginate(15);

        return view('Seller.reviews', compact('reviews', 'seller'));
    }

    public function replyReview(Request $request, Review $review): RedirectResponse
    {
        $seller = $request->user()->sellerProfile;
        abort_unless($seller && (int) $review->orderItem?->sellerOrder?->seller_profile_id === (int) $seller->id, 403);

        $data = $request->validate(['seller_reply' => ['required', 'string', 'max:2000']]);
        $review->update(['seller_reply' => $data['seller_reply'], 'seller_replied_at' => now()]);

        return back()->with('status', 'Review reply saved.');
    }

    public function exportReviews(Request $request)
    {
        return $this->reviews($request);
    }

    public function marketing(Request $request): View
    {
        $seller = $request->user()->sellerProfile;
        abort_unless($seller, 403);

        return view('Seller.marketing', [
            'seller' => $seller,
            'vouchers' => $seller->vouchers()->withCount('sellerOrders')->latest()->paginate(15),
        ]);
    }

    public function storeCampaign(Request $request): RedirectResponse
    {
        $seller = $request->user()->sellerProfile;
        abort_unless($seller, 403);

        $data = $request->validate([
            'code' => ['required', 'string', 'max:80'],
            'name' => ['required', 'string', 'max:150'],
            'discount_type' => ['required', 'string', 'in:PERCENT,FIXED'],
            'discount_value' => ['required', 'numeric', 'min:0'],
            'minimum_order_amount' => ['nullable', 'numeric', 'min:0'],
            'maximum_discount_amount' => ['nullable', 'numeric', 'min:0'],
            'usage_limit' => ['nullable', 'integer', 'min:1'],
            'per_user_limit' => ['nullable', 'integer', 'min:1'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date'],
        ]);

        Voucher::create($data + [
            'seller_profile_id' => $seller->id,
            'is_active' => true,
        ]);

        return back()->with('status', 'Voucher saved.');
    }

    public function toggleCampaign(Request $request, Voucher $voucher): RedirectResponse
    {
        $seller = $request->user()->sellerProfile;
        abort_unless($seller && (int) $voucher->seller_profile_id === (int) $seller->id, 403);

        $voucher->update(['is_active' => ! $voucher->is_active]);

        return back()->with('status', 'Voucher updated.');
    }

    public function notifications(Request $request): View
    {
        return view('Seller.notifications', [
            'notifications' => $request->user()->notifications()->latest()->paginate(20),
        ]);
    }

    public function readAll(Request $request): RedirectResponse
    {
        Notification::where('user_id', $request->user()->id)->whereNull('read_at')->update(['read_at' => now()]);

        return back()->with('status', 'Notifications marked as read.');
    }
}
