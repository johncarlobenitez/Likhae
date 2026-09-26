<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\CommissionTransaction;
use App\Models\Admin\Dispute;
use App\Models\Auth\RegistrationApplication;
use App\Models\Buyer\Order;
use App\Models\Logistics\Shipment;
use App\Models\Seller\Product;
use App\Models\User;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        return view('Admin.dashboard', [
            'stats' => [
                'users' => User::count(),
                'pending_registrations' => RegistrationApplication::whereIn('status', ['PENDING', 'UNDER_REVIEW'])->count(),
                'products' => Product::count(),
                'orders' => Order::count(),
                'shipments' => Shipment::count(),
                'open_disputes' => Dispute::whereIn('status', ['OPEN', 'UNDER_REVIEW'])->count(),
                'commission_due' => (float) CommissionTransaction::where('status', 'PENDING')->sum('commission_amount'),
            ],
            'registrations' => RegistrationApplication::with('user')->latest()->limit(8)->get(),
            'orders' => Order::with('buyer')->latest()->limit(8)->get(),
        ]);
    }
}
