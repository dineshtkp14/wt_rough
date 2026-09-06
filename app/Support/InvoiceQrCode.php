<?php

namespace App\Support;

use App\Models\invoice;
use chillerlan\QRCode\Common\EccLevel;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class InvoiceQrCode
{
    public static function dataUri(invoice $invoice, ?string $customerName = null): string
    {
        $payload = implode("\n", array_filter([
            'INVOICE: INV-' . $invoice->id,
            'DATE: ' . ($invoice->inv_date ?? ''),
            'TOTAL: NPR ' . number_format((float) $invoice->total, 2, '.', ''),
            $customerName ? 'CUSTOMER: ' . $customerName : null,
        ]));

        return (new QRCode(new QROptions([
            'eccLevel' => EccLevel::M,
            'outputBase64' => true,
            'addQuietzone' => true,
        ])))->render($payload);
    }
}
