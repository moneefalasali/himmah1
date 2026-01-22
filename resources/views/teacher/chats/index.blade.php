@extends('layouts.app')

@section('title', 'محادثات الكورسات')

@section('content')
<div class="container">
    <h1 class="mb-4">محادثات الكورسات</h1>

    @if($courses->isEmpty())
        <div class="alert alert-info">لا توجد دورات لإظهار محادثات لها.</div>
    @else
        <div class="list-group">
            @foreach($courses as $course)
                <a href="{{ route('chat.course', $course) }}" class="list-group-item list-group-item-action">
                    {{ $course->title }}
                    <span class="float-end text-muted">فتح المحادثة</span>
                </a>
            @endforeach
        </div>
    @endif
</div>
@endsection
