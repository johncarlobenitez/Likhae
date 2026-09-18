<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Category;
use App\Models\SellerProfile;
use App\Models\PlatformSetting;
use App\Models\WorkspaceNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class RegistrationController extends Controller
{

    public function create(Request $request): View
    {
        abort_unless((bool) PlatformSetting::valueOf('registration_enabled', true), 403, 'New registrations are temporarily disabled.');

        $role = (string) $request->query('role', 'buyer');
        if ($role === 'courier') {
            $role = 'rider';
        }
        if (! in_array($role, ['buyer', 'seller', 'logistics', 'rider'], true)) {
            $role = 'buyer';
        }

        return view('auth.register', [
            'preselectedRole' => $role,
            'googleBuyerRegistration' => $request->session()->get('google_buyer_registration'),
            'sellerLineOfBusinessCategories' => Category::whereNull('parent_id')
                ->where('status', 'active')
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless((bool) PlatformSetting::valueOf('registration_enabled', true), 403, 'New registrations are temporarily disabled.');
        if (is_string($request->input('email'))) {
            $request->merge(['email' => mb_strtolower(trim($request->input('email')))]);
        }
        // The form sends 'account_type'; normalise to 'role'
        if ($request->has('account_type')) {
            $request->merge(['role' => $request->input('account_type')]);
        }

        $googleBuyerRegistration = $request->session()->get('google_buyer_registration');
        if (is_array($googleBuyerRegistration)) {
            $request->merge([
                'role' => 'buyer',
                'account_type' => 'buyer',
                'email' => $googleBuyerRegistration['email'] ?? null,
            ]);
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

        $addressFields = ['region', 'province', 'municipality', 'barangay'];
        $legacyCodedAddress = collect($addressFields)->every(function (string $field) use ($request): bool {
            return ! $request->filled($field.'_code')
                && preg_match('/^\d{9,10}$/', (string) $request->input($field)) === 1;
        });

        if ($legacyCodedAddress) {
            $request->merge(collect($addressFields)
                ->mapWithKeys(fn (string $field) => [$field.'_code' => $request->input($field)])
                ->all());
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
            'contact_number' => ['required', 'string', 'max:20', 'regex:/^(?:\+63|0)9\d{9}$/'],
            'birthday' => ['required', 'date', 'before:today'],
            'region' => ['required', 'string', 'max:120'],
            'region_code' => ['required', 'string', 'max:20'],
            'postal_code' => ['required', 'string', 'max:20'],
            'landmark' => ['nullable', 'string', 'max:255'],
            'province' => ['required', 'string', 'max:120'],
            'province_code' => ['required', 'string', 'max:20'],
            'municipality' => ['required', 'string', 'max:120'],
            'municipality_code' => ['required', 'string', 'max:20'],
            'barangay' => ['required', 'string', 'max:120'],
            'barangay_code' => ['required', 'string', 'max:20'],
            'house_number' => ['nullable', 'string', 'max:120'],
            'street' => ['required', 'string', 'max:255'],
            'valid_id' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'password' => ['required', 'confirmed', 'max:72', Password::min(8)->mixedCase()->numbers()],
            'terms' => ['accepted'],
            'business_name' => [Rule::excludeIf(! in_array($role, ['seller', 'logistics'], true)), 'required', 'string', 'max:255'],
            'store_name' => [Rule::excludeIf($role !== 'seller'), 'nullable', 'string', 'max:255'],
            'line_of_business' => [Rule::excludeIf($role !== 'seller'), 'required', function (string $attribute, mixed $value, \Closure $fail): void {
                if (is_numeric($value)) {
                    $exists = Category::whereKey((int) $value)->whereNull('parent_id')->where('status', 'active')->exists();
                    if (! $exists) $fail('The selected line of business is invalid.');
                    return;
                }

                if (! is_string($value) || trim($value) === '' || mb_strlen($value) > 255) {
                    $fail('The line of business field is invalid.');
                }
            }],
            'business_type' => [Rule::excludeIf(! in_array($role, ['seller', 'logistics'], true)), 'nullable', 'string', 'max:100'],
            'dti_sec_number' => [Rule::excludeIf(! in_array($role, ['seller', 'logistics'], true)), 'nullable', 'string', 'max:100'],
            'tin' => [Rule::excludeIf(! in_array($role, ['seller', 'logistics'], true)), 'nullable', 'string', 'max:100'],
            'business_permit' => [Rule::excludeIf(! in_array($role, ['seller', 'logistics'], true)), 'required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'vehicle_type' => [Rule::excludeIf($role !== 'courier'), 'required', Rule::in(['motorcycle', 'car', 'van', 'truck'])],
            'plate_number' => [Rule::excludeIf($role !== 'courier'), 'required', 'string', 'max:30'],
            'or_cr' => [Rule::excludeIf($role !== 'courier'), 'required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'drivers_license' => [Rule::excludeIf($role !== 'courier'), 'required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ]);

        if (! $legacyCodedAddress && ! PhilippineAddressController::selectionIsValid(
            regionCode: (string) $validated['region_code'],
            regionName: (string) $validated['region'],
            provinceCode: (string) $validated['province_code'],
            provinceName: (string) $validated['province'],
            municipalityCode: (string) $validated['municipality_code'],
            municipalityName: (string) $validated['municipality'],
            barangayCode: (string) $validated['barangay_code'],
            barangayName: (string) $validated['barangay'],
        )) {
            return back()->withErrors(['province' => 'The selected Philippine address could not be verified. Please reselect your address and try again.'])->withInput();
        }

        $expectedPostalCode = $legacyCodedAddress ? null : PhilippineAddressController::expectedPostalCodeFor(
            province: (string) $validated['province_code'],
            municipality: (string) $validated['municipality_code'],
            provinceName: (string) $validated['province'],
            municipalityName: (string) $validated['municipality']
        );

        if ($expectedPostalCode !== null && $validated['postal_code'] !== $expectedPostalCode) {
            return back()
                ->withErrors(['postal_code' => 'The postal code does not match the selected Philippine address.'])
                ->withInput();
        }

        $uploads = ['valid_id', 'business_permit', 'or_cr', 'drivers_license'];
        $attributes = Arr::except($validated, [
            ...$uploads,
            'terms',
            'region_code',
            'province_code',
            'municipality_code',
            'barangay_code',
        ]);
        if (is_array($googleBuyerRegistration)) {
            $attributes['google_id'] = $googleBuyerRegistration['id'] ?? null;
            $attributes['google_avatar_url'] = $googleBuyerRegistration['avatar'] ?? null;
            $attributes['email_verified_at'] = now();
        }
        $paths = [];
        $lineOfBusinessCategoryId = isset($attributes['line_of_business']) && is_numeric($attributes['line_of_business'])
            ? (int) $attributes['line_of_business']
            : null;
        $lineOfBusinessName = $lineOfBusinessCategoryId
            ? Category::whereKey($lineOfBusinessCategoryId)->value('name')
            : null;

        if ($lineOfBusinessName) {
            $attributes['line_of_business'] = $lineOfBusinessName;
        }

        try {
            foreach ($uploads as $field) {
                if (isset($validated[$field])) {
                    $paths[$field.'_path'] = $validated[$field]->store('registration/'.$field, 'registrations');
                }
            }

            DB::transaction(function () use ($attributes, $paths, $lineOfBusinessCategoryId) {
                $user = new User([...$attributes, ...$paths]);
                $user->name = trim($attributes['first_name'].' '.$attributes['last_name']);
                $user->status = 'pending';
                $user->save();

                if ($user->role === 'seller' && $lineOfBusinessCategoryId) {
                    SellerProfile::updateOrCreate(
                        ['seller_id' => $user->id],
                        [
                            'line_of_business_category_id' => $lineOfBusinessCategoryId,
                            'shop_name' => $user->store_name ?: $user->business_name,
                            'location' => collect([$user->municipality, $user->province])->filter()->implode(', '),
                        ]
                    );
                }

                User::query()
                    ->where('role', 'admin')
                    ->where('status', 'active')
                    ->pluck('id')
                    ->each(fn (int $adminId) => WorkspaceNotification::create([
                        'user_id' => $adminId,
                        'type' => 'operations',
                        'title' => 'New registration application',
                        'body' => $user->name.' submitted a '.str($user->role)->headline().' registration.',
                        'action_url' => route('admin.registrations'),
                    ]));
            });
        } catch (\Throwable $exception) {
            Storage::disk('registrations')->delete(array_values($paths));
            throw $exception;
        }

        $request->session()->forget('google_buyer_registration');

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
