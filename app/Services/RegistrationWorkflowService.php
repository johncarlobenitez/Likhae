<?php

namespace App\Services;

use App\Http\Requests\StoreRegistrationRequest;
use App\Models\Admin\AuditLog;
use App\Models\Auth\ApplicationDocument;
use App\Models\Auth\RegistrationApplication;
use App\Models\Buyer\Address;
use App\Models\Logistics\LogisticsApplicationData;
use App\Models\Logistics\LogisticsCenter;
use App\Models\Rider\RiderApplicationData;
use App\Models\Rider\RiderProfile;
use App\Models\Seller\SellerApplicationData;
use App\Models\Seller\SellerProfile;
use App\Models\User;
use App\Notifications\RegistrationDecisionNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;

class RegistrationWorkflowService
{
    public function submit(StoreRegistrationRequest $request): RegistrationApplication
    {
        $data = $request->validated();
        $storedPaths = [];

        try {
            return DB::transaction(function () use ($request, $data, &$storedPaths): RegistrationApplication {
                $user = User::create([
                    'account_type' => $request->accountTypeConstant(),
                    'first_name' => trim($data['first_name']),
                    'middle_initial' => filled($data['middle_initial'] ?? null) ? trim((string) $data['middle_initial']) : null,
                    'last_name' => trim($data['last_name']),
                    'sex' => $this->normalizeSex((string) $data['sex']),
                    'email' => mb_strtolower(trim($data['email'])),
                    'contact_number' => trim($data['contact_number']),
                    'birthday' => $data['birthday'],
                    'password' => $data['password'],
                    'status' => User::STATUS_PENDING,
                ]);

                $address = Address::create([
                    'user_id' => $user->id,
                    'label' => 'Primary',
                    'recipient_name' => $user->name,
                    'contact_number' => $user->contact_number,
                    'province_code' => $data['province_code'],
                    'province_name' => $data['province'],
                    'municipality_code' => $data['municipality_code'],
                    'municipality_name' => $data['municipality'],
                    'barangay_code' => $data['barangay_code'],
                    'barangay_name' => $data['barangay'],
                    'postal_code' => $data['postal_code'] ?? null,
                    'house_number' => $data['house_number'] ?? null,
                    'street_address' => trim($data['street']),
                    'landmark' => $data['landmark'] ?? null,
                    'is_default' => true,
                ]);

                $application = RegistrationApplication::create([
                    'application_number' => $this->newApplicationNumber(),
                    'user_id' => $user->id,
                    'status' => RegistrationApplication::STATUS_PENDING,
                    'submitted_at' => now(),
                ]);

                match ($user->account_type) {
                    User::TYPE_SELLER => SellerApplicationData::create([
                        'registration_application_id' => $application->id,
                        'category_id' => (int) $data['line_of_business'],
                        'business_name' => trim($data['business_name']),
                        'business_registration_number' => $data['business_registration_number'] ?? null,
                    ]),
                    User::TYPE_LOGISTICS => LogisticsApplicationData::create([
                        'registration_application_id' => $application->id,
                        'business_address_id' => $address->id,
                        'business_name' => trim($data['business_name']),
                        'business_registration_number' => $data['business_registration_number'] ?? null,
                        'dti_registration_number' => $data['dti_registration_number'] ?? null,
                    ]),
                    User::TYPE_RIDER => RiderApplicationData::create([
                        'registration_application_id' => $application->id,
                        'target_logistics_center_id' => (int) $data['target_logistics_center_id'],
                        'vehicle_type' => $data['vehicle_type'],
                        'plate_number' => mb_strtoupper(trim($data['plate_number'])),
                        'drivers_license_number' => mb_strtoupper(trim($data['drivers_license_number'])),
                    ]),
                    default => null,
                };

                $documentMap = [
                    'valid_id' => 'VALID_ID',
                    'business_permit' => 'BUSINESS_PERMIT',
                    'or_cr' => 'VEHICLE_OR_CR',
                    'drivers_license' => 'DRIVERS_LICENSE',
                ];

                foreach ($documentMap as $input => $type) {
                    $file = $request->file($input);
                    if (! $file) {
                        continue;
                    }

                    $path = $file->store('registration/'.$application->application_number, 'registrations');
                    $storedPaths[] = $path;

                    ApplicationDocument::create([
                        'registration_application_id' => $application->id,
                        'document_type' => $type,
                        'file_path' => $path,
                        'original_name' => $file->getClientOriginalName(),
                        'mime_type' => $file->getClientMimeType(),
                        'verification_status' => 'PENDING',
                    ]);
                }

                $this->audit(
                    actor: null,
                    event: 'registration.submitted',
                    record: $application,
                    oldValues: null,
                    newValues: [
                        'status' => RegistrationApplication::STATUS_PENDING,
                        'account_type' => $user->account_type,
                    ],
                    request: $request,
                );

                return $application->load(['user', 'documents', 'sellerData', 'logisticsData', 'riderData']);
            });
        } catch (Throwable $exception) {
            foreach ($storedPaths as $path) {
                Storage::disk('registrations')->delete($path);
            }

            throw $exception;
        }
    }

