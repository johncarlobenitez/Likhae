<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRegistrationRequest;
use App\Models\Admin\PlatformSetting;
use App\Models\Logistics\LogisticsCenter;
use App\Models\Seller\Category;
use App\Services\RegistrationWorkflowService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RegistrationController extends Controller
{
    public function __construct(
        private readonly RegistrationWorkflowService $workflow,
    ) {
    }

    public function create(Request $request): View
    {
        abort_unless(
            (bool) PlatformSetting::valueOf('registration_enabled', true),
            403,
            'New registrations are temporarily disabled.',
        );

        $requested = strtolower((string) $request->query('role', 'buyer'));
        $preselectedRole = in_array($requested, ['buyer', 'seller', 'logistics', 'rider'], true)
            ? $requested
            : 'buyer';

        return view('auth.register', [
            'preselectedRole' => $preselectedRole,
            'googleBuyerRegistration' => $request->session()->get('google_buyer_registration'),
            'sellerLineOfBusinessCategories' => Category::query()
                ->where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name']),
            'logisticsCenters' => LogisticsCenter::query()
                ->where('status', 'ACTIVE')
                ->orderBy('business_name')
                ->get(['id', 'code', 'business_name']),
        ]);
    }

    public function store(StoreRegistrationRequest $request): RedirectResponse
    {
        abort_unless(
            (bool) PlatformSetting::valueOf('registration_enabled', true),
            403,
            'New registrations are temporarily disabled.',
        );

        $data = $request->validated();

        abort_unless(
            PhilippineAddressController::selectionIsValid(
                (string) $data['region_code'],
                (string) $data['region'],
                (string) $data['province_code'],
                (string) $data['province'],
                (string) $data['municipality_code'],
                (string) $data['municipality'],
                (string) $data['barangay_code'],
                (string) $data['barangay'],
            ),
            422,
            'The selected Philippine address is invalid.',
        );

        $expectedPostalCode = PhilippineAddressController::expectedPostalCodeFor(
            (string) $data['province_code'],
            (string) $data['municipality_code'],
            (string) $data['province'],
            (string) $data['municipality'],
        );

        if ($expectedPostalCode !== null && filled($data['postal_code'] ?? null) && (string) $data['postal_code'] !== $expectedPostalCode) {
            return back()
                ->withErrors(['postal_code' => 'The postal code does not match the selected Philippine address.'])
                ->withInput();
        }

        $application = $this->workflow->submit($request);
        $request->session()->forget('google_buyer_registration');

        $type = strtolower((string) $application->user->account_type);
        $reviewer = $type === 'rider' ? 'the selected logistics center' : 'the LIKHAE administrator';
        $loginRoute = in_array($type, ['logistics', 'rider'], true) ? 'logistics.login' : 'login';

        return redirect()
            ->route($loginRoute)
            ->with(
                'status',
                'Registration submitted successfully. Application '.$application->application_number.' is pending review by '.$reviewer.'.',
            );
    }
}
