<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\User;
use App\Models\Admin\AdminAuditLog;
use App\Models\Seller\WorkspaceNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    public const ROLES = [
        'all' => 'All users', 'buyers' => 'Buyers', 'sellers' => 'Sellers',
        'logistics' => 'Logistics centers', 'riders' => 'Riders / Couriers', 'admins' => 'Administrators',
    ];

    public const STATUSES = ['all' => 'All statuses', 'pending' => 'Pending approval', 'active' => 'Active', 'rejected' => 'Rejected', 'suspended' => 'Suspended'];

    public function index(Request $request): View
    {
        $filters = $request->validate([
            'role' => ['sometimes', Rule::in(array_keys(self::ROLES))],
            'status' => ['sometimes', Rule::in(array_keys(self::STATUSES))],
            'q' => ['nullable', 'string', 'max:100'],
        ]);
        $role = $filters['role'] ?? 'all';
        $status = $filters['status'] ?? 'all';
        $search = trim($filters['q'] ?? '');
        $query = User::query();

        $roles = ['buyers' => ['buyer'], 'sellers' => ['seller'], 'logistics' => ['logistics'], 'riders' => ['rider', 'courier'], 'admins' => ['admin']];
        if (isset($roles[$role])) {
            $query->anyRole($roles[$role]);
        }
        if ($status !== 'all') {
            $query->where('status', $status);
        }
        if ($search !== '') {
            $query->where(function ($query) use ($search) {
                $term = '%'.mb_strtolower($search).'%';
                $query->whereRaw('LOWER(name) LIKE ?', [$term])->orWhereRaw('LOWER(email) LIKE ?', [$term]);
                if (preg_match('/^(?:USR-)?(\d+)$/i', $search, $matches)) {
                    $query->orWhere('id', $matches[1]);
                }
            });
        }

        $users = $query->latest()->orderByDesc('id')->paginate(20)->withQueryString();
        $stats = [
            'total' => User::count(),
            'buyers' => User::role('buyer')->count(),
            'sellers' => User::role('seller')->count(),
            'delivery' => User::anyRole(['logistics', 'rider'])->count(),
        ];

        return view('Admin.users', [
            'users' => $users, 'stats' => $stats, 'role' => $role, 'status' => $status, 'search' => $search,
            'roles' => self::ROLES, 'statuses' => self::STATUSES,
        ]);
    }

    public function show(User $user): View
    {
        $user->load(['addresses', 'sellers', 'logisticsProvider', 'rider']);
        $changes = AdminAuditLog::query()
            ->where('target_type', 'User')
            ->where('target_id', $user->id)
            ->whereIn('action', ['user.suspended', 'user.active'])
            ->with('actor')
            ->latest()
            ->orderByDesc('id')
            ->paginate(10);

        return view('Admin.user-details', compact('user', 'changes'));
    }

    public function suspend(Request $request, User $user): RedirectResponse
    {
        return $this->changeStatus($request, $user, 'active', 'suspended');
    }

    public function reactivate(Request $request, User $user): RedirectResponse
    {
        return $this->changeStatus($request, $user, 'suspended', 'active');
    }

    private function changeStatus(Request $request, User $user, string $previousStatus, string $status): RedirectResponse
    {
        abort_unless(in_array($user->primary_role, User::MANAGED_ROLES, true) && $user->id !== $request->user()->id, 403);
        $validated = $request->validate(['reason' => ['required', 'string', 'max:2000']]);

        DB::transaction(function () use ($request, $user, $previousStatus, $status, $validated) {
            $account = User::whereKey($user->id)->lockForUpdate()->firstOrFail();
            abort_unless(in_array($account->primary_role, User::MANAGED_ROLES, true), 403);
            if ($account->status !== $previousStatus) {
                throw ValidationException::withMessages(['status' => 'This account can no longer be changed with that action. Refresh the page to see its current status.']);
            }

            $account->status = $status;
            $account->is_suspended = $status === 'suspended';
            if ($status === 'suspended') {
                $account->remember_token = Str::random(60);
            }
            $account->save();

            WorkspaceNotification::create([
                'user_id' => $account->id,
                'type' => 'system',
                'title' => $status === 'active' ? 'Account reactivated' : 'Account suspended',
                'body' => $validated['reason'],
            ]);

            AdminAuditLog::create([
                'actor_id' => $request->user()->id,
                'action' => 'user.'.$status,
                'target_type' => 'User',
                'target_id' => $account->id,
                'description' => $validated['reason'],
                'ip_address' => $request->ip(),
                'metadata' => [
                    'before' => ['status' => $previousStatus],
                    'after' => ['status' => $status],
                ],
            ]);
        });

        $action = $status === 'active' ? 'reactivated' : 'suspended';

        return redirect()->route('admin.users.show', $user)->with('success', "{$user->name}'s account has been {$action}.");
    }
}
