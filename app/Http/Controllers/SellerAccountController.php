<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Seller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SellerAccountController extends Controller
{
    public function store(Request $request): View
    {
        $shop = $this->shop($request);
        return view('Seller.store', ['tab' => $request->input('tab', 'profile'), 'seller' => $shop->owner, 'storeProfile' => $this->profile($shop)]);
    }

    public function updateStore(Request $request): RedirectResponse
    {
        $shop = $this->shop($request);
        $data = $request->validate([
            'shop_name' => ['nullable', 'string', 'max:120'], 'tagline' => ['nullable', 'string', 'max:180'],
            'description' => ['nullable', 'string', 'max:2000'], 'location' => ['nullable', 'string', 'max:255'],
            'business_days' => ['nullable', 'string', 'max:80'], 'business_hours' => ['nullable', 'string', 'max:80'],
            'processing_days' => ['nullable', 'integer', 'min:1', 'max:30'], 'order_cutoff' => ['nullable', 'string', 'max:80'],
            'vacation_mode' => ['nullable', 'boolean'], 'auto_accept_orders' => ['nullable', 'boolean'], 'store_visibility' => ['nullable', 'boolean'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'], 'banner' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:8192'],
        ]);
        $settings = $shop->settings ?? [];
        foreach (['tagline','location','business_days','business_hours','processing_days','order_cutoff'] as $key) {
            if (array_key_exists($key, $data)) $settings[$key] = $data[$key];
        }
        foreach (['vacation_mode','auto_accept_orders','store_visibility'] as $key) {
            if (array_key_exists($key, $data)) $settings[$key] = $request->boolean($key);
        }
        $changes = ['settings' => $settings, 'description' => $data['description'] ?? $shop->description];
        if (filled($data['shop_name'] ?? null)) $changes += ['name' => $data['shop_name'], 'slug' => $this->uniqueSlug($data['shop_name'], $shop->id)];
        if ($request->hasFile('avatar')) { $this->deletePublic($shop->logo_path); $changes['logo_path'] = $request->file('avatar')->store("sellers/{$shop->id}/profile", 'public'); }
        if ($request->hasFile('banner')) { $this->deletePublic($shop->banner_path); $changes['banner_path'] = $request->file('banner')->store("sellers/{$shop->id}/profile", 'public'); }
        $shop->update($changes);
        return back()->with('status', 'Store settings saved.');
    }

    public function account(Request $request): View
    {
        $shop = $this->shop($request);
        return view('Seller.account', ['tab' => $request->input('tab', 'profile'), 'seller' => $shop->owner, 'storeProfile' => $this->profile($shop)]);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $owner = $this->shop($request)->owner;
        $data = $request->validate(['first_name' => ['required','string','max:80'], 'last_name' => ['required','string','max:80'], 'email' => ['required','email',Rule::unique('users','email')->ignore($owner->id)], 'contact_number' => ['nullable','string','max:30']]);
        $owner->update($data + ['name' => trim($data['first_name'].' '.$data['last_name'])]);
        return back()->with('status', 'Profile updated.');
    }

    public function updateBusiness(Request $request): RedirectResponse
    {
        $shop = $this->shop($request);
        $data = $request->validate(['business_name'=>['nullable','string','max:160'],'business_type'=>['nullable','string','max:80'],'dti_sec_number'=>['nullable','string','max:80'],'tin'=>['nullable','string','max:80'],'contact_number'=>['nullable','string','max:30'],'province'=>['nullable','string','max:100'],'municipality'=>['nullable','string','max:100'],'barangay'=>['nullable','string','max:100'],'street'=>['nullable','string','max:120'],'house_number'=>['nullable','string','max:40']]);
        $shop->owner->update(collect($data)->only(['contact_number'])->all());
        $settings = $shop->settings ?? [];
        foreach (['business_name','business_type','dti_sec_number','tin'] as $field) {
            $settings[$field] = $data[$field] ?? null;
        }
        $shop->update(['settings' => $settings]);
        $line1 = trim(collect([$data['house_number'] ?? null, $data['street'] ?? null])->filter()->implode(' '));
        $address = $shop->pickupAddress ?: new Address(['user_id' => $shop->user_id, 'label' => 'Seller pickup', 'recipient' => $shop->owner->name, 'phone' => $data['contact_number'] ?? $shop->owner->contact_number, 'postal_code' => '0000']);
        $address->fill(['line1'=>$line1,'barangay'=>$data['barangay'] ?? null,'city'=>$data['municipality'] ?? null,'province'=>$data['province'] ?? null]);
        $address->save();
        if ($shop->pickup_address_id !== $address->id) $shop->update(['pickup_address_id' => $address->id]);
        return back()->with('status', 'Business information updated.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $owner = $this->shop($request)->owner;
        $data = $request->validate(['current_password'=>['required','current_password'],'password'=>['required','string','min:8','confirmed']]);
        $owner->update(['password' => Hash::make($data['password'])]);
        return back()->with('status', 'Password changed successfully.');
    }

    public function updateNotifications(Request $request): RedirectResponse
    {
        $shop = $this->shop($request); $settings = $shop->settings ?? []; $preferences = [];
        foreach (['new_order','order_cancellation','pickup_shipping','inventory_alerts','buyer_messages','finance_payouts','marketing_updates'] as $key) foreach (['email','push','sms'] as $channel) $preferences[$key][$channel] = $request->boolean("preferences.{$key}.{$channel}");
        $settings['notification_preferences'] = $preferences; $shop->update(['settings' => $settings]);
        return back()->with('status', 'Notification preferences saved.');
    }

    private function shop(Request $request): Seller { return $request->user()->sellers()->where('status','approved')->with(['owner','pickupAddress'])->firstOrFail(); }
    private function profile(Seller $shop): object
    {
        $settings = array_replace([
            'tagline' => '', 'location' => '', 'business_days' => '', 'business_hours' => '',
            'processing_days' => 1, 'order_cutoff' => '', 'vacation_mode' => false,
            'auto_accept_orders' => false, 'store_visibility' => true,
        ], $shop->settings ?? []);
        $address = $shop->pickupAddress;
        return (object) array_merge($settings, [
            'shop_name' => $shop->name, 'description' => $shop->description,
            'avatar_path' => $shop->logo_path, 'banner_path' => $shop->banner_path,
            'notification_preferences' => $settings['notification_preferences'] ?? [],
            'province' => $address?->province, 'municipality' => $address?->city,
            'barangay' => $address?->barangay, 'house_number' => '', 'street' => $address?->line1,
        ]);
    }
    private function deletePublic(?string $path): void { if ($path) Storage::disk('public')->delete($path); }
    private function uniqueSlug(string $name, int $ignore): string { $base=Str::slug($name) ?: 'shop'; $slug=$base; $i=2; while(Seller::where('slug',$slug)->whereKeyNot($ignore)->exists()) $slug=$base.'-'.$i++; return $slug; }
}
