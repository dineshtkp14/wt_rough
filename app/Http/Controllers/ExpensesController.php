<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use App\Models\Expense;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpensesController extends Controller
{
    public function index()
    {
       
        $breadcrumb = [
            'subtitle' => 'view',
            'title' => 'view Expenses table',
            'link' => 'view  Expenses table'
        ];

            return view('Expense.list', ['breadcrumb' => $breadcrumb]);
        }

       
    

    public function create()
    {
        if (Auth::check()) {
            $breadcrumb = [
                'subtitle' => 'Add',
                'title' => 'Add Expense',
                'link' => 'Add Expense'
            ];

            return view('Expense.create', ['breadcrumb' => $breadcrumb]);
        }

        return redirect('/login');
    }

    public function store(Request $request)
    {
        if (Auth::check()) {
            $validator = Validator::make($request->all(), [
                'date' => 'required|date',
                'particulars' => 'required|string',
                'billno' => 'nullable|string',
                'amount' => 'required|numeric|min:0',
                'notes' => 'nullable|string',
            ]);

            if ($validator->passes()) {
                $expense = new Expense();
                $expense->date = $request->date;
                $expense->particulars = $request->particulars;
                $expense->billno = $request->billno;
                $expense->amount = $request->amount;
                $expense->notes = $request->notes;
                $expense->save();

                return redirect()->route('expenses.index')->with('success', 'Expense added successfully');
            } else {
                return redirect()->route('expenses.create')->withErrors($validator)->withInput();
            }
        }

        return redirect('/login');
    }

    public function edit($id)
    {
        if (Auth::check()) {
            $breadcrumb = [
                'subtitle' => 'Edit',
                'title' => 'Edit Expense',
                'link' => 'Edit Expense'
            ];
            $expense = Expense::findOrFail($id);

            return view('Expense.edit', ['expense' => $expense, 'breadcrumb' => $breadcrumb]);
        }

        return redirect('/login');
    }

    public function update(Request $request, $id)
    {
        if (Auth::check()) {
            $validator = Validator::make($request->all(), [
                'date' => 'required|date',
                'particulars' => 'required|string',
                'billno' => 'nullable|string',
                'amount' => 'required|numeric|min:0',
                'notes' => 'nullable|string',
            ]);

            if ($validator->passes()) {
                $expense = Expense::findOrFail($id);
                $expense->date = $request->date;
                $expense->particulars = $request->particulars;
                $expense->billno = $request->billno;
                $expense->amount = $request->amount;
                $expense->notes = $request->notes;
                $expense->save();

                return redirect()->route('expenses.index')->with('success', 'Expense updated successfully');
            } else {
                return redirect()->route('expenses.edit', $id)->withErrors($validator)->withInput();
            }
        }

        return redirect('/login');
    }

    public function destroy($id)
    {
        if (Auth::check()) {
            $expense = Expense::findOrFail($id);
            $expense->delete();

            return redirect()->route('expenses.index')->with('success', 'Expense deleted successfully');
        }

        return redirect('/login');
    
}


public function search(Request $request)
{
    $breadcrumb = [
        'subtitle' => 'search',
        'title' => 'Search Expense by Date',
        'link' => 'Search Expense by Date'
    ];

    // Validate the input
    $request->validate([
        'start_date' => 'nullable|date',
        'end_date' => 'nullable|date|after_or_equal:start_date',
    ]);

    // Get start and end dates from the request
    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');

    // Query expenses within the date range
    $expenses = Expense::query();

    if ($startDate) {
        $expenses->whereDate('date', '>=', $startDate);
    }

    if ($endDate) {
        $expenses->whereDate('date', '<=', $endDate);
    }

    // Calculate total sum of expenses within the date range
    $totalSum = $expenses->sum('amount');

    $expenses = $expenses->orderBy('date')->paginate(10);

    return view('Expense.viewtotalsumbydate', compact('expenses', 'breadcrumb', 'totalSum'));
}
}