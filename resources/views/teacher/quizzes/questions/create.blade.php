@extends('layouts.teacher')

@section('title', 'إضافة سؤال')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-3">إضافة سؤال إلى: {{ $quiz->title }}</h1>

    <form action="{{ route('teacher.quizzes.questions.store', $quiz) }}" method="post">
        @csrf

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="mb-3">
            <label class="form-label">نص السؤال</label>
            <textarea name="question_text" class="form-control" rows="3">{{ old('question_text') }}</textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">نوع السؤال</label>
            <select name="type" id="q-type" class="form-control">
                <option value="multiple_choice" @if(old('type')=='multiple_choice') selected @endif>اختياري متعدد</option>
                <option value="true_false" @if(old('type')=='true_false') selected @endif>صح/خطأ</option>
                <option value="short_answer" @if(old('type')=='short_answer') selected @endif>إجابة قصيرة</option>
            </select>
        </div>

        <div id="options-wrapper" class="mb-3">
            <label class="form-label">الاختيارات</label>
            <div id="options-list">
                @php
                    $oldOptions = old('options', []);
                @endphp

                @if(!empty($oldOptions) && is_array($oldOptions))
                    @foreach($oldOptions as $i => $opt)
                        <div class="input-group mb-2 option-row">
                            <input type="text" name="options[{{ $i }}][option_text]" class="form-control" placeholder="نص الاختيار" value="{{ $opt['option_text'] ?? '' }}">
                            <div class="input-group-text">
                                <label class="form-check-label me-2">صحيح
                                    <input type="checkbox" name="options[{{ $i }}][is_correct]" value="1" class="form-check-input ms-2" @if(!empty($opt['is_correct'])) checked @endif>
                                </label>
                            </div>
                            <button type="button" class="btn btn-danger btn-sm remove-option">حذف</button>
                        </div>
                    @endforeach
                @else
                    <div class="input-group mb-2 option-row">
                        <input type="text" name="options[0][option_text]" class="form-control" placeholder="نص الاختيار">
                        <div class="input-group-text">
                            <label class="form-check-label me-2">صحيح
                                <input type="checkbox" name="options[0][is_correct]" value="1" class="form-check-input ms-2">
                            </label>
                        </div>
                        <button type="button" class="btn btn-danger btn-sm remove-option">حذف</button>
                    </div>
                @endif
            </div>
            <button type="button" id="add-option" class="btn btn-sm btn-outline-primary">إضافة اختيار</button>
        </div>

        <div class="mb-3">
            <label class="form-label">النقاط</label>
            <input type="number" name="points" class="form-control" value="{{ old('points', 1) }}">
        </div>

        <button class="btn btn-primary">حفظ</button>
    </form>
</div>

@push('scripts')
<script>
    (function(){
        let idx = document.querySelectorAll('#options-list .option-row').length || 0;
        document.getElementById('add-option').addEventListener('click', function(){
            const list = document.getElementById('options-list');
            const div = document.createElement('div');
            div.className = 'input-group mb-2 option-row';
            div.innerHTML = `
                <input type="text" name="options[${idx}][option_text]" class="form-control" placeholder="نص الاختيار">
                <div class="input-group-text">
                    <label class="form-check-label me-2">صحيح
                        <input type="checkbox" name="options[${idx}][is_correct]" value="1" class="form-check-input ms-2">
                    </label>
                </div>
                <button type="button" class="btn btn-danger btn-sm remove-option">حذف</button>
            `;
            list.appendChild(div);
            idx++;
        });

        document.addEventListener('click', function(e){
            if(e.target && e.target.classList.contains('remove-option')){
                e.target.closest('.option-row').remove();
            }
        });
    })();
</script>
@endpush

@endsection