    public function approveByAdmin(
        RegistrationApplication $application,
        User $reviewer,
        ?string $notes = null,
        ?Request $request = null,
    ): RegistrationApplication {
        $application->loadMissing(['user', 'documents', 'sellerData', 'logisticsData', 'riderData']);
        $type = $application->user?->account_type;

        if (! in_array($type, [User::TYPE_BUYER, User::TYPE_SELLER, User::TYPE_LOGISTICS], true)) {
            throw ValidationException::withMessages([
                'application' => 'This application must be reviewed by its assigned logistics center.',
            ]);
        }

        return $this->approve(
            application: $application,
            reviewer: $reviewer,
            notes: $notes,
            request: $request,
            expectedType: $type,
        );
    }

    public function approveRider(
        RegistrationApplication $application,
        User $reviewer,
        ?string $notes = null,
        ?Request $request = null,
    ): RegistrationApplication {
        $application->loadMissing(['user', 'documents', 'riderData.targetLogisticsCenter']);

        if ($application->user?->account_type !== User::TYPE_RIDER) {
            throw ValidationException::withMessages(['application' => 'This is not a rider application.']);
        }

        $center = $reviewer->logisticsCenter()->where('status', 'ACTIVE')->first();
        if (! $center || (int) $application->riderData?->target_logistics_center_id !== (int) $center->id) {
            abort(403, 'This rider application belongs to another logistics center.');
        }

        return $this->approve(
            application: $application,
            reviewer: $reviewer,
            notes: $notes,
            request: $request,
            expectedType: User::TYPE_RIDER,
        );
    }

    public function reject(
        RegistrationApplication $application,
        User $reviewer,
        string $reason,
        ?Request $request = null,
    ): RegistrationApplication {
        $application->loadMissing(['user', 'documents']);
        $before = $application->status;

        if (! in_array($before, [RegistrationApplication::STATUS_PENDING, RegistrationApplication::STATUS_UNDER_REVIEW], true)) {
            throw ValidationException::withMessages(['application' => 'Only pending applications can be rejected.']);
        }

        DB::transaction(function () use ($application, $reviewer, $reason, $request, $before): void {
            $application->update([
                'status' => RegistrationApplication::STATUS_REJECTED,
                'reviewed_by_user_id' => $reviewer->id,
                'reviewed_at' => now(),
                'rejection_reason' => $reason,
                'decision_notes' => null,
            ]);

            $application->documents()->update([
                'verification_status' => 'REJECTED',
                'verified_by_user_id' => $reviewer->id,
                'verified_at' => now(),
                'rejection_reason' => $reason,
            ]);

            $this->audit(
                actor: $reviewer,
                event: 'registration.rejected',
                record: $application,
                oldValues: ['status' => $before],
                newValues: ['status' => RegistrationApplication::STATUS_REJECTED, 'reason' => $reason],
                request: $request,
            );
        });

        $this->notifyDecision($application->fresh('user'));

        return $application->fresh(['user', 'documents']);
    }

