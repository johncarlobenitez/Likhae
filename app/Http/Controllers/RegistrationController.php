<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class RegistrationController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        if (is_string($request->input('email'))) {
            $request->merge(['email' => mb_strtolower(trim($request->input('email')))]);
        }
        // The form sends 'account_type'; normalise to 'role'
        if ($request->has('account_type')) {
            $request->merge(['role' => $request->input('account_type')]);
        }

        // Normalise 'rider' → 'courier' (the form uses 'rider', DB uses 'courier')
        if ($request->input('role') === 'rider') {
            $request->merge(['role' => 'courier']);
        }

        $role = $request->input('role');

        // The form sends 'contact_no'; normalise to 'contact_number'
        if (! $request->has('contact_number') && $request->has('contact_no')) {
            $request->merge(['contact_number' => $request->input('contact_no')]);
        }

        $validated = $request->validate([
            'role' => ['required', Rule::in(['buyer', 'seller', 'courier', 'logistics'])],
            'first_name' => ['required', 'string', 'max:80'],
            'last_name' => ['required', 'string', 'max:80'],
            'middle_initial' => ['nullable', 'string', 'max:80'],
            'sex' => ['required', Rule::in(['male', 'female', 'Male', 'Female', 'prefer_not_to_say'])],
            'email' => ['required', 'email', 'max:255', function ($attribute, $value, $fail) {
                if (User::whereRaw('LOWER(email) = ?', [$value])->exists()) {
                    $fail('The email has already been taken.');
                }
            }],
            'contact_number' => ['required', 'string', 'max:30'],
            'birthday' => ['required', 'date', 'before:today'],
            'region' => ['required', 'string', 'max:120'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'landmark' => ['nullable', 'string', 'max:255'],
            'province' => ['required', 'string', 'max:120'],
            'municipality' => ['required', 'string', 'max:120'],
            'barangay' => ['required', 'string', 'max:120'],
            'house_number' => ['nullable', 'string', 'max:120'],
            'street' => ['required', 'string', 'max:255'],
            'valid_id' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'password' => ['required', 'confirmed', 'max:72', Password::min(8)->mixedCase()->numbers()],
            'terms' => ['accepted'],
            'business_name' => [Rule::excludeIf(! in_array($role, ['seller', 'logistics'], true)), 'required', 'string', 'max:255'],
            'store_name' => [Rule::excludeIf($role !== 'seller'), 'nullable', 'string', 'max:255'],
            'line_of_business' => [Rule::excludeIf($role !== 'seller'), 'required', 'string', 'max:255'],
            'business_type' => [Rule::excludeIf(! in_array($role, ['seller', 'logistics'], true)), 'nullable', 'string', 'max:100'],
            'dti_sec_number' => [Rule::excludeIf(! in_array($role, ['seller', 'logistics'], true)), 'nullable', 'string', 'max:100'],
            'tin' => [Rule::excludeIf(! in_array($role, ['seller', 'logistics'], true)), 'nullable', 'string', 'max:100'],
            'business_permit' => [Rule::excludeIf(! in_array($role, ['seller', 'logistics'], true)), 'required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'vehicle_type' => [Rule::excludeIf($role !== 'courier'), 'required', Rule::in(['motorcycle', 'car', 'van', 'truck'])],
            'plate_number' => [Rule::excludeIf($role !== 'courier'), 'required', 'string', 'max:30'],
            'or_cr' => [Rule::excludeIf($role !== 'courier'), 'required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'drivers_license' => [Rule::excludeIf($role !== 'courier'), 'required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ]);

        $uploads = ['valid_id', 'business_permit', 'or_cr', 'drivers_license'];
        $attributes = Arr::except($validated, [...$uploads, 'terms']);
        $paths = [];

        try {
            foreach ($uploads as $field) {
                if (isset($validated[$field])) {
                    $paths[$field.'_path'] = $validated[$field]->store('registration/'.$field, 'registrations');
                }
            }

            DB::transaction(function () use ($attributes, $paths) {
                $user = new User([...$attributes, ...$paths]);
                $user->name = trim($attributes['first_name'].' '.$attributes['last_name']);
                $user->status = 'pending';
                $user->save();
            });
        } catch (\Throwable $exception) {
            Storage::disk('registrations')->delete(array_values($paths));
            throw $exception;
        }

        $accountTypeLabel = match ($validated['role']) {
            'buyer' => 'Buyer',
            'seller' => 'Seller',
            'courier' => 'Rider',
            'logistics' => 'Logistics',
            default => 'LIKHAE',
        };

        return redirect()->route('registration.pending', ['type' => $accountTypeLabel])
            ->with('status', 'Registration submitted successfully.');
    }
}
