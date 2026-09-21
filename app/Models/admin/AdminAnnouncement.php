<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AdminAnnouncement extends Model
{
    protected $fillable = ['title', 'body', 'audience', 'priority', 'status', 'publish_at', 'published_at', 'created_by'];

    protected function casts(): array
    {
        return ['publish_at' => 'datetime', 'published_at' => 'datetime'];
    }

    public function publish(): void
    {
        if ($this->published_at) {
            return;
        }

        DB::transaction(function (): void {
            $roles = match ($this->audience) {
                'buyers' => ['buyer'],
                'sellers' => ['seller'],
                'logistics' => ['logistics'],
                'riders' => ['rider', 'courier'],
                default => User::MANAGED_ROLES,
            };

            User::query()
                ->anyRole($roles)
                ->where('status', 'active')
                ->select('id')
                ->chunkById(500, function ($users): void {
                    $now = now();
                    WorkspaceNotification::insert($users->map(fn (User $user) => [
                        'user_id' => $user->id,
                        'type' => $this->priority === 'urgent' ? 'risk' : 'system',
                        'title' => $this->title,
                        'body' => $this->body,
                        'action_url' => null,
                        'read_at' => null,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ])->all());
                });

            $this->forceFill(['status' => 'published', 'published_at' => now()])->save();
        });
    }
}
