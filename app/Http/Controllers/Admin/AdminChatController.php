<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatRoom;
use Illuminate\Http\Request;

class AdminChatController extends Controller
{
    public function index()
    {
        // دردشات الخدمات (طالب/إدارة)
        $serviceRooms = ChatRoom::where('type', 'service')
            ->with(['user', 'lastMessage'])
            ->latest('updated_at')
            ->get();

        // دردشات الكورسات (طلاب/معلم/إدارة)
        $courseRooms = ChatRoom::where('type', 'course')
            ->with(['course', 'lastMessage'])
            ->latest('updated_at')
            ->get();
            
        return view('admin.chats.index', compact('serviceRooms', 'courseRooms'));
    }

    public function show(ChatRoom $room)
    {
        $room->load(['messages.user', 'course', 'lastMessage']);
        $messages = $room->messages()->with('user')->orderBy('created_at')->get();
        return view('admin.chats.show', compact('room', 'messages'));
    }
}
