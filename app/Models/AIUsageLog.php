<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AIUsageLog extends Model
{
    /**
     * Explicit table name to avoid Laravel converting "AI" into "A_I".
     */
    protected $table = 'ai_usage_logs';
    protected $fillable = [
        'user_id',
        'tokens_used',
        'feature',
        'course_id',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
