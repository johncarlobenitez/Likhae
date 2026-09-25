<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

use App\Http\Requests\SellerApplicationRequest;
use App\Models\Seller\Seller;
use App\Models\Admin\PlatformSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SellerApplicationController extends Controller
{
    public function create(Request $request): View|RedirectResponse
    {
        $seller = $request->user()->sellers()->latest()->first();
        if ($seller && in_array($seller->status, ['pending', 'approved', 'suspended'], true)) {
            return redirect()->route('seller.entry');
        }

        return view('auth.onboarding.seller-apply', ['addresses' => $request->user()->addresses()->latest()->get(), 'seller' => $seller]);
    }

    public function store(SellerApplicationRequest $request): RedirectResponse
    {
        $existing = $request->user()->sellers()->first();
        abort_if($existing && in_array($existing->status, ['pending', 'approved', 'suspended'], true), 409, 'You already have a shop or pending application.');
        $data = $request->validated();
        $logoPath = $request->file('logo')->store('sellers/logos', 'public');
        $permitPath = $request->file('permit')->store('sellers/permits', 'local');

        try {
            DB::transaction(function () use ($data, $existing, $logoPath, $permitPath, $request): void {
                Seller::updateOrCreate(['user_id' => $request->user()->id], [
                    'name' => $data['name'],
                    'slug' => $existing?->slug ?: Str::slug($data['name']).'-'.Str::lower(Str::random(4)),
                    'description' => $data['description'], 'pickup_address_id' => $data['address_id'],
                    'logo_path' => $logoPath, 'permit_path' => $permitPath,
                    'status' => 'pending', 'rejection_reason' => null, 'approved_by' => null, 'approved_at' => null,
                    'commission_bps' => PlatformSetting::commissionBps(),
                ]);
            });
        } catch (\Throwable $exception) {
            Storage::disk('public')->delete($logoPath);
            Storage::disk('local')->delete($permitPath);
            throw $exception;
        }

        if ($existing?->logo_path) Storage::disk('public')->delete($existing->logo_path);
        if ($existing?->permit_path) Storage::disk('local')->delete($existing->permit_path);

        return redirect()->route('seller.entry')->with('status', 'Your shop application was submitted for review.');
    }

    public function entry(Request $request): View|RedirectResponse
    {
        $seller = $request->user()->sellers()->latest()->first();
        if (! $seller) {
            return view('auth.onboarding.status', ['type' => 'shop', 'record' => null, 'applyRoute' => route('sell.create')]);
        }
        if ($seller->status === 'approved' && $request->user()->hasRole('seller')) {
            return redirect()->route('seller.dashboard');
        }

        return view('auth.onboarding.status', ['type' => 'shop', 'record' => $seller, 'applyRoute' => route('sell.create')]);
    }
}
