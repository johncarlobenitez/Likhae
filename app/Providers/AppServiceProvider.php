<?php

namespace App\Providers;

use App\Models\Message;
use App\Models\Order;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
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
            ];

            if ($seller?->role === 'seller' && Schema::hasTable('orders')) {
                Order::where('seller_id', $seller->id)
                    ->pluck('status')
                    ->each(function (?string $status) use (&$counts): void {
                        $key = match ($status) {
                            'pending' => 'to_process',
                            'shipped' => 'shipping',
                            'disputed' => 'returns',
                            default => $status ?: 'to_process',
                        };

                        if (array_key_exists($key, $counts)) {
                            $counts[$key]++;
                        }
                    });
            }

            if ($seller?->role === 'seller' && Schema::hasTable('messages')) {
                $counts['messages'] = Message::where('recipient_id', $seller->id)
                    ->whereNull('read_at')
                    ->count();
            }

            $view->with('sellerSidebarCounts', $counts);
            $view->with('sellerUiCounts', $counts);
        });
    }
}
