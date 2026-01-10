<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class AttendanceMediaController extends Controller
{
    // Menyajikan media presensi secara aman.
    public function show(Request $request, string $path): Response
    {
        $path = trim($path, '/');

        $allowedPrefixes = [
            'attendance_proofs/',
            'attendance_supporting_documents/',
            'attendance_photos/',
        ];

        $isAllowed = false;
        foreach ($allowedPrefixes as $prefix) {
            if (str_starts_with($path, $prefix)) {
                $isAllowed = true;
                break;
            }
        }

        if (!$isAllowed || !Storage::disk('public')->exists($path)) {
            abort(404);
        }

        $fullPath = Storage::disk('public')->path($path);
        return response()->file($fullPath);
    }
}
