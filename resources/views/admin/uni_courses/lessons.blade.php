@extends('layouts.admin')

@section('title', 'إدارة دروس المقرر')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>إدارة دروس المقرر</h2>
            <p class="text-muted mb-0">
                {{ $uniCourse->university->name }} - {{ $uniCourse->display_name }}
            </p>
        </div>
        <div class="btn-group">
            <a href="{{ route('admin.uni_courses.show', $uniCourse) }}" class="btn btn-info">
                <i class="fas fa-eye me-2"></i> عرض المقرر
            </a>
            <a href="{{ route('admin.uni_courses.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i> العودة للقائمة
            </a>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">دروس المقرر ({{ $mappings->count() }})</h5>
                    <button type="button" class="btn btn-sm btn-success" id="saveOrder" style="display: none;">
                        <i class="fas fa-save me-2"></i> حفظ الترتيب
                    </button>
                </div>
                
                <div class="card-body">
                    @if($mappings->count() > 0)
                        <div id="lessonsList" class="list-group">
                            @foreach($mappings as $mapping)
                                <div class="list-group-item d-flex justify-content-between align-items-center" 
                                     data-id="{{ $mapping->id }}" data-order="{{ $mapping->order }}">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-grip-vertical text-muted me-3" style="cursor: move;"></i>
                                        <div>
                                            <h6 class="mb-1">{{ $mapping->lesson->title }}</h6>
                                            <small class="text-muted">
                                                الترتيب: <span class="order-number">{{ $mapping->order }}</span>
                                                @if($mapping->lesson->duration)
                                                    | المدة: {{ $mapping->lesson->formatted_duration }}
                                                @endif
                                            </small>
                                        </div>
                                    </div>
                                    <div class="btn-group">
                                        <form action="{{ route('admin.uni_courses.remove_lesson', [$uniCourse, $mapping]) }}" 
                                              method="POST" class="d-inline"
                                              onsubmit="return confirm('هل أنت متأكد من حذف هذا الدرس؟')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-list fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">لا توجد دروس</h5>
                            <p class="text-muted">لم يتم إضافة أي دروس لهذا المقرر بعد</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">إضافة درس جديد</h5>
                </div>
                
                <div class="card-body">
                    @if($availableLessons->count() > 0)
                        <form action="{{ route('admin.uni_courses.add_lesson', $uniCourse) }}" method="POST">
                            @csrf
                            
                            <div class="mb-3">
                                <label for="lesson_id" class="form-label">الدرس</label>
                                <select class="form-control @error('lesson_id') is-invalid @enderror" 
                                        id="lesson_id" 
                                        name="lesson_id" 
                                        required>
                                    <option value="">اختر الدرس</option>
                                    @foreach($availableLessons as $lesson)
                                        <option value="{{ $lesson->id }}">
                                            {{ $lesson->title }}
                                            @if($lesson->duration)
                                                ({{ $lesson->formatted_duration }})
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('lesson_id')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="order" class="form-label">الترتيب</label>
                                <input type="number" 
                                       class="form-control @error('order') is-invalid @enderror" 
                                       id="order" 
                                       name="order" 
                                       value="{{ $mappings->count() + 1 }}" 
                                       min="1"
                                       required>
                                @error('order')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-plus me-2"></i> إضافة الدرس
                            </button>
                        </form>
                    @else
                        <div class="text-center">
                            <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                            <p class="text-muted mb-0">تم إضافة جميع دروس المقرر</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const lessonsList = document.getElementById('lessonsList');
    const saveOrderBtn = document.getElementById('saveOrder');
    
    if (lessonsList) {
        const sortable = Sortable.create(lessonsList, {
            handle: '.fa-grip-vertical',
            animation: 150,
            onEnd: function(evt) {
                updateOrderNumbers();
                saveOrderBtn.style.display = 'inline-block';
            }
        });
        
        function updateOrderNumbers() {
            const items = lessonsList.querySelectorAll('.list-group-item');
            items.forEach((item, index) => {
                const orderSpan = item.querySelector('.order-number');
                if (orderSpan) {
                    orderSpan.textContent = index + 1;
                }
                item.setAttribute('data-order', index + 1);
            });
        }
        
        saveOrderBtn.addEventListener('click', function() {
            const items = lessonsList.querySelectorAll('.list-group-item');
            const mappings = [];
            
            items.forEach((item, index) => {
                mappings.push({
                    id: parseInt(item.getAttribute('data-id')),
                    order: index + 1
                });
            });
            
            fetch('{{ route("admin.uni_courses.update_lesson_order", $uniCourse) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    mappings: mappings
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    saveOrderBtn.style.display = 'none';
                    // Show success message
                    const alert = document.createElement('div');
                    alert.className = 'alert alert-success alert-dismissible fade show';
                    alert.innerHTML = `
                        ${data.message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    `;
                    document.querySelector('.card-body').insertBefore(alert, lessonsList);
                    
                    setTimeout(() => {
                        alert.remove();
                    }, 3000);
                } else {
                    alert('حدث خطأ في حفظ الترتيب');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('حدث خطأ في حفظ الترتيب');
            });
        });
    }
});
</script>
@endpush

