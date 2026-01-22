<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Builder;

class CourseFilterService
{
    /**
     * فلترة الدورات بناءً على المعايير المتسلسلة
     */
    public function filter(array $params)
    {
        $query = Course::published()
            ->with(['category', 'subject', 'teacher']);

        // 1. الفلترة حسب المرحلة التعليمية
        if (!empty($params['category_id'])) {
            $query->where('category_id', $params['category_id']);
        }

        // 2. الفلترة حسب نوع الدورة (مسجل / أونلاين)
        if (!empty($params['type'])) {
            $query->where('type', $params['type']);
        }

        // 3. الفلترة حسب المقرر / التخصص
        if (!empty($params['subject_id'])) {
            $query->where('subject_id', $params['subject_id']);
        }

        // 4. البحث النصي (الاسم أو المقرر)
        if (!empty($params['search'])) {
            $searchTerm = '%' . $params['search'] . '%';
            $query->where(function (Builder $q) use ($searchTerm) {
                $q->where('title', 'like', $searchTerm)
                  ->orWhereHas('subject', function ($sq) use ($searchTerm) {
                      $sq->where('name', 'like', $searchTerm);
                  });
            });
        }

        return $query->latest()->paginate(12);
    }

    /**
     * الحصول على المقررات المرتبطة بمرحلة معينة
     */
    public function getSubjectsByCategory($categoryId)
    {
        if (!$categoryId) return collect();
        
        return Subject::where('category_id', $categoryId)
            ->orderBy('name')
            ->get();
    }
}
