<?php

namespace App\Console\Commands;

use App\Models\Admin\AdminAnnouncement;
use Illuminate\Console\Command;

class PublishScheduledAnnouncements extends Command
{
    protected $signature = 'likhae:publish-announcements';
    protected $description = 'Publish scheduled LIKHAE admin announcements that are due.';

    public function handle(): int
    {
        AdminAnnouncement::query()
            ->where('status', 'scheduled')
            ->whereNotNull('publish_at')
            ->where('publish_at', '<=', now())
            ->orderBy('id')
            ->get()
            ->each->publish();

        return self::SUCCESS;
    }
}
