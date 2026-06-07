<?php

namespace App\Http\Controllers;

use App\Models\customerinfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerAPI extends Controller
{
    private function normalizeSearchText($value): string
    {
        return strtolower(preg_replace('/[^a-z0-9]+/i', '', (string) $value));
    }

    private function fuzzyTermMatches($term, $text): bool
    {
        $term = $this->normalizeSearchText($term);
        $tokens = collect(preg_split('/\s+/', (string) $text))
            ->map(fn ($token) => $this->normalizeSearchText($token))
            ->filter();

        if ($term === '' || $tokens->isEmpty()) {
            return false;
        }

        foreach ($tokens as $token) {
            if ($token === '' || str_contains($token, $term) || str_contains($term, $token)) {
                return true;
            }

            $distance = levenshtein($term, $token);
            $allowedDistance = strlen($term) <= 5 ? 2 : 3;

            if ($distance <= $allowedDistance) {
                return true;
            }
        }

        return false;
    }

    private function fuzzyCustomerScore($customer, $terms): int
    {
        $haystack = trim(($customer->name ?? '') . ' ' . ($customer->address ?? ''));
        $score = 0;

        foreach ($terms as $term) {
            $term = $this->normalizeSearchText($term);
            $bestDistance = 99;

            foreach (preg_split('/\s+/', $haystack) as $token) {
                $token = $this->normalizeSearchText($token);
                if ($token === '') {
                    continue;
                }

                if (str_contains($token, $term) || str_contains($term, $token)) {
                    $bestDistance = 0;
                    break;
                }

                $bestDistance = min($bestDistance, levenshtein($term, $token));
            }

            $score += $bestDistance;
        }

        return $score;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $req)
    {
        $search = trim((string) $req->name);
        $terms = collect(preg_split('/\s+/', $search))
            ->filter()
            ->values();

        $cus = customerinfo::query()
            ->when($terms->isNotEmpty(), function ($query) use ($terms) {
                foreach ($terms as $term) {
                    $query->where(function ($termQuery) use ($term) {
                        $like = '%'.$term.'%';

                        if (preg_match('/\d/', $term)) {
                            $termQuery->where('phoneno', 'LIKE', $like)
                                ->orWhere('alternate_phoneno', 'LIKE', $like);
                        } else {
                            $termQuery->where('name', 'LIKE', $like)
                                ->orWhere('address', 'LIKE', $like);
                        }
                    });
                }
            })
            ->when($search !== '', function ($query) use ($search) {
                $query->orderByRaw(
                    'CASE
                        WHEN name LIKE ? THEN 0
                        WHEN name LIKE ? THEN 1
                        WHEN phoneno LIKE ? THEN 2
                        WHEN alternate_phoneno LIKE ? THEN 3
                        WHEN address LIKE ? THEN 4
                        ELSE 5
                    END',
                    [
                        $search.'%',
                        '%'.$search.'%',
                        '%'.$search.'%',
                        '%'.$search.'%',
                        '%'.$search.'%',
                    ]
                );
            })
            ->when($terms->isNotEmpty(), function ($query) use ($terms) {
                foreach ($terms as $term) {
                    $query->orderByRaw(
                        'CASE
                            WHEN name LIKE ? THEN 0
                            WHEN name LIKE ? THEN 1
                            ELSE 2
                        END',
                        [$term.'%', '%'.$term.'%']
                    );
                }
            })
            ->orderBy('name')
            ->limit(20)
            ->get();

        if ($cus->isEmpty() && $terms->isNotEmpty() && !$terms->contains(fn ($term) => preg_match('/\d/', $term))) {
            $firstTerm = $this->normalizeSearchText($terms->first());
            $firstLetter = substr($firstTerm, 0, 1);

            $fallbackCustomers = customerinfo::query()
                ->where(function ($query) use ($firstLetter) {
                    $query->where('name', 'LIKE', $firstLetter.'%')
                        ->orWhere('name', 'LIKE', '% '.$firstLetter.'%')
                        ->orWhere('address', 'LIKE', $firstLetter.'%')
                        ->orWhere('address', 'LIKE', '% '.$firstLetter.'%');
                })
                ->limit(250)
                ->get();

            $cus = $fallbackCustomers
                ->filter(function ($customer) use ($terms) {
                    return $terms->every(function ($term) use ($customer) {
                        return $this->fuzzyTermMatches($term, ($customer->name ?? '') . ' ' . ($customer->address ?? ''));
                    });
                })
                ->sortBy(function ($customer) use ($terms) {
                    return $this->fuzzyCustomerScore($customer, $terms);
                })
                ->take(20)
                ->values();
        }

        $customerIds = $cus->pluck('id');

        $ledgerDue = collect();
        $creditNoteCredit = collect();

        if ($customerIds->isNotEmpty()) {
            $ledgerDue = DB::table('customerledgerdetails')
                ->select('customerid', DB::raw('SUM(COALESCE(debit, 0) - COALESCE(credit, 0)) as due'))
                ->whereIn('customerid', $customerIds)
                ->whereIn('invoicetype', ['credit', 'payment', 'settlement'])
                ->groupBy('customerid')
                ->pluck('due', 'customerid');

            $creditNoteCredit = DB::table('creditnotes_customerledgerdetails as cn')
                ->select('cn.customerid', DB::raw('SUM(COALESCE(cn.debit, cn.credit, 0)) as credit'))
                ->whereIn('cn.customerid', $customerIds)
                ->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('customerledgerdetails as oldcl')
                        ->whereColumn('oldcl.customerid', 'cn.customerid')
                        ->where(function ($match) {
                            $match->whereColumn('oldcl.cninvoiceid', 'cn.invoiceid')
                                ->orWhereColumn('oldcl.returnidforcreditnotes', 'cn.invoiceid')
                                ->orWhere(function ($sameReturn) {
                                    $sameReturn->whereColumn('oldcl.date', 'cn.date')
                                        ->where(function ($returnType) {
                                            $returnType->whereIn('oldcl.particulars', ['salesreturn', 'Goods_Return'])
                                                ->orWhereIn('oldcl.voucher_type', ['return', 'Return']);
                                        })
                                        ->whereRaw('oldcl.credit BETWEEN COALESCE(cn.debit, cn.credit, 0) - 0.01 AND COALESCE(cn.debit, cn.credit, 0) + 0.01');
                                });
                        });
                })
                ->groupBy('cn.customerid')
                ->pluck('credit', 'customerid');
        }

        $cus->transform(function ($customer) use ($ledgerDue, $creditNoteCredit) {
            $due = (float) ($ledgerDue[$customer->id] ?? 0) - (float) ($creditNoteCredit[$customer->id] ?? 0);
            $customer->total_due = max(0, $due);
            $customer->total_due_formatted = number_format($customer->total_due, 2);

            return $customer;
        });

        return response()->json($cus);
      



    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
