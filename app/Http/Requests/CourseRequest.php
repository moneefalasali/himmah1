<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CourseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // Authorization will be handled by Policies in the Controller, 
        // but for FormRequest we can allow it if the user is authenticated.
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'instructor_name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:active,inactive',
            'duration' => 'nullable|integer|min:0',
            'course_size' => 'required|in:normal,large',
            'includes_summary' => 'boolean',
            'includes_tajmeeat' => 'boolean',
            
            // قواعد التحقق لنظام التصنيف الموحد
            'category_id' => 'required|exists:categories,id',
            'type' => ['required', Rule::in(['recorded', 'online'])],
            'subject_id' => [
                'required',
                'exists:subjects,id',
                // التحقق المتقاطع: يجب أن يكون المقرر تابعاً للمرحلة المختارة
                Rule::exists('subjects', 'id')->where(function ($query) {
                    $query->where('category_id', $this->category_id);
                }),
            ],
        ];

        // For update, image is nullable, for create it might be required depending on your UI
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['image'] = 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048';
        }

        return $rules;
    }

    /**
     * Get custom validation messages.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'category_id.required' => 'يجب اختيار المرحلة التعليمية.',
            'category_id.exists' => 'المرحلة التعليمية المختارة غير صالحة.',
            'type.required' => 'يجب تحديد نوع الدورة (مسجل/أونلاين).',
            'type.in' => 'نوع الدورة غير صالح.',
            'subject_id.required' => 'يجب اختيار المقرر الدراسي.',
            'subject_id.exists' => 'المقرر الدراسي غير صالح أو لا ينتمي للمرحلة التعليمية المختارة.',
        ];
    }
}
