<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Buyer\Address;
use App\Models\Seller\SellerProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SellerAccountController extends Controller
{
    public function store(Request $request): View
    {
        $shop = $this->shop($request);

        return view('Seller.store', [
            'tab' => $request->input('tab', 'profile'),
            'seller' => $shop->user,
            'storeProfile' => $this->profile($shop),
        ]);
    }

    public function updateStore(Request $request): RedirectResponse
    {
        $shop = $this->shop($request);

        $data = $request->validate([
            'shop_name' => ['nullable', 'string', 'max:200'],
            'tagline' => ['nullable', 'string', 'max:180'],
            'description' => ['nullable', 'string', 'max:2000'],
            'location' => ['nullable', 'string', 'max:255'],
            'business_days' => ['nullable', 'string', 'max:80'],
            'business_hours' => ['nullable', 'string', 'max:80'],
            'processing_days' => ['nullable', 'integer', 'min:1', 'max:30'],
            'order_cutoff' => ['nullable', 'string', 'max:80'],
            'vacation_mode' => ['nullable', 'boolean'],
            'auto_accept_orders' => ['nullable', 'boolean'],
            'store_visibility' => ['nullable', 'boolean'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'banner' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:8192'],
        ]);

        if (filled($data['shop_name'] ?? null)) {
            $shop->update(['business_name' => $data['shop_name']]);
        }

        return back()->with('status', 'Store profile saved. Extended branding fields are display-only until a future schema revision adds storefront branding columns.');
    }

    public function account(Request $request): View
    {
        $shop = $this->shop($request);

        return view('Seller.account', [
            'tab' => $request->input('tab', 'profile'),
            'seller' => $shop->user,
            'storeProfile' => $this->profile($shop),
        ]);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $owner = $this->shop($request)->user;

        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($owner->id)],
            'contact_number' => ['nullable', 'string', 'max:30', Rule::unique('users', 'contact_number')->ignore($owner->id)],
        ]);

        $owner->update($data);

        return back()->with('status', 'Profile updated.');
    }

    public function updateBusiness(Request $request): RedirectResponse
    {
        $shop = $this->shop($request);

        $data = $request->validate([
            'business_name' => ['nullable', 'string', 'max:200'],
            'business_type' => ['nullable', 'string', 'max:80'],
            'dti_sec_number' => ['nullable', 'string', 'max:100'],
            'tin' => ['nullable', 'string', 'max:80'],
            'contact_number' => ['nullable', 'string', 'max:30'],
            'province' => ['nullable', 'string', 'max:150'],
            'municipality' => ['nullable', 'string', 'max:150'],
            'barangay' => ['nullable', 'string', 'max:150'],
            'street' => ['nullable', 'string', 'max:255'],
            'house_number' => ['nullable', 'string', 'max:100'],
        ]);

        if (filled($data['contact_number'] ?? null)) {
            $shop->user->update(['contact_number' => $data['contact_number']]);
        }

        $profileChanges = [];

        if (filled($data['business_name'] ?? null)) {
            $profileChanges['business_name'] = $data['business_name'];
        }

        if (filled($data['dti_sec_number'] ?? null)) {
            $profileChanges['business_registration_number'] = $data['dti_sec_number'];
        }

        $addressInputPresent = collect([
            $data['province'] ?? null,
            $data['municipality'] ?? null,
            $data['barangay'] ?? null,
            $data['street'] ?? null,
            $data['house_number'] ?? null,
        ])->contains(fn ($value): bool => filled($value));

        if ($addressInputPresent) {
            $address = $shop->businessAddress ?: new Address([
                'user_id' => $shop->user_id,
                'label' => 'Seller business',
                'recipient_name' => $shop->business_name,
                'contact_number' => $data['contact_number'] ?? $shop->user->contact_number,
                'is_default' => false,
            ]);

            $province = $data['province'] ?? $address->province_name ?? 'N/A';
            $municipality = $data['municipality'] ?? $address->municipality_name ?? 'N/A';
            $barangay = $data['barangay'] ?? $address->barangay_name ?? 'N/A';

            $address->fill([
                'recipient_name' => $shop->business_name,
                'contact_number' => $data['contact_number'] ?? $shop->user->contact_number,
                'province_code' => $address->province_code ?: $this->stableCode($province),
                'province_name' => $province,
                'municipality_code' => $address->municipality_code ?: $this->stableCode($municipality),
                'municipality_name' => $municipality,
                'barangay_code' => $address->barangay_code ?: $this->stableCode($barangay),
                'barangay_name' => $barangay,
                'postal_code' => $address->postal_code,
                'house_number' => $data['house_number'] ?? $address->house_number,
                'street_address' => $data['street'] ?? $address->street_address ?? 'N/A',
                'landmark' => $address->landmark,
            ]);

            $address->save();
            $profileChanges['business_address_id'] = $address->id;
        }

        if ($profileChanges !== []) {
            $shop->update($profileChanges);
        }

        return back()->with('status', 'Business information updated.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $owner = $this->shop($request)->user;

        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $owner->update(['password' => Hash::make($request->string('password')->toString())]);

        return back()->with('status', 'Password changed successfully.');
    }

    public function updateNotifications(Request $request): RedirectResponse
    {
        $data = $request->validate(['preferences' => ['nullable', 'array']]);
        $allowedKeys = [
            'new_order', 'order_cancellation', 'pickup_shipping', 'inventory_alerts',
            'buyer_messages', 'finance_payouts', 'marketing_updates',
        ];
        $channels = ['email', 'push', 'sms'];
        $input = $data['preferences'] ?? [];
        $preferences = [];

        foreach ($allowedKeys as $key) {
            foreach ($channels as $channel) {
                $preferences[$key][$channel] = filter_var(
                    data_get($input, "$key.$channel", false),
                    FILTER_VALIDATE_BOOLEAN,
                );
            }
        }

        $this->shop($request)->user->forceFill(['notification_preferences' => $preferences])->save();

        return back()->with('status', 'Notification preferences saved.');
    }

    private function shop(Request $request): SellerProfile
    {
        return $request->user()
            ->sellerProfile()
            ->with(['user', 'businessAddress', 'primaryCategory'])
            ->where('status', 'ACTIVE')
            ->firstOrFail();
    }

    private function profile(SellerProfile $shop): object
    {
        $address = $shop->businessAddress;

        return (object) [
            'shop_name' => $shop->business_name,
            'description' => 'Seller profile managed through the final LIKHAE seller_profiles table.',
            'avatar_path' => null,
            'banner_path' => null,
            'tagline' => '',
            'location' => $address?->formatted() ?? '',
            'business_days' => '',
            'business_hours' => '',
            'processing_days' => 1,
            'order_cutoff' => '',
            'vacation_mode' => false,
            'auto_accept_orders' => false,
            'store_visibility' => true,
            'notification_preferences' => $shop->user?->notification_preferences ?? [],
            'business_name' => $shop->business_name,
            'business_type' => $shop->primaryCategory?->name ?? '',
            'dti_sec_number' => $shop->business_registration_number,
            'tin' => '',
            'province' => $address?->province_name,
            'municipality' => $address?->municipality_name,
            'barangay' => $address?->barangay_name,
            'house_number' => $address?->house_number,
            'street' => $address?->street_address,
        ];
    }

    private function stableCode(string $value): string
    {
        $clean = trim($value) !== '' ? trim($value) : 'N/A';

        return strtoupper(substr(preg_replace('/[^A-Za-z0-9]+/', '-', $clean) ?: 'NA', 0, 50));
    }
}
