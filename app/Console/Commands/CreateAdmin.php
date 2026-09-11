<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class CreateAdmin extends Command
{
    protected $signature = 'app:create-admin';

    protected $description = 'Create an active administrator with a password entered privately';

    public function handle(): int
    {
        $name = $this->ask('Administrator name');
        $email = mb_strtolower(trim($this->ask('Administrator email') ?? ''));
        $password = $this->secret('Password (8-72 characters, uppercase, lowercase and a number)');
        $confirmation = $this->secret('Confirm password');

        $validator = Validator::make([
            'name' => $name, 'email' => $email, 'password' => $password, 'password_confirmation' => $confirmation,
        ], [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', function ($attribute, $value, $fail) {
                if (User::whereRaw('LOWER(email) = ?', [$value])->exists()) {
                    $fail('That email is already registered. Existing accounts are not modified.');
                }
            }],
            'password' => ['required', 'confirmed', 'max:72', Password::min(8)->mixedCase()->numbers()],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        $user = new User(['name' => $name, 'email' => $email, 'password' => $password, 'role' => 'admin']);
        $user->status = 'active';
        $user->save();
        $this->info('Administrator created. Sign in at '.route('login').' to review registrations.');

        return self::SUCCESS;
    }
}
