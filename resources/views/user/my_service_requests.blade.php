@extends('layouts.app')

@section('title', 'طلباتي')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>طلبات الخدمات التعليمية</h2>
        <a href="{{ route('services.index') }}" class="btn btn-primary">طلب خدمة جديدة</a>
    </div>
    
    @if($serviceRequests->isEmpty())
        <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i> لا توجد طلبات حالية.
        </div>
    @else
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>النوع</th>
                                <th>الحالة</th>
                                <th>التاريخ</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($serviceRequests as $request)
                                <tr>
                                    <td>{{ $request->id }}</td>
                                    <td>{{ $request->serviceType->name }}</td>
                                    <td>
                                        <span class="badge 
                                            @if($request->status == 'pending') bg-warning
                                            @elseif($request->status == 'in_progress') bg-info
                                            @elseif($request->status == 'completed') bg-success
                                            @else bg-secondary @endif">
                                            {{ $request->getStatusInArabicAttribute() }}
                                        </span>
                                    </td>
                                    <td>{{ $request->created_at->format('Y-m-d') }}</td>
                                    <td>
                                        <a href="{{ route('services.show', $request) }}" class="btn btn-sm btn-outline-primary">
                                            عرض
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