<?php

namespace App\Services;

use App\Models\Delivery;
use App\Models\Order;
use App\Models\ParcelAssignment;
use App\Models\ParcelScanEvent;
use App\Models\ParcelStatusHistory;
use App\Models\User;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\SvgWriter;
use Illuminate\Support\Facades\DB;
use Picqer\Barcode\BarcodeGeneratorSVG;

class ParcelWorkflow
{
    public function ensureDelivery(Order $order, ?User $actor = null): Delivery
    {
        return DB::transaction(function () use ($order, $actor) {
            $delivery = Delivery::where('order_id', $order->id)->lockForUpdate()->first();
            if (! $delivery) {
                $initialStatus = in_array($order->status, ['ready_for_pickup', 'ready_pickup'], true) ? 'ready_for_pickup' : 'preparing';
                $delivery = Delivery::create([
                    'order_id' => $order->id,
                    'address' => $order->shipping_address ?: 'Delivery address pending',
                    'seller_address' => $this->addressFor($order->seller),
                    'status' => $initialStatus,
                    'provider' => 'LIKHAE Logistics',
                ]);
            }

            if (! $delivery->tracking_number) {
                $delivery->tracking_number = 'LKH-'.now()->format('Y').'-'.str_pad((string) $delivery->id, 6, '0', STR_PAD_LEFT);
                $delivery->save();
            }

            if (! $delivery->statusHistory()->exists()) {
                $this->recordHistory($delivery, null, $delivery->status, $actor, 'Parcel record and persistent tracking number created.');
            }

            return $delivery->fresh();
        }, 3);
    }

    public function transition(Delivery $delivery, string $status, ?User $actor, ?string $remarks = null, array $attributes = []): Delivery
    {
        return DB::transaction(function () use ($delivery, $status, $actor, $remarks, $attributes) {
            $locked = Delivery::whereKey($delivery->id)->lockForUpdate()->firstOrFail();
            $previous = $locked->status;
            if ($previous === $status) return $locked;

            $locked->forceFill($attributes + ['status' => $status])->save();
            $this->recordHistory($locked, $previous, $status, $actor, $remarks);

            return $locked->fresh();
        }, 3);
    }

    public function assign(Delivery $delivery, User $rider, string $type, User $assigner): ParcelAssignment
    {
        return ParcelAssignment::updateOrCreate(
            ['delivery_id' => $delivery->id, 'assignment_type' => $type],
            ['rider_id' => $rider->id, 'assigned_by' => $assigner->id, 'status' => 'assigned', 'assigned_at' => now(), 'accepted_at' => null, 'picked_up_at' => null, 'completed_at' => null]
        );
    }

    public function scan(Delivery $delivery, string $type, User $actor, ?ParcelAssignment $assignment = null): ParcelScanEvent
    {
        return ParcelScanEvent::firstOrCreate([
            'delivery_id' => $delivery->id,
            'scan_type' => $type,
            'scanned_by_user_id' => $actor->id,
        ], [
            'tracking_number' => $delivery->tracking_number,
            'scanned_by_role' => $actor->role,
            'logistics_center_id' => $delivery->logistics_center_id,
            'assignment_id' => $assignment?->id,
            'created_at' => now(),
        ]);
    }

    public function qrSvg(string $tracking): string
    {
        $qr = new QrCode(
            data: $tracking,
            encoding: new Encoding('ISO-8859-1'),
            errorCorrectionLevel: ErrorCorrectionLevel::Medium,
            size: 220,
            margin: 10,
            roundBlockSizeMode: RoundBlockSizeMode::Margin,
        );

        return (new SvgWriter())->write($qr)->getString();
    }

    public function barcodeSvg(string $tracking): string
    {
        return (new BarcodeGeneratorSVG())->getBarcode($tracking, BarcodeGeneratorSVG::TYPE_CODE_128, 2, 64);
    }

    private function recordHistory(Delivery $delivery, ?string $previous, string $next, ?User $actor, ?string $remarks): void
    {
        ParcelStatusHistory::create([
            'delivery_id' => $delivery->id,
            'order_id' => $delivery->order_id,
            'previous_status' => $previous,
            'new_status' => $next,
            'performed_by' => $actor?->id,
            'performed_by_role' => $actor?->role,
            'remarks' => $remarks,
            'created_at' => now(),
        ]);
    }

    private function addressFor(?User $user): ?string
    {
        if (! $user) return null;
        return collect([$user->house_number, $user->street, $user->barangay, $user->municipality, $user->province, $user->postal_code])->filter()->implode(', ') ?: null;
    }
}
