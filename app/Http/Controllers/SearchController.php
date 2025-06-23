<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User; // or your searchable model

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('query');
        $results = [];

        if ($query) {
            $results = User::where('name', 'like', "%{$query}%")
                ->orWhere('email', 'like', "%{$query}%")
                ->get();
        }

        return view('search.results', compact('results', 'query'));
    }
}