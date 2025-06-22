<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class supplierviewPostController extends Controller
{
    public function supplies(){
        return view(supplier);
    }
}
