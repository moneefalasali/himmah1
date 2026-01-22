<?php

namespace App\Notifications;

use App\Models\ChatMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewChatMessageNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $message;

    public function __construct(ChatMessage $message)
    {
        $this->message = $message;
    }

    public function via($notifiable)
    {
        // إرسال بريد إلكتروني فقط إذا لم يكن المستخدم متصلاً (يمكن تخصيص هذا المنطق)
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('رسالة جديدة في دورة: ' . $this->message->room->course->title)
            ->line('لديك رسالة جديدة من ' . $this->message->user->name)
            ->line('المحتوى: ' . ($this->message->message ?? 'ملف مرفق'))
            ->action('عرض الدردشة', route('chat.show', $this->message->room->course_id))
            ->line('شكراً لاستخدامك منصتنا التعليمية!');
    }

    public function toArray($notifiable)
    {
        return [
            'chat_room_id' => $this->message->chat_room_id,
            'course_title' => $this->message->room->course->title,
            'sender_name' => $this->message->user->name,
            'message_snippet' => substr($this->message->message, 0, 50),
        ];
    }
}
