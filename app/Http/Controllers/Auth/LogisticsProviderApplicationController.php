<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

use App\Http\Requests\LogisticsProviderApplicationRequest;
use App\Models\Logistics\LogisticsProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class LogisticsProviderApplicationController extends Controller
{
    public function create(Request $request): View|RedirectResponse
    {
        $provider = $request->user()->logisticsProvider;
        if ($provider && in_array($provider->status, ['pending', 'approved', 'suspended'], true)) {
            return redirect()->route('partner.status');
        }

        return view('auth.onboarding.partner-apply', compact('provider'));
    }

    public function store(LogisticsProviderApplicationRequest $request): RedirectResponse
    {
        $existing = $request->user()->logisticsProvider;
        abort_if($existing && in_array($existing->status, ['pending', 'approved', 'suspended'], true), 409, 'You already have a courier company or pending application.');
        $data = $request->validated();
        $documentPath = $request->file('document')->store('providers/documents', 'local');

        try {
            DB::transaction(function () use ($data, $existing, $documentPath, $request): void {
                LogisticsProvider::updateOrCreate(['user_id' => $request->user()->id], [
                    'name' => $data['name'],
                    'slug' => $existing?->slug ?: Str::slug($data['name']).'-'.Str::lower(Str::random(4)),
                    'contact_phone' => $data['contact_phone'], 'document_path' => $documentPath,
                    'status' => 'pending', 'rejection_reason' => null, 'approved_by' => null, 'approved_at' => null,
                ]);
            });
        } catch (\Throwable $exception) {
            Storage::disk('local')->delete($documentPath);
            throw $exception;
        }

        if ($existing?->document_path) Storage::disk('local')->delete($existing->document_path);

        return redirect()->route('partner.status')->with('status', 'Your courier application was submitted for review.');
    }

    public function status(Request $request): View|RedirectResponse
    {
        $provider = $request->user()->logisticsProvider;
        if ($provider?->status === 'approved' && $request->user()->hasRole('logistics')) {
            return redirect()->route('logistics.dashboard');
        }

        return view('auth.onboarding.status', ['type' => 'courier company', 'record' => $provider, 'applyRoute' => route('partner.create')]);
    }
}
