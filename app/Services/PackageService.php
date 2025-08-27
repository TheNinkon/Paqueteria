<?php

namespace App\Services;

use App\Models\Package;
use App\Models\Client;
use Illuminate\Http\Request;

class PackageService
{
    public function getPackagesWithFilters(Request $request)
    {
        $query = Package::query();

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->input('client_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('unique_code', 'like', "%{$searchTerm}%")
                  ->orWhere('shipment_id', 'like', "%{$searchTerm}%");
            });
        }

        return $query->with('client')->latest()->get();
    }

    public function identifyClientFromCode(string $uniqueCode): ?Client
    {
        foreach (Client::all() as $client) {
            if ($client->label_pattern && preg_match('/' . $client->label_pattern . '/', $uniqueCode)) {
                return $client;
            }
        }
        return null;
    }
}
