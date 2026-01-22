<?php

namespace App\Http\Controllers;

use App\Models\ServiceRequest;
use App\Models\Message;
use App\Models\MessageFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class MessageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Store a new message in a service request.
     */
    public function store(Request $request, ServiceRequest $serviceRequest)
    {
        $user = Auth::user();
        
        // Check if user owns this service request or is admin
        if ($serviceRequest->user_id !== $user->id && !$user->isAdmin()) {
            abort(403);
        }

        $validator = Validator::make($request->all(), [
            'message' => 'required|string',
            'files.*' => 'nullable|file|max:10240', // 10MB max per file
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        // Create message
        $message = Message::create([
            'service_request_id' => $serviceRequest->id,
            'sender_id' => $user->id,
            'message' => $request->message,
            'is_read' => false,
        ]);

        // Handle file uploads
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $fileName = $file->getClientOriginalName();
                $filePath = $file->store('messages/' . $message->id, 'public');
                
                MessageFile::create([
                    'message_id' => $message->id,
                    'file_name' => $fileName,
                    'file_path' => $filePath,
                    'file_type' => $file->getClientMimeType(),
                ]);
            }
        }

        // If user is admin and request is pending, mark as in progress
        if ($user->isAdmin() && $serviceRequest->isPending()) {
            $serviceRequest->markAsInProgress();
        }

        return back()->with('success', 'تم إرسال الرسالة بنجاح.');
    }

    /**
     * Mark messages as read.
     */
    public function markAsRead(ServiceRequest $serviceRequest)
    {
        $user = Auth::user();
        
        // Check if user owns this service request or is admin
        if ($serviceRequest->user_id !== $user->id && !$user->isAdmin()) {
            abort(403);
        }

        // Mark all messages from other users as read
        $serviceRequest->messages()
            ->where('sender_id', '!=', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    /**
     * Download a message file.
     */
    public function downloadFile(MessageFile $file)
    {
        $user = Auth::user();
        
        // Check if user owns this service request or is admin
        $serviceRequest = $file->message->serviceRequest;
        if ($serviceRequest->user_id !== $user->id && !$user->isAdmin()) {
            abort(403);
        }

        if (!Storage::disk('public')->exists($file->file_path)) {
            abort(404, 'الملف غير موجود.');
        }

        return Storage::disk('public')->download($file->file_path, $file->file_name);
    }

    /**
     * Get messages for a service request (AJAX).
     */
    public function getMessages(ServiceRequest $serviceRequest)
    {
        $user = Auth::user();
        
        // Check if user owns this service request or is admin
        if ($serviceRequest->user_id !== $user->id && !$user->isAdmin()) {
            abort(403);
        }

        $messages = $serviceRequest->messages()
            ->with(['sender', 'files'])
            ->orderBy('created_at')
            ->get();

        return response()->json([
            'messages' => $messages->map(function ($message) use ($user) {
                return [
                    'id' => $message->id,
                    'message' => $message->message,
                    'sender_name' => $message->sender->name,
                    'is_from_admin' => $message->isFromAdmin(),
                    'is_own_message' => $message->sender_id === $user->id,
                    'created_at' => $message->created_at->format('Y-m-d H:i:s'),
                    'formatted_time' => $message->formatted_time,
                    'files' => $message->files->map(function ($file) {
                        return [
                            'id' => $file->id,
                            'name' => $file->file_name,
                            'size' => $file->file_size,
                            'icon' => $file->file_icon,
                            'download_url' => route('messages.download-file', $file),
                        ];
                    }),
                ];
            }),
        ]);
    }
}

