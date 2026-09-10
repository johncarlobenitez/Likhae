<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserStatusChange;
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
            $query->whereIn('role', $roles[$role]);
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
        $counts = User::selectRaw('role, COUNT(*) AS total')->groupBy('role')->pluck('total', 'role');
        $stats = [
            'total' => $counts->sum(), 'buyers' => $counts['buyer'] ?? 0, 'sellers' => $counts['seller'] ?? 0,
            'delivery' => ($counts['logistics'] ?? 0) + ($counts['courier'] ?? 0) + ($counts['rider'] ?? 0),
        ];

        return view('Admin.users', [
            'users' => $users, 'stats' => $stats, 'role' => $role, 'status' => $status, 'search' => $search,
            'roles' => self::ROLES, 'statuses' => self::STATUSES,
        ]);
    }

    public function show(User $user): View
    {
        $user->load('reviewer');
        $changes = UserStatusChange::where('user_id', $user->id)->with('administrator')->latest()->orderByDesc('id')->paginate(10);

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
        abort_unless(in_array($user->role, User::PUBLIC_ROLES, true) && $user->id !== $request->user()->id, 403);
        $validated = $request->validate(['reason' => ['required', 'string', 'max:2000']]);

        DB::transaction(function () use ($request, $user, $previousStatus, $status, $validated) {
            $account = User::whereKey($user->id)->lockForUpdate()->firstOrFail();
            abort_unless(in_array($account->role, User::PUBLIC_ROLES, true), 403);
            if ($account->status !== $previousStatus) {
                throw ValidationException::withMessages(['status' => 'This account can no longer be changed with that action. Refresh the page to see its current status.']);
            }

            $account->status = $status;
            if ($status === 'suspended') {
                $account->remember_token = Str::random(60);
            }
            $account->save();

            UserStatusChange::create([
                'user_id' => $account->id, 'changed_by' => $request->user()->id,
                'previous_status' => $previousStatus, 'status' => $status, 'reason' => $validated['reason'],
            ]);
        });

        $action = $status === 'active' ? 'reactivated' : 'suspended';

        return redirect()->route('admin.users.show', $user)->with('success', "{$user->name}'s account has been {$action}.");
    }
}
