<?php

namespace App\Console\Commands;

use App\Models\Admin\Announcement;
use App\Models\Admin\Notification;
use App\Models\User;
use Illuminate\Console\Command;

class PublishScheduledAnnouncements extends Command
{
    protected $signature = 'likhae:publish-announcements';

    protected $description = 'Publish active announcements to users when their publish time arrives.';

    public function handle(): int
    {
        $announcements = Announcement::query()
            ->where('is_active', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->where(function ($query): void {
                $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->get();

        $count = 0;

        foreach ($announcements as $announcement) {
            User::query()->where('status', 'ACTIVE')->chunkById(100, function ($users) use ($announcement, &$count): void {
                foreach ($users as $user) {
                    Notification::firstOrCreate(
                        [
                            'user_id' => $user->id,
                            'type' => 'ANNOUNCEMENT',
                            'reference_type' => Announcement::class,
                            'reference_id' => $announcement->id,
                        ],
                        [
                            'title' => $announcement->title,
                            'message' => str($announcement->body)->limit(250)->toString(),
                            'action_url' => route('home'),
                        ],
                    );
                    $count++;
                }
            });
        }

        $this->info("Announcement notifications checked: {$count}");

        return self::SUCCESS;
    }
}
