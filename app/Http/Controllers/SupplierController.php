<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use App\Models\Inventory;


class SupplierController extends Controller
{
    public function index()
    {
        $supplierCount = Supplier::count();
        $suppliers = Supplier::all();
        return view('suppliers.index', compact('suppliers','supplierCount'));
    }
    public function create(){

    }
    public function store(Request $request){

}
public function show($id){

public function approved()
{
    $suppliers = Supplier::all();
    return view('supplier.approved', compact('suppliers'));
}

public function requests()
{
    return view('supplier.requests');
}

public function orders()
{
    return view('supplier.orders');
}

public function messages()
{
    return view('supplier.messages');
}
}
public function edit($id){}
}