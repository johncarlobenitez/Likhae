<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\PlatformSetting;
use App\Models\Product;
use App\Models\Refund;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        $accounts = User::query()->whereIn('role', User::PUBLIC_ROLES);

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
            'flagged' => Product::where('admin_status', 'flagged')->count(),
        ];

        $refundStats = [
            'open' => Refund::whereNotIn('status', ['approved', 'rejected', 'resolved', 'completed'])->count(),
        ];

        $eligibleTransactions = Transaction::whereIn('status', ['paid', 'completed']);
        $grossTransactionValue = (float) (clone $eligibleTransactions)->sum('amount');
        $commissionRate = PlatformSetting::commissionRate();
        $platformCommission = round($grossTransactionValue * $commissionRate, 2);

        $todayOrders = Order::whereBetween('created_at', [now()->startOfDay(), now()->endOfDay()]);
        $orderStats = [
            'today' => (clone $todayOrders)->count(),
            'processing' => (clone $todayOrders)->whereIn('status', ['pending', 'confirmed', 'processing', 'preparing'])->count(),
            'shipping' => (clone $todayOrders)->whereIn('status', ['ready_for_pickup', 'picked_up', 'sorting', 'assigned', 'out_for_delivery', 'shipping'])->count(),
            'delivered' => (clone $todayOrders)->where('status', 'delivered')->count(),
            'completed_other' => (clone $todayOrders)->whereNotIn('status', [
                'pending', 'confirmed', 'processing', 'preparing', 'ready_for_pickup', 'picked_up',
                'sorting', 'assigned', 'out_for_delivery', 'shipping', 'delivered',
            ])->count(),
        ];

        $recentOrders = Order::with(['buyer', 'seller', 'delivery'])
            ->latest()
            ->take(5)
            ->get();

        $chartStart = now()->subDays(6)->startOfDay();
        $chartOrders = Order::query()
            ->where('created_at', '>=', $chartStart)
            ->whereNotIn('status', ['cancelled'])
            ->get(['created_at', 'total_amount']);

        $revenueChart = collect(range(0, 6))->map(function (int $offset) use ($chartStart, $chartOrders) {
            $date = $chartStart->copy()->addDays($offset);
            $total = (float) $chartOrders
                ->filter(fn (Order $order) => $order->created_at?->isSameDay($date))
                ->sum(fn (Order $order) => (float) $order->total_amount);

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
