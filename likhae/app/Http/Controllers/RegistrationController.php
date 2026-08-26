<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class RegistrationController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $role = $request->input('role');

        $validated = $request->validate([
            'role' => ['required', Rule::in(['buyer', 'seller', 'courier'])],
            'first_name' => ['required', 'string', 'max:80'],
            'last_name' => ['required', 'string', 'max:80'],
            'middle_initial' => ['nullable', 'string', 'max:2'],
            'sex' => ['required', Rule::in(['male', 'female', 'prefer_not_to_say'])],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'contact_number' => ['required', 'string', 'max:30'],
            'birthday' => ['required', 'date', 'before:today'],
            'province' => ['required', 'string', 'max:120'],
            'municipality' => ['required', 'string', 'max:120'],
            'barangay' => ['required', 'string', 'max:120'],
            'house_number' => ['required', 'string', 'max:120'],
            'street' => ['required', 'string', 'max:255'],
            'valid_id' => [Rule::requiredIf(in_array($role, ['buyer', 'seller'], true)), 'nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
            'terms' => ['accepted'],
            'business_name' => [Rule::requiredIf($role === 'seller'), 'nullable', 'string', 'max:255'],
            'store_name' => [Rule::requiredIf($role === 'seller'), 'nullable', 'string', 'max:255'],
            'line_of_business' => [Rule::requiredIf($role === 'seller'), 'nullable', 'string', 'max:255'],
            'business_type' => ['nullable', 'string', 'max:100'],
            'dti_sec_number' => ['nullable', 'string', 'max:100'],
            'tin' => ['nullable', 'string', 'max:100'],
            'business_permit' => [Rule::requiredIf($role === 'seller'), 'nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'vehicle_type' => [Rule::requiredIf($role === 'courier'), 'nullable', Rule::in(['motorcycle', 'car', 'van', 'truck'])],
            'plate_number' => [Rule::requiredIf($role === 'courier'), 'nullable', 'string', 'max:30'],
            'or_cr' => [Rule::requiredIf($role === 'courier'), 'nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'drivers_license' => [Rule::requiredIf($role === 'courier'), 'nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ]);

        $validIdPath = $request->hasFile('valid_id')
            ? $request->file('valid_id')->store('registration/valid-ids', 'public')
            : null;
        $businessPermitPath = $request->hasFile('business_permit')
            ? $request->file('business_permit')->store('registration/business-permits', 'public')
            : null;
        $orCrPath = $request->hasFile('or_cr')
            ? $request->file('or_cr')->store('registration/or-cr', 'public')
            : null;
        $driversLicensePath = $request->hasFile('drivers_license')
            ? $request->file('drivers_license')->store('registration/drivers-licenses', 'public')
            : null;

        User::create([
            'name' => trim($validated['first_name'].' '.$validated['last_name']),
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'middle_initial' => $validated['middle_initial'] ?? null,
            'sex' => $validated['sex'],
            'birthday' => $validated['birthday'],
            'contact_number' => $validated['contact_number'],
            'province' => $validated['province'],
            'municipality' => $validated['municipality'],
            'barangay' => $validated['barangay'],
            'house_number' => $validated['house_number'],
            'street' => $validated['street'],
            'valid_id_path' => $validIdPath,
            'business_name' => $validated['business_name'] ?? null,
            'store_name' => $validated['store_name'] ?? null,
            'line_of_business' => $validated['line_of_business'] ?? null,
            'business_type' => $validated['business_type'] ?? null,
            'dti_sec_number' => $validated['dti_sec_number'] ?? null,
            'tin' => $validated['tin'] ?? null,
            'business_permit_path' => $businessPermitPath,
            'vehicle_type' => $validated['vehicle_type'] ?? null,
            'plate_number' => $validated['plate_number'] ?? null,
            'or_cr_path' => $orCrPath,
            'drivers_license_path' => $driversLicensePath,
        ]);

        $redirectRoute = match ($validated['role']) {
            'buyer' => 'buyer.pending',
            'seller' => 'seller.application-status',
            'courier' => 'courier.application-status',
        };

        return redirect()->route($redirectRoute)
            ->with('status', 'Registration submitted successfully.');
    }
}
