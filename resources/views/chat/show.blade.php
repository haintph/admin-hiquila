@extends('admin.layouts.master')
@section('content')
    <style>
        .scrollbar-none::-webkit-scrollbar {
            display: none;
        }

        .scrollbar-none {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .icons-collapsed {
            width: 0;
            opacity: 0;
            overflow: hidden;
            margin-right: 0;
        }

        .online-status {
            width: 10px;
            height: 10px;
            position: absolute;
            bottom: 0;
            right: 0;
            border: 2px solid #fff;
            border-radius: 50%;
        }

        .user-avatar {
            width: 45px;
            /* Tăng kích thước ảnh */
            height: 45px;
            border: 3px solid #fff;
            /* Thêm viền trắng */
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            /* Hiệu ứng bóng nhẹ */
            object-fit: cover;
            /* Cắt ảnh vừa khung tròn */
        }

        .header-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            border: 3px solid #fff;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            object-fit: cover;
        }

        textarea {
            display: block;
            word-wrap: break-word;
            white-space: pre-wrap;
            overflow-wrap: break-word;
            transition: width 0.3s ease;
            line-height: 1.2;
            padding: 8px 0;
            margin: 0;
            width: 100%;
            min-width: 0;
            writing-mode: horizontal-tb !important;
            text-rendering: auto;
            letter-spacing: normal;
            word-spacing: normal;
            text-transform: none;
            text-indent: 0px;
            text-shadow: none;
            appearance: none;
        }

        /* .btn-back {
                        transition: all 0.2s;
                    }

                    .btn-back:hover {
                        transform: translateX(-2px);
                    } */
        .btn-subtle-primary {
            color: #ff6c2f;
            background-color: rgba(67, 94, 190, 0.1);
        }

        .btn-subtle-primary:hover {
            color: #fff;
            background-color: #ff6c2f;
        }

        .btn-subtle-secondary {
            color: #6c757d;
            background-color: rgba(108, 117, 125, 0.1);
        }

        .btn-subtle-secondary:hover {
            color: #fff;
            background-color: #6c757d;
        }

        .btn-icon {
            padding: 0.5rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }

        .status-dot {
            font-size: 0.6rem;
            margin-right: 0.25rem;
            display: inline-block;
            vertical-align: middle;
        }

        .dropdown-item i {
            width: 1.25rem;
            text-align: center;
        }





        .chat-box {
            height: calc(100vh - 300px);
            overflow-y: auto;
            padding: 1.5rem;
            background: #f8f9fa;
        }

        .message-container {
            margin-bottom: 1.5rem;
            max-width: 80%;
        }

        .message-container.text-end {
            margin-left: auto;
        }

        .message-container.text-start {
            margin-right: auto;
        }

        .message-bubble {
            position: relative;
            padding: 0.875rem 1rem;
            border-radius: 1rem;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        }

        .sender .message-bubble {
            background: #435ebe;
            color: #fff;
            border-top-right-radius: 0.25rem;
        }

        .receiver .message-bubble {
            background: #ffffff;
            border-top-left-radius: 0.25rem;
        }

        .message-content {
            word-wrap: break-word;
        }

        .message-media { 
            border-radius: 0.5rem;
            overflow: hidden;
            margin: 0.5rem 0;
        }

        .message-media img {
            max-width: 300px;
            border-radius: 0.5rem;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .message-media img:hover {
            transform: scale(1.02);
        }

        .message-file {
            display: flex;
            align-items: center;
            padding: 0.5rem;
            background: rgba(0, 0, 0, 0.05);
            border-radius: 0.5rem;
            margin: 0.5rem 0;
        }

        .message-meta {
            font-size: 0.75rem;
            margin-top: 0.25rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .message-time {
            opacity: 0.7;
        }

        .message-status {
            display: flex;
            align-items: center;
        }

        .avatar-sm {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            margin-right: 0.5rem;
        }
    </style>
    <div class="page-content">
        <div class="container-xxl">
            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        {{-- Header with user info --}}
                        {{-- <div class="card-header d-flex justify-content-between align-items-center p-3">
                            <div class="d-flex align-items-center">
                                <a href="{{ route('chat.index') }}" class="btn btn-light btn-sm me-2">
                                    <i class="fas fa-arrow-left"></i> Back
                                </a>
                                <div class="position-relative">
                                    <img src="{{ $user->avatar ? asset('storage/' . $user->avatar) : asset('/admin/assets/images/users/avatar-1.jpg') }}"
                                        alt="{{ $user->name }}" class="header-avatar">
                                    <span
                                        class="online-status {{ $user->isOnline() ? 'bg-success' : 'bg-secondary' }}"></span>
                                </div>
                                <div class="ms-3">
                                    <h5 class="mb-0">{{ $user->name }}</h5>
                                    <small class="text-muted">
                                        {{ $user->isOnline() ? 'Đang hoạt động' : ($user->last_seen ? 'Hoạt động ' . \Carbon\Carbon::parse($user->last_seen)->diffForHumans() : 'Không hoạt động') }}
                                    </small>
                                </div>
                            </div>
                            <div>
                                <button class="btn btn-light btn-sm me-2">
                                    <i class="fas fa-phone"></i>
                                </button>
                                <button class="btn btn-light btn-sm me-2">
                                    <i class="fas fa-video"></i>
                                </button>
                                <button class="btn btn-light btn-sm">
                                    <i class="fas fa-info-circle"></i>
                                </button>
                            </div>
                        </div> --}}
                        {{-- Replace existing card-header with this new version --}}
                        <div class="card-header border-bottom">
                            <div class="row align-items-center g-3">
                                <div class="col-auto">
                                    <a href="{{ route('chat.index') }}"
                                        class="btn btn-icon btn-sm btn-subtle-secondary rounded-circle">
                                        <i class="fas fa-arrow-left"></i>
                                    </a>
                                </div>
                                <div class="col">
                                    <div class="d-flex align-items-center">
                                        <div class="position-relative me-2">
                                            <img src="{{ $user->avatar ? asset('storage/' . $user->avatar) : asset('/admin/assets/images/users/avatar-1.jpg') }}"
                                                alt="{{ $user->name }}" class="header-avatar">
                                            <span
                                                class="online-status {{ $user->isOnline() ? 'bg-success' : 'bg-secondary' }}"></span>
                                        </div>
                                        <div class="overflow-hidden">
                                            <h6 class="mb-0 text-truncate">{{ $user->name }}</h6>
                                            <p class="text-muted small mb-0">
                                                <span
                                                    class="status-dot {{ $user->isOnline() ? 'text-success' : 'text-secondary' }}">●</span>
                                                {{ $user->isOnline() ? 'Đang hoạt động' : ($user->last_seen ? 'Hoạt động ' . \Carbon\Carbon::parse($user->last_seen)->diffForHumans() : 'Không hoạt động') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-icon btn-sm btn-subtle-primary rounded-circle"
                                            title="Gọi thoại">
                                            <i class="fas fa-phone"></i>
                                        </button>
                                        <button type="button" class="btn btn-icon btn-sm btn-subtle-primary rounded-circle"
                                            title="Gọi video">
                                            <i class="fas fa-video"></i>
                                        </button>
                                        <div class="dropdown">
                                            <button type="button"
                                                class="btn btn-icon btn-sm btn-subtle-primary rounded-circle"
                                                data-bs-toggle="dropdown">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a class="dropdown-item" href="#"><i
                                                            class="fas fa-search me-2"></i>Tìm kiếm</a></li>
                                                <li><a class="dropdown-item" href="#"><i
                                                            class="fas fa-bell-slash me-2"></i>Tắt thông báo</a></li>
                                                <li><a class="dropdown-item" href="#"><i
                                                            class="fas fa-user-circle me-2"></i>Xem trang cá nhân</a></li>
                                                <li>
                                                    <hr class="dropdown-divider">
                                                </li>
                                                <li><a class="dropdown-item text-danger" href="#"><i
                                                            class="fas fa-ban me-2"></i>Chặn</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- Messages Container --}}
                        {{-- <div class="card-body chat-box custom-scrollbar" id="chat-box"
                            style="height: calc(100vh - 300px); overflow-y: auto;">
                            @foreach ($messages as $message)
                                <div
                                    class="message-container mb-3 {{ $message->sender_id == auth()->id() ? 'text-end' : 'text-start' }}">
                                    <div class="d-inline-block">
                                        @if ($message->sender_id != auth()->id())
                                            <small class="text-muted d-block mb-1">{{ $message->sender->name }}</small>
                                        @endif

                                        <div
                                            class="message-content p-3 rounded-3 
                                {{ $message->sender_id == auth()->id() ? 'bg-primary text-white' : 'bg-light' }}">

                                            @if ($message->type === 'text')
                                                <p class="mb-0">{{ $message->content }}</p>
                                            @endif

                                            @if ($message->isImage())
                                                <img src="{{ asset('storage/' . $message->file_path) }}"
                                                    class="img-fluid rounded" style="max-width: 300px;"
                                                    data-bs-toggle="modal" data-bs-target="#imageModal{{ $message->id }}"
                                                    role="button">
                                            @endif

                                            @if ($message->isVideo())
                                                <video controls class="rounded" style="max-width: 300px;">
                                                    <source src="{{ asset('storage/' . $message->file_path) }}"
                                                        type="video/mp4">
                                                </video>
                                            @endif

                                            @if ($message->isAudio())
                                                <audio controls class="w-100">
                                                    <source src="{{ asset('storage/' . $message->file_path) }}"
                                                        type="audio/mpeg">
                                                </audio>
                                            @endif

                                            @if ($message->isFile())
                                                <a href="{{ asset('storage/' . $message->file_path) }}"
                                                    class="btn btn-sm {{ $message->sender_id == auth()->id() ? 'btn-light' : 'btn-primary' }}"
                                                    download>
                                                    <i class="fas fa-download me-1"></i>
                                                    Tải file
                                                </a>
                                            @endif

                                            <div class="message-meta mt-1">
                                                <small
                                                    class="{{ $message->sender_id == auth()->id() ? 'text-white-50' : 'text-muted' }}">
                                                    {{ $message->created_at->format('H:i') }}
                                                    @if ($message->is_read && $message->sender_id == auth()->id())
                                                        <i class="fas fa-check-double ms-1"></i>
                                                    @endif
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @if ($message->isImage())
                                    <div class="modal fade" id="imageModal{{ $message->id }}" tabindex="-1">
                                        <div class="modal-dialog modal-lg modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-body p-0">
                                                    <img src="{{ asset('storage/' . $message->file_path) }}"
                                                        class="img-fluid w-100">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div> --}}

                        <div class="card-body chat-box custom-scrollbar" id="chat-box">
                            @foreach ($messages as $message)
                                <div class="message-container {{ $message->sender_id == auth()->id() ? 'text-end sender' : 'text-start receiver' }}">
                                    <div class="d-flex {{ $message->sender_id == auth()->id() ? 'justify-content-end' : 'align-items-start' }}">
                                        @if ($message->sender_id != auth()->id())
                                            <img src="{{ $message->sender->avatar ? asset('storage/' . $message->sender->avatar) : asset('/admin/assets/images/users/avatar-1.jpg') }}"
                                                 alt="{{ $message->sender->name }}" class="avatar-sm d-none d-md-block">
                                        @endif
                                        
                                        <div class="message-bubble">
                                            @if ($message->sender_id != auth()->id())
                                                <div class="sender-name mb-1 text-muted small">
                                                    {{ $message->sender->name }}
                                                </div>
                                            @endif
                        
                                            <div class="message-content">
                                                @if ($message->type === 'text')
                                                    <div class="text-wrap">{{ $message->content }}</div>
                                                @endif
                        
                                                @if ($message->isImage())
                                                    <div class="message-media">
                                                        <img src="{{ asset('storage/' . $message->file_path) }}"
                                                             alt="Image"
                                                             data-bs-toggle="modal" 
                                                             data-bs-target="#imageModal{{ $message->id }}"
                                                             loading="lazy">
                                                    </div>
                                                @endif
                        
                                                @if ($message->isVideo())
                                                    <div class="message-media">
                                                        <video controls class="w-100" style="max-width: 300px;">
                                                            <source src="{{ asset('storage/' . $message->file_path) }}" type="video/mp4">
                                                            Your browser does not support the video tag.
                                                        </video>
                                                    </div>
                                                @endif
                        
                                                @if ($message->isAudio())
                                                    <div class="message-media">
                                                        <audio controls class="w-100">
                                                            <source src="{{ asset('storage/' . $message->file_path) }}" type="audio/mpeg">
                                                            Your browser does not support the audio tag.
                                                        </audio>
                                                    </div>
                                                @endif
                        
                                                @if ($message->isFile())
                                                    <div class="message-file">
                                                        <i class="fas fa-file-alt me-2 text-primary"></i>
                                                        <div class="flex-grow-1 text-truncate">{{ basename($message->file_path) }}</div>
                                                        <a href="{{ asset('storage/' . $message->file_path) }}"
                                                           class="btn btn-sm btn-link ms-2"
                                                           download>
                                                            <i class="fas fa-download"></i>
                                                        </a>
                                                    </div>
                                                @endif
                        
                                                <div class="message-meta">
                                                    <span class="message-time">
                                                        {{ $message->created_at->format('H:i') }}
                                                    </span>
                                                    @if ($message->sender_id == auth()->id())
                                                        <span class="message-status">
                                                            @if ($message->is_read)
                                                                <i class="fas fa-check-double text-primary"></i>
                                                            @else
                                                                <i class="fas fa-check"></i>
                                                            @endif
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                        
                                @if ($message->isImage())
                                    <div class="modal fade" id="imageModal{{ $message->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-lg modal-dialog-centered">
                                            <div class="modal-content bg-transparent border-0">
                                                <div class="modal-header border-0">
                                                    <button type="button" class="btn-close bg-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body p-0 text-center">
                                                    <img src="{{ asset('storage/' . $message->file_path) }}"
                                                         class="img-fluid rounded"
                                                         alt="Full size image">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>

                        {{-- Message Input --}}
                        <div class="card-footer bg-light p-3">
                            <form id="chat-form" action="{{ route('chat.send') }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="receiver_id" value="{{ $user->id }}">

                                <div id="filePreviewContainer" class="d-none mb-3">
                                    <div class="d-flex align-items-center bg-light rounded p-2">
                                        <div class="flex-grow-1" id="filePreviewContent"></div>
                                        <button type="button" class="btn btn-link text-danger" id="removeFileBtn">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center">
                                    <div id="leftIcons" class="d-flex align-items-center me-2">
                                        <div class="position-relative me-2">
                                            <button type="button" id="menuButton" class="btn btn-link text-primary p-0">
                                                <i class="fas fa-plus-circle fa-2x"></i>
                                            </button>
                                            <div id="menuPopup"
                                                class="d-none position-absolute bottom-100 start-0 bg-white rounded-lg shadow-lg p-3"
                                                style="width: 250px;">
                                                <div class="row g-2">
                                                    <div class="col-4 text-center">
                                                        <button type="button" class="btn btn-outline-primary btn-sm"
                                                            id="file-btn">
                                                            <i class="fas fa-file-alt fa-2x"></i>
                                                            <small class="d-block">File</small>
                                                        </button>
                                                        <input type="file" id="file-input" name="file"
                                                            class="d-none"
                                                            accept="image/*,video/*,audio/*,.pdf,.doc,.docx,.xls,.xlsx">
                                                    </div>
                                                    <div class="col-4 text-center">
                                                        <button type="button" class="btn btn-outline-primary btn-sm">
                                                            <i class="fas fa-image fa-2x"></i>
                                                            <small class="d-block">Ảnh</small>
                                                        </button>
                                                    </div>
                                                    <div class="col-4 text-center">
                                                        <button type="button" class="btn btn-outline-primary btn-sm">
                                                            <i class="fas fa-microphone fa-2x"></i>
                                                            <small class="d-block">Âm thanh</small>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-link text-primary p-0 me-2">
                                            <i class="fas fa-camera fa-2x"></i>
                                        </button>
                                    </div>

                                    <div class="flex-grow-1 position-relative">
                                        <textarea name="content" class="form-control bg-white" placeholder="Nhập tin nhắn..." rows="1"
                                            style="resize: none; min-height: 40px; max-height: 120px;" oninput="adjustTextareaHeight(this)"></textarea>
                                        <button type="button" id="emojiButton"
                                            class="btn btn-link text-primary position-absolute end-0 top-50 translate-middle-y">
                                            <i class="fas fa-smile"></i>
                                        </button>
                                    </div>

                                    <button type="submit" id="sendButton" class="btn btn-primary ms-2">
                                        <i class="fas fa-paper-plane"></i>
                                    </button>
                                </div>
                            </form>

                            {{-- Emoji Panel --}}
                            <div id="emojiPanel" class="d-none bg-white rounded-lg shadow-lg mt-2 p-3">
                                <div class="d-flex flex-wrap justify-content-center">
                                    @php
                                        $emojis = [
                                            '😊',
                                            '❤️',
                                            '😂',
                                            '👍',
                                            '😭',
                                            '🥰',
                                            '😍',
                                            '😘',
                                            '🤗',
                                            '😮',
                                            '😢',
                                            '😡',
                                            '🎉',
                                            '✨',
                                            '🌟',
                                            '🔥',
                                            '💕',
                                            '💯',
                                            '🙏',
                                            '👋',
                                        ];
                                    @endphp
                                    @foreach ($emojis as $emoji)
                                        <button type="button" class="btn btn-light m-1 emoji-btn"
                                            data-emoji="{{ $emoji }}">
                                            {{ $emoji }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- <script src="https://cdn.tailwindcss.com"></script> --}}
    <script>
        function adjustTextareaHeight(textarea) {
            textarea.style.height = 'auto';
            textarea.style.height = Math.min(textarea.scrollHeight, 120) + 'px';
        }

        document.addEventListener('DOMContentLoaded', function() {

            const filePreviewContainer = document.getElementById('filePreviewContainer');
            const filePreviewContent = document.getElementById('filePreviewContent');
            const removeFileBtn = document.getElementById('removeFileBtn');
            let currentFile = null;


            const chatBox = document.getElementById('chat-box');
            const chatForm = document.getElementById('chat-form');
            const textarea = document.querySelector('textarea');
            const sendButton = document.getElementById('sendButton');
            const menuButton = document.getElementById('menuButton');
            const menuPopup = document.getElementById('menuPopup');
            const fileInput = document.getElementById('file-input');
            const fileBtn = document.getElementById('file-btn');
            const emojiButton = document.getElementById('emojiButton');
            const emojiPanel = document.getElementById('emojiPanel');

            // File input change handler
            fileInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    currentFile = file;
                    showFilePreview(file);
                }
            });

            // Remove file button handler
            removeFileBtn.addEventListener('click', function() {
                fileInput.value = '';
                currentFile = null;
                filePreviewContainer.classList.add('d-none');
            });

            function showFilePreview(file) {
                let preview = '';

                if (file.type.startsWith('image/')) {
                    preview = `
                <div class="d-flex align-items-center">
                    <img src="${URL.createObjectURL(file)}" class="rounded" style="max-height: 50px;">
                    <span class="ms-2">${file.name}</span>
                </div>`;
                } else if (file.type.startsWith('video/')) {
                    preview = `
                <div class="d-flex align-items-center">
                    <i class="fas fa-video fa-2x text-primary"></i>
                    <span class="ms-2">${file.name}</span>
                </div>`;
                } else if (file.type.startsWith('audio/')) {
                    preview = `
                <div class="d-flex align-items-center">
                    <i class="fas fa-music fa-2x text-primary"></i>
                    <span class="ms-2">${file.name}</span>
                </div>`;
                } else {
                    preview = `
                <div class="d-flex align-items-center">
                    <i class="fas fa-file fa-2x text-primary"></i>
                    <span class="ms-2">${file.name}</span>
                </div>`;
                }

                filePreviewContent.innerHTML = preview;
                filePreviewContainer.classList.remove('d-none');
            }

            // Scroll to bottom initially
            chatBox.scrollTop = chatBox.scrollHeight;

            // Menu button toggle
            menuButton.addEventListener('click', () => {
                menuPopup.classList.toggle('d-none');
                menuButton.querySelector('i').style.transform = menuPopup.classList.contains('d-none') ?
                    'rotate(0)' : 'rotate(45deg)';
            });

            // File input trigger
            fileBtn.addEventListener('click', () => fileInput.click());

            // Emoji handling
            emojiButton.addEventListener('click', () => {
                emojiPanel.classList.toggle('d-none');
            });

            document.querySelectorAll('.emoji-btn').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    const emoji = e.currentTarget.dataset.emoji;
                    textarea.value += emoji;
                    textarea.focus();
                    adjustTextareaHeight(textarea);
                });
            });

            // Close panels when clicking outside
            document.addEventListener('click', (e) => {
                if (!menuButton.contains(e.target) && !menuPopup.contains(e.target)) {
                    menuPopup.classList.add('d-none');
                    menuButton.querySelector('i').style.transform = 'rotate(0)';
                }

                if (!emojiButton.contains(e.target) && !emojiPanel.contains(e.target)) {
                    emojiPanel.classList.add('d-none');
                }
            });


            // Update the form submission handler
            chatForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);

                // Add loading state
                const submitBtn = this.querySelector('button[type="submit"]');
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

                fetch(this.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            // Add new message to chat
                            appendMessage(data);

                            // Reset form and preview
                            this.reset();
                            fileInput.value = '';
                            currentFile = null;
                            filePreviewContainer.classList.add('d-none');

                            // Scroll to bottom
                            chatBox.scrollTop = chatBox.scrollHeight;
                        }
                    })
                    .catch(error => console.error('Error:', error))
                    .finally(() => {
                        // Reset submit button
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i>';
                    });
            });

            function appendMessage(data) {
                const message = data.message;
                const isSender = message.sender_id === {{ auth()->id() }};

                const messageHTML = `
            <div class="message-container mb-3 ${isSender ? 'text-end' : 'text-start'}">
                <div class="d-inline-block">
                    ${!isSender ? `<small class="text-muted d-block mb-1">${message.sender.name}</small>` : ''}
                    <div class="message-content p-3 rounded-3 ${isSender ? 'bg-primary text-white' : 'bg-light'}">
                        ${getMessageContent(message, data.file_url)}
                        <div class="message-meta mt-1">
                            <small class="${isSender ? 'text-white-50' : 'text-muted'}">
                                ${new Date().toLocaleTimeString('vi-VN', {hour: '2-digit', minute:'2-digit'})}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        `;

                chatBox.insertAdjacentHTML('beforeend', messageHTML);
            }

            function getMessageContent(message, fileUrl) {
                switch (message.type) {
                    case 'image':
                        return `
                    <img src="${fileUrl}" class="img-fluid rounded" style="max-width: 300px;" 
                         data-bs-toggle="modal" data-bs-target="#imageModal${message.id}" role="button">
                `;
                    case 'video':
                        return `
                    <video controls class="rounded" style="max-width: 300px;">
                        <source src="${fileUrl}" type="video/mp4">
                    </video>
                `;
                    case 'audio':
                        return `
                    <audio controls class="w-100">
                        <source src="${fileUrl}" type="audio/mpeg">
                    </audio>
                `;
                    case 'file':
                        return `
                    <a href="${fileUrl}" class="btn btn-sm ${message.sender_id === {{ auth()->id() }} ? 'btn-light' : 'btn-primary'}" download>
                        <i class="fas fa-download me-1"></i>
                        Tải file ${message.content}
                    </a>
                `;
                    default:
                        return `<p class="mb-0">${message.content}</p>`;
                }
            }

            function renderFileMessage(data) {
                const fileUrl = data.file_url;
                switch (data.message.type) {
                    case 'image':
                        return `<img src="${fileUrl}" class="img-fluid rounded" style="max-width: 300px;">`;
                    case 'video':
                        return `<video controls class="rounded" style="max-width: 300px;">
                        <source src="${fileUrl}" type="video/mp4">
                    </video>`;
                    case 'audio':
                        return `<audio controls class="w-100">
                        <source src="${fileUrl}" type="audio/mpeg">
                    </audio>`;
                    default:
                        return `<a href="${fileUrl}" class="btn btn-light" download>
                        <i class="fas fa-download me-1"></i>Tải file
                    </a>`;
                }
            }
        });
    </script>
@endsection
