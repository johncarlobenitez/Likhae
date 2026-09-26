<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\CommissionTransaction;
use App\Models\Admin\Dispute;
use App\Models\Admin\SellerComplianceCase;
use App\Models\Auth\RegistrationApplication;
use App\Models\Buyer\Order;
use App\Models\Logistics\Shipment;
use App\Models\Seller\Product;
use App\Models\Seller\SellerOrder;
use App\Models\User;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        $pendingApplications = RegistrationApplication::query()
            ->whereIn('status', [RegistrationApplication::STATUS_PENDING, RegistrationApplication::STATUS_UNDER_REVIEW]);
        $sevenDayStart = now()->subDays(6)->startOfDay();
        $sevenDayOrders = Order::query()
            ->whereNotIn('status', ['CANCELLED'])
            ->whereBetween('placed_at', [$sevenDayStart, now()])
            ->get(['placed_at', 'grand_total']);
        $dailyRevenue = $sevenDayOrders->groupBy(fn (Order $order): string => $order->placed_at->toDateString())
            ->map(fn ($orders): float => (float) $orders->sum('grand_total'));
        $maxDailyRevenue = max(1, (float) $dailyRevenue->max());
        $revenueChart = collect(range(0, 6))->map(function (int $offset) use ($sevenDayStart, $dailyRevenue, $maxDailyRevenue): array {
            $date = $sevenDayStart->copy()->addDays($offset);
            $total = (float) $dailyRevenue->get($date->toDateString(), 0);

            return [
                'date' => $date->format('M d'),
                'label' => $date->format('D'),
                'total' => $total,
                'height' => (int) round($total / $maxDailyRevenue * 100),
            ];
        });

        return view('Admin.dashboard', [
            'accountStats' => [
                'pending' => (clone $pendingApplications)->count(),
                'active' => User::query()->where('status', User::STATUS_ACTIVE)->where('account_type', '!=', User::TYPE_ADMIN)->count(),
                'pending_older_than_day' => (clone $pendingApplications)->where('submitted_at', '<', now()->subDay())->count(),
            ],
            'productStats' => [
                'total' => Product::count(),
                'flagged' => SellerComplianceCase::query()->whereIn('status', ['OPEN', 'UNDER_REVIEW'])->whereNotNull('product_id')->distinct()->count('product_id'),
            ],
            'refundStats' => [
                'open' => Dispute::query()->whereIn('status', ['OPEN', 'UNDER_REVIEW'])->count(),
            ],
            'platformCommission' => (float) CommissionTransaction::query()->where('status', 'SETTLED')->sum('commission_amount'),
            'commissionRate' => (float) config('likhae.seller_commission_rate'),
            'sevenDayGross' => (float) $sevenDayOrders->sum('grand_total'),
            'revenueChart' => $revenueChart,
            'orderStats' => [
                'today' => Order::query()->whereDate('placed_at', today())->count(),
                'processing' => SellerOrder::query()->whereIn('status', ['PLACED', 'CONFIRMED', 'PREPARING'])->count(),
                'shipping' => Shipment::query()->whereIn('current_status', ['PICKED_UP', 'AT_SORTING_CENTER', 'SORTED', 'ASSIGNED_TO_RIDER', 'OUT_FOR_DELIVERY'])->count(),
                'delivered' => Shipment::query()->where('current_status', 'DELIVERED')->count(),
                'completed_other' => SellerOrder::query()->whereIn('status', ['COMPLETED', 'CANCELLED'])->count(),
            ],
            'recentOrders' => SellerOrder::query()
                ->with(['order.buyer', 'sellerProfile.user', 'shipment.logisticsCenter'])
                ->latest()
                ->limit(8)
                ->get(),
        ]);
    }
}
