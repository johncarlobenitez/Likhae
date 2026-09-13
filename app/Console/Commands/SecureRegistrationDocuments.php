<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class SecureRegistrationDocuments extends Command
{
    protected $signature = 'app:secure-registration-documents';

    protected $description = 'Move existing registration documents from public to private storage';

    public function handle(): int
    {
        $public = Storage::disk('public');
        $private = Storage::disk('registrations');
        $moved = 0;
        foreach (User::cursor() as $user) {
            foreach (['valid_id_path', 'business_permit_path', 'or_cr_path', 'drivers_license_path'] as $field) {
                $path = $user->getAttribute($field);
                if (! $path || ! str_starts_with($path, 'registration/') || str_contains($path, '..') || ! $public->exists($path)) {
                    continue;
                }
                $contents = $public->get($path);
                if (! $private->exists($path)) {
                    $private->put($path, $contents);
                }
                if (! hash_equals(hash('sha256', $contents), hash('sha256', $private->get($path)))) {
                    $this->error('A private document conflicts with its public copy. No public copy was removed for this document.');

                    return self::FAILURE;
                }
                if (! $public->delete($path)) {
                    $this->error('Could not remove a public copy. Run this command again after checking storage permissions.');

                    return self::FAILURE;
                }
                $moved++;
            }
        }
        $this->info("Moved {$moved} registration document(s) to private storage.");

        return self::SUCCESS;
    }
}
