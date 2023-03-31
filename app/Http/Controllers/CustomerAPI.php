<?php

namespace App\Http\Controllers;

use App\Models\customerinfo;
use Illuminate\Http\Request;

class CustomerAPI extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $req)
    {

        $cus = customerinfo::where('name', 'LIKE', '%'.$req->name.'%')->orWhere('email', 'LIKE', '%'.$req->name.'%')->orWhere('phoneno', 'LIKE', '%'.$req->name.'%')->get();
        return json_encode($cus);
      



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
