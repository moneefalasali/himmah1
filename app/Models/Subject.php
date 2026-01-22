<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = ['category_id', 'name', 'slug'];

    /**
     * العلاقة مع المرحلة التعليمية
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * العلاقة مع الدورات
     */
    public function courses()
    {
        return $this->hasMany(Course::class);
    }
}
