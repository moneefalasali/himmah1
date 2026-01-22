@extends('layouts.app')

@section('title', 'سجل الدفع')

@section('content')
    <h2 class="mb-4">سجل المدفوعات</h2>
    
    @if($purchases->isEmpty())
        <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i> لا توجد معاملات مالية حتى الآن.
        </div>
    @else
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>الوصف</th>
                                <th>المبلغ</th>
                                <th>الحالة</th>
                                <th>التاريخ</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($purchases as $purchase)
                                <tr>
                                    <td>{{ $purchase->id }}</td>
                                    <td>
                                        شراء دورة: {{ $purchase->course->title }}
                                    </td>
                                    <td>{{ number_format($purchase->amount, 2) }} ر.س</td>
                                    <td>
                                        <span class="badge 
                                            @if($purchase->payment_status == 'completed') bg-success
                                            @elseif($purchase->payment_status == 'pending') bg-warning
                                            @else bg-danger @endif">
                                            {{ $purchase->payment_status == 'completed' ? 'مكتمل' : ($purchase->payment_status == 'pending' ? 'في الانتظار' : 'فشل') }}
                                        </span>
                                    </td>
                                    <td>{{ $purchase->created_at->format('Y-m-d H:i') }}</td>
                                    <td>
                                        <a href="{{ route('courses.show', $purchase->course) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i> عرض الدورة
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                

            </div>
        </div>
    @endif
@endsection