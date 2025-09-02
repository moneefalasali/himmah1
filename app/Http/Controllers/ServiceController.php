<?php

namespace App\Http\Controllers;

use App\Models\ServiceType;
use App\Models\ServiceRequest;
use App\Models\ServiceRequestFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ServiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of service types.
     */
    public function index()
    {
        $serviceTypes = ServiceType::all();
        return view('services.index', compact('serviceTypes'));
    }

    /**
     * Show the form for creating a new service request.
     */
    public function create($serviceTypeName)
    {
        $serviceType = ServiceType::where('name', $serviceTypeName)->firstOrFail();
        return view('services.create', compact('serviceType'));
    }

    /**
     * Store a newly created service request.
     */
    public function store(Request $request, $serviceTypeName)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'requirements' => 'nullable|string',
            'files.*' => 'nullable|file|max:10240', // 10MB max per file
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = Auth::user();
        $serviceType = ServiceType::where('name', $serviceTypeName)->firstOrFail();

        // Create service request
        $serviceRequest = ServiceRequest::create([
            'user_id' => $user->id,
            'service_type_id' => $serviceType->id,
            'title' => $request->title,
            'description' => $request->description,
            'requirements' => $request->requirements,
            'status' => 'pending',
            'is_urgent' => $request->has('urgent'),
            'amount' => 0, // Will be set by admin later
        ]);

        // Handle file uploads
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $fileName = $file->getClientOriginalName();
                $filePath = $file->store('service_requests/' . $serviceRequest->id, 'public');
                
                ServiceRequestFile::create([
                    'service_request_id' => $serviceRequest->id,
                    'file_name' => $fileName,
                    'file_path' => $filePath,
                    'file_type' => $file->getClientMimeType(),
                    'uploaded_by' => 'user',
                ]);
            }
        }

        return redirect()->route('services.show', $serviceRequest)
            ->with('success', 'تم إرسال طلب الخدمة بنجاح! سيتم التواصل معك قريباً.');
    }

    /**
     * Display the specified service request.
     */
    public function show(ServiceRequest $serviceRequest)
    {
        $user = Auth::user();
        
        // Check if user owns this service request or is admin
        if ($serviceRequest->user_id !== $user->id && !$user->isAdmin()) {
            abort(403);
        }

        $serviceRequest->load(['serviceType', 'files', 'messages.sender', 'messages.files']);

        // Mark messages as read for current user
        $serviceRequest->messages()
            ->where('sender_id', '!=', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return view('services.show', compact('serviceRequest'));
    }

    /**
     * Show user's service requests.
     */
    public function myRequests()
    {
        $user = Auth::user();
        $serviceRequests = ServiceRequest::with(['serviceType', 'latestMessage'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('services.my-requests', compact('serviceRequests'));
    }

    /**
     * Cancel a service request.
     */
    public function cancel(ServiceRequest $serviceRequest)
    {
        $user = Auth::user();
        
        // Check if user owns this service request
        if ($serviceRequest->user_id !== $user->id) {
            abort(403);
        }

        // Can only cancel pending requests
        if (!$serviceRequest->isPending()) {
            return back()->with('error', 'لا يمكن إلغاء هذا الطلب في حالته الحالية.');
        }

        $serviceRequest->markAsCancelled();

        return back()->with('success', 'تم إلغاء الطلب بنجاح.');
    }

    /**
     * Download a service request file.
     */
    public function downloadFile(ServiceRequestFile $file)
    {
        $user = Auth::user();
        
        // Check if user owns this service request or is admin
        if ($file->serviceRequest->user_id !== $user->id && !$user->isAdmin()) {
            abort(403);
        }

        if (!Storage::disk('public')->exists($file->file_path)) {
            abort(404, 'الملف غير موجود.');
        }

        return Storage::disk('public')->download($file->file_path, $file->file_name);
    }
}

