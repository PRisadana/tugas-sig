<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Location;

class PolygonController extends Controller
{
    public function index()
    {
        $polygons = Location::where('type', 'polygon')->get();
        return view('polygons.index', compact('polygons'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'geometry' => 'required|string',
        ]);

        if ($request->filled('id')) {
            $polygon = Location::findOrFail($request->id);
            $polygon->update([
                'name' => $request->name,
                'geometry' => $request->geometry
            ]);
            return redirect()->route('polygons.index')->with('success', 'Polygon berhasil diperbarui.');
        } else {
            Location::create([
                'name' => $request->name,
                'type' => 'polygon',
                'geometry' => $request->geometry
            ]);
            return redirect()->route('polygons.index')->with('success', 'Polygon berhasil ditambahkan.');
        }
    }

    public function destroy($id)
    {
        Location::findOrFail($id)->delete();
        return redirect()->route('polygons.index')->with('success', 'Polygon berhasil dihapus.');
    }
}

