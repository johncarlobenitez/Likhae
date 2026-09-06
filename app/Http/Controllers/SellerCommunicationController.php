<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use App\Models\WorkspaceNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SellerCommunicationController extends Controller
{
    public function messages(): View
    {
        $seller = $this->seller();
        return view('Seller/communication', [
            'mode' => 'messages',
            'messages' => $seller ? Message::with('sender')->where('recipient_id', $seller->id)->latest()->get() : collect(),
            'contacts' => User::whereIn('role', ['courier', 'logistics'])->where('status', 'active')->orderBy('name')->get(),
        ]);
    }

    public function notifications(): View
    {
        $seller = $this->seller();
        return view('Seller/communication', [
            'mode' => 'notifications',
            'notifications' => $seller ? WorkspaceNotification::where('user_id', $seller->id)->latest()->get() : collect(),
        ]);
    }

    public function sendMessage(Request $request): RedirectResponse
    {
        $seller = $this->seller();
        abort_unless($seller, 403);
        $validated = $request->validate([
            'recipient_id' => ['required', 'exists:users,id'],
            'body' => ['required', 'string', 'max:2000'],
        ]);
        $recipient = User::whereKey($validated['recipient_id'])->whereIn('role', ['courier', 'logistics'])->where('status', 'active')->firstOrFail();
        Message::create(['sender_id' => $seller->id, 'recipient_id' => $recipient->id, 'body' => $validated['body']]);
        WorkspaceNotification::create(['user_id' => $recipient->id, 'type' => 'message', 'title' => 'New seller message', 'body' => "Message from {$seller->name}", 'action_url' => $recipient->role === 'courier' ? route('courier.messages') : route('logistics.messages')]);
        return back()->with('status', "Message sent to {$recipient->name}.");
    }

    private function seller(): ?User
    {
        $email = request()->session()->get('demo_user.email');
        return $email ? User::where('email', $email)->where('role', 'seller')->first() : null;
    }
}
