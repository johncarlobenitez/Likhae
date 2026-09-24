<?php

namespace App\Services;

use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\SvgWriter;
use Picqer\Barcode\BarcodeGeneratorSVG;

class ShipmentCodeService
{
    public function qr(string $trackingCode): string
    {
        $code = new QrCode(
            data: $trackingCode,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::Medium,
            size: 240,
            margin: 8,
            roundBlockSizeMode: RoundBlockSizeMode::Margin,
        );

        return (new SvgWriter())->write($code)->getString();
    }

    public function barcode(string $trackingCode): string
    {
        return (new BarcodeGeneratorSVG())->getBarcode($trackingCode, BarcodeGeneratorSVG::TYPE_CODE_128, 2, 64);
    }
}
