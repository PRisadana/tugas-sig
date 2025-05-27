<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Location;

class PointController extends Controller
{
    public function index()
    {
        $points = Location::where('type', 'point')->get();
        return view('points.index', compact('points'));
    }

    public function create()
    {
        return view('points.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric'
        ]);

        Location::create([
            'name' => $request->name,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'type' => 'point'
        ]);

        return redirect()->route('points.index')->with('success', 'Titik berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $point = Location::findOrFail($id);
        return view('points.edit', compact('point'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric'
        ]);

        $point = Location::findOrFail($id);
        $point->update($request->only('name', 'latitude', 'longitude'));

        return redirect()->route('points.index')->with('success', 'Titik berhasil diperbarui.');
    }

    public function destroy($id)
    {
        Location::findOrFail($id)->delete();
        return redirect()->route('points.index')->with('success', 'Titik berhasil dihapus.');
    }
}
