@extends('layouts.admin')

@section('content')
@include('layouts.partials.admin_page_header', ['title' => 'تقارير الذكاء الاصطناعي', 'subtitle' => 'نتائج التحليل'])

<div class="container py-3">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">تقرير ذكي للمنصة</h3>
    <div>
      <a href="{{ route('admin.ai.reports.download') }}" class="btn btn-dark">تحميل PDF</a>
      <div class="small text-muted mt-1">(لتوليد PDF مباشر، ثبّت الحزمة <code>barryvdh/laravel-dompdf</code> إذا لزم)</div>
    </div>
  </div>

  <div class="row g-3">
    <div class="col-md-4">
      <div class="card p-3 text-center">
        <h5>إجمالي الدورات</h5>
        <div class="display-6">{{ $stats['total_courses'] }}</div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card p-3 text-center">
        <h5>طلبات AI</h5>
        <div class="display-6">{{ $stats['total_ai_requests'] }}</div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card p-3 text-center">
        <h5>أعلي الدورات</h5>
        <div>{{ $stats['most_active_courses']->count() }}</div>
      </div>
    </div>
  </div>

  <div class="card mt-3">
    <div class="card-body">
      <h5 class="card-title">تحليل وتوصيات</h5>
      <div class="mb-2" style="white-space:pre-wrap;">{!! nl2br(e($response)) !!}</div>
    </div>
  </div>

  <div class="card mt-3">
    <div class="card-body">
      <h5 class="card-title">تفاصيل أعلى الدورات</h5>
      @if($stats['most_active_courses']->count())
        <table class="table table-striped">
          <thead>
            <tr><th>المعرف</th><th>اسم الدورة</th><th>الاستعلامات</th></tr>
          </thead>
          <tbody>
            @foreach($stats['most_active_courses'] as $c)
            <tr>
              <td>{{ $c->course_id }}</td>
              <td>{{ optional($c->course)->title ?? '—' }}</td>
              <td>{{ $c->total }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      @else
        <p class="mb-0">لا توجد بيانات.</p>
      @endif
    </div>
  </div>
</div>

@endsection
