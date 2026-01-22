@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-3 d-none d-lg-block">
            @include('partials.student_sidebar')
        </div>
        <div class="col-12 col-lg-9">
            @yield('student_content')
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .student-sidebar {
        background: #fff;
        border-left: 1px solid #e2e8f0;
        padding: 1rem;
        border-radius: 0.5rem;
    }
</style>
@endpush
