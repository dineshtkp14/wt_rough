<?xml version="1.0"?>
<?mso-application progid="Excel.Sheet"?>
<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">
<Styles>
    <Style ss:ID="Header"><Font ss:Bold="1" ss:Color="#FFFFFF"/><Interior ss:Color="#173B72" ss:Pattern="Solid"/><Alignment ss:Horizontal="Center" ss:Vertical="Center"/><Borders><Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#173B72"/></Borders></Style>
    <Style ss:ID="Total"><Font ss:Bold="1"/><Interior ss:Color="#DCEBFF" ss:Pattern="Solid"/><Borders><Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="2" ss:Color="#173B72"/></Borders></Style>
    <Style ss:ID="Money"><NumberFormat ss:Format="#,##0.00"/></Style>
    <Style ss:ID="TotalMoney"><Font ss:Bold="1"/><Interior ss:Color="#DCEBFF" ss:Pattern="Solid"/><NumberFormat ss:Format="#,##0.00"/></Style>
</Styles>

@php
    $sheets = [
        'Above 1 Lakh' => $aboveOneLakh,
        'Others' => $others,
    ];
@endphp
@foreach($sheets as $sheetName => $rows)
    <Worksheet ss:Name="{{ e($sheetName) }}">
        <Table>
            <Column ss:Width="55"/><Column ss:Width="250"/><Column ss:Width="110"/><Column ss:Width="85"/><Column ss:Width="135"/><Column ss:Width="120"/><Column ss:Width="150"/>
            <Row ss:Height="28"><Cell ss:MergeAcross="6"><Data ss:Type="String">{{ e($sheetName) }} - {{ e($firm->name) }}</Data></Cell></Row>
            <Row><Cell ss:MergeAcross="6"><Data ss:Type="String">Purchase period: {{ e($bsFrom ?: ($bsMonth ?: 'Selected date range')) }}{{ $bsTo ? ' to '.e($bsTo) : '' }}</Data></Cell></Row>
            <Row>
                <Cell ss:StyleID="Header"><Data ss:Type="String">Serial Number</Data></Cell>
                <Cell ss:StyleID="Header"><Data ss:Type="String">Party/Company Name</Data></Cell>
                <Cell ss:StyleID="Header"><Data ss:Type="String">PAN Number</Data></Cell>
                <Cell ss:StyleID="Header"><Data ss:Type="String">Number of Bills</Data></Cell>
                <Cell ss:StyleID="Header"><Data ss:Type="String">Total Taxable Amount</Data></Cell>
                <Cell ss:StyleID="Header"><Data ss:Type="String">Total VAT Amount</Data></Cell>
                <Cell ss:StyleID="Header"><Data ss:Type="String">Grand Total Including VAT</Data></Cell>
            </Row>
            @foreach($rows as $party)
                <Row>
                    <Cell><Data ss:Type="Number">{{ $loop->iteration }}</Data></Cell>
                    <Cell><Data ss:Type="String">{{ e($party['name']) }}</Data></Cell>
                    <Cell><Data ss:Type="String">{{ e($party['pan'] ?: '-') }}</Data></Cell>
                    <Cell><Data ss:Type="Number">{{ $party['bill_count'] }}</Data></Cell>
                    <Cell ss:StyleID="Money"><Data ss:Type="Number">{{ number_format($party['taxable'], 2, '.', '') }}</Data></Cell>
                    <Cell ss:StyleID="Money"><Data ss:Type="Number">{{ number_format($party['vat'], 2, '.', '') }}</Data></Cell>
                    <Cell ss:StyleID="Money"><Data ss:Type="Number">{{ number_format($party['grand'], 2, '.', '') }}</Data></Cell>
                </Row>
            @endforeach
            @php
                $sheetTaxable = $rows->sum('taxable');
                $sheetVat = $rows->sum('vat');
                $sheetGrand = $rows->sum('grand');
            @endphp
            <Row>
                <Cell ss:StyleID="Total" ss:MergeAcross="3"><Data ss:Type="String">TOTAL</Data></Cell>
                <Cell ss:StyleID="TotalMoney"><Data ss:Type="Number">{{ number_format($sheetTaxable, 2, '.', '') }}</Data></Cell>
                <Cell ss:StyleID="TotalMoney"><Data ss:Type="Number">{{ number_format($sheetVat, 2, '.', '') }}</Data></Cell>
                <Cell ss:StyleID="TotalMoney"><Data ss:Type="Number">{{ number_format($sheetGrand, 2, '.', '') }}</Data></Cell>
            </Row>
        </Table>
    </Worksheet>
@endforeach
</Workbook>
