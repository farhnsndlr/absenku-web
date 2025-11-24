<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;
// Import Request Validation
use App\Http\Requests\LocationStoreRequest;
use App\Http\Requests\LocationUpdateRequest;

class LocationController extends Controller
{
    // ============================================================
    // 1. INDEX (Menampilkan Daftar Lokasi)
    // ============================================================
    public function index()
    {
        // Ambil semua data lokasi dengan pagination
        $locations = Location::latest()->paginate(10);
        return view('admin.locations.index', compact('locations'));
    }

    // ============================================================
    // 2. CREATE (Menampilkan Form Tambah)
    // ============================================================
    public function create()
    {
        return view('admin.locations.create');
    }

    // ============================================================
    // 3. STORE (Memproses Simpan Data Baru)
    // ============================================================
    public function store(LocationStoreRequest $request)
    {
        // Data sudah divalidasi dan siap disimpan
        Location::create($request->validated());

        return redirect()->route('admin.locations.index')
            ->with('success', 'Lokasi baru berhasil ditambahkan!');
    }

    // ============================================================
    // 4. EDIT (Menampilkan Form Edit)
    // ============================================================
    public function edit(Location $location)
    {
        return view('admin.locations.edit', compact('location'));
    }

    // ============================================================
    // 5. UPDATE (Memproses Perubahan Data)
    // ============================================================
    public function update(LocationUpdateRequest $request, Location $location)
    {
        // Data sudah divalidasi
        $location->update($request->validated());

        return redirect()->route('admin.locations.index')
            ->with('success', 'Data lokasi berhasil diperbarui.');
    }

    // ============================================================
    // 6. DESTROY (Menghapus Data)
    // ============================================================
    public function destroy(Location $location)
    {
        // Peringatan: Hapus lokasi bisa menyebabkan sesi presensi yang terkait
        // menjadi bermasalah jika tidak ditangani oleh onDelete('set null') di migrasi.
        $location->delete();

        return redirect()->route('admin.locations.index')
            ->with('success', 'Lokasi berhasil dihapus.');
    }
}
