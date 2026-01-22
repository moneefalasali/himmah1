@extends('layouts.teacher')

@section('title', 'إحصائيات الأرباح')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="p-4 bg-white rounded shadow">
            <div class="text-sm text-gray-500">إجمالي المبيعات</div>
            <div class="text-2xl font-semibold">{{ number_format($totalGross, 2) }} ر.س</div>
        </div>
        <div class="p-4 bg-white rounded shadow">
            <div class="text-sm text-gray-500">رسوم المنصة</div>
            <div class="text-2xl font-semibold">{{ number_format($platformFees, 2) }} ر.س</div>
        </div>
        <div class="p-4 bg-white rounded shadow">
            <div class="text-sm text-gray-500">نصيبك (50%)</div>
            <div class="text-2xl font-semibold text-green-600">{{ number_format($teacherShare, 2) }} ر.س</div>
        </div>
    </div>

    <div class="bg-white rounded shadow p-4 mb-6">
        <div class="text-sm text-gray-500">عدد الطلاب المشتركين</div>
        <div class="text-lg font-semibold">{{ $enrolledCount ?? 0 }}</div>
        @if(!empty($enrolledStudents) && $enrolledStudents->count())
            <div class="mt-3 text-sm text-gray-600">قائمة الطلاب المشتركين:</div>
            <ul class="mt-2 text-sm">
                @foreach($enrolledStudents as $stud)
                    <li>{{ $stud->name }} — {{ $stud->email }}</li>
                @endforeach
            </ul>
        @endif
    </div>
    @if(!empty($includesManualEstimates))
        <div class="text-xs text-gray-500 mb-4">الأرقام المعروضة تشمل الطلاب المضافين يدويًا (إذا وُجدوا).</div>
    @endif

    <div class="bg-white rounded shadow p-4">
        <h3 class="mb-4">الأرباح الشهرية (آخر 6 أشهر)</h3>
        <canvas id="earningsChart" height="120"></canvas>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const labels = {!! json_encode($labels) !!};
    const data = {!! json_encode($data) !!};

    const ctx = document.getElementById('earningsChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'أرباحك (ر.س)',
                data: data,
                backgroundColor: 'rgba(34,197,94,0.7)'
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
</script>
@endsection
