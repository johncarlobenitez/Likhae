<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        $accounts = User::whereIn('role', User::PUBLIC_ROLES);

        $accountStats = [
            'pending' => (clone $accounts)->where('status', 'pending')->count(),
            'active' => (clone $accounts)->where('status', 'active')->count(),
            'pending_older_than_day' => (clone $accounts)
                ->where('status', 'pending')
                ->where('created_at', '<', now()->subDay())
                ->count(),
        ];

        return view('Admin.dashboard', compact('accountStats'));
    }
}
