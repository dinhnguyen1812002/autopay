<?php

namespace App\Http\Controllers\Agency;

use App\Data\AgencyData;
use App\Http\Controllers\Controller;
use App\Models\Agency\Agency;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class AgencyController extends Controller
{
    public function index()
    {
        $agencies = Agency::all();

        return response()->json([
            "message" => 'Agencies retrieved successfully.',
            'data' => $agencies
        ]);
    }

    public function store(AgencyData $agencyData)
    {
        $logoPath = null;
        if ($agencyData->logo) {
            $path = $agencyData->logo->store('brands', 'public');
            $logoPath = Storage::url($path);
        }

        $agency = Agency::create([
            'name' => $agencyData->name,
            'email' => $agencyData->email,
            'phone' => $agencyData->phone,
            'address' => $agencyData->address,
            'logo_url' => $logoPath,
            'website_url' => $agencyData->websiteUrl,
            'support_email' => $agencyData->supportEmail,
            'custom_domain' => $agencyData->customDomain,
            'is_active' => true
        ]);

        return response()->json([
            'message' => 'Agency added successfully.',
            'data' => $agency

        ], Response::HTTP_CREATED);
    }

    public function show(string $agencyId)
    {
        $agency = Agency::findOrFail($agencyId);
        return response()->json([
            'message' => 'Agency detail retrieved successfully.',
            'data' => $agency

        ], Response::HTTP_OK);
    }

    public function update(AgencyData $agencyData, string $id)
    {
        $agency = Agency::findOrFail($id);


        if ($agencyData->logo && $agency->logoUrl) {

            $oldPath = str_replace('/storage/', '', parse_url($agency->logoUrl, PHP_URL_PATH));
            Storage::disk('public')->delete($oldPath);
        }

        $logoUrl = $agency->logoUrl;

        if ($agencyData->logo) {
            $path = $agencyData->logo->store('logos', 'public');
            $logoUrl = Storage::url($path);
        }

        $agency->update([
            'name' => $agencyData->name,
            'email' => $agencyData->email,
            'phone' => $agencyData->phone,
            'address' => $agencyData->address,
            'logo_url' => $logoUrl,
            'website_url' => $agencyData->websiteUrl,
            'support_email' => $agencyData->supportEmail,
            'custom_domain' => $agencyData->customDomain,
            'is_active' => $agencyData->isActive
        ]);

        return response()->json([
            'message' => 'Agency updated successfully.',
            'data' => $agency

        ]);
    }
    public function destroy(string $id)
    {
        $agency = Agency::findOrFail($id);

        // Delete logo file if it exists
        if ($agency->logoUrl) {
            $path = str_replace('/storage/', '', parse_url($agency->logoUrl, PHP_URL_PATH));
            Storage::disk('public')->delete($path);
        }

        $agency->delete();

        return response()->json([
            'message' => 'Agency deleted successfully.'
        ]);
    }


}
