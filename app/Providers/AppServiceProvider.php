<?php

namespace App\Providers;

use App\Models\Admin\Notification;
use App\Models\Communication\Message;
use App\Models\Logistics\LogisticsCenter;
use App\Models\Rider\RiderProfile;
use App\Models\Seller\SellerOrder;
use App\Models\Seller\SellerProfile;
use App\Models\User;
use App\Policies\LogisticsCenterPolicy;
use App\Policies\RiderProfilePolicy;
use App\Policies\SellerProfilePolicy;
use Illuminate\Support\Facades\Gate;
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
        Gate::policy(SellerProfile::class, SellerProfilePolicy::class);
        Gate::policy(LogisticsCenter::class, LogisticsCenterPolicy::class);
        Gate::policy(RiderProfile::class, RiderProfilePolicy::class);

        $unreadMessageCount = static function (?User $user): int {
            if (! $user || ! Schema::hasTable('messages') || ! Schema::hasTable('conversation_participants')) {
                return 0;
            }

            return Message::query()
                ->join('conversation_participants as cp', function ($join) use ($user): void {
                    $join->on('cp.conversation_id', '=', 'messages.conversation_id')
                        ->where('cp.user_id', '=', $user->id)
                        ->whereNull('cp.left_at');
                })
                ->whereNull('messages.deleted_at')
                ->where(function ($query) use ($user): void {
                    $query->whereNull('messages.sender_user_id')
                        ->orWhere('messages.sender_user_id', '<>', $user->id);
                })
                ->where(function ($query): void {
                    $query->whereNull('cp.last_read_at')
                        ->orWhereColumn('messages.sent_at', '>', 'cp.last_read_at');
                })
                ->count('messages.id');
        };

        $unreadNotificationCount = static function (?User $user): int {
            if (! $user || ! Schema::hasTable('notifications')) {
                return 0;
            }

            return Notification::query()
                ->where('user_id', $user->id)
                ->whereNull('read_at')
                ->count();
        };

        View::composer(['components.admin.sidebar', 'components.admin.header'], function ($view) use ($unreadMessageCount, $unreadNotificationCount): void {
            /** @var User|null $admin */
            $admin = auth()->user();

            $view->with('adminUiCounts', [
                'messages' => $admin?->isAccountType(User::TYPE_ADMIN) ? $unreadMessageCount($admin) : 0,
                'notifications' => $admin?->isAccountType(User::TYPE_ADMIN) ? $unreadNotificationCount($admin) : 0,
            ]);
        });

        View::composer(['components.seller.sidebar', 'components.seller.header'], function ($view) use ($unreadMessageCount, $unreadNotificationCount): void {
            /** @var User|null $sellerUser */
            $sellerUser = auth()->user();

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

            if ($sellerUser?->isAccountType(User::TYPE_SELLER) && Schema::hasTable('seller_orders')) {
                $sellerProfileId = $sellerUser->sellerProfile()->where('status', 'ACTIVE')->value('id');

                if ($sellerProfileId) {
                    SellerOrder::query()
                        ->where('seller_profile_id', $sellerProfileId)
                        ->selectRaw('status, COUNT(*) as total')
                        ->groupBy('status')
                        ->pluck('total', 'status')
                        ->each(function ($total, string $status) use (&$counts): void {
                            $key = match ($status) {
                                'PLACED' => 'to_process',
                                'CONFIRMED', 'PREPARING' => 'to_prepare',
                                'READY_FOR_PICKUP' => 'ready_pickup',
                                'PICKED_UP' => 'shipping',
                                'COMPLETED' => 'completed',
                                default => null,
                            };

                            if ($key !== null) {
                                $counts[$key] += (int) $total;
                            }
                        });
                }

                $counts['messages'] = $unreadMessageCount($sellerUser);
                $counts['notifications'] = $unreadNotificationCount($sellerUser);
            }

            $view->with('sellerSidebarCounts', $counts);
            $view->with('sellerUiCounts', $counts);
        });

        View::composer(['components.buyer.sidebar', 'components.buyer.header'], function ($view) use ($unreadMessageCount, $unreadNotificationCount): void {
            /** @var User|null $buyer */
            $buyer = auth()->user();

            $counts = ['cart' => 0, 'messages' => 0, 'notifications' => 0];

            if ($buyer?->isAccountType(User::TYPE_BUYER)) {
                if (Schema::hasTable('carts') && Schema::hasTable('cart_items')) {
                    $counts['cart'] = (int) ($buyer->carts()
                        ->where('status', 'ACTIVE')
                        ->withCount('items')
                        ->latest('id')
                        ->value('items_count') ?? 0);
                }

                $counts['messages'] = $unreadMessageCount($buyer);
                $counts['notifications'] = $unreadNotificationCount($buyer);
            }

            $view->with('buyerUiCounts', $counts);
        });
    }
}
