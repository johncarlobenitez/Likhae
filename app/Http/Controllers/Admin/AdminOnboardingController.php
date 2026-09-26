<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Auth\ApplicationDocument;
use App\Models\Auth\RegistrationApplication;
use App\Models\User;
use App\Services\RegistrationWorkflowService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminOnboardingController extends Controller
{
    public function __construct(
        private readonly RegistrationWorkflowService $workflow,
    ) {
    }

    public function index(Request $request): View
    {
        $filters = $request->validate([
            'status' => ['nullable', Rule::in([
                RegistrationApplication::STATUS_PENDING,
                RegistrationApplication::STATUS_UNDER_REVIEW,
                RegistrationApplication::STATUS_APPROVED,
                RegistrationApplication::STATUS_REJECTED,
                RegistrationApplication::STATUS_CANCELLED,
            ])],
            'type' => ['nullable', Rule::in([
                User::TYPE_BUYER,
                User::TYPE_SELLER,
                User::TYPE_LOGISTICS,
            ])],
        ]);

        $status = $filters['status'] ?? RegistrationApplication::STATUS_PENDING;
        $type = $filters['type'] ?? null;

        $records = RegistrationApplication::query()
            ->with([
                'user.addresses',
                'documents',
                'sellerData.category',
                'logisticsData.businessAddress',
                'reviewer',
            ])
            ->where('status', $status)
            ->whereHas('user', function ($query) use ($type): void {
                $query->whereIn('account_type', [
                    User::TYPE_BUYER,
                    User::TYPE_SELLER,
                    User::TYPE_LOGISTICS,
                ]);

                if ($type) {
                    $query->where('account_type', $type);
                }
            })
            ->latest('submitted_at')
            ->paginate(20)
            ->withQueryString();

        return view('auth.onboarding.admin-queue', [
            'records' => $records,
            'status' => $status,
            'type' => $type,
        ]);
    }

    public function show(RegistrationApplication $application): View
    {
        $application->load([
            'user.addresses',
            'documents',
            'sellerData.category',
            'logisticsData.businessAddress',
            'reviewer',
        ]);

        abort_unless(
            $application->user
                && in_array($application->user->account_type, [User::TYPE_BUYER, User::TYPE_SELLER, User::TYPE_LOGISTICS], true),
            404,
        );

        if ($application->status === RegistrationApplication::STATUS_PENDING) {
            $application->update(['status' => RegistrationApplication::STATUS_UNDER_REVIEW]);
        }

        return view('auth.onboarding.admin-application', compact('application'));
    }

    public function approve(Request $request, RegistrationApplication $application): RedirectResponse
    {
        $data = $request->validate([
            'decision_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $this->workflow->approveByAdmin(
            application: $application,
            reviewer: $request->user(),
            notes: $data['decision_notes'] ?? null,
            request: $request,
        );

        return redirect()
            ->route('admin.registrations.show', $application)
            ->with('success', 'Registration approved. The account is now active.');
    }

    public function reject(Request $request, RegistrationApplication $application): RedirectResponse
    {
        $this->ensureAdminReviewable($application);

        $data = $request->validate([
            'reason' => ['required', 'string', 'max:1000'],
        ]);

        $this->workflow->reject(
            application: $application,
            reviewer: $request->user(),
            reason: $data['reason'],
            request: $request,
        );

        return redirect()
            ->route('admin.registrations.show', $application)
            ->with('success', 'Registration rejected.');
    }

    public function document(
        RegistrationApplication $application,
        ApplicationDocument $document,
    ): StreamedResponse {
        $this->ensureAdminReviewable($application);
        abort_unless($document->registration_application_id === $application->id, 404);
        abort_unless(Storage::disk('registrations')->exists($document->file_path), 404);

        $extension = pathinfo($document->original_name ?: $document->file_path, PATHINFO_EXTENSION);
        $filename = strtolower($document->document_type).($extension ? '.'.$extension : '');

        return Storage::disk('registrations')->download(
            $document->file_path,
            $filename,
            ['Cache-Control' => 'private, no-store'],
        );
    }

    public function sellers(): RedirectResponse
    {
        return redirect()->route('admin.registrations', ['type' => User::TYPE_SELLER]);
    }

    public function couriers(): RedirectResponse
    {
        return redirect()->route('admin.registrations', ['type' => User::TYPE_LOGISTICS]);
    }

    private function ensureAdminReviewable(RegistrationApplication $application): void
    {
        $application->loadMissing('user');

        abort_unless(
            $application->user
                && in_array($application->user->account_type, [User::TYPE_BUYER, User::TYPE_SELLER, User::TYPE_LOGISTICS], true),
            404,
        );
    }
}

