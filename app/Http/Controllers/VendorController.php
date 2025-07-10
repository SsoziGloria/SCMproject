<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class VendorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('vendors.index', ['vendors' => Vendor::all()]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('vendors.create');
    }

    /**
     * Store a newly created resource in storage.
     */
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

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return view('vendors.show', ['vendor' => Vendor::findOrFail($id)]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $vendor = Vendor::findOrFail($id);
        return view('vendors.edit', compact('vendor'));
    }

    /**
     * Update the specified resource in storage.
     */
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $vendor = Vendor::findOrFail($id);
        $vendor->delete();

        return redirect()->route('vendors.index')->with('success', 'Vendor deleted successfully!');
    }

    /**
     * Show the form to upload vendor PDF.
     */
    public function showValidationForm()
    {
        return view('vendors.validate');
    }

    /**
     * Handle PDF upload and validate via Java API.
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

            $result = $response->body();
        } catch (\Exception $e) {
            $result = "Error connecting to Java API: " . $e->getMessage();
        }

        return view('vendors.result', ['result' => $result]);
    }

    /**
     * Test if the Java API is online.
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
