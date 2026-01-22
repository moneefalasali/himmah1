<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Get the service requests for this service type.
     */
    public function serviceRequests()
    {
        return $this->hasMany(ServiceRequest::class);
    }

    /**
     * Get active service requests count.
     */
    public function activeRequestsCount()
    {
        return $this->serviceRequests()
            ->whereIn('status', ['pending', 'in_progress'])
            ->count();
    }

    /**
     * Get completed service requests count.
     */
    public function completedRequestsCount()
    {
        return $this->serviceRequests()
            ->where('status', 'completed')
            ->count();
    }
}

