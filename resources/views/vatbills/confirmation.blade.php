<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>VAT Confirmation Letter</title>
    <style>
        body{background:linear-gradient(135deg,#dbe8f4,#f5f7fa);margin:0;padding-bottom:35px}.letter-actions{display:flex;gap:10px;justify-content:center;padding:16px}.letter-actions a,.letter-actions button{border:0;border-radius:7px;box-shadow:0 3px 9px rgba(15,23,42,.15);color:#fff;cursor:pointer;font-family:Arial,sans-serif;font-size:14px;font-weight:700;padding:11px 19px;text-decoration:none;transition:transform .15s}.letter-actions a:hover,.letter-actions button:hover{transform:translateY(-1px)}.print-button{background:#334155}.pdf-button{background:#dc2626}.back-button{background:#2563eb}@media print{body{background:#fff;padding:0}.letter-actions{display:none}}
    </style>
</head>
<body>
    <div class="letter-actions">
        <button type="button" class="print-button" onclick="window.print()">Print Confirmation Letter</button>
        <a class="pdf-button" target="_blank"
            href="{{ route('vat-bills.confirmation.pdf', ['invoice' => $invoice->id, 'firm_type' => $firmType, 'from_date' => $fromDate, 'to_date' => $toDate]) }}">
            PDF
        </a>
        <a class="back-button" href="{{ route('vat-bills.show', ['invoice' => $invoice->id, 'firm_type' => $firmType, 'from_date' => $fromDate, 'to_date' => $toDate]) }}">Back to Ledger</a>
    </div>

    @include('vatbills._confirmation_letter')
</body>
</html>
