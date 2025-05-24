<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Nhà Hàng') - Đặt Bàn</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <!-- Navigation đơn giản -->
    <nav class="bg-white shadow">
        <div class="container mx-auto px-4 py-3">
            <div class="flex justify-between items-center">
                <h1 class="text-lg font-bold">🍽️ Nhà Hàng ABC</h1>
                
                @auth
                <div class="flex items-center space-x-4 text-sm">
                    <span>{{ Auth::user()->name }}</span>
                    <a href="{{ route('customer.dashboard') }}" class="text-blue-600">Dashboard</a>
                    <a href="{{ route('customer.select-table') }}" class="text-blue-600">Chọn bàn</a>
                    <a href="{{ route('customer.reservations') }}" class="text-blue-600">Đặt bàn của tôi</a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button class="text-red-600">Đăng xuất</button>
                    </form>
                </div>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Thông báo đơn giản -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 mx-4 mt-4 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 mx-4 mt-4 rounded">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 mx-4 mt-4 rounded">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Main Content -->
    <main class="py-6">
        @yield('content')
    </main>

    <!-- Footer đơn giản -->
    <footer class="bg-gray-800 text-white text-center py-4 mt-8">
        <p>&copy; 2025 Nhà Hàng ABC - Dự án tốt nghiệp</p>
    </footer>
</body>
</html>