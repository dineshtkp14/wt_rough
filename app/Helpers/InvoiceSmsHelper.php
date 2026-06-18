<?php

namespace App\Helpers;

use App\Models\invoice;
use App\Models\customerinfo;

class InvoiceSmsHelper
{
    private const SMS_LIMIT = 160;

    /**
     * Generate SMS message for invoice creation
     * 
     * @param invoice $invoice
     * @return string
     */
    public static function invoiceCreatedMessage(invoice $invoice, ?float $totalDue = null)
    {
        $invoice->loadMissing(['customer', 'salesitems.item']);

        $customer = $invoice->customer;
        $customerName = $customer ? $customer->name : 'Customer';
        $totalDue = $totalDue ?? (float) $invoice->total;
        $itemSummary = self::invoiceItemSummary($invoice);

        $messages = [
            'Namaste ' . $customerName . ', inv ' . $invoice->id . ': ' . $itemSummary['names']
                . ' Rs ' . self::formatAmount($invoice->total)
                . '. Total due till today Rs ' . self::formatAmount($totalDue) . '.',
            'Namaste ' . $customerName . ', inv ' . $invoice->id . ': ' . $itemSummary['short']
                . ' Rs ' . self::formatAmount($invoice->total)
                . '. Total due till today Rs ' . self::formatAmount($totalDue) . '.',
            'Inv ' . $invoice->id . ': ' . $itemSummary['short']
                . ' Rs ' . self::formatAmount($invoice->total)
                . '. Total due till today Rs ' . self::formatAmount($totalDue) . '.',
            'Inv ' . $invoice->id . ' Rs ' . self::formatAmount($invoice->total)
                . '. Total due till today Rs ' . self::formatAmount($totalDue) . '.',
        ];

        return self::firstMessageWithinLimit($messages);
    }

    public static function invoiceItemSummary(invoice $invoice): array
    {
        $invoice->loadMissing(['salesitems.item']);

        $items = $invoice->salesitems->map(function ($saleItem) {
            return trim((string) ($saleItem->item->itemsname ?? $saleItem->unstockedname ?? 'Item'));
        })->filter()->values();

        if ($items->isEmpty()) {
            return [
                'names' => 'items',
                'short' => 'items',
            ];
        }

        return [
            'names' => $items->implode(', '),
            'short' => $items->count() === 1 ? $items->first() : $items->count() . ' items',
        ];
    }

    /**
     * Generate SMS message for invoice reminder (payment due)
     * 
     * @param invoice $invoice
     * @return string
     */
    public static function invoiceDueMessage(invoice $invoice)
    {
        $customer = $invoice->customer;
        $customerName = $customer ? $customer->name : 'Customer';
        
        $message = "Namaste {$customerName}, ";
        $message .= "Invoice #{$invoice->id} amount Rs " . number_format($invoice->total, 2) . " is due. ";
        $message .= "Please clear at your earliest convenience. Thank you!";

        return $message;
    }

    /**
     * Generate SMS message for payment confirmation
     * 
     * @param invoice $invoice
     * @param string $paidAmount
     * @return string
     */
    public static function paymentConfirmationMessage(invoice $invoice, $paidAmount)
    {
        $customer = $invoice->customer;
        $customerName = $customer ? $customer->name : 'Customer';

        return self::paymentReceivedMessage($customerName, $paidAmount, null, null);
    }

    public static function paymentReceivedMessage($customerName, $paidAmount, $receiptNo = null, $remainingDue = null)
    {
        $receiptText = $receiptNo ? '. Rcpt ' . $receiptNo : '';
        $dueText = $remainingDue !== null ? '. Total due till today Rs ' . self::formatAmount($remainingDue) : '';

        $messages = [
            'Namaste ' . ($customerName ?: 'Customer')
                . ', payment Rs ' . self::formatAmount($paidAmount)
                . ' received' . $receiptText . $dueText . '.',
            'Payment Rs ' . self::formatAmount($paidAmount)
                . ' received' . $receiptText . $dueText . '.',
            'Payment Rs ' . self::formatAmount($paidAmount) . ' received' . $dueText . '.',
        ];

        return self::firstMessageWithinLimit($messages);
    }

    /**
     * Truncate message to 160 characters (single SMS limit)
     * 
     * @param string $message
     * @return string
     */
    public static function truncateMessage($message)
    {
        $message = self::withTeamSignature($message);

        if (strlen($message) > self::SMS_LIMIT) {
            return substr($message, 0, self::SMS_LIMIT - 3) . '...';
        }
        return $message;
    }

    public static function withTeamSignature($message)
    {
        $signature = 'Team Om Hari';

        if (strpos($message, $signature) !== false) {
            return $message;
        }

        return rtrim($message) . ' ' . $signature;
    }

    private static function firstMessageWithinLimit(array $messages): string
    {
        foreach ($messages as $message) {
            $message = self::withTeamSignature($message);

            if (strlen($message) <= self::SMS_LIMIT) {
                return $message;
            }
        }

        return self::truncateMessage(end($messages));
    }

    private static function formatAmount($amount): string
    {
        $amount = (float) $amount;

        if (floor($amount) == $amount) {
            return number_format($amount, 0);
        }

        return number_format($amount, 2);
    }
}
