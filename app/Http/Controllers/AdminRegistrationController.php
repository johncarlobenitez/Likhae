<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminRegistrationController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->query('type', 'buyers');

        $roleMap = [
            'buyers'    => 'buyer',
            'sellers'   => 'seller',
            'logistics' => 'logistics',
            'riders'    => 'courier',
        ];

        $role = $roleMap[$type] ?? 'buyer';

        $applications = User::where('role', $role)
            ->orderByRaw("FIELD(status, 'pending', 'active', 'rejected', 'suspended')")
            ->orderBy('created_at', 'desc')
            ->get();

        $counts = [
            'buyers'    => User::where('role', 'buyer')->where('status', 'pending')->count(),
            'sellers'   => User::where('role', 'seller')->where('status', 'pending')->count(),
            'logistics' => User::where('role', 'logistics')->where('status', 'pending')->count(),
            'riders'    => User::where('role', 'courier')->where('status', 'pending')->count(),
        ];

        $stats = [
            'pending'  => User::where('status', 'pending')->count(),
            'approved' => User::where('status', 'active')->count(),
            'rejected' => User::where('status', 'rejected')->count(),
        ];

        return view('Admin.registrations', compact('applications', 'type', 'counts', 'stats'));
    }

    public function approve(User $user)
    {
        $user->update(['status' => 'active']);

        return back()->with('success', "{$user->name}'s application has been approved.");
    }

    public function reject(Request $request, User $user)
    {
        $user->update(['status' => 'rejected']);

        return back()->with('success', "{$user->name}'s application has been rejected.");
    }
}
