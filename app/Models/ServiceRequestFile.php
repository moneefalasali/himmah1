<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ServiceRequestFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_request_id',
        'file_name',
        'file_path',
        'file_type',
        'uploaded_by',
    ];

    /**
     * Get the service request that owns this file.
     */
    public function serviceRequest()
    {
        return $this->belongsTo(ServiceRequest::class);
    }

    /**
     * Get the file size in human readable format.
     */
    public function getFileSizeAttribute()
    {
        if (Storage::exists($this->file_path)) {
            $bytes = Storage::size($this->file_path);
            return $this->formatBytes($bytes);
        }
        return 'غير معروف';
    }

    /**
     * Get the file URL.
     */
    public function getFileUrlAttribute()
    {
        return Storage::url($this->file_path);
    }

    /**
     * Check if file is an image.
     */
    public function isImage()
    {
        $imageTypes = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'webp'];
        $extension = strtolower(pathinfo($this->file_name, PATHINFO_EXTENSION));
        return in_array($extension, $imageTypes);
    }

    /**
     * Check if file is a document.
     */
    public function isDocument()
    {
        $documentTypes = ['pdf', 'doc', 'docx', 'txt', 'rtf', 'odt'];
        $extension = strtolower(pathinfo($this->file_name, PATHINFO_EXTENSION));
        return in_array($extension, $documentTypes);
    }

    /**
     * Get file icon based on type.
     */
    public function getFileIconAttribute()
    {
        $extension = strtolower(pathinfo($this->file_name, PATHINFO_EXTENSION));
        
        return match($extension) {
            'pdf' => 'fas fa-file-pdf text-danger',
            'doc', 'docx' => 'fas fa-file-word text-primary',
            'xls', 'xlsx' => 'fas fa-file-excel text-success',
            'ppt', 'pptx' => 'fas fa-file-powerpoint text-warning',
            'jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'webp' => 'fas fa-file-image text-info',
            'zip', 'rar', '7z' => 'fas fa-file-archive text-secondary',
            'mp4', 'avi', 'mov', 'wmv' => 'fas fa-file-video text-purple',
            'mp3', 'wav', 'ogg' => 'fas fa-file-audio text-success',
            default => 'fas fa-file text-muted'
        };
    }

    /**
     * Format bytes to human readable format.
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}

