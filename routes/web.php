<?php

use App\Http\Controllers\SupplierController;

Route::get('/', [SupplierController::class, 'index'])->name('suppliers.index');