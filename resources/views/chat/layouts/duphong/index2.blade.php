@extends('admin.layouts.master')

@section('content')
    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 3px;
        }

        .chat-list-item:hover {
            background-color: #f3f4f6;
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
            width: 50px;
            height: 50px;
            border-radius: 50%;
            border: 2px solid #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            object-fit: cover;
        }

        .chat-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            border: 2px solid #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            object-fit: cover;
        }

        .chat-name {
            font-size: 0.95rem;
            font-weight: 600;
            color: #2d3748;
            margin: 0;
        }

        .chat-time {
            font-size: 0.75rem;
            color: #718096;
            white-space: nowrap;
        }

        .search-container {
            position: relative;
            margin-bottom: 1rem;
        }

        .search-input {
            width: 100%;
            padding: 0.75rem 2.5rem;
            border-radius: 0.5rem;
            border: 1px solid #e2e8f0;
            background-color: #f8fafc;
            transition: all 0.3s ease;
        }

        .search-input:focus {
            background-color: #fff;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            outline: none;
        }

        .search-icon {
            position: absolute;
            left: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            color: #64748b;
            font-size: 0.875rem;
        }
    </style>
    <div class="page-content">
        <div class="container-xxl">
            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <img src="{{ auth()->user()->avatar ? asset('storage/' . auth()->user()->avatar) : asset('/admin/assets/images/users/avatar-1.jpg') }}"
                                    alt="Profile" class="header-avatar">
                                <h4 class="card-title mb-0 ms-3">Chats</h4>
                            </div>
                            <div>
                                <button class="btn btn-light btn-sm me-2">
                                    <i class="fas fa-video"></i>
                                </button>
                                <button class="btn btn-light btn-sm">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="search-container">
                                <input type="text" class="search-input" placeholder="Tìm kiếm bạn bè..."
                                    autocomplete="off">
                                <i class="fas fa-search search-icon"></i>
                            </div>

                            <div class="custom-scrollbar" style="max-height: calc(100vh - 300px); overflow-y: auto;">
                                @forelse ($users as $user)
                                    <div onclick="window.location.href='{{ route('chat.show', $user->id) }}'"
                                        class="chat-list-item d-flex align-items-center p-3 rounded cursor-pointer">
                                        <div class="position-relative">
                                            <img src="{{ $user->avatar ? asset('storage/' . $user->avatar) : asset('/admin/assets/images/users/avatar-1.jpg') }}"
                                                alt="{{ $user->name }}" class="chat-avatar">
                                            <span
                                                class="position-absolute bottom-0 end-0 transform translate-middle p-1 border-2 border-white rounded-circle
                                            {{ $user->isOnline() ? 'bg-success' : 'bg-secondary' }}">
                                            </span>
                                        </div>
                                        <div class="ms-3 flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <h6 class="chat-name">{{ $user->name }}</h6>
                                                @if ($user->last_message)
                                                    <small
                                                        class="chat-time">{{ $user->last_message->created_at->diffForHumans() }}</small>
                                                @else
                                                    Bắt đầu cuộc trò chuyện
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-4 text-muted">
                                        <i class="fas fa-users fa-3x mb-3"></i>
                                        <p>Không có bạn bè nào</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- <script src="https://cdn.tailwindcss.com"></script> --}}
@endsection
