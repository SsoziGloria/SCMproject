<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Supplier;

class SettingController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::with('user')->get();

        $globalSupplierSetting = (bool) Setting::get('show_supplier_products', true);

        $supplierSettings = [];
        foreach ($suppliers as $supplier) {
            if ($supplier->user) {
                $supplierSettings[$supplier->id] = [
                    'name' => $supplier->user->name,
                    'visible' => (bool) Setting::get('show_supplier_products', $globalSupplierSetting, $supplier->id),
                    'is_active' => (bool) $supplier->user->is_active
                ];
            }
        }

        return view('admin.settings.index', compact('suppliers', 'globalSupplierSetting', 'supplierSettings'));
    }

    public function update(Request $request)
    {
        foreach ($request->except(['_token', '_method']) as $key => $value) {
            Setting::set($key, $value);
        }

        return redirect()->back()->with('success', 'Settings updated successfully');
    }

    public function toggleSupplierProducts(Request $request)
    {
        $supplierId = $request->input('supplier_id');

        if ($supplierId) {
            $supplier = Supplier::with('user')->findOrFail($supplierId);
            $userId = $supplier->id;

            $currentValue = (bool) Setting::get('show_supplier_products', true, $userId);
            Setting::set('show_supplier_products', !$currentValue, $userId);

            $message = !$currentValue
                ? "Products from {$supplier->user->name} are now visible"
                : "Products from {$supplier->user->name} are now hidden";
        } else {
            $currentValue = (bool) Setting::get('show_supplier_products', true);
            Setting::set('show_supplier_products', !$currentValue);
            $message = !$currentValue
                ? "All supplier products are now visible by default"
                : "All supplier products are now hidden by default";
        }

        return redirect()->back()->with('success', $message);
    }
}
