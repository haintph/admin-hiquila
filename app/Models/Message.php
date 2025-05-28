<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = ['sender_id', 'receiver_id', 'content', 'file_path', 'type', 'is_read'];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    // Kiểm tra nếu tin nhắn là hình ảnh
    public function isImage()
    {
        return $this->type === 'image';
    }

    // Kiểm tra nếu tin nhắn là video
    public function isVideo()
    {
        return $this->type === 'video';
    }

    // Kiểm tra nếu tin nhắn là file âm thanh
    public function isAudio()
    {
        return $this->type === 'audio';
    }

    // Kiểm tra nếu tin nhắn là file đính kèm
    public function isFile()
    {
        return $this->type === 'file';
    }
}
