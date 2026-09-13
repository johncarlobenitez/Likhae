<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class AdminRegistrationController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->query('type', 'buyers');

        $roleMap = [
            'buyers' => 'buyer',
            'sellers' => 'seller',
            'logistics' => 'logistics',
            'riders' => 'courier',
        ];

        if (! is_string($type) || ! array_key_exists($type, $roleMap)) {
            $type = 'buyers';
        }
        $role = $roleMap[$type];

        $applications = User::whereIn('role', $role === 'courier' ? ['courier', 'rider'] : [$role])
            ->with('reviewer')
            ->orderByRaw("CASE status WHEN 'pending' THEN 0 WHEN 'active' THEN 1 WHEN 'rejected' THEN 2 ELSE 3 END")
            ->orderBy('created_at', 'desc')
            ->paginate(20)->withQueryString();

        $counts = [
            'buyers' => User::where('role', 'buyer')->where('status', 'pending')->count(),
            'sellers' => User::where('role', 'seller')->where('status', 'pending')->count(),
            'logistics' => User::where('role', 'logistics')->where('status', 'pending')->count(),
            'riders' => User::whereIn('role', ['courier', 'rider'])->where('status', 'pending')->count(),
        ];

        $stats = [
            'pending' => User::whereIn('role', User::PUBLIC_ROLES)->where('status', 'pending')->count(),
            'approved' => User::whereIn('role', User::PUBLIC_ROLES)->where('status', 'active')->count(),
            'rejected' => User::whereIn('role', User::PUBLIC_ROLES)->where('status', 'rejected')->count(),
        ];

        return view('Admin.registrations', compact('applications', 'type', 'counts', 'stats'));
    }

    public function approve(Request $request, User $user)
    {
        return $this->review($request, $user, 'active');
    }

    public function reject(Request $request, User $user)
    {
        $validated = $request->validate(['rejection_reason' => ['required', 'string', 'max:2000']]);

        return $this->review($request, $user, 'rejected', $validated['rejection_reason']);
    }

    private function review(Request $request, User $user, string $status, ?string $reason = null)
    {
        abort_unless(in_array($user->role, User::PUBLIC_ROLES, true), 403);
        // The conditional update prevents a second reviewer from overwriting a decision.
        $updated = User::whereKey($user->id)->where('status', 'pending')->update([
            'status' => $status,
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
            'rejection_reason' => $reason,
        ]);
        if (! $updated) {
            throw ValidationException::withMessages(['application' => 'This application is no longer pending. Refresh the review queue.']);
        }
        $decision = $status === 'active' ? 'approved' : 'rejected';

        return back()->with('success', "{$user->name}'s application has been {$decision}.");
    }

    public function document(User $user, string $document)
    {
        abort_unless(in_array($document, ['valid_id', 'business_permit', 'or_cr', 'drivers_license'], true), 404);
        $path = $user->getAttribute($document.'_path');
        abort_unless($path && str_starts_with($path, 'registration/') && ! str_contains($path, '..'), 404);
        $disk = Storage::disk('registrations');
        // Support applications uploaded before private registration storage was introduced.
        if (! $disk->exists($path)) {
            $disk = Storage::disk('public');
        }
        abort_unless($disk->exists($path), 404);

        return $disk->download($path, $document.'.'.pathinfo($path, PATHINFO_EXTENSION), [
            'Cache-Control' => 'private, no-store',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
