<?php

namespace App\Http\Livewire;

use App\Models\item;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Models\salesitem;
use App\Models\invoice;
use App\Models\customerinfo;



class ItemSaleslivewire extends Component
{
   
    protected $paginationTheme = 'bootstrap';
    use WithPagination;
    public $searchTerm = '';

    public function render()
    {
        if (Auth::check()) {
            $breadcrumb = [
                'subtitle' => 'View',
                'title' => 'View Invoice Sales Details',
                'link' => 'View Invoice Sales Details'
            ];

            $cus = Salesitem::select('salesitems.*', 'items.itemsname', 'items.mrp')
            ->leftJoin('items', 'salesitems.itemid', '=', 'items.id')
            ->where('items.itemsname', 'like', '%' . $this->searchTerm . '%')
            ->orWhere('salesitems.unstockedname', 'like', '%' . $this->searchTerm . '%')
            ->orWhere('salesitems.quantity', 'like', '%' . $this->searchTerm . '%')
            ->orWhere('salesitems.invoiceid', 'like', '%' . $this->searchTerm . '%')
            ->orWhere('salesitems.date', 'like', '%' . $this->searchTerm . '%')

            ->orWhere('salesitems.price', 'like', '%' . $this->searchTerm . '%')
            // ->orWhere('salesitems.discount', 'like', '%' . $this->searchTerm . '%')
            ->orWhere('salesitems.subtotal', 'like', '%' . $this->searchTerm . '%')
            ->orderBy('salesitems.id', 'DESC')
            ->paginate(50);

            foreach ($cus as $data) {

                //this line is new start
                if ($data->invoiceid) {
                    $forcidfrominv = invoice::where('id', $data->invoiceid)->select('customerid','inv_type')->first();
                   
                    if ($forcidfrominv) {
                        $customerid = $forcidfrominv->customerid;
                        $data->inv_type = $forcidfrominv->inv_type;

                
                        // Fetch customer details from customerinfo
                        $customerInfo = customerinfo::where('id', $customerid)->select('name')->first();
                
                        if ($customerInfo) {
                            $data->customeridx = $customerid;
                            $data->customername = $customerInfo->name; // Assuming you want to add the customer name
                        }
                        }
                }
                //endofnewline


                if ($data->itemid) {
                    $item = item::where('id', $data->itemid)->select('itemsname', 'mrp', 'costprice')->first();

                    if ($item) {
                        $data->itemname = $item->itemsname;
                        $data->itemprice = $item->mrp;
                        $data->itemdlp = $item->costprice;
                    }
                }
            }

            return view('livewire.item-saleslivewire', compact('cus', 'breadcrumb'));
        }

        return redirect('/login');
    }

    public function generateTodaysalesdetailsPDF()
    {
        $cus = Salesitem::select('salesitems.*', 'items.itemsname', 'items.mrp')
        ->leftJoin('items', 'salesitems.itemid', '=', 'items.id')
        ->where('items.itemsname', 'like', '%' . $this->searchTerm . '%')
        ->orWhere('salesitems.unstockedname', 'like', '%' . $this->searchTerm . '%')
        ->orWhere('salesitems.quantity', 'like', '%' . $this->searchTerm . '%')
        ->orWhere('salesitems.invoiceid', 'like', '%' . $this->searchTerm . '%')
        ->orWhere('salesitems.date', 'like', '%' . $this->searchTerm . '%')

        ->orWhere('salesitems.price', 'like', '%' . $this->searchTerm . '%')
        // ->orWhere('salesitems.discount', 'like', '%' . $this->searchTerm . '%')
        ->orWhere('salesitems.subtotal', 'like', '%' . $this->searchTerm . '%')
        ->orderBy('salesitems.id', 'DESC')
        ->paginate(50);

        foreach ($cus as $data) {

            //this line is new start
            if ($data->invoiceid) {
                $forcidfrominv = invoice::where('id', $data->invoiceid)->select('customerid','inv_type')->first();
               
                if ($forcidfrominv) {
                    $customerid = $forcidfrominv->customerid;
                    $data->inv_type = $forcidfrominv->inv_type;

            
                    // Fetch customer details from customerinfo
                    $customerInfo = customerinfo::where('id', $customerid)->select('name')->first();
            
                    if ($customerInfo) {
                        $data->customeridx = $customerid;
                        $data->customername = $customerInfo->name; // Assuming you want to add the customer name
                    }
                    }
            }
            //endofnewline


            if ($data->itemid) {
                $item = item::where('id', $data->itemid)->select('itemsname', 'mrp', 'costprice')->first();

                if ($item) {
                    $data->itemname = $item->itemsname;
                    $data->itemprice = $item->mrp;
                    $data->itemdlp = $item->costprice;
                }
            }
    
        // Prepare the view for PDF
        $pdfView = view('allsalesdetails.todaysalesdetails', [
            'all' => $results,
        ])->render();
    
        // Generate PDF using FacadePdf
        $pdf = FacadePdf::setOptions(['dpi' => 150, 'defaultFont' => 'dejavu serif'])->loadHtml($pdfView);
    
        // Save the PDF to a temporary file
        $pdfFile = tempnam(sys_get_temp_dir(), 'TodaySalesList');
        $pdf->save($pdfFile);
    
        // Create a filename with today's date
        $todayDate = date('Y-m-d_H.i.s');
        $filename = "TodaySalesList{$todayDate}.pdf";
    
        // Send headers to instruct the browser to open the PDF in a new tab
        return response()->file($pdfFile, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "inline; filename=\"{$filename}\"",
        ]);
    }
    
}
}