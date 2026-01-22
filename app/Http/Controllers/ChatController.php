<?php
namespace App\Http\Controllers;

use App\Models\ChatRoom;
use Illuminate\Support\Facades\Schema;
use App\Models\Course;
use App\Services\ChatService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    protected $chatService;

    public function __construct(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    /**
     * عرض غرفة الدردشة بناءً على النوع
     */
    public function show(ChatRoom $room)
    {
        $user = Auth::user();

        // 1. منطق دردشة الخدمات (طالب + إدارة فقط)
        if ($room->type === 'service') {
            if (!$user->isAdmin() && $room->user_id !== $user->id) {
                abort(403, 'هذه الدردشة خاصة بالدعم الفني فقط.');
            }
        } 
        // 2. منطق دردشة الكورسات (طلاب الكورس + المعلم + الإدارة)
        else if ($room->course_id) {
            $course = $room->course;
            $isStudent = $user->isEnrolledIn($course);
            $isTeacher = ($course->user_id === $user->id);
            $isAdmin = $user->isAdmin();

            if (!$isStudent && !$isTeacher && !$isAdmin) {
                abort(403, 'يجب أن تكون مسجلاً في الكورس للوصول للدردشة.');
            }
        }

        return view('chat.show', [
            'room' => $room,
            'messages' => $this->chatService->getRoomMessages($room->id, 50, $user->id)
        ]);
    }

    /**
     * فتح أو إنشاء دردشة خدمات مع الإدارة
     */
    public function adminChat()
    {
        // If the chat_rooms table doesn't support service chats (missing columns),
        // fall back to the appropriate listing page instead of attempting the query.
        if (!Schema::hasColumn('chat_rooms', 'user_id') || !Schema::hasColumn('chat_rooms', 'type')) {
            // Redirect admins to admin chat list, teachers/students to their course chats
            $user = auth()->user();
            if ($user && $user->isAdmin()) {
                return redirect()->route('admin.chat.index');
            }

            // Teachers: redirect to teacher's course chats index if available
            if ($user && $user->role === 'teacher') {
                return redirect()->route('teacher.chats.index');
            }

            // Default fallback to the services page (direct users to the services/contact area)
            return redirect()->route('services.index');
        }

        $admin = \App\Models\User::where("role", "admin")->first();
        $room = ChatRoom::firstOrCreate([
            "user_id" => auth()->id(),
            "type" => "service"
        ], [
            "admin_id" => $admin->id,
            "name" => "الدعم الفني والخدمات"
        ]);

        return redirect()->route("chat.show", $room->id);
    }

    /**
     * فتح أو إنشاء دردشة كورس
     */
    public function courseChat(Course $course)
    {
        $room = ChatRoom::firstOrCreate([
            'course_id' => $course->id,
            'type' => 'course'
        ], [
            'name' => "دردشة كورس: " . $course->title
        ]);

        return redirect()->route("chat.show", $room->id);
    }

    /**
     * Store a new chat message in a room (AJAX)
     */
    public function store(Request $request, ChatRoom $chatRoom)
    {
        $user = Auth::user();

        $request->validate([
            'content' => 'required_without:files|string|max:2000',
            'files.*' => 'nullable|file|max:10240'
        ]);

        $data = ['message' => $request->input('content')];
        if ($request->hasFile('files')) {
            $data['files'] = $request->file('files');
        }

        $message = $this->chatService->sendMessage($user, $chatRoom->id, $data);

        \Log::info('Chat message stored', ['user_id' => $user->id, 'room_id' => $chatRoom->id, 'message_id' => $message->id]);

        // Return a consistent wrapped JSON shape so clients can rely on `data.message`
        return response()->json(['message' => $message]);
    }

    /**
     * Return latest messages for the given chat room as JSON (used by polling fallback)
     */
    public function messagesJson(Request $request, ChatRoom $chatRoom)
    {
        $user = Auth::user();

        // reuse access rules from show()
        if ($chatRoom->type === 'service') {
            if (!$user->isAdmin() && $chatRoom->user_id !== $user->id) {
                abort(403);
            }
        } elseif ($chatRoom->course_id) {
            $course = $chatRoom->course;
            $isStudent = $user->isEnrolledIn($course);
            $isTeacher = ($course->user_id === $user->id);
            $isAdmin = $user->isAdmin();

            if (!$isStudent && !$isTeacher && !$isAdmin) {
                abort(403);
            }
        }

        $messages = $this->chatService->getRoomMessages($chatRoom->id, 50, $user->id);

        // add more detailed diagnostics for polling requests (IP and UA)
        \Log::info('Chat messages json requested', [
            'user_id' => $user->id,
            'room_id' => $chatRoom->id,
            'count' => count($messages->items()),
            'ip' => $request->ip(),
            'ua' => substr($request->header('User-Agent') ?? '', 0, 200),
        ]);

        return response()->json($messages->items());
    }

    /**
     * Delete a message for current user or everyone (sender only)
     */
    public function deleteMessage(Request $request, \App\Models\ChatMessage $message)
    {
        $user = Auth::user();
        $action = $request->input('action', 'me');

        if ($action === 'everyone') {
            // only sender can delete for everyone
            if ($message->user_id !== $user->id) {
                return response()->json(['error' => 'unauthorized'], 403);
            }
            // mark deletion for everyone
            \App\Models\ChatMessageDeletion::updateOrCreate(
                ['chat_message_id' => $message->id, 'user_id' => $user->id],
                ['deleted_for_everyone' => true]
            );

            // broadcast deletion event
            event(new \App\Events\MessageDeleted($message->id, $message->chat_room_id));
            return response()->json(['ok' => true]);
        }

        // delete for me (current user)
        \App\Models\ChatMessageDeletion::updateOrCreate(
            ['chat_message_id' => $message->id, 'user_id' => $user->id],
            ['deleted_for_everyone' => false]
        );

        return response()->json(['ok' => true]);
    }
}
