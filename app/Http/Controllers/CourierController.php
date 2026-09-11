<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\Message;
use App\Models\User;
use App\Models\WorkspaceNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CourierController extends Controller
{
    public function index(string $mode = 'courier'): View
    {
        $deliveries = Delivery::with('rider')->latest()->get();
        $currentUser = $this->currentUser();
        $messages = $currentUser ? Message::with('sender')->where('recipient_id', $currentUser->id)->latest()->get() : collect();
        $notifications = $currentUser ? WorkspaceNotification::where('user_id', $currentUser->id)->latest()->get() : collect();
        $contacts = User::whereIn('role', ['logistics', 'seller'])->where('status', 'active')->orderBy('name')->get();

        $view = $mode === 'courier' ? 'Courier.home' : 'Courier.screen';

        return view($view, [
            'mode' => $mode,
            'deliveries' => $deliveries,
            'stats' => [
                'pickups' => $deliveries->whereIn('status', ['received', 'scanned'])->count(),
                'deliveries' => $deliveries->whereIn('status', ['assigned', 'out_for_delivery'])->count(),
                'completed' => $deliveries->where('status', 'delivered')->count(),
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
        $recipient = User::whereKey($validated['recipient_id'])->whereIn('role', ['logistics', 'seller'])->where('status', 'active')->firstOrFail();
        Message::create(['sender_id' => $sender->id, 'recipient_id' => $recipient->id, 'body' => $validated['body']]);
        WorkspaceNotification::create(['user_id' => $recipient->id, 'type' => 'message', 'title' => 'New courier message', 'body' => "Message from {$sender->name}", 'action_url' => $recipient->role === 'seller' ? route('seller.messages') : route('logistics.messages')]);
        return back()->with('status', "Message sent to {$recipient->name}.");
    }

    public function update(Request $request, Delivery $delivery): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:received,scanned,out_for_delivery,delivered,failed'],
        ]);

        $delivery->update([
            'status' => $validated['status'],
            'delivered_at' => $validated['status'] === 'delivered' ? now() : $delivery->delivered_at,
        ]);

        return back()->with('status', "Parcel {$delivery->parcel_code} updated.");
    }

    private function currentUser(): ?User
    {
        $email = request()->session()->get('demo_user.email');
        return $email ? User::where('email', $email)->where('role', 'courier')->first() : null;
    }
}
