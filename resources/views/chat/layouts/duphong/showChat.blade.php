@extends('chat.layouts.master')
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

        .text-ellipsis {
            text-overflow: ellipsis;
            white-space: nowrap;
            overflow: hidden;
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
        

        .flex-grow {
            flex: 1 1 auto;
        }

        .ml-2 {
            margin-left: 0.5rem;
        }

        .expanded-textarea {
            width: 100% !important;
        }

        @media (max-width: 640px) {
            .expanded-textarea {
                width: 280px !important;
            }
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
    </style>
    <div class="page-content">
        <div class="container-xxl">
            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        {{-- Tiêu đề --}}
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="card-title mb-0">Chat với {{ $user->name }}</h4>
                        </div>

                        {{-- Nội dung tin nhắn --}}
                        <div class="card-body" style="height: 400px; overflow-y: scroll;" id="chat-box">
                            @foreach ($messages as $message)
                                <div
                                    class="d-flex {{ $message->sender_id == auth()->id() ? 'justify-content-end' : 'justify-content-start' }} mb-2">
                                    <div class="p-2 rounded bg-light">
                                        <p class="mb-1">
                                            <strong>{{ $message->sender_id == auth()->id() ? 'Bạn' : $user->name }}:</strong>
                                        </p>

                                        {{-- Hiển thị tin nhắn văn bản --}}
                                        @if ($message->type === 'text')
                                            <p class="mb-0">{{ $message->content }}</p>
                                        @endif

                                        {{-- Hiển thị ảnh --}}
                                        @if ($message->isImage())
                                            <img src="{{ asset('storage/' . $message->file_path) }}" alt="Ảnh"
                                                class="img-fluid rounded" style="max-width: 250px;">
                                        @endif

                                        {{-- Hiển thị video --}}
                                        @if ($message->isVideo())
                                            <video controls style="max-width: 250px;">
                                                <source src="{{ asset('storage/' . $message->file_path) }}"
                                                    type="video/mp4">
                                            </video>
                                        @endif

                                        {{-- Hiển thị âm thanh --}}
                                        @if ($message->isAudio())
                                            <audio controls>
                                                <source src="{{ asset('storage/' . $message->file_path) }}"
                                                    type="audio/mpeg">
                                            </audio>
                                        @endif

                                        {{-- Hiển thị file đính kèm --}}
                                        @if ($message->isFile())
                                            <a href="{{ asset('storage/' . $message->file_path) }}"
                                                class="btn btn-sm btn-secondary" download>
                                                Tải file
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="card-footer bg-white p-3">
                            <form id="chat-form" action="{{ route('chat.send') }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="receiver_id" value="{{ $user->id }}">
                                <div class="flex items-center space-x-4">
                                    <div id="leftIcons" class="flex items-center space-x-4 transition-all duration-300">
                                        <div class="relative">
                                            <button type="button" id="menuButton" class="text-blue-600 text-2xl">
                                                <i class="fas fa-plus-circle"></i>
                                            </button>
                                            <div id="menuPopup"
                                                class="hidden absolute bottom-12 left-0 bg-white rounded-lg shadow-lg p-4 w-64">
                                                <div class="flex items-center justify-between py-2 border-b hover:bg-gray-100 cursor-pointer">
                                                    <span>Chia sẻ file</span>
                                                    <i class="fas fa-file-alt text-blue-600"></i>
                                                </div>
                                                <div class="flex items-center justify-between py-2 border-b hover:bg-gray-100 cursor-pointer">
                                                    <span>Vị trí</span>
                                                    <i class="fas fa-location-arrow text-blue-600"></i>
                                                </div>
                                                <div class="flex items-center justify-between py-2 border-b hover:bg-gray-100 cursor-pointer">
                                                    <span>Chơi game</span>
                                                    <i class="fas fa-gamepad text-blue-600"></i>
                                                </div>
                                                <div class="flex items-center justify-between py-2 border-b hover:bg-gray-100 cursor-pointer">
                                                    <span>Tưởng tượng</span>
                                                    <i class="fas fa-image text-blue-600"></i>
                                                </div>
                                                <div class="flex items-center justify-between py-2 hover:bg-gray-100 cursor-pointer">
                                                    <span>Meta AI</span>
                                                    <span>♾️</span>
                                                </div>
                                            </div>
                                        </div>
                                        <button type="button" class="text-blue-600 text-2xl"><i
                                                class="fas fa-camera"></i></button>
                                        <button type="button" class="text-blue-600 text-2xl"><i
                                                class="fas fa-image"></i></button>
                                        <button type="button" class="text-blue-600 text-2xl"><i
                                                class="fas fa-microphone"></i></button>
                                    </div>

                                    <button type="button" id="expandButton" class="hidden text-blue-600 text-xl">
                                        <i class="fas fa-chevron-right"></i>
                                    </button>

                                    <div class="flex items-center bg-gray-100 rounded-full px-4 py-2 flex-grow border border-gray-300">
                                        <div class="flex-1 min-w-0">
                                            <textarea name="content" 
                                                class="bg-transparent resize-none text-gray-500 focus:outline-none scrollbar-none w-full" 
                                                placeholder="Aa" 
                                                rows="1" 
                                                required 
                                                style="word-wrap: break-word; word-break: break-word; min-height: 24px; max-height: 120px;"
                                                oninput="this.style.height = '24px'; this.style.height = (this.scrollHeight) + 'px';"
                                            ></textarea>
                                        </div>
                                        <button type="button" id="emojiButton" class="text-blue-600 text-2xl ml-2 flex-shrink-0">
                                            <i class="fas fa-smile"></i>
                                        </button>
                                    </div>

                                    <button type="submit" id="sendButton" class="text-blue-600 text-2xl">
                                        <i class="fas fa-thumbs-up"></i>
                                    </button>
                                </div>
                            </form>

                            <div id="emojiPanel" class="hidden bg-white rounded-lg shadow-lg p-4 mt-2">
                                <div class="grid grid-cols-5 gap-2 h-[150px] overflow-y-auto scrollbar-none">
                                    <button class="text-2xl hover:bg-gray-100 p-2 rounded-lg transition-colors">😊</button>
                                    <button class="text-2xl hover:bg-gray-100 p-2 rounded-lg transition-colors">❤️</button>
                                    <button class="text-2xl hover:bg-gray-100 p-2 rounded-lg transition-colors">😂</button>
                                    <button class="text-2xl hover:bg-gray-100 p-2 rounded-lg transition-colors">👍</button>
                                    <button class="text-2xl hover:bg-gray-100 p-2 rounded-lg transition-colors">😭</button>
                                    <button class="text-2xl hover:bg-gray-100 p-2 rounded-lg transition-colors">🥰</button>
                                    <button class="text-2xl hover:bg-gray-100 p-2 rounded-lg transition-colors">😍</button>
                                    <button class="text-2xl hover:bg-gray-100 p-2 rounded-lg transition-colors">😘</button>
                                    <button class="text-2xl hover:bg-gray-100 p-2 rounded-lg transition-colors">🤗</button>
                                    <button class="text-2xl hover:bg-gray-100 p-2 rounded-lg transition-colors">😮</button>
                                    <button class="text-2xl hover:bg-gray-100 p-2 rounded-lg transition-colors">😢</button>
                                    <button class="text-2xl hover:bg-gray-100 p-2 rounded-lg transition-colors">😡</button>
                                    <button class="text-2xl hover:bg-gray-100 p-2 rounded-lg transition-colors">🎉</button>
                                    <button class="text-2xl hover:bg-gray-100 p-2 rounded-lg transition-colors">✨</button>
                                    <button class="text-2xl hover:bg-gray-100 p-2 rounded-lg transition-colors">🌟</button>
                                    <button class="text-2xl hover:bg-gray-100 p-2 rounded-lg transition-colors">🔥</button>
                                    <button class="text-2xl hover:bg-gray-100 p-2 rounded-lg transition-colors">💕</button>
                                    <button class="text-2xl hover:bg-gray-100 p-2 rounded-lg transition-colors">💯</button>
                                    <button class="text-2xl hover:bg-gray-100 p-2 rounded-lg transition-colors">🙏</button>
                                    <button class="text-2xl hover:bg-gray-100 p-2 rounded-lg transition-colors">👋</button>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        const sendButton = document.getElementById('sendButton');
        const textarea = document.querySelector('textarea');
        const menuButton = document.getElementById('menuButton');
        const menuPopup = document.getElementById('menuPopup');
        const emojiButton = document.getElementById('emojiButton');
        const emojiPanel = document.getElementById('emojiPanel');
        const leftIcons = document.getElementById('leftIcons');
        const expandButton = document.getElementById('expandButton');
        let isMenuOpen = false;
        let isEmojiPanelOpen = false;
        let activeButton = null;
        let isCollapsed = false;


        textarea.addEventListener('input', function() {
            // Reset height first
            this.style.height = '24px';

            // Calculate new height
            const newHeight = Math.min(this.scrollHeight, 120);
            this.style.height = newHeight + 'px';

            // Toggle send/thumbs-up icon
            if (this.value.trim().length > 0) {
                sendButton.innerHTML = '<i class="fas fa-paper-plane"></i>';
            } else {
                sendButton.innerHTML = '<i class="fas fa-thumbs-up"></i>';
            }

            // Handle icon collapse/expand
            if (this.value.length > 0 && !isCollapsed) {
                collapseIcons();
            } else if (this.value.length === 0 && isCollapsed) {
                expandIcons();
            }
        });

        // Function to reset all icons to initial blue color
        function resetAllToBlue() {
            const allButtons = document.querySelectorAll('button');
            allButtons.forEach(button => {
                button.classList.remove('text-gray-600');
                button.classList.add('text-blue-600');
            });
            activeButton = null;
        }

        // Function to set all icons to gray except active one
        function setActiveButton(clickedButton) {
            if (activeButton === clickedButton) {
                resetAllToBlue(); // Reset all to blue if clicking same button
            } else {
                const allButtons = document.querySelectorAll('button');
                allButtons.forEach(button => {
                    if (button !== clickedButton) {
                        button.classList.remove('text-blue-600');
                        button.classList.add('text-gray-600');
                    } else {
                        button.classList.remove('text-gray-600');
                        button.classList.add('text-blue-600');
                    }
                });
                activeButton = clickedButton;
            }
        }

        menuButton.addEventListener('click', () => {
            isMenuOpen = !isMenuOpen;
            menuPopup.classList.toggle('hidden');
            setActiveButton(menuButton);

            const icon = menuButton.querySelector('i');
            icon.style.transform = isMenuOpen ? 'rotate(45deg)' : 'rotate(0)';
            icon.style.transition = 'transform 0.3s ease';
        });

        sendButton.addEventListener('click', () => {
            const message = textarea.value.trim();
            if (message) {
                // Reset everything to initial state
                textarea.value = '';
                textarea.style.height = '24px';

                // Reset all buttons to blue
                const allButtons = document.querySelectorAll('button');
                allButtons.forEach(button => {
                    button.classList.remove('text-gray-600');
                    button.classList.add('text-blue-600');
                    // Reset icon colors inside buttons
                    const icon = button.querySelector('i');
                    if (icon) {
                        icon.classList.remove('text-gray-600');
                        icon.classList.add('text-blue-600');
                    }
                });

                // Change back to thumbs up
                sendButton.innerHTML = '<i class="fas fa-thumbs-up text-blue-600 text-xl"></i>';

                // Reset panels
                emojiPanel.classList.add('hidden');
                isEmojiPanelOpen = false;
                menuPopup.classList.add('hidden');
                isMenuOpen = false;
                menuButton.querySelector('i').style.transform = 'rotate(0)';

                // Show all icons
                expandIcons();

                // Reset active button state
                activeButton = null;

                console.log('Sending message:', message);
            }
        });

        emojiButton.addEventListener('click', () => {
            isEmojiPanelOpen = !isEmojiPanelOpen;
            emojiPanel.classList.toggle('hidden');

            // Make emoji button gray, others blue
            const allButtons = document.querySelectorAll('button');
            allButtons.forEach(button => {
                if (button === emojiButton) {
                    button.classList.remove('text-blue-600');
                    button.classList.add('text-gray-600');
                } else {
                    button.classList.remove('text-gray-600');
                    button.classList.add('text-blue-600');
                }
            });
        });

        // Add click handlers for other buttons
        document.querySelectorAll('button').forEach(button => {
            if (button !== menuButton && button !== emojiButton) {
                button.addEventListener('click', () => {
                    setActiveButton(button);
                });
            }
        });

        // Add emoji click handler
        document.querySelector('.grid').addEventListener('click', (e) => {
            if (e.target.tagName === 'BUTTON') {
                const emoji = e.target.textContent;
                textarea.value += emoji;

                // Change thumbs-up to paper-plane without changing other icons
                sendButton.innerHTML = '<i class="fas fa-paper-plane text-blue-600 text-xl"></i>';

                // Don't close emoji panel - allow multiple selections
                e.stopPropagation();
                e.preventDefault();

                // Keep emoji button gray, others blue
                const allButtons = document.querySelectorAll('button');
                allButtons.forEach(button => {
                    if (button === emojiButton) {
                        button.classList.remove('text-blue-600');
                        button.classList.add('text-gray-600');
                    } else {
                        button.classList.remove('text-gray-600');
                        button.classList.add('text-blue-600');
                    }
                });
            }
        });

        // Close panels when clicking outside
        document.addEventListener('click', (e) => {
            if (!menuButton.contains(e.target) && !menuPopup.contains(e.target) && isMenuOpen) {
                isMenuOpen = false;
                menuPopup.classList.add('hidden');
                menuButton.querySelector('i').style.transform = 'rotate(0)';
            }

            if (!emojiButton.contains(e.target) && !emojiPanel.contains(e.target) && isEmojiPanelOpen) {
                isEmojiPanelOpen = false;
                emojiPanel.classList.add('hidden');
            }

            if (!textarea.contains(e.target) && textarea.value.length === 0 && isCollapsed) {
                expandIcons();
            }
        });

        // Update textarea focus handler
        textarea.addEventListener('focus', () => {
            if (isEmojiPanelOpen) {
                // Close emoji panel when clicking textarea
                emojiPanel.classList.add('hidden');
                isEmojiPanelOpen = false;
                // Reset emoji button color to blue
                emojiButton.classList.remove('text-gray-600');
                emojiButton.classList.add('text-blue-600');
            }
        });

        function collapseIcons() {
            leftIcons.classList.add('icons-collapsed');
            expandButton.classList.remove('hidden');
            isCollapsed = true;
            textarea.classList.add('expanded-textarea');
        }

        function expandIcons() {
            leftIcons.classList.remove('icons-collapsed');
            expandButton.classList.add('hidden');
            isCollapsed = false;
            textarea.classList.remove('expanded-textarea');

            // Reset all icons to blue when expanding
            resetAllToBlue();
        }



        expandButton.addEventListener('click', () => {
            expandIcons();
            event.stopPropagation();
        });

        document.getElementById('chat-form').addEventListener('submit', function(event) {
            event.preventDefault();
            let formData = new FormData(this);

            fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    let chatBox = document.getElementById('chat-box');
                    let content = `<div class='d-flex justify-content-end mb-2'><div class='p-2 rounded bg-light'>
            <p class='mb-1'><strong>Bạn:</strong></p>`;

                    if (data.message.type === 'text') {
                        content += `<p class='mb-0'>${data.message.content}</p>`;
                    } else if (data.message.type === 'image') {
                        content +=
                            `<img src='/storage/${data.message.file_path}' class='img-fluid rounded' style='max-width: 250px;'>`;
                    }

                    content += `</div></div>`;
                    chatBox.innerHTML += content;
                    this.reset();
                    chatBox.scrollTop = chatBox.scrollHeight;

                    // Reset UI state
                    resetAllToBlue();
                    expandIcons();
                    sendButton.innerHTML = '<i class="fas fa-thumbs-up"></i>';
                });
        });
    </script>
@endsection
