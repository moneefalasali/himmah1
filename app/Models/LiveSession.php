<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class LiveSession extends Model
{
    protected $fillable = [
        'course_id',
        'teacher_id',
        'topic',
        'meeting_id',
        'start_url',
        'join_url',
        'start_time',
        'duration',
        'status',
        'recording_path',
        'hls_path',
    ];

    protected $casts = [
        'start_time' => 'datetime',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * التحقق مما إذا كانت الجلسة متاحة حالياً
     */
    public function isLive()
    {
        $now = Carbon::now();
        $end = $this->start_time->copy()->addMinutes($this->duration);
        
        return $now->between($this->start_time->subMinutes(10), $end);
    }

    /**
     * التحقق مما إذا كانت الجلسة قد انتهت
     */
    public function isFinished()
    {
        return Carbon::now()->isAfter($this->start_time->copy()->addMinutes($this->duration));
    }
}
