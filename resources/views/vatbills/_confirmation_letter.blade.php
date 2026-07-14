@php
    $isMalika = $firmType === 'Malika & Nav Durga Traders';
    $firmDisplayName = $isMalika ? 'Malika & Nawadurga Traders' : 'Durga and Dinesh Traders';
    $firmInitials = $isMalika ? 'MN' : 'DD';

    $formatBalance = function ($amount) {
        if (abs($amount) < 0.01) {
            return 'Nil';
        }

        return 'NPR ' . number_format(abs($amount), 2) . ($amount > 0 ? ' Dr' : ' Cr');
    };

    $formatTransactionAmount = function ($amount) {
        return abs((float) $amount) < 0.01 ? '-' : number_format((float) $amount, 2);
    };
@endphp

<div class="confirmation-letter">
    <div class="letter-top-line"></div>

    <table class="brand-table">
        <tr>
            <td class="brand-logo-cell">
                <div class="brand-logo">{{ $firmInitials }}</div>
            </td>
            <td class="brand-name-cell">
                <h1>{{ $firmDisplayName }}</h1>
                <div class="brand-rule"></div>
                <div class="brand-location">Tikapur, Kailali</div>
                <div class="brand-contact">
                    VAT No: <strong>{{ $firmVatNo }}</strong>
                    <span>|</span>
                    Contact: <strong>{{ $firmContactNumbers }}</strong>
                </div>
            </td>
        </tr>
    </table>

    <div class="document-label">BALANCE CONFIRMATION LETTER</div>

    <div class="letter-date"><span>Date (B.S.)</span><strong>{{ $letterDateBs }}</strong></div>

    <section class="recipient-card">
        <div class="recipient-label">ADDRESSED TO</div>
        <table>
            <tr>
                <td><span>Party Name</span><strong>{{ $customer->name }}</strong></td>
                <td><span>VAT No.</span><strong>{{ $partyVatNo }}</strong></td>
            </tr>
            <tr>
                <td colspan="2"><span>Address</span><strong>{{ $customer->address ?: '-' }}</strong></td>
            </tr>
        </table>
    </section>

    <div class="subject-box">
        <span>SUBJECT</span>
        Confirmation of Sales Transactions and Balance at the end of FY {{ $fiscalYear }}
    </div>

    <p class="salutation">Dear Sir/Madam,</p>

    <p class="intro-text">
        We hereby confirm that the following transaction details are recorded with your organization for the
        period <strong>{{ $periodFromBs }}</strong> to <strong>{{ $periodToBs }}</strong>. Kindly verify the
        figures below and return a duly signed and stamped copy as confirmation of your acceptance.
    </p>

    <table class="confirmation-table">
        <thead>
            <tr>
                <th>S.No</th>
                <th>Particulars</th>
                <th>Exempted Sales</th>
                <th>Taxable Sales</th>
                <th>VAT 13%</th>
                <th>Total (NPR)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td><td>Purchase</td>
                <td>{{ $formatTransactionAmount($purchaseRow['exempted']) }}</td>
                <td>{{ $formatTransactionAmount($purchaseRow['taxable']) }}</td>
                <td>{{ $formatTransactionAmount($purchaseRow['vat']) }}</td>
                <td>{{ $formatTransactionAmount($purchaseRow['total']) }}</td>
            </tr>
            <tr>
                <td>2</td><td>Purchase Return</td>
                <td>{{ $formatTransactionAmount($purchaseReturnRow['exempted']) }}</td>
                <td>{{ $formatTransactionAmount($purchaseReturnRow['taxable']) }}</td>
                <td>{{ $formatTransactionAmount($purchaseReturnRow['vat']) }}</td>
                <td>{{ $formatTransactionAmount($purchaseReturnRow['total']) }}</td>
            </tr>
            <tr class="sales-row">
                <td>3</td><td>Sales</td>
                <td>{{ $formatTransactionAmount($salesRow['exempted']) }}</td>
                <td>{{ number_format($salesRow['taxable'], 2) }}</td>
                <td>{{ number_format($salesRow['vat'], 2) }}</td>
                <td>{{ number_format($salesRow['total'], 2) }}</td>
            </tr>
            <tr>
                <td>4</td><td>Sales Return</td>
                <td>{{ $formatTransactionAmount($salesReturnRow['exempted']) }}</td>
                <td>{{ $formatTransactionAmount($salesReturnRow['taxable']) }}</td>
                <td>{{ $formatTransactionAmount($salesReturnRow['vat']) }}</td>
                <td>{{ $formatTransactionAmount($salesReturnRow['total']) }}</td>
            </tr>
        </tbody>
    </table>

    <section class="balance-card">
        <div class="balance-title">BALANCE SUMMARY</div>
        <table>
            <tr>
                <td>Opening balance at the beginning of the period</td>
                <th>{{ $formatBalance($openingBalance) }}</th>
            </tr>
            <tr>
                <td>Closing balance at the end of the period</td>
                <th>{{ $formatBalance($closingBalance) }}</th>
            </tr>
        </table>
    </section>

    <div class="confirmation-note">
        Please return a duly signed and stamped copy within seven days of receipt of this letter. Otherwise,
        the figures stated above will be considered accepted and confirmed.
    </div>

    <p class="thanks">Thanking you,<br><strong>Yours faithfully</strong></p>

    <table class="signatures">
        <tr>
            <td>
                <div class="signature-space"></div>
                <div class="signature-line"></div>
                <strong>Authorized Signatory</strong>
                <span>For {{ $firmDisplayName }}</span>
                <small>Company Seal</small>
            </td>
            <td>
                <div class="signature-space"></div>
                <div class="signature-line"></div>
                <strong>Authorized Signatory</strong>
                <span>For {{ $customer->name }}</span>
                <small>Party Seal</small>
            </td>
        </tr>
    </table>

    <div class="letter-footer">
        <span>{{ $firmDisplayName }}</span>
        <span>VAT Confirmation • FY {{ $fiscalYear }}</span>
    </div>
</div>

<style>
*{box-sizing:border-box}.confirmation-letter{background:#fff;box-shadow:0 12px 35px rgba(15,47,87,.14);color:#172033;font-family:DejaVu Sans,Arial,sans-serif;font-size:12px;line-height:1.48;margin:0 auto;max-width:790px;min-height:1080px;padding:0 44px 24px;position:relative}.letter-top-line{background:#0b477f;border-bottom:5px solid #d9a928;height:13px;margin:0 -44px 20px}.brand-table{border:0!important;border-collapse:collapse!important;display:table!important;margin:0 0 10px!important;table-layout:fixed!important;width:100%!important}.brand-table tr{display:table-row!important}.brand-table td{border:0!important;padding:0!important;vertical-align:middle}.brand-logo-cell{width:105px}.brand-logo{border:4px double #0b477f;border-radius:50%;color:#0b477f;font-family:Georgia,serif;font-size:25px;font-weight:900;height:78px;letter-spacing:-2px;line-height:70px;text-align:center;width:78px}.brand-name-cell{text-align:center}.brand-name-cell h1{color:#0b376d;font-family:Georgia,serif;font-size:25px;letter-spacing:.3px;margin:0;text-transform:uppercase}.brand-rule{border-top:2px solid #d9a928;margin:6px auto 5px;width:82%}.brand-location{color:#334155;font-size:12px;font-weight:700}.brand-contact{color:#475569;font-size:10.5px;margin-top:2px}.brand-contact span{color:#d9a928;padding:0 8px}.document-label{background:#0b477f;border-radius:4px;color:#fff;font-size:13px;font-weight:900;letter-spacing:1.4px;margin:13px auto 16px;padding:7px 28px;text-align:center;width:330px}.letter-date{text-align:right}.letter-date span{color:#64748b;font-size:10px;font-weight:800;margin-right:8px;text-transform:uppercase}.letter-date strong{border-bottom:1px solid #94a3b8;padding:0 4px 3px}.recipient-card{background:#f4f8fc;border-left:5px solid #0b477f;border-radius:4px;margin:19px 0 17px;padding:10px 14px 8px;position:relative}.recipient-label{color:#0b477f;font-size:9px;font-weight:900;letter-spacing:1.1px;margin-bottom:5px}.recipient-card table{border:0!important;border-collapse:collapse!important;display:table!important;table-layout:fixed!important;width:100%!important}.recipient-card tr{display:table-row!important}.recipient-card td{border:0!important;padding:3px 10px 3px 0!important;width:50%}.recipient-card span{color:#64748b;display:block;font-size:9px;font-weight:800;text-transform:uppercase}.recipient-card strong{font-size:12px}.subject-box{border:1px solid #c9d8e7;border-radius:4px;color:#172033;font-weight:700;margin:0 0 17px;padding:9px 12px;text-align:center}.subject-box span{background:#d9a928;border-radius:3px;color:#fff;font-size:9px;letter-spacing:1px;margin-right:8px;padding:3px 7px}.salutation{margin:0 0 11px}.intro-text{margin:0 0 15px;text-align:justify}.confirmation-table{border-collapse:collapse!important;display:table!important;font-size:10.5px;margin:12px 0 18px!important;table-layout:fixed!important;width:100%!important}.confirmation-table thead{display:table-header-group!important}.confirmation-table tbody{display:table-row-group!important}.confirmation-table tr{display:table-row!important}.confirmation-table th,.confirmation-table td{border:1px solid #b8c9db!important;padding:7px 6px!important;text-align:right;vertical-align:middle}.confirmation-table thead th{background:#0b477f!important;color:#fff;font-size:9.5px;font-weight:900;text-align:center;text-transform:uppercase}.confirmation-table tbody tr:nth-child(even) td{background:#f6f9fc}.confirmation-table .sales-row td{background:#edf5ff;font-weight:900}.confirmation-table th:nth-child(1),.confirmation-table td:nth-child(1){text-align:center;width:7%}.confirmation-table th:nth-child(2),.confirmation-table td:nth-child(2){text-align:left;width:21%}.confirmation-table th:nth-child(3){width:18%}.confirmation-table th:nth-child(4){width:18%}.confirmation-table th:nth-child(5){width:16%}.confirmation-table th:nth-child(6){width:20%}.balance-card{border:1px solid #b8c9db;border-radius:4px;margin:0 0 17px;overflow:hidden}.balance-title{background:#e8f0f8;color:#0b477f;font-size:9px;font-weight:900;letter-spacing:1px;padding:6px 10px}.balance-card table{border:0!important;border-collapse:collapse!important;display:table!important;table-layout:fixed!important;width:100%!important}.balance-card tr{display:table-row!important}.balance-card td,.balance-card th{border:0!important;border-top:1px solid #dce5ee!important;padding:7px 10px!important}.balance-card td{width:65%}.balance-card th{color:#0b477f;text-align:right}.confirmation-note{background:#fff9e9;border-left:4px solid #d9a928;border-radius:3px;font-size:10.5px;margin:0 0 18px;padding:9px 12px;text-align:justify}.thanks{margin:0 0 5px}.signatures{border:0!important;border-collapse:collapse!important;display:table!important;margin:5px 0 22px!important;table-layout:fixed!important;width:100%!important}.signatures tr{display:table-row!important}.signatures td{border:0!important;padding:0 25px 0 0!important;vertical-align:bottom;width:50%}.signatures td:last-child{padding-left:35px!important;padding-right:0!important}.signature-space{height:35px}.signature-line{border-top:1px solid #334155;margin-bottom:4px;width:175px}.signatures strong,.signatures span,.signatures small{display:block}.signatures span{font-size:10px;margin-top:2px}.signatures small{color:#94a3b8;font-size:8px;margin-top:5px;text-transform:uppercase}.letter-footer{border-top:1px solid #cbd5e1;color:#64748b;display:flex;font-size:8.5px;justify-content:space-between;letter-spacing:.3px;padding-top:7px;text-transform:uppercase}@media print{.confirmation-letter{box-shadow:none;max-width:none;min-height:0;padding:0 28px 10px}.letter-top-line{margin-left:-28px;margin-right:-28px}}
</style>
