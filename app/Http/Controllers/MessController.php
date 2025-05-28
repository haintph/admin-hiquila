<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class MessController extends Controller
{
    // Trang danh sách chat
    public function index()
    {
        $users = User::where('id', '!=', auth()->id())->get();
        $currentUser = auth()->user();
        return view('chat.index', compact('users', 'currentUser'));
    }


    // Xem tin nhắn với một người
    public function show(User $user)
    {
        $messages = Message::where(function ($query) use ($user) {
            $query->where('sender_id', Auth::id())
                ->where('receiver_id', $user->id);
        })->orWhere(function ($query) use ($user) {
            $query->where('sender_id', $user->id)
                ->where('receiver_id', Auth::id());
        })->orderBy('created_at')->get();

        return view('chat.show', compact('user', 'messages'));
    }

    // Gửi tin nhắn
    public function send(Request $request)
    {
        // \Log::info($request->all());
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'content' => 'nullable|string',
            'file' => 'nullable|file|max:20480' // Hỗ trợ file tối đa 20MB
        ]);

        $filePath = null;
        $type = 'text';

        // Xử lý file tải lên
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filePath = $file->store('uploads/messages', 'public'); // Lưu vào thư mục storage/app/public/uploads/messages
            $mimeType = $file->getMimeType();

            // Xác định loại file
            if (str_contains($mimeType, 'image')) {
                $type = 'image';
            } elseif (str_contains($mimeType, 'video')) {
                $type = 'video';
            } elseif (str_contains($mimeType, 'audio')) {
                $type = 'audio';
            } else {
                $type = 'file';
            }
        }

        // Lưu tin nhắn vào database
        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'content' => $request->content,
            'file_path' => $filePath,
            'type' => $type,
        ]);

        return response()->json(['message' => $message]);
    }

}

