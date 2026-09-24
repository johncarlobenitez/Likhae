<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Models\PlatformSetting;
use App\Models\Product;
use App\Models\ReturnRequest;
use App\Models\SellerOrder;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        $accounts = User::query()->anyRole(User::MANAGED_ROLES);

        $accountStats = [
            'pending' => (clone $accounts)->where('status', 'pending')->count(),
            'active' => (clone $accounts)->where('status', 'active')->count(),
            'pending_older_than_day' => (clone $accounts)
                ->where('status', 'pending')
                ->where('created_at', '<', now()->subDay())
                ->count(),
        ];

        $productStats = [
            'total' => Product::count(),
            'flagged' => 0,
        ];

        $refundStats = [
            'open' => ReturnRequest::whereIn('status', ['requested', 'approved', 'disputed'])->count(),
        ];

        $grossTransactionValue = ((int) Payment::whereIn('status', ['paid', 'completed'])->sum('amount_minor')) / 100;
        $commissionRate = PlatformSetting::commissionRate();
        $platformCommission = round($grossTransactionValue * $commissionRate, 2);

        $todayRange = [now()->startOfDay(), now()->endOfDay()];
        $todayOrders = Order::whereBetween('created_at', $todayRange);
        $todaySellerOrders = SellerOrder::whereHas('order', fn ($query) => $query->whereBetween('created_at', $todayRange));
        $orderStats = [
            'today' => (clone $todayOrders)->count(),
            'processing' => (clone $todaySellerOrders)->whereIn('status', ['pending', 'accepted', 'packed', 'ready_to_ship'])->count(),
            'shipping' => (clone $todaySellerOrders)->where('status', 'shipped')->count(),
            'delivered' => (clone $todaySellerOrders)->where('status', 'delivered')->count(),
            'completed_other' => (clone $todaySellerOrders)->whereIn('status', ['completed', 'cancelled', 'refunded'])->count(),
        ];

        $recentOrders = SellerOrder::with(['order.buyer', 'seller', 'shipment.provider'])
            ->latest()
            ->take(5)
            ->get();

        $chartStart = now()->subDays(6)->startOfDay();
        $chartOrders = Order::query()
            ->where('created_at', '>=', $chartStart)
            ->whereHas('sellerOrders', fn ($query) => $query->whereNotIn('status', ['cancelled', 'refunded']))
            ->get(['created_at', 'total_minor']);

        $revenueChart = collect(range(0, 6))->map(function (int $offset) use ($chartStart, $chartOrders) {
            $date = $chartStart->copy()->addDays($offset);
            $total = (float) $chartOrders
                ->filter(fn (Order $order) => $order->created_at?->isSameDay($date))
                ->sum(fn (Order $order) => ((int) $order->total_minor) / 100);

            return [
                'label' => $date->format('D'),
                'date' => $date->toDateString(),
                'total' => $total,
            ];
        });

        $maxRevenue = max(1, (float) $revenueChart->max('total'));
        $revenueChart = $revenueChart->map(fn (array $point) => [
            ...$point,
            'height' => $point['total'] > 0 ? max(8, round(($point['total'] / $maxRevenue) * 100, 2)) : 0,
        ]);

        $sevenDayGross = (float) $revenueChart->sum('total');

        return view('Admin.dashboard', compact(
            'accountStats',
            'productStats',
            'refundStats',
            'orderStats',
            'recentOrders',
            'revenueChart',
            'sevenDayGross',
            'commissionRate',
            'platformCommission',
        ));
    }
}