    private function approve(
        RegistrationApplication $application,
        User $reviewer,
        ?string $notes,
        ?Request $request,
        string $expectedType,
    ): RegistrationApplication {
        $before = $application->status;

        if (! in_array($before, [RegistrationApplication::STATUS_PENDING, RegistrationApplication::STATUS_UNDER_REVIEW], true)) {
            throw ValidationException::withMessages(['application' => 'Only pending applications can be approved.']);
        }

        DB::transaction(function () use ($application, $reviewer, $notes, $request, $expectedType, $before): void {
            $application->refresh();
            $application->loadMissing(['user.addresses', 'sellerData', 'logisticsData', 'riderData']);
            $user = $application->user;

            if (! $user || $user->account_type !== $expectedType) {
                throw ValidationException::withMessages(['application' => 'The application account type is invalid.']);
            }

            $defaultAddress = $user->addresses()->where('is_default', true)->first() ?? $user->addresses()->first();

            if ($expectedType === User::TYPE_SELLER) {
                $data = $application->sellerData;
                if (! $data) {
                    throw ValidationException::withMessages(['application' => 'Seller application data is missing.']);
                }

                SellerProfile::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'primary_category_id' => $data->category_id,
                        'business_address_id' => $defaultAddress?->id,
                        'business_name' => $data->business_name,
                        'business_registration_number' => $data->business_registration_number,
                        'status' => 'ACTIVE',
                        'approved_by_user_id' => $reviewer->id,
                        'approved_at' => now(),
                    ],
                );
            }

            if ($expectedType === User::TYPE_LOGISTICS) {
                $data = $application->logisticsData;
                if (! $data) {
                    throw ValidationException::withMessages(['application' => 'Logistics application data is missing.']);
                }

                LogisticsCenter::updateOrCreate(
                    ['owner_user_id' => $user->id],
                    [
                        'address_id' => $data->business_address_id ?: $defaultAddress?->id,
                        'code' => 'LC-'.str_pad((string) $application->id, 6, '0', STR_PAD_LEFT),
                        'business_name' => $data->business_name,
                        'business_registration_number' => $data->business_registration_number,
                        'dti_registration_number' => $data->dti_registration_number,
                        'status' => 'ACTIVE',
                        'approved_by_user_id' => $reviewer->id,
                        'approved_at' => now(),
                    ],
                );
            }

            if ($expectedType === User::TYPE_RIDER) {
                $data = $application->riderData;
                if (! $data) {
                    throw ValidationException::withMessages(['application' => 'Rider application data is missing.']);
                }

                RiderProfile::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'logistics_center_id' => $data->target_logistics_center_id,
                        'vehicle_type' => $data->vehicle_type,
                        'plate_number' => $data->plate_number,
                        'drivers_license_number' => $data->drivers_license_number,
                        'status' => 'ACTIVE',
                        'approved_by_user_id' => $reviewer->id,
                        'approved_at' => now(),
                    ],
                );
            }

            $user->update([
                'status' => User::STATUS_ACTIVE,
                'email_verified_at' => $user->email_verified_at ?: now(),
            ]);

            $application->update([
                'status' => RegistrationApplication::STATUS_APPROVED,
                'reviewed_by_user_id' => $reviewer->id,
                'reviewed_at' => now(),
                'decision_notes' => $notes,
                'rejection_reason' => null,
            ]);

            $application->documents()->update([
                'verification_status' => 'VERIFIED',
                'verified_by_user_id' => $reviewer->id,
                'verified_at' => now(),
                'rejection_reason' => null,
            ]);

            $this->audit(
                actor: $reviewer,
                event: 'registration.approved',
                record: $application,
                oldValues: ['status' => $before],
                newValues: ['status' => RegistrationApplication::STATUS_APPROVED, 'account_type' => $expectedType],
                request: $request,
            );
        });

        $fresh = $application->fresh(['user', 'documents']);
        $this->notifyDecision($fresh);

        return $fresh;
    }

    private function normalizeSex(string $sex): string
    {
        return match (strtolower($sex)) {
            'male' => 'MALE',
            'female' => 'FEMALE',
            'other' => 'OTHER',
            default => 'PREFER_NOT_TO_SAY',
        };
    }

    private function newApplicationNumber(): string
    {
        do {
            $number = 'REG-'.now()->format('Ymd').'-'.Str::upper(Str::random(8));
        } while (RegistrationApplication::query()->where('application_number', $number)->exists());

        return $number;
    }

    private function notifyDecision(?RegistrationApplication $application): void
    {
        $user = $application?->user;
        if (! $user) {
            return;
        }

        try {
            $user->notify(new RegistrationDecisionNotification($application));
        } catch (Throwable $exception) {
            report($exception);
        }
    }

    private function audit(
        ?User $actor,
        string $event,
        RegistrationApplication $record,
        ?array $oldValues,
        ?array $newValues,
        ?Request $request,
    ): void {
        AuditLog::create([
            'actor_user_id' => $actor?->id,
            'event' => $event,
            'auditable_type' => RegistrationApplication::class,
            'auditable_id' => $record->id,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
            'created_at' => now(),
        ]);
    }
}
