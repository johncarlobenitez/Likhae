<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\Message;
use App\Models\User;
use App\Models\WorkspaceNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LogisticsController extends Controller
{
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
        $recipient = User::whereKey($validated['recipient_id'])->whereIn('role', ['courier', 'seller'])->where('status', 'active')->firstOrFail();
        Message::create(['sender_id' => $sender->id, 'recipient_id' => $recipient->id, 'body' => $validated['body']]);
        WorkspaceNotification::create(['user_id' => $recipient->id, 'type' => 'message', 'title' => 'New logistics message', 'body' => "Message from {$sender->name}", 'action_url' => $recipient->role === 'seller' ? route('seller.messages') : route('courier.messages')]);
        return back()->with('status', "Message sent to {$recipient->name}.");
    }

    public function updateParcel(Request $request, Delivery $delivery): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:received,scanned,sorted,assigned,out_for_delivery,delivered,failed'],
            'area' => ['nullable', 'string', 'max:120'],
            'rider_id' => ['nullable', 'exists:users,id'],
        ]);

        $delivery->update([
            'status' => $validated['status'],
            'area' => $validated['area'] ?? $delivery->area,
            'rider_id' => $validated['rider_id'] ?? $delivery->rider_id,
            'assigned_at' => $validated['status'] === 'assigned' ? now() : $delivery->assigned_at,
            'delivered_at' => $validated['status'] === 'delivered' ? now() : $delivery->delivered_at,
        ]);

        return back()->with('status', "Parcel {$delivery->parcel_code} updated.");
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
        $email = request()->session()->get('demo_user.email');

        if (! $email || $email === 'guest' || $email === 'logistics@likhae.com') {
            return null;
        }

        return User::where('email', $email)->where('role', 'logistics')->value('id');
    }

    private function currentUser(): ?User
    {
        $email = request()->session()->get('demo_user.email');
        return $email ? User::where('email', $email)->where('role', 'logistics')->first() : null;
    }
}
