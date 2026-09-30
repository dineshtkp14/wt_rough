<?php

namespace App\Http\Controllers;

use App\Models\item;
use App\Models\pricelist;
use Illuminate\Http\Request;

class ItemsSearchAPI extends Controller
{
    public function index(Request $req)
    {
        $items=null;
        //old data

        // $items = Item::where('itemsname', 'LIKE', '%'.$req->name.'%')
        // ->orWhere('id', 'LIKE', '%'.$req->name.'%')
        // ->where('quantity', '>', 0)
        // ->get();


//also you van check all and limited for itemsales and creditnotes
        $quantity_case = $req->input('quantity');

        $search = trim((string) $req->name);
        $normalizedSearch = preg_replace('/[^\pL\pN.]+/u', ' ', mb_strtolower($search));
        $terms = collect(preg_split('/\s+/', trim($normalizedSearch), -1, PREG_SPLIT_NO_EMPTY))
            ->map(fn ($term) => trim($term, '.'))
            ->filter(fn ($term) => $term !== '')
            ->unique()
            ->values();

        $matchesAllTerms = function ($query) use ($terms) {
            foreach ($terms as $term) {
                $query->where(function ($part) use ($term) {
                    $part->whereRaw('LOWER(itemsname) LIKE ?', ['%' . $term . '%'])
                        ->orWhere('id', 'LIKE', '%' . $term . '%');
                });
            }
        };

        if ($quantity_case == 'all') {
            $items = Item::where($matchesAllTerms)->get();
        } else {
            $items = Item::where($matchesAllTerms)
                        ->where('quantity', '>', 0)
                        ->get();
        }

        $priceListByName = pricelist::query()
            ->whereIn('itemname', $items->pluck('itemsname')->filter()->unique()->values())
            ->latest('id')
            ->get()
            ->unique('itemname')
            ->keyBy('itemname');

        $items->transform(function ($item) use ($priceListByName) {
            $priceList = $priceListByName->get($item->itemsname);

            $item->source_type = 'stock';
            $item->list_price = $priceList ? (float) $priceList->saleprice : null;
            $item->price_list_saleprice = $priceList ? (float) $priceList->saleprice : null;
            $item->price_list_wholesaleprice = $priceList ? (float) $priceList->wholesaleprice : null;

            return $item;
        });

        $priceListItems = pricelist::query()
            ->where(function ($query) use ($terms) {
                foreach ($terms as $term) {
                    $query->whereRaw('LOWER(itemname) LIKE ?', ['%' . $term . '%']);
                }
            })
            ->latest('id')
            ->get()
            ->unique('itemname')
            ->map(function ($priceList) {
                return [
                    'id' => 'price-list-'.$priceList->id,
                    'source_type' => 'price_list',
                    'price_list_id' => $priceList->id,
                    'itemsname' => $priceList->itemname,
                    'quantity' => '-',
                    'unit' => $priceList->unit ?: '',
                    'mrp' => (float) $priceList->saleprice,
                    'list_price' => (float) $priceList->saleprice,
                    'price_list_saleprice' => (float) $priceList->saleprice,
                    'price_list_wholesaleprice' => (float) $priceList->wholesaleprice,
                    'note' => $priceList->note,
                ];
            })
            ->values();

        $items = $items->concat($priceListItems)->values();
        
        
        return response()->json($items);
        
        
        

         
       
 


        
        
        

      



    }

}
