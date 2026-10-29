<?xml version="1.0"?>
<?mso-application progid="Excel.Sheet"?>
<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">
<Styles><Style ss:ID="h"><Font ss:Bold="1" ss:Color="#FFFFFF"/><Interior ss:Color="#173B72" ss:Pattern="Solid"/><Alignment ss:Horizontal="Center"/></Style><Style ss:ID="m"><NumberFormat ss:Format="#,##0.00"/></Style><Style ss:ID="t"><Font ss:Bold="1"/><Interior ss:Color="#DCEBFF" ss:Pattern="Solid"/><NumberFormat ss:Format="#,##0.00"/></Style></Styles>
@foreach([['name'=>'Above 1 Lakh','heading'=>'ABOVE 1 LAKH DATA OF HAVING PAN NO','rows'=>$above],['name'=>'Others','heading'=>'DATA HAVING NO PAN NO THAT CAN BE ABOVE 1 LAKH OR BELOW 1 LAKH','rows'=>$others]] as $sheet)
@php($rows=$sheet['rows'])
<Worksheet ss:Name="{{ e($sheet['name']) }}"><Table>
<Column ss:Width="55"/><Column ss:Width="260"/><Column ss:Width="115"/><Column ss:Width="90"/><Column ss:Width="140"/><Column ss:Width="120"/><Column ss:Width="155"/>
<Row ss:Height="28"><Cell ss:MergeAcross="6"><Data ss:Type="String">{{ e($sheet['heading']) }}</Data></Cell></Row>
<Row><Cell ss:MergeAcross="6"><Data ss:Type="String">Firm: {{ e($firm->name) }} | Period: {{ e($bsFrom ?: ($bsMonth ?: 'Selected date range')) }}{{ $bsTo ? ' to '.e($bsTo) : '' }}</Data></Cell></Row>
<Row><Cell ss:StyleID="h"><Data ss:Type="String">Serial Number</Data></Cell><Cell ss:StyleID="h"><Data ss:Type="String">Party/Customer Name</Data></Cell><Cell ss:StyleID="h"><Data ss:Type="String">PAN Number</Data></Cell><Cell ss:StyleID="h"><Data ss:Type="String">Number of Bills</Data></Cell><Cell ss:StyleID="h"><Data ss:Type="String">Total Taxable Amount</Data></Cell><Cell ss:StyleID="h"><Data ss:Type="String">Total VAT Amount</Data></Cell><Cell ss:StyleID="h"><Data ss:Type="String">Grand Total Including VAT</Data></Cell></Row>
@foreach($rows as $party)
<Row><Cell><Data ss:Type="Number">{{ $loop->iteration }}</Data></Cell><Cell><Data ss:Type="String">{{ e($party['name']) }}</Data></Cell><Cell><Data ss:Type="String">{{ e($party['pan'] ?: '-') }}</Data></Cell><Cell><Data ss:Type="Number">{{ $party['bills'] }}</Data></Cell><Cell ss:StyleID="m"><Data ss:Type="Number">{{ number_format($party['taxable'],2,'.','') }}</Data></Cell><Cell ss:StyleID="m"><Data ss:Type="Number">{{ number_format($party['vat'],2,'.','') }}</Data></Cell><Cell ss:StyleID="m"><Data ss:Type="Number">{{ number_format($party['grand'],2,'.','') }}</Data></Cell></Row>
@endforeach
@php
    $taxable = $rows->sum('taxable');
    $vat = $rows->sum('vat');
    $grand = $rows->sum('grand');
@endphp
<Row><Cell ss:MergeAcross="3"><Data ss:Type="String">TOTAL</Data></Cell><Cell ss:StyleID="t"><Data ss:Type="Number">{{ number_format($taxable,2,'.','') }}</Data></Cell><Cell ss:StyleID="t"><Data ss:Type="Number">{{ number_format($vat,2,'.','') }}</Data></Cell><Cell ss:StyleID="t"><Data ss:Type="Number">{{ number_format($grand,2,'.','') }}</Data></Cell></Row>
</Table></Worksheet>
@endforeach
</Workbook>
