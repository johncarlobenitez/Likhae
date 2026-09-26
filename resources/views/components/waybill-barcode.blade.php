@props(['tracking'])

@php
    $generator = new \Picqer\Barcode\BarcodeGeneratorSVG();
    $barcode = $generator->getBarcode(
        $tracking,
        \Picqer\Barcode\BarcodeGenerator::TYPE_CODE_128,
        2,
        54,
    );
@endphp

<div role="img" aria-label="Waybill barcode for {{ $tracking }}" style="display:flex;justify-content:center;max-width:100%;overflow:hidden;margin:10px auto;">
    {!! $barcode !!}
</div>