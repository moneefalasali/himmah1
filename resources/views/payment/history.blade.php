@extends('layouts.app')

@section('title', 'سجل المدفوعات')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card">
            <div class="card-header bg-light">
                <h3 class="mb-0">سجل المدفوعات</h3>
            </div>
            <div class="card-body">
                @if($purchases->isEmpty())
                    <div class="alert alert-info">لم يتم العثور على مدفوعات سابقة.</div>
                @else
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>الدورة</th>
                                <th>المبلغ</th>
                                <th>الحالة</th>
                                <th>طريقة الدفع</th>
                                <th>التاريخ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($purchases as $purchase)
                                <tr>
                                    <td>{{ optional($purchase->course)->title ?? '—' }}</td>
                                    <td>{{ number_format($purchase->amount,2) }} ر.س</td>
                                    <td>{{ ucfirst($purchase->payment_status) }}</td>
                                    <td>{{ $purchase->payment_method ?? '—' }}</td>
                                    <td>{{ $purchase->created_at->format('Y-m-d') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="mt-3">
                        {{ $purchases->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
