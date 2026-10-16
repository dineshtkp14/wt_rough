<?php

namespace App\Services;

use App\Models\customerinfo;
use App\Models\customerledgerdetails;
use App\Models\invoice;
use App\Models\item;
use App\Models\salesitem;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;

class InvoicePdfService
{
    /**
     * Build the same invoice PDF used by the print screen.
     */
    public function make(invoice $invoice): array
    {
        $invoice->loadMissing('customer');
        $items = salesitem::where('invoiceid', $invoice->id)->get();

        foreach ($items as $data) {
            $item = item::where('id', $data->itemid)->select('id', 'itemsname', 'mrp', 'unit')->first();
            if ($item) {
                $data->itemid = $item->itemsname;
                $data->itemidorg = $item->id;
                $data->mrp = $item->mrp;
                $data->unit = $item->unit;
            } else {
                $data->itemid = $data->unstockedname;
            }
        }

        $invoiceType = customerledgerdetails::where('invoiceid', $invoice->id)->first();
        $customer = customerinfo::find($invoice->customerid);
        $invoices = collect([$invoice]);

        $pdf = FacadePdf::setOptions([
            'dpi' => 150,
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'NotoSansDevanagari',
            'chroot' => public_path(),
            'enable_font_subsetting' => false,
        ])->loadView('customerledgerhistory.customerbillnoinvoiceconvertpdf', [
            'allinvoices' => $invoices,
            'allcusbyid' => $items,
            'invoiceid' => $invoice->id,
            'cinfodetails' => $customer ? collect([$customer]) : collect(),
            'forinvoicetype' => $invoiceType,
        ])->setPaper('A5', 'portrait');

        $customerName = $customer?->name ?: 'customer';
        $safeName = trim((string) preg_replace('/[^A-Za-z0-9]+/', '-', $customerName), '-');

        return [
            'contents' => $pdf->output(),
            'filename' => ($safeName ?: 'customer') . '-invoice-' . $invoice->id . '.pdf',
        ];
    }
}
