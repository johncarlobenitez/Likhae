<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

use App\Models\Buyer\Address;
use App\Models\Admin\PlatformSetting;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class RegistrationController extends Controller
{
    public function create(Request $request): View
    {
        abort_unless((bool) PlatformSetting::valueOf('registration_enabled', true), 403, 'New registrations are temporarily disabled.');
        return view('auth.register', ['preselectedRole' => 'buyer', 'googleBuyerRegistration' => $request->session()->get('google_buyer_registration')]);
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless((bool) PlatformSetting::valueOf('registration_enabled', true), 403, 'New registrations are temporarily disabled.');
        $request->merge(['email' => mb_strtolower(trim((string) $request->input('email')))]);
        $google = $request->session()->get('google_buyer_registration');
        if (is_array($google)) $request->merge(['email' => $google['email'] ?? null]);

        $data = $request->validate([
            'first_name'=>['required','string','max:80'], 'last_name'=>['required','string','max:80'],
            'middle_initial'=>['nullable','string','max:10'], 'sex'=>['required','in:male,female,Male,Female,prefer_not_to_say'],
            'email'=>['required','email','max:255','unique:users,email'], 'contact_number'=>['required','string','max:20','regex:/^(?:\+63|0)9\d{9}$/'],
            'birthday'=>['required','date','before:today'], 'region'=>['required','string','max:120'], 'region_code'=>['required','string','max:20'],
            'province'=>['required','string','max:120'], 'province_code'=>['required','string','max:20'],
            'municipality'=>['required','string','max:120'], 'municipality_code'=>['required','string','max:20'],
            'barangay'=>['required','string','max:120'], 'barangay_code'=>['required','string','max:20'],
            'house_number'=>['nullable','string','max:120'], 'street'=>['required','string','max:255'],
            'postal_code'=>['required','string','max:20'], 'landmark'=>['nullable','string','max:255'],
            'valid_id'=>['required','file','mimes:jpg,jpeg,png,pdf','max:5120'],
            'password'=>['required','confirmed','max:72',Password::min(8)->mixedCase()->numbers()], 'terms'=>['accepted'],
        ]);

        abort_unless(PhilippineAddressController::selectionIsValid((string)$data['region_code'],(string)$data['region'],(string)$data['province_code'],(string)$data['province'],(string)$data['municipality_code'],(string)$data['municipality'],(string)$data['barangay_code'],(string)$data['barangay']), 422, 'The selected Philippine address is invalid.');
        $expected = PhilippineAddressController::expectedPostalCodeFor((string)$data['province_code'],(string)$data['municipality_code'],(string)$data['province'],(string)$data['municipality']);
        if ($expected !== null && $data['postal_code'] !== $expected) return back()->withErrors(['postal_code'=>'The postal code does not match the selected Philippine address.'])->withInput();

        $idPath = $request->file('valid_id')->store('registration/identity', 'registrations');
        try {
            $user = DB::transaction(function () use ($data,$google,$idPath) {
                $user=User::create(['name'=>trim($data['first_name'].' '.$data['last_name']),'first_name'=>$data['first_name'],'last_name'=>$data['last_name'],'middle_initial'=>$data['middle_initial']??null,'sex'=>strtolower($data['sex']),'birthday'=>$data['birthday'],'contact_number'=>$data['contact_number'],'email'=>$data['email'],'password'=>$data['password'],'valid_id_path'=>$idPath,'status'=>'active','google_id'=>$google['id']??null,'google_avatar_url'=>$google['avatar']??null,'email_verified_at'=>is_array($google)?now():null]);
                $user->grant('buyer');
                Address::create(['user_id'=>$user->id,'label'=>'Home','recipient'=>$user->name,'phone'=>$data['contact_number'],'line1'=>trim(collect([$data['house_number']??null,$data['street']])->filter()->implode(' ')),'region'=>$data['region'],'region_code'=>$data['region_code'],'province'=>$data['province'],'province_code'=>$data['province_code'],'city'=>$data['municipality'],'city_code'=>$data['municipality_code'],'barangay'=>$data['barangay'],'barangay_code'=>$data['barangay_code'],'postal_code'=>$data['postal_code'],'landmark'=>$data['landmark']??null,'is_default'=>true]);
                return $user;
            });
        } catch (\Throwable $exception) { Storage::disk('registrations')->delete($idPath); throw $exception; }
        if (!$user->hasVerifiedEmail()) $user->sendEmailVerificationNotification();
        Auth::login($user); $request->session()->forget('google_buyer_registration');
        return $user->hasVerifiedEmail()?redirect()->route('buyer.home'):redirect()->route('verification.notice')->with('status','verification-link-sent');
    }
}
