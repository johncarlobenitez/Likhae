<?php

namespace App\Console\Commands;

use App\Models\Communication\Message;
use App\Models\Seller\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class PurgeExpiredSoftDeletes extends Command
{
    protected $signature = 'likhae:purge-expired-soft-deletes';

    protected $description = 'Permanently remove soft-deleted products and messages after the 30-day recovery period';

    public function handle(): int
    {
        $cutoff = now()->subDays(30);
        $products = Product::onlyTrashed()->where('deleted_at', '<=', $cutoff)->with('images')->get();
        foreach ($products as $product) {
            foreach ($product->images as $image) {
                if (filled($image->file_path)) {
                    Storage::disk('public')->delete($image->file_path);
                }
            }
            $variantIds = $product->variants()->pluck('id');
            if ($variantIds->isNotEmpty()) {
                DB::table('cart_items')->whereIn('product_variant_id', $variantIds)->delete();
            }
            $product->forceDelete();
        }

        $messages = Message::onlyTrashed()->where('deleted_at', '<=', $cutoff)->delete();
        $this->info('Permanently removed '.$products->count().' expired products and '.$messages.' expired messages.');

        return self::SUCCESS;
    }
}
