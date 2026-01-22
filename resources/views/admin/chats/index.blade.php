@extends('layouts.admin')

@section('title', 'دردشات الإدارة')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>دردشات العملاء والدورات</h2>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <strong>دردشات الخدمات</strong>
        </div>
        <div class="card-body">
            @if($serviceRooms->count())
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>المستخدم</th>
                                <th>آخر رسالة</th>
                                <th>آخر تحديث</th>
                                <th>إجراء</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($serviceRooms as $room)
                                <tr>
                                    <td>{{ $room->user?->name ?? 'مجهول' }}</td>
                                    <td>{{ \Illuminate\Support\Str::limit($room->lastMessage?->message, 80) }}</td>
                                    <td>{{ $room->updated_at->diffForHumans() }}</td>
                                    <td>
                                        <a href="{{ route('admin.customer-chats.show', $room) }}" class="btn btn-sm btn-outline-primary">فتح</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-muted">لا توجد دردشات خدمات حتى الآن.</p>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <strong>دردشات الكورسات</strong>
        </div>
        <div class="card-body">
            @if($courseRooms->count())
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>الكورس</th>
                                <th>آخر رسالة</th>
                                <th>آخر تحديث</th>
                                <th>إجراء</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($courseRooms as $room)
                                <tr>
                                    <td>{{ $room->course?->title ?? '—' }}</td>
                                    <td>{{ \Illuminate\Support\Str::limit($room->lastMessage?->message, 80) }}</td>
                                    <td>{{ $room->updated_at->diffForHumans() }}</td>
                                    <td>
                                        <a href="{{ route('admin.customer-chats.show', $room) }}" class="btn btn-sm btn-outline-primary">فتح</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-muted">لا توجد دردشات كورسات حتى الآن.</p>
            @endif
        </div>
    </div>
</div>
@endsection
