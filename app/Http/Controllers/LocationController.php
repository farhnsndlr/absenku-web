<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;
use App\Http\Requests\LocationStoreRequest;
use App\Http\Requests\LocationUpdateRequest;

class LocationController extends Controller
{
    // Menampilkan daftar lokasi.
    public function index()
    {
        $locations = Location::latest()->paginate(10);
        return view('admin.locations.index', compact('locations'));
    }

    // Menampilkan form tambah lokasi.
    public function create()
    {
        return view('admin.locations.create');
    }

    // Menyimpan lokasi baru.
    public function store(LocationStoreRequest $request)
    {
        Location::create($request->validated());

        return redirect()->route('admin.locations.index')
            ->with('success', 'Lokasi baru berhasil ditambahkan!');
    }

    // Menampilkan form edit lokasi.
    public function edit(Location $location)
    {
        return view('admin.locations.edit', compact('location'));
    }

    // Memperbarui data lokasi.
    public function update(LocationUpdateRequest $request, Location $location)
    {
        $location->update($request->validated());

        return redirect()->route('admin.locations.index')
            ->with('success', 'Data lokasi berhasil diperbarui.');
    }

    // Menghapus lokasi.
    public function destroy(Location $location)
    {
        $location->delete();

        return redirect()->route('admin.locations.index')
            ->with('success', 'Lokasi berhasil dihapus.');
    }
}
