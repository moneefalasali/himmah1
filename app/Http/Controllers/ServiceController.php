<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ServiceType;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class ServiceController extends Controller
{
    public function index()
    {
        // عرض الخدمات المتاحة فقط
        // If the `is_active` column is missing (older DB), fall back to returning all service types
        try {
            if (Schema::hasColumn('service_types', 'is_active')) {
                $services = ServiceType::where('is_active', true)->get();
            } else {
                Log::warning('service_types.is_active column missing; returning all service types');
                $services = ServiceType::all();
            }
        } catch (\Exception $e) {
            // In case of any DB/schema issues, log and return an empty collection to avoid a hard exception for users
            Log::error('Error retrieving service types: ' . $e->getMessage());
            $services = collect();
        }

        return view('services.index', compact('services'));
    }
}
