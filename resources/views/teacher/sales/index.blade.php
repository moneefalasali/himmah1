@extends('layouts.teacher')

@section('title', 'المبيعات والأرباح')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white p-6 rounded-lg shadow-sm border-b-4 border-indigo-500">
        <div class="text-gray-500 text-sm">إجمالي المبيعات</div>
        <div class="text-2xl font-bold text-gray-800">${{ number_format($stats['total_revenue'], 2) }}</div>
    </div>
    <div class="bg-white p-6 rounded-lg shadow-sm border-b-4 border-green-500">
        <div class="text-gray-500 text-sm">أرباحك (40%)</div>
        <div class="text-2xl font-bold text-gray-800">${{ number_format($stats['teacher_earnings'], 2) }}</div>
        @if(!empty($stats['manual_estimated_teacher_earnings']) && $stats['manual_estimated_teacher_earnings'] > 0)
            <div class="text-xs text-gray-500">الأرقام تشمل أرباحًا مُقدّرة للطلاب المضافين يدويًا.</div>
        @endif
    </div>
    <div class="bg-white p-6 rounded-lg shadow-sm border-b-4 border-blue-500">
        <div class="text-gray-500 text-sm">عدد العمليات</div>
        <div class="text-2xl font-bold text-gray-800">{{ $stats['total_sales_count'] }}</div>
    </div>
</div>

<div class="bg-white rounded-lg shadow-sm overflow-hidden mb-8">
    <div class="p-6 border-b border-gray-100">
        <h3 class="font-bold text-gray-800">الأرباح حسب الدورة</h3>
    </div>
    @if($salesByCourse->isEmpty())
        <div class="p-6 text-sm text-gray-600">لا توجد مبيعات بعد. عدد الطلاب المشتركين: {{ $stats['enrolled_count'] ?? 0 }}</div>
        @if(!empty($enrolledStudents) && $enrolledStudents->count())
            <div class="p-4">
                <div class="text-sm text-gray-500">قائمة الطلاب المشتركين:</div>
                <ul class="mt-2 text-sm">
                    @foreach($enrolledStudents as $stud)
                        <li>{{ $stud->name }} — {{ $stud->email }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    @else
    <table class="w-full text-right">
        <thead class="bg-gray-50 text-gray-500 text-sm">
            <tr>
                <th class="p-4">الدورة</th>
                <th class="p-4">عدد المبيعات</th>
                <th class="p-4">إجمالي الإيرادات</th>
                <th class="p-4">ربح المعلم</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach($salesByCourse as $sale)
            <tr>
                <td class="p-4 font-semibold">{{ $sale->course->title }}</td>
                <td class="p-4">{{ $sale->total_sales }}</td>
                <td class="p-4">${{ number_format($sale->total_amount, 2) }}</td>
                <td class="p-4 text-green-600 font-bold">${{ number_format($sale->teacher_profit, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</div>

<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <div class="p-6 border-b border-gray-100">
        <h3 class="font-bold text-gray-800">آخر العمليات</h3>
    </div>
    <table class="w-full text-right">
        <thead class="bg-gray-50 text-gray-500 text-sm">
            <tr>
                <th class="p-4">الطالب</th>
                <th class="p-4">الدورة</th>
                <th class="p-4">المبلغ</th>
                <th class="p-4">ربحك</th>
                <th class="p-4">التاريخ</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach($recentSales as $sale)
            <tr>
                <td class="p-4">
                    <div class="font-semibold">{{ $sale->student->name }}</div>
                    <div class="text-xs text-gray-400">{{ $sale->student->email }}</div>
                </td>
                <td class="p-4">{{ $sale->course->title }}</td>
                <td class="p-4">${{ number_format($sale->amount, 2) }}</td>
                <td class="p-4 text-green-600">${{ number_format($sale->teacher_commission, 2) }}</td>
                <td class="p-4 text-gray-500 text-sm">{{ $sale->created_at->format('Y/m/d') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="p-4">
        {{ $recentSales->links() }}
    </div>
</div>
@endsection
