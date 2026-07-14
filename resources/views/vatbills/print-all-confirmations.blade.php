<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>All VAT Confirmation Letters</title>
    <style>
        body{background:#e5e7eb;margin:0}.bulk-actions{background:#0f172a;display:flex;gap:10px;justify-content:center;padding:13px;position:sticky;top:0;z-index:20}.bulk-actions button,.bulk-actions a{background:#16a34a;border:0;border-radius:6px;color:#fff;cursor:pointer;font:700 14px Arial;padding:10px 18px;text-decoration:none}.bulk-actions a{background:#64748b}.print-page{margin:18px auto}.empty-print{background:#fff;border-radius:6px;margin:40px auto;max-width:600px;padding:40px;text-align:center}@media print{@page{size:A4 portrait;margin:0}body{background:#fff}.bulk-actions{display:none}.print-page{break-after:page;margin:0;page-break-after:always}.print-page:last-child{break-after:auto;page-break-after:auto}}
    </style>
</head>
<body>
    <div class="bulk-actions">
        <button type="button" onclick="window.print()">Print All Confirmation Letters</button>
        <a href="{{ route('vat-bills.index') }}">Back</a>
    </div>

    @forelse ($letters as $letter)
        <section class="print-page">
            @include('vatbills._confirmation_letter', $letter)
        </section>
    @empty
        <div class="empty-print">No confirmation letters found to print.</div>
    @endforelse
</body>
</html>
