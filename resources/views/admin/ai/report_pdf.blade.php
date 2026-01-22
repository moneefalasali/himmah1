<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>تقرير الذكاء الاصطناعي - Himmah</title>
  <style>
    body { font-family: DejaVu Sans, Arial, 'Noto Naskh Arabic', Tahoma; color:#222; }
    .header { text-align:center; padding:20px 0; background:linear-gradient(90deg,#0d6efd,#0b5ed7); color:#fff }
    .container { padding:20px; }
    .card { border:1px solid #e9ecef; border-radius:6px; margin-bottom:16px; padding:12px }
    h1,h2,h3 { margin:6px 0 }
    pre { white-space:pre-wrap; font-size:14px }
    table { width:100%; border-collapse:collapse; margin-top:8px }
    th,td { padding:8px; border:1px solid #ddd; text-align:right }
  </style>
</head>
<body>
  <div class="header">
    <h1>تقرير أداء المنصة</h1>
    <div>توليد تلقائي بواسطة نظام الذكاء الاصطناعي</div>
  </div>
  <div class="container">
    <div class="card">
      <h2>الملخص</h2>
      <p><strong>إجمالي الدورات:</strong> {{ $stats['total_courses'] }}</p>
      <p><strong>إجمالي طلبات الذكاء الاصطناعي:</strong> {{ $stats['total_ai_requests'] }}</p>
    </div>

    <div class="card">
      <h2>أهم الدورات بناءً على استخدام AI</h2>
      @if($stats['most_active_courses']->count())
      <table>
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
        <p>لا توجد بيانات.</p>
      @endif
    </div>

    <div class="card">
      <h2>تحليل وتوصيات</h2>
      <pre>{{ $response }}</pre>
    </div>

    <div style="text-align:center; font-size:12px; color:#666; margin-top:20px">تاريخ الإنشاء: {{ \Carbon\Carbon::now()->toDateTimeString() }}</div>
  </div>
</body>
</html>
