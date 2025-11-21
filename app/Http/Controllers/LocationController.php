<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    // Tampilkan tabel daftar lokasi
    public function index()
    {
        $locations = Location::latest()->get();
        return view('admin.locations.index', compact('locations'));
    }

    // Tampilkan form tambah lokasi
    public function create()
    {
        return view('admin.locations.create');
    }

    // Simpan data lokasi baru
    public function store(Request $request)
    {
        $request->validate([
            'location_name' => 'required|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius_meters' => 'required|integer|min:10',
        ]);

        Location::create($request->all());

        return redirect()->route('admin.locations.index')
            ->with('success', 'Lokasi baru berhasil ditambahkan!');
    }

    // (Opsional) Method edit, update, destroy bisa ditambahkan nanti
}
