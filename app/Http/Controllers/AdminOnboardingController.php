<?php

namespace App\Http\Controllers;

use App\Models\LogisticsProvider;
use App\Models\Seller;
use App\Models\AdminAuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminOnboardingController extends Controller
{
    public function sellers(Request $request): View
    {
        return $this->queue($request, Seller::query()->with(['owner', 'pickupAddress']), 'sellers');
    }

    public function couriers(Request $request): View
    {
        return $this->queue($request, LogisticsProvider::query()->with('owner'), 'couriers');
    }

    private function queue(Request $request, $query, string $type): View
    {
        $status = $request->validate(['status' => ['nullable', Rule::in(['pending', 'approved', 'rejected', 'suspended'])]])['status'] ?? 'pending';

        return view('onboarding.admin-queue', ['records' => $query->where('status', $status)->latest()->paginate(20)->withQueryString(), 'type' => $type, 'status' => $status]);
    }

    public function approveSeller(Request $request, Seller $seller): RedirectResponse
    {
        Gate::authorize('update', $seller);
        return $this->approve($request, $seller, 'seller');
    }

    public function approveCourier(Request $request, LogisticsProvider $provider): RedirectResponse
    {
        Gate::authorize('update', $provider);
        return $this->approve($request, $provider, 'logistics');
    }

    private function approve(Request $request, Model $record, string $role): RedirectResponse
    {
        abort_unless($record->status === 'pending', 422);
        DB::transaction(function () use ($request, $record, $role) {
            $previousStatus = $record->status;
            $record->update(['status' => 'approved', 'rejection_reason' => null, 'approved_by' => $request->user()->id, 'approved_at' => now()]);
            $record->owner->grant($role);
            $this->audit($request, $record, $this->auditPrefix($record).'.approved', $previousStatus, 'approved');
        });

        return back()->with('success', ucfirst($role).' application approved.');
    }

    public function rejectSeller(Request $request, Seller $seller): RedirectResponse
    {
        Gate::authorize('update', $seller);
        return $this->reject($request, $seller);
    }

    public function rejectCourier(Request $request, LogisticsProvider $provider): RedirectResponse
    {
        Gate::authorize('update', $provider);
        return $this->reject($request, $provider);
    }

    private function reject(Request $request, Model $record): RedirectResponse
    {
        $data = $request->validate(['reason' => ['required', 'string', 'max:500']]);
        abort_unless($record->status === 'pending', 422);
        DB::transaction(function () use ($request, $record, $data): void {
            $previousStatus = $record->status;
            $record->update(['status' => 'rejected', 'rejection_reason' => $data['reason'], 'approved_by' => null, 'approved_at' => null]);
            $this->audit($request, $record, $this->auditPrefix($record).'.rejected', $previousStatus, 'rejected', $data['reason']);
        });

        return back()->with('success', 'Application rejected.');
    }

    public function suspendSeller(Seller $seller): RedirectResponse
    {
        Gate::authorize('update', $seller);
        return $this->transition(request(), $seller, 'approved', 'suspended', 'Shop suspended.');
    }

    public function reinstateSeller(Seller $seller): RedirectResponse
    {
        Gate::authorize('update', $seller);
        return $this->transition(request(), $seller, 'suspended', 'approved', 'Shop reinstated.');
    }

    public function suspendCourier(LogisticsProvider $provider): RedirectResponse
    {
        Gate::authorize('update', $provider);
        return $this->transition(request(), $provider, 'approved', 'suspended', 'Courier suspended.');
    }

    public function reinstateCourier(LogisticsProvider $provider): RedirectResponse
    {
        Gate::authorize('update', $provider);
        return $this->transition(request(), $provider, 'suspended', 'approved', 'Courier reinstated.');
    }

    private function transition(Request $request, Model $record, string $from, string $to, string $message): RedirectResponse
    {
        abort_unless($record->status === $from, 422);
        DB::transaction(function () use ($request, $record, $to): void {
            $previousStatus = $record->status;
            $record->update(['status' => $to]);
            $action = $to === 'approved' ? 'reinstated' : 'suspended';
            $this->audit($request, $record, $this->auditPrefix($record).'.'.$action, $previousStatus, $to);
        });

        return back()->with('success', $message);
    }

    private function auditPrefix(Model $record): string
    {
        return $record instanceof Seller ? 'seller' : 'courier';
    }

    private function audit(Request $request, Model $record, string $action, string $previousStatus, string $newStatus, ?string $reason = null): void
    {
        AdminAuditLog::create([
            'actor_id' => $request->user()->id,
            'action' => $action,
            'target_type' => $record::class,
            'target_id' => $record->id,
            'description' => $reason,
            'ip_address' => $request->ip(),
            'metadata' => ['before' => ['status' => $previousStatus], 'after' => ['status' => $newStatus], 'reason' => $reason],
        ]);
    }

    public function sellerDocument(Seller $seller)
    {
        Gate::authorize('view', $seller);
        return $this->download($seller->permit_path, 'seller-permit');
    }

    public function courierDocument(LogisticsProvider $provider)
    {
        Gate::authorize('view', $provider);
        return $this->download($provider->document_path, 'courier-document');
    }

    private function download(?string $path, string $name)
    {
        abort_unless($path && ! str_contains($path, '..') && Storage::disk('local')->exists($path), 404);

        return Storage::disk('local')->download($path, $name.'.'.pathinfo($path, PATHINFO_EXTENSION), ['Cache-Control' => 'private, no-store']);
    }
}
