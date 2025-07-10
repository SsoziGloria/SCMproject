<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class VendorController extends Controller
{
    public function index()
    {
        return view('vendors.index', ['vendors' => Vendor::all()]);
    }

    public function create()
    {
        return view('vendors.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:vendors,email',
            'company_name' => 'required',
        ]);

        Vendor::create($request->all());

        return redirect()->route('vendors.index')->with('success', 'Vendor submitted successfully!');
    }

    public function show(string $id)
    {
        return view('vendors.show', ['vendor' => Vendor::findOrFail($id)]);
    }

    public function edit(string $id)
    {
        $vendor = Vendor::findOrFail($id);
        return view('vendors.edit', compact('vendor'));
    }

    public function update(Request $request, string $id)
    {
        $vendor = Vendor::findOrFail($id);

        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:vendors,email,' . $vendor->id,
            'company_name' => 'required',
        ]);

        $vendor->update($request->all());

        return redirect()->route('vendors.index')->with('success', 'Vendor updated successfully!');
    }

    public function destroy(string $id)
    {
        $vendor = Vendor::findOrFail($id);
        $vendor->delete();

        return redirect()->route('vendors.index')->with('success', 'Vendor deleted successfully!');
    }

    /**
     * Show vendor PDF upload form.
     */
    public function showValidationForm()
    {
        return view('vendors.validate');
    }

    /**
     * Send uploaded PDF to Spring Boot for validation.
     */
    public function validateViaJava(Request $request)
    {
        $request->validate([
            'vendor_pdf' => 'required|file|mimes:pdf|max:2048',
        ]);

        try {
            $response = Http::attach(
                'file',
                $request->file('vendor_pdf')->get(),
                $request->file('vendor_pdf')->getClientOriginalName()
            )->post(env('JAVA_API_URL') . '/vendors/validate');

            if ($response->successful()) {
                $result = $response->body(); // APPROVED / REJECTED
            } else {
                $result = 'Validation failed. Status: ' . $response->status();
            }
        } catch (\Exception $e) {
            $result = "Java API error: " . $e->getMessage();
        }

        return view('vendors.result', ['result' => $result]);
    }

    /**
     * Test Java API connectivity.
     */
    public function testJavaApi()
    {
        try {
            $response = Http::get(env('JAVA_API_URL') . '/vendors/ping');
            return response()->json(['status' => $response->body()]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'Java API unreachable', 'error' => $e->getMessage()], 500);
        }
    }
}
