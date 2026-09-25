<?php

namespace App\Http\Controllers\Logistics;

use App\Http\Controllers\Controller;

use App\Models\Rider\Rider;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProviderRiderController extends Controller
{
    public function index(Request $request): View
    {
        $provider = $request->user()->logisticsProvider()->firstOrFail();

        return view('auth.onboarding.riders', ['provider' => $provider, 'riders' => $provider->riders()->with('user')->latest()->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $provider = $request->user()->logisticsProvider()->firstOrFail();
        $data = $request->validate(['name' => ['required', 'string', 'max:120'], 'email' => ['required', 'email', 'max:255', 'unique:users,email'], 'phone' => ['required', 'string', 'max:40'], 'vehicle_type' => ['required', Rule::in(['motorcycle', 'car', 'van', 'truck'])], 'plate_no' => ['required', 'string', 'max:30']]);
        $password = Str::password(12);
        DB::transaction(function () use ($data, $provider, $password) {
            $user = User::create(['name' => $data['name'], 'email' => strtolower($data['email']), 'contact_number' => $data['phone'], 'password' => $password, 'email_verified_at' => now(), 'status' => 'active']);
            $user->grant('rider');
            Rider::create(['user_id' => $user->id, 'logistics_provider_id' => $provider->id, 'vehicle_type' => $data['vehicle_type'], 'plate_no' => $data['plate_no'], 'is_active' => true]);
        });

        return back()->with('rider_password', $password)->with('success', 'Rider account created. Copy the temporary password now.');
    }

    public function update(Request $request, Rider $rider): RedirectResponse
    {
        Gate::authorize('update', $rider);
        $data = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:120'],
            'email' => ['sometimes', 'required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($rider->user_id)],
            'phone' => ['sometimes', 'required', 'string', 'max:40'],
            'vehicle_type' => ['sometimes', 'required', Rule::in(['motorcycle', 'car', 'van', 'truck'])],
            'plate_no' => ['sometimes', 'required', 'string', 'max:30'],
            'is_active' => ['sometimes', 'required', 'boolean'],
        ]);

        DB::transaction(function () use ($rider, $data): void {
            $rider->user?->update(array_filter([
                'name' => $data['name'] ?? null,
                'email' => isset($data['email']) ? strtolower($data['email']) : null,
                'contact_number' => $data['phone'] ?? null,
            ], static fn ($value) => $value !== null));
            $rider->update(array_filter([
                'vehicle_type' => $data['vehicle_type'] ?? null,
                'plate_no' => $data['plate_no'] ?? null,
                'is_active' => $data['is_active'] ?? null,
            ], static fn ($value) => $value !== null));
        });

        return back()->with('success', $rider->is_active ? 'Rider reactivated.' : 'Rider deactivated.');
    }

    public function edit(Request $request, Rider $rider): View
    {
        Gate::authorize('view', $rider);

        return view('auth.onboarding.riders', [
            'provider' => $request->user()->logisticsProvider()->firstOrFail(),
            'riders' => $request->user()->logisticsProvider()->firstOrFail()->riders()->with('user')->latest()->get(),
            'editingRider' => $rider,
        ]);
    }

    public function activate(Request $request, Rider $rider): RedirectResponse
    {
        Gate::authorize('update', $rider);
        $rider->update(['is_active' => true]);

        return back()->with('success', 'Rider reactivated.');
    }

    public function deactivate(Request $request, Rider $rider): RedirectResponse
    {
        Gate::authorize('update', $rider);
        $rider->update(['is_active' => false]);

        return back()->with('success', 'Rider deactivated.');
    }
}
