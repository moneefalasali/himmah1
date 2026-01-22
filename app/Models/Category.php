<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug'];

    /**
     * العلاقة مع المقررات
     */
    public function subjects()
    {
        return $this->hasMany(Subject::class);
    }

    /**
     * العلاقة مع الدورات
     */
    public function courses()
    {
        return $this->hasMany(Course::class);
    }
}
