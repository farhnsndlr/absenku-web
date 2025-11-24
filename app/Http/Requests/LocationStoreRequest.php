<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LocationStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'location_name' => ['required', 'string', 'max:255'],
            // Latitude & Longitude harus format angka (decimal)
            'latitude' => ['required', 'numeric'],
            'longitude' => ['required', 'numeric'],
            // Radius harus angka bulat positif, minimal 10 meter (misal)
            'radius_meters' => ['required', 'integer', 'min:10'],
        ];
    }
}
