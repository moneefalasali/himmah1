@extends('layouts.admin')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-6">دردشات العملاء (نظام الخدمات)</h1>
    
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <table class="w-full text-right">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="p-4">المستخدم</th>
                    <th class="p-4">آخر رسالة</th>
                    <th class="p-4">التاريخ</th>
                    <th class="p-4">الإجراء</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rooms as $room)
                <tr class="border-b hover:bg-gray-50">
                    <td class="p-4 font-bold">{{ $room->user->name }}</td>
                    <td class="p-4 text-gray-600">{{ Str::limit($room->lastMessage?->message, 50) }}</td>
                    <td class="p-4 text-sm text-gray-500">{{ $room->updated_at->diffForHumans() }}</td>
                    <td class="p-4">
                        <a href="{{ route('admin.customer-chats.show', $room->id) }}" class="text-blue-600 hover:underline">فتح الدردشة</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        {{ $rooms->links() }}
    </div>
</div>
@endsection
