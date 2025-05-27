<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Location;

class LineController extends Controller
{
    public function index()
    {
        $lines = Location::where('type', 'line')->get();
        return view('lines.index', compact('lines'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'geometry' => 'required|string',
        ]);

        Location::create([
            'name' => $request->name,
            'type' => 'line',
            'geometry' => $request->geometry,
        ]);

        return redirect()->route('lines.index')->with('success', 'Garis berhasil ditambahkan.');
    }

    public function destroy($id)
    {
        Location::findOrFail($id)->delete();
        return redirect()->route('lines.index')->with('success', 'Garis berhasil dihapus.');
    }
}

