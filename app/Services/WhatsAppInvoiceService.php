<?php

namespace App\Services;

use App\Models\invoice;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppInvoiceService
{
    public function __construct(private InvoicePdfService $pdfService)
    {
    }

    public function send(invoice $invoice, string $phone, ?string $caption = null): array
    {
        $config = config('services.whatsapp');
        if (empty($config['access_token']) || empty($config['phone_number_id'])) {
            return ['success' => false, 'configured' => false, 'message' => 'WhatsApp Cloud API is not configured.'];
        }

        $phone = preg_replace('/\D+/', '', $phone);
        if (strlen($phone) === 10) {
            $phone = '977' . $phone;
        }

        $pdf = $this->pdfService->make($invoice);
        $baseUrl = rtrim($config['base_url'], '/') . '/' . $config['api_version'];
        $headers = ['Authorization' => 'Bearer ' . $config['access_token']];

        $mediaResponse = Http::withHeaders($headers)
            ->attach('file', $pdf['contents'], $pdf['filename'])
            ->post($baseUrl . '/' . $config['phone_number_id'] . '/media', [
                'messaging_product' => 'whatsapp',
                'type' => 'application/pdf',
            ]);

        if ($mediaResponse->failed() || !$mediaResponse->json('id')) {
            throw new \RuntimeException('WhatsApp PDF upload failed: ' . $mediaResponse->body());
        }

        $messageResponse = Http::withToken($config['access_token'])
            ->post($baseUrl . '/' . $config['phone_number_id'] . '/messages', [
                'messaging_product' => 'whatsapp',
                'to' => $phone,
                'type' => 'document',
                'document' => [
                    'id' => $mediaResponse->json('id'),
                    'caption' => $caption ?: 'Invoice ' . ($invoice->visible_invoice_no ?? $invoice->id),
                    'filename' => $pdf['filename'],
                ],
            ]);

        if ($messageResponse->failed()) {
            throw new \RuntimeException('WhatsApp PDF message failed: ' . $messageResponse->body());
        }

        return ['success' => true, 'message_id' => $messageResponse->json('messages.0.id')];
    }

    public function trySend(invoice $invoice, ?string $phone, ?string $caption = null): array
    {
        if (!$phone) {
            return ['success' => false, 'message' => 'Customer has no phone number.'];
        }

        try {
            return $this->send($invoice, $phone, $caption);
        } catch (\Throwable $e) {
            Log::error('Invoice WhatsApp PDF send failed', [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage(),
            ]);

            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
