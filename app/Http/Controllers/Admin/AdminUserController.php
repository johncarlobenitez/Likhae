<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\AuditLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    public function index(Request $request): View
    {
        $accountTypes = [User::TYPE_BUYER, User::TYPE_SELLER, User::TYPE_ADMIN, User::TYPE_LOGISTICS];
        $role = strtolower((string) $request->query('role', $request->query('type', 'all')));
        $roleTypes = [
            'buyers' => User::TYPE_BUYER,
            'sellers' => User::TYPE_SELLER,
            'admins' => User::TYPE_ADMIN,
            'logistics' => User::TYPE_LOGISTICS,
        ];
        $role = match ($role) {
            'buyer' => 'buyers',
            'seller' => 'sellers',
            'admin' => 'admins',
            default => $role,
        };
        $role = array_key_exists($role, $roleTypes) ? $role : 'all';
        $status = strtolower((string) $request->query('status', 'all'));
        $status = in_array($status, ['pending', 'active', 'suspended', 'deactivated'], true) ? $status : 'all';
        $search = trim((string) $request->query('q', ''));

        $users = User::query()
            ->whereIn('account_type', $accountTypes)
            ->when($role !== 'all', fn ($query) => $query->where('account_type', $roleTypes[$role]))
            ->when($status !== 'all', fn ($query) => $query->where('status', strtoupper($status)))
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('first_name', 'like', '%'.$search.'%')
                        ->orWhere('last_name', 'like', '%'.$search.'%')
                        ->orWhere('email', 'like', '%'.$search.'%');
                    if (preg_match('/^USR-(\d+)$/i', $search, $matches)) {
                        $query->orWhereKey((int) $matches[1]);
                    }
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $stats = [
            'total' => User::query()->whereIn('account_type', $accountTypes)->count(),
            'buyers' => User::query()->where('account_type', User::TYPE_BUYER)->count(),
            'sellers' => User::query()->where('account_type', User::TYPE_SELLER)->count(),
            'delivery' => User::query()->where('account_type', User::TYPE_LOGISTICS)->count(),
        ];

        return view('Admin.users', compact('users', 'role', 'status', 'search', 'stats'));
    }

    public function show(User $user): View
    {
        $this->ensureAdminManaged($user);
        $user->load(['addresses', 'sellerProfile', 'logisticsCenter', 'registrationApplications.documents']);
        $changes = AuditLog::query()
            ->with('actor')
            ->where('auditable_type', User::class)
            ->where('auditable_id', $user->id)
            ->latest('created_at')
            ->paginate(10);

        return view('Admin.user-details', compact('user', 'changes'));
    }

    public function suspend(User $user): RedirectResponse
    {
        $this->ensureAdminManaged($user);
        $user->update(['status' => 'SUSPENDED']);
        $user->sellerProfile?->update(['status' => 'SUSPENDED']);
        $user->logisticsCenter?->update(['status' => 'SUSPENDED']);

        return back()->with('status', 'User suspended.');
    }

    public function reactivate(User $user): RedirectResponse
    {
        $this->ensureAdminManaged($user);
        $user->update(['status' => 'ACTIVE']);
        $user->sellerProfile?->update(['status' => 'ACTIVE']);
        $user->logisticsCenter?->update(['status' => 'ACTIVE']);

        return back()->with('status', 'User reactivated.');
    }

    private function ensureAdminManaged(User $user): void
    {
        abort_unless(in_array($user->account_type, [
            User::TYPE_BUYER,
            User::TYPE_SELLER,
            User::TYPE_ADMIN,
            User::TYPE_LOGISTICS,
        ], true), 404);
    }
}
