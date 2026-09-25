<?php

namespace App\Providers;

use App\Models\Seller\Message;
use App\Models\Seller\SellerOrder;
use App\Models\Seller\WorkspaceNotification;
use App\Support\BuyerMarketplace;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {

        View::composer(['components.admin.sidebar', 'components.admin.header'], function ($view) {
            $admin = auth()->user();
            $counts = ['messages' => 0, 'notifications' => 0];

            if ($admin?->hasRole('admin')) {
                if (Schema::hasTable('messages')) {
                    $counts['messages'] = Message::where('recipient_id', $admin->id)->whereNull('read_at')->count();
                }
                if (Schema::hasTable('workspace_notifications')) {
                    $counts['notifications'] = WorkspaceNotification::where('user_id', $admin->id)->whereNull('read_at')->count();
                }
            }

            $view->with('adminUiCounts', $counts);
        });

        View::composer(['components.seller.sidebar', 'components.seller.header'], function ($view) {
            $seller = auth()->user();
            $counts = [
                'to_process' => 0,
                'to_prepare' => 0,
                'ready_pickup' => 0,
                'shipping' => 0,
                'completed' => 0,
                'returns' => 0,
                'messages' => 0,
                'notifications' => 0,
            ];

            if ($seller?->hasRole('seller') && Schema::hasTable('seller_orders')) {
                $shopIds = $seller->sellers()->where('status', 'approved')->pluck('id');
                SellerOrder::whereIn('seller_id', $shopIds)
                    ->pluck('status')
                    ->each(function (?string $status) use (&$counts): void {
                        $key = match ($status) {
                            'pending' => 'to_process',
                            'accepted', 'packed' => 'to_prepare',
                            'ready_to_ship' => 'ready_pickup',
                            'shipped', 'delivered' => 'shipping',
                            'refunded' => 'returns',
                            default => $status ?: 'to_process',
                        };

                        if (array_key_exists($key, $counts)) {
                            $counts[$key]++;
                        }
                    });
            }

            if ($seller?->hasRole('seller') && Schema::hasTable('messages')) {
                $counts['messages'] = Message::where('recipient_id', $seller->id)
                    ->whereNull('read_at')
                    ->count();
            }

            if ($seller?->hasRole('seller') && Schema::hasTable('workspace_notifications')) {
                $counts['notifications'] = WorkspaceNotification::where('user_id', $seller->id)
                    ->whereNull('read_at')
                    ->count();
            }

            $view->with('sellerSidebarCounts', $counts);
            $view->with('sellerUiCounts', $counts);
        });

        View::composer(['components.buyer.sidebar', 'components.buyer.header'], function ($view) {
            $buyer = auth()->user();
            $counts = ['cart' => 0, 'messages' => 0, 'notifications' => 0];

            if (
                $buyer?->hasRole('buyer')
                && Schema::hasTable('carts')
                && Schema::hasTable('cart_items')
                && Schema::hasTable('messages')
                && Schema::hasTable('workspace_notifications')
            ) {
                $counts = BuyerMarketplace::buyerCounts($buyer);
            }

            $view->with('buyerUiCounts', $counts);
        });
    }
}
