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

}
public function edit($id){}
}