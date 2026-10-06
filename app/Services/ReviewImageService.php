<?php

namespace App\Services;

use App\Models\Admin\AuditLog;
use App\Models\Buyer\Review;
use App\Models\User;
use App\Services\Media\ImageOptimizationService;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

class ReviewImageService
{
    private const EVENT = 'buyer.review.image.updated';

    public function __construct(private readonly ImageOptimizationService $images) {}

    public function urlsFor(Collection|EloquentCollection $reviews): array
    {
        $reviewIds = $reviews->filter()->pluck('id')->all();
        if ($reviewIds === []) {
            return [];
        }

        return AuditLog::query()
            ->where('event', self::EVENT)
            ->where('auditable_type', Review::class)
            ->whereIn('auditable_id', $reviewIds)
            ->orderByDesc('id')
            ->get(['auditable_id', 'new_values'])
            ->unique('auditable_id')
            ->mapWithKeys(function (AuditLog $entry): array {
                $path = data_get($entry->new_values, 'image_path');

                return $path ? [(int) $entry->auditable_id => Storage::disk('public')->url($path)] : [];
            })
            ->all();
    }

    public function store(Review $review, UploadedFile $image, User $actor, Request $request): void
    {
        $previousUrl = $this->urlsFor(collect([$review]))[$review->id] ?? null;
        $previousPath = $previousUrl ? $this->pathFor($review) : null;
        $path = $this->images->store($image, 'reviews/'.$review->id);
        if (! is_string($path)) {
            throw new RuntimeException('The review image could not be saved.');
        }

        try {
            AuditLog::query()->create([
                'actor_user_id' => $actor->id,
                'event' => self::EVENT,
                'auditable_type' => Review::class,
                'auditable_id' => $review->id,
                'old_values' => $previousPath ? ['image_path' => $previousPath] : null,
                'new_values' => ['image_path' => $path],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now(),
            ]);
        } catch (Throwable $exception) {
            Storage::disk('public')->delete($path);
            throw $exception;
        }

        if ($previousPath && $previousPath !== $path) {
            Storage::disk('public')->delete($previousPath);
        }
    }

    private function pathFor(Review $review): ?string
    {
        $entry = AuditLog::query()
            ->where('event', self::EVENT)
            ->where('auditable_type', Review::class)
            ->where('auditable_id', $review->id)
            ->latest('id')
            ->first();

        return $entry ? data_get($entry->new_values, 'image_path') : null;
    }
}
