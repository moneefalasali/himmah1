<?php

namespace App\Services;

use App\Models\ChatMessage;
use App\Models\ChatRoom;
use App\Models\ChatAttachment;
use App\Events\MessageSent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ChatService
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function sendMessage($user, $roomId, $data)
    {
        return DB::transaction(function () use ($user, $roomId, $data) {
            $message = ChatMessage::create([
                'chat_room_id' => $roomId,
                'user_id' => $user->id,
                'message' => $data['message'] ?? null,
                'type' => $data['type'] ?? 'text',
            ]);

            if (isset($data['files']) && count($data['files']) > 0) {
                foreach ($data['files'] as $file) {
                    // استخدام ImageService لرفع الملف بشكل آمن وPrivate
                    $path = $this->imageService->uploadFile($file, 'chats/' . $roomId);
                    
                    $attachment = ChatAttachment::create([
                        'chat_message_id' => $message->id,
                        'file_path' => $path,
                        'file_name' => $file->getClientOriginalName(),
                        'file_type' => $file->getMimeType(),
                        'file_size' => $file->getSize(),
                    ]);

                    // تشغيل معالجة الملف في الخلفية
                    \App\Jobs\ProcessChatAttachment::dispatch($attachment);
                }
                $message->update(['type' => $this->determineMessageType($data['files'])]);
            }

            broadcast(new MessageSent($message))->toOthers();

            // log that broadcast was attempted (diagnostic)
            \Log::info('Message broadcast attempted', [
                'message_id' => $message->id,
                'room_id' => $roomId,
                'user_id' => $user->id,
            ]);

            // إرسال إشعارات للمشاركين الآخرين (المعلم أو الطلاب)
            $this->notifyParticipants($message);

            return $message->load(['user', 'attachments']);
        });
    }

    protected function notifyParticipants($message)
    {
        $course = $message->room->course;
        $sender = $message->user;

        // إذا كان المرسل طالباً، نرسل إشعاراً للمعلم
        if ($sender->isStudent()) {
            $teacher = $course->teacher;
            if ($teacher) {
                $teacher->notify(new \App\Notifications\NewChatMessageNotification($message));
            }
        } 
        // إذا كان المرسل معلماً، نرسل إشعاراً لجميع الطلاب المشتركين
        elseif ($sender->isTeacher()) {
            $students = $course->students()->where('users.id', '!=', $sender->id)->get();
            foreach ($students as $student) {
                $student->notify(new \App\Notifications\NewChatMessageNotification($message));
            }
        }
    }

    protected function determineMessageType($files)
    {
        foreach ($files as $file) {
            if (str_contains($file->getMimeType(), 'image')) {
                return 'image';
            }
        }
        return 'file';
    }

    public function getRoomMessages($roomId, $perPage = 50, $forUserId = null)
    {
        $query = ChatMessage::with(['user', 'attachments'])
            ->where('chat_room_id', $roomId)
            ->latest();

        if ($forUserId) {
            $query->whereNotExists(function ($q) use ($forUserId) {
                $q->select(DB::raw(1))
                  ->from('chat_message_deletions')
                  ->whereColumn('chat_message_deletions.chat_message_id', 'chat_messages.id')
                  ->where(function ($sq) use ($forUserId) {
                      $sq->where('chat_message_deletions.deleted_for_everyone', true)
                         ->orWhere('chat_message_deletions.user_id', $forUserId);
                  });
            });
        }

        return $query->paginate($perPage);
    }
}
