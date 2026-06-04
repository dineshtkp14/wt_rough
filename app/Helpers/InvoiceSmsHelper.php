<?php

namespace App\Helpers;

use App\Models\invoice;
use App\Models\customerinfo;

class InvoiceSmsHelper
{
    /**
     * Generate SMS message for invoice creation
     * 
     * @param invoice $invoice
     * @return string
     */
    public static function invoiceCreatedMessage(invoice $invoice)
    {
        $customer = $invoice->customer;
        $customerName = $customer ? $customer->name : 'Customer';
        
        $message = "Namaste {$customerName}, Your invoice #{$invoice->id} has been created. ";
        $message .= "Total: Rs " . number_format($invoice->total, 2) . ". ";
        
        if ($invoice->inv_type === 'credit') {
            $message .= "Due Date: " . ($invoice->inv_due_date ?? 'Contact for details') . ". ";
        }
        
        $message .= "Thank you!";

        return $message;
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
        
        $message = "Namaste {$customerName}, ";
        $message .= "We received your payment of Rs " . number_format($paidAmount, 2) . ". ";
        $message .= "Thank you for your business!";

        return $message;
    }

    /**
     * Truncate message to 720 characters (SMS limit)
     * 
     * @param string $message
     * @return string
     */
    public static function truncateMessage($message)
    {
        $message = self::withTeamSignature($message);

        if (strlen($message) > 720) {
            return substr($message, 0, 717) . '...';
        }
        return $message;
    }

    public static function withTeamSignature($message)
    {
        $signature = 'Team Om Hari';

        if (strpos($message, $signature) !== false) {
            return $message;
        }

        return rtrim($message) . "\n" . $signature;
    }
}
