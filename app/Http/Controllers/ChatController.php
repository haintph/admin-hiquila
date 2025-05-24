<?php

namespace App\Http\Controllers;

use App\Events\NewMessage;
use App\Models\Message;
use App\Models\User;
use App\Models\MessageStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Pusher\Pusher;
use Exception;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    public function index()
    {
        $users = User::where('id', '!=', auth()->id())
            ->withCount([
                'receivedMessages as unread_count' => function ($query) {
                    $query->where('sender_id', '!=', auth()->id())
                        ->where('is_read', false);
                }
            ])
            ->get()
            ->map(function ($user) {
                $lastMessage = Message::where(function ($query) use ($user) {
                    $query->where('sender_id', Auth::id())
                        ->where('receiver_id', $user->id);
                })->orWhere(function ($query) use ($user) {
                    $query->where('sender_id', $user->id)
                        ->where('receiver_id', Auth::id());
                })
                    ->latest()
                    ->first();

                $user->last_message = $lastMessage;
                $user->is_online = Cache::has('user-is-online-' . $user->id);

                return $user;
            });

        $currentUser = auth()->user();
        return view('chat.index', compact('users', 'currentUser'));
    }

    public function show(User $user)
    {
        // Đánh dấu tất cả tin nhắn là đã đọc
        Message::where('sender_id', $user->id)
            ->where('receiver_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $messages = Message::with(['sender', 'receiver'])
            ->where(function ($query) use ($user) {
                $query->where('sender_id', Auth::id())
                    ->where('receiver_id', $user->id);
            })->orWhere(function ($query) use ($user) {
                $query->where('sender_id', $user->id)
                    ->where('receiver_id', Auth::id());
            })
            ->orderBy('created_at')
            ->get();

        $user->is_online = Cache::has('user-is-online-' . $user->id);
        $user->last_seen = Cache::get('user-last-seen-' . $user->id);

        return view('chat.show', compact('user', 'messages'));
    }

    public function send(Request $request)
    {
        try {
            // Validate request
            $request->validate([
                'content' => 'nullable|string',
                'receiver_id' => 'required|exists:users,id',
                'file' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,gif,mp4,avi,mov,mp3,wav,pdf,doc,docx,xls,xlsx'
            ]);

            // Create message
            $message = Message::create([
                'sender_id' => Auth::id(),
                'receiver_id' => $request->receiver_id,
                'content' => $request->content,
                'type' => 'text',
                'is_read' => false
            ]);

            // Handle file upload
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $path = $file->store('chat_files', 'public');

                $message->update([
                    'file_path' => $path,
                    'type' => $this->getFileType($file)
                ]);
            }

            $message->load(['sender', 'receiver']);

            try {
                // Initialize Pusher
                $pusher = new Pusher(
                    config('broadcasting.connections.pusher.key'),
                    config('broadcasting.connections.pusher.secret'),
                    config('broadcasting.connections.pusher.app_id'),
                    [
                        'cluster' => config('broadcasting.connections.pusher.options.cluster'),
                        'useTLS' => true
                    ]
                );

                // Debug logging
                Log::info('Pusher Configuration:', [
                    'key' => config('broadcasting.connections.pusher.key'),
                    'cluster' => config('broadcasting.connections.pusher.options.cluster'),
                    'channel' => 'private-chat.' . $message->receiver_id
                ]);

                // Trigger event
                $pusher->trigger(
                    'private-chat.' . $message->receiver_id,
                    'new-message',
                    [
                        'message' => $message,
                        'file_url' => $message->file_path ? asset('storage/' . $message->file_path) : null
                    ]
                );

                return response()->json([
                    'status' => 'success',
                    'message' => $message,
                    'file_url' => $message->file_path ? asset('storage/' . $message->file_path) : null
                ]);
            } catch (Exception $e) {
                Log::error('Pusher Error:', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);

                // Still return success if message was saved but broadcasting failed
                return response()->json([
                    'status' => 'success',
                    'message' => $message,
                    'warning' => 'Message saved but real-time notification failed'
                ]);
            }
        } catch (Exception $e) {
            Log::error('Message Error:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Could not send message: ' . $e->getMessage()
            ], 500);
        }
    }


    // public function send(Request $request)
    // {
    //     $message = Message::create([
    //         'sender_id' => auth()->id(),
    //         'receiver_id' => $request->receiver_id,
    //         'content' => $request->content,
    //         'type' => 'text'
    //     ]);

    //     if ($request->hasFile('file')) {
    //         // Handle file upload
    //         $file = $request->file('file');
    //         $path = $file->store('chat_files', 'public');

    //         $message->update([
    //             'file_path' => $path,
    //             'type' => $this->getFileType($file)
    //         ]);

    //         $fileUrl = asset('storage/' . $path);
    //     }

    //     $message->load('sender', 'receiver');

    //     return response()->json([
    //         'message' => $message,
    //         'file_url' => $fileUrl ?? null
    //     ]);
    // }
    public function markAsRead(Request $request)
    {
        $request->validate([
            'message_id' => 'required|exists:messages,id'
        ]);

        $message = Message::findOrFail($request->message_id);

        if ($message->receiver_id === Auth::id()) {
            $message->update(['is_read' => true]);

            MessageStatus::updateOrCreate(
                ['message_id' => $message->id, 'user_id' => Auth::id()],
                ['status' => 'read']
            );
        }

        return response()->json(['success' => true]);
    }

    public function deleteMessage(Request $request)
    {
        $request->validate([
            'message_id' => 'required|exists:messages,id'
        ]);

        $message = Message::findOrFail($request->message_id);

        if ($message->sender_id === Auth::id()) {
            // Xóa file nếu có
            if ($message->file_path) {
                Storage::disk('public')->delete($message->file_path);
            }

            $message->delete();
            return response()->json(['success' => true]);
        }

        return response()->json(['error' => 'Unauthorized'], 403);
    }

    private function getFileType($file)
    {
        $mime = $file->getMimeType();

        if (str_starts_with($mime, 'image/'))
            return 'image';
        if (str_starts_with($mime, 'video/'))
            return 'video';
        if (str_starts_with($mime, 'audio/'))
            return 'audio';

        return 'file';
    }
}
