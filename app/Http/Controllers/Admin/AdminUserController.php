<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    public function index(Request $request): View
    {
        $type = strtoupper((string) $request->query('type', $request->query('role', '')));
        $status = strtoupper((string) $request->query('status', ''));

        $users = User::query()
            ->when($type !== '', fn ($query) => $query->where('account_type', str_replace('RIDERS', 'RIDER', str_replace('SELLERS', 'SELLER', $type))))
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->with(['sellerProfile', 'logisticsCenter', 'riderProfile'])
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('Admin.users', compact('users', 'type', 'status'));
    }

    public function show(User $user): View
    {
        $user->load(['addresses', 'sellerProfile', 'logisticsCenter', 'riderProfile', 'registrationApplications.documents']);

        return view('Admin.user-details', compact('user'));
    }

    public function suspend(User $user): RedirectResponse
    {
        $user->update(['status' => 'SUSPENDED']);
        $user->sellerProfile?->update(['status' => 'SUSPENDED']);
        $user->logisticsCenter?->update(['status' => 'SUSPENDED']);
        $user->riderProfile?->update(['status' => 'SUSPENDED']);

        return back()->with('status', 'User suspended.');
    }

    public function reactivate(User $user): RedirectResponse
    {
        $user->update(['status' => 'ACTIVE']);
        $user->sellerProfile?->update(['status' => 'ACTIVE']);
        $user->logisticsCenter?->update(['status' => 'ACTIVE']);
        $user->riderProfile?->update(['status' => 'ACTIVE']);

        return back()->with('status', 'User reactivated.');
    }
}
