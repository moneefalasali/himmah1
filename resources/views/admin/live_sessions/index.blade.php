@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">إدارة الحصص المباشرة (Zoom)</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>العنوان</th>
                            <th>الكورس</th>
                            <th>المعلم</th>
                            <th>الموعد</th>
                            <th>المدة</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sessions as $session)
                        <tr>
                            <td>{{ $session->topic }}</td>
                            <td>{{ $session->course->title }}</td>
                            <td>{{ $session->teacher->name }}</td>
                            <td>{{ $session->start_time->format('Y-m-d H:i') }}</td>
                            <td>{{ $session->duration }} دقيقة</td>
                            <td>
                                <span class="badge badge-{{ $session->status == 'active' ? 'success' : ($session->status == 'finished' ? 'secondary' : 'danger') }}">
                                    {{ $session->status }}
                                </span>
                            </td>
                            <td>
                                <form action="{{ route('admin.live-sessions.update-status', $session) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" onchange="this.form.submit()" class="form-control form-control-sm d-inline-block" style="width: auto;">
                                        <option value="active" {{ $session->status == 'active' ? 'selected' : '' }}>نشطة</option>
                                        <option value="cancelled" {{ $session->status == 'cancelled' ? 'selected' : '' }}>ملغاة</option>
                                        <option value="finished" {{ $session->status == 'finished' ? 'selected' : '' }}>منتهية</option>
                                    </select>
                                </form>
                                <form action="{{ route('admin.live-sessions.destroy', $session) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">حذف</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $sessions->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
