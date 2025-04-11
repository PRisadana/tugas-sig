<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Location;

class LocationController extends Controller
{
    public function index()
    {
        $locations = Location::all();
        return view('map', compact('locations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        Location::create($request->all());
        return redirect()->route('locations.index')->with('success', 'Lokasi ditambahkan!');
    }

    public function edit(Location $location)
    {
        $locations = Location::all();
        return view('map', compact('location', 'locations'));
    }

    public function update(Request $request, Location $location)
    {
        $request->validate([
            'name' => 'required',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $location->update($request->all());
        return redirect()->route('locations.index')->with('success', 'Lokasi diperbarui!');
    }

    public function destroy(Location $location)
    {
        $location->delete();
        return redirect()->route('locations.index')->with('success', 'Lokasi dihapus!');
    }
}
