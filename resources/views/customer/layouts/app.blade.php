<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Hiquila Restaurant') - Nhà hàng hải sản cao cấp</title>
    
    <!-- Favicon -->
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🦐</text></svg>">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Custom CSS -->
    <style>
        /* Custom animations */
        @keyframes wave {
            0%, 100% { transform: rotate(-3deg); }
            50% { transform: rotate(3deg); }
        }
        
        .wave {
            animation: wave 2s ease-in-out infinite;
        }
        
        /* Smooth transitions */
        * {
            transition: all 0.3s ease;
        }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        
        ::-webkit-scrollbar-thumb {
            background: linear-gradient(45deg, #0ea5e9, #06b6d4);
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(45deg, #0284c7, #0891b2);
        }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-50 to-blue-50 min-h-screen">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="{{ route('customer.dashboard') }}" class="flex items-center group">
                        <span class="text-4xl mr-3 wave">🦐</span>
                        <div>
                            <h1 class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-teal-600 bg-clip-text text-transparent">
                                Hiquila Restaurant
                            </h1>
                            <p class="text-xs text-gray-500 group-hover:text-blue-500 transition-colors">
                                Nhà hàng hải sản cao cấp
                            </p>
                        </div>
                    </a>
                </div>
                
                @auth
                <!-- Desktop Navigation -->
                <div class="hidden md:flex items-center space-x-6">
                    <a href="{{ route('customer.dashboard') }}" 
                       class="flex items-center px-4 py-2 rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-600 font-medium transition-all
                              {{ request()->routeIs('customer.dashboard') ? 'bg-blue-50 text-blue-600' : '' }}">
                        <span class="text-lg mr-2">🏠</span>
                        Dashboard
                    </a>
                    
                    <a href="{{ route('customer.select-table') }}" 
                       class="flex items-center px-4 py-2 rounded-lg text-gray-700 hover:bg-teal-50 hover:text-teal-600 font-medium transition-all
                              {{ request()->routeIs('customer.select-table') ? 'bg-teal-50 text-teal-600' : '' }}">
                        <span class="text-lg mr-2">🦞</span>
                        Chọn bàn
                    </a>
                    
                    <a href="{{ route('customer.reservations') }}" 
                       class="flex items-center px-4 py-2 rounded-lg text-gray-700 hover:bg-green-50 hover:text-green-600 font-medium transition-all
                              {{ request()->routeIs('customer.reservations') ? 'bg-green-50 text-green-600' : '' }}">
                        <span class="text-lg mr-2">📋</span>
                        Đặt bàn của tôi
                    </a>
                    
                    <a href="{{ route('customer.floor-plan') }}" 
                       class="flex items-center px-4 py-2 rounded-lg text-gray-700 hover:bg-purple-50 hover:text-purple-600 font-medium transition-all
                              {{ request()->routeIs('customer.floor-plan') ? 'bg-purple-50 text-purple-600' : '' }}">
                        <span class="text-lg mr-2">🗺️</span>
                        Sơ đồ
                    </a>
                </div>

                <!-- User Menu -->
                <div class="flex items-center space-x-4">
                    <!-- User Info -->
                    <div class="hidden md:flex items-center px-4 py-2 bg-gray-50 rounded-lg">
                        <span class="text-lg mr-2">👤</span>
                        <div class="text-sm">
                            <div class="font-semibold text-gray-800">{{ Auth::user()->name }}</div>
                        </div>
                    </div>
                    
                    <!-- Logout Button -->
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button class="flex items-center px-4 py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 font-medium transition-all transform hover:scale-105">
                            <span class="text-lg mr-2">🚪</span>
                            <span class="hidden md:inline">Đăng xuất</span>
                        </button>
                    </form>

                    <!-- Mobile Menu Button -->
                    <button id="mobile-menu-btn" class="md:hidden p-2 rounded-lg bg-gray-100 hover:bg-gray-200 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>
                @endauth
            </div>
        </div>

        <!-- Mobile Navigation -->
        <div id="mobile-menu" class="md:hidden bg-white border-t hidden">
            <div class="px-4 py-2 space-y-2">
                <a href="{{ route('customer.dashboard') }}" 
                   class="flex items-center px-4 py-3 rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-600 font-medium">
                    <span class="text-lg mr-3">🏠</span>
                    Dashboard
                </a>
                <a href="{{ route('customer.select-table') }}" 
                   class="flex items-center px-4 py-3 rounded-lg text-gray-700 hover:bg-teal-50 hover:text-teal-600 font-medium">
                    <span class="text-lg mr-3">🦞</span>
                    Chọn bàn
                </a>
                <a href="{{ route('customer.reservations') }}" 
                   class="flex items-center px-4 py-3 rounded-lg text-gray-700 hover:bg-green-50 hover:text-green-600 font-medium">
                    <span class="text-lg mr-3">📋</span>
                    Đặt bàn của tôi
                </a>
                <a href="{{ route('customer.floor-plan') }}" 
                   class="flex items-center px-4 py-3 rounded-lg text-gray-700 hover:bg-purple-50 hover:text-purple-600 font-medium">
                    <span class="text-lg mr-3">🗺️</span>
                    Sơ đồ tầng
                </a>
                <div class="border-t pt-2 mt-2">
                    <div class="flex items-center px-4 py-2 text-sm text-gray-600">
                        <span class="text-lg mr-2">👤</span>
                        {{ Auth::user()->name }}
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Alert Messages -->
    <div class="max-w-7xl mx-auto px-4 mt-4">
        @if(session('success'))
            <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-400 text-green-800 px-6 py-4 rounded-lg shadow-md mb-4 flex items-center">
                <span class="text-2xl mr-3">✅</span>
                <div>
                    <div class="font-semibold">Thành công!</div>
                    <div>{{ session('success') }}</div>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-gradient-to-r from-red-50 to-pink-50 border-l-4 border-red-400 text-red-800 px-6 py-4 rounded-lg shadow-md mb-4 flex items-center">
                <span class="text-2xl mr-3">❌</span>
                <div>
                    <div class="font-semibold">Có lỗi xảy ra!</div>
                    <div>{{ session('error') }}</div>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="bg-gradient-to-r from-yellow-50 to-orange-50 border-l-4 border-yellow-400 text-yellow-800 px-6 py-4 rounded-lg shadow-md mb-4">
                <div class="flex items-start">
                    <span class="text-2xl mr-3">⚠️</span>
                    <div>
                        <div class="font-semibold mb-2">Vui lòng kiểm tra lại:</div>
                        <ul class="list-disc list-inside space-y-1 text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Main Content -->
    <main class="min-h-screen">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gradient-to-r from-gray-800 to-gray-900 text-white">
        <div class="max-w-7xl mx-auto px-4 py-12">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Restaurant Info -->
                <div>
                    <div class="flex items-center mb-4">
                        <span class="text-3xl mr-3">🦐</span>
                        <div>
                            <h3 class="text-xl font-bold">Hiquila Restaurant</h3>
                            <p class="text-gray-300 text-sm">Nhà hàng hải sản cao cấp</p>
                        </div>
                    </div>
                    <p class="text-gray-300 text-sm leading-relaxed">
                        Thưởng thức hải sản tươi ngon nhất với không gian sang trọng 
                        và dịch vụ chuyên nghiệp tại Hiquila Restaurant.
                    </p>
                </div>

                <!-- Contact Info -->
                <div>
                    <h4 class="text-lg font-semibold mb-4 text-blue-300">Liên hệ</h4>
                    <div class="space-y-3 text-sm">
                        <div class="flex items-center">
                            <span class="text-lg mr-3">📍</span>
                            <span class="text-gray-300">123 Đường Biển, Quận 1, TP.HCM</span>
                        </div>
                        <div class="flex items-center">
                            <span class="text-lg mr-3">📞</span>
                            <span class="text-gray-300">0901 234 567</span>
                        </div>
                        <div class="flex items-center">
                            <span class="text-lg mr-3">✉️</span>
                            <span class="text-gray-300">info@hiquila-restaurant.com</span>
                        </div>
                        <div class="flex items-center">
                            <span class="text-lg mr-3">🕐</span>
                            <span class="text-gray-300">8:00 - 23:00 hàng ngày</span>
                        </div>
                    </div>
                </div>

                <!-- Quick Links -->
                <div>
                    <h4 class="text-lg font-semibold mb-4 text-teal-300">Liên kết nhanh</h4>
                    <div class="space-y-2 text-sm">
                        <a href="{{ route('customer.dashboard') }}" class="block text-gray-300 hover:text-white hover:pl-2 transition-all">
                            🏠 Trang chủ
                        </a>
                        <a href="{{ route('customer.select-table') }}" class="block text-gray-300 hover:text-white hover:pl-2 transition-all">
                            🦞 Đặt bàn
                        </a>
                        <a href="{{ route('customer.floor-plan') }}" class="block text-gray-300 hover:text-white hover:pl-2 transition-all">
                            🗺️ Sơ đồ nhà hàng
                        </a>
                        <a href="{{ route('customer.reservations') }}" class="block text-gray-300 hover:text-white hover:pl-2 transition-all">
                            📋 Quản lý đặt bàn
                        </a>
                    </div>
                </div>
            </div>

            <!-- Bottom Footer -->
            <div class="border-t border-gray-700 mt-8 pt-6 text-center">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <p class="text-gray-400 text-sm">
                        &copy; 2025 Hiquila Restaurant. Tất cả quyền được bảo lưu.
                    </p>
                    <div class="flex items-center mt-4 md:mt-0 space-x-4">
                        <span class="text-gray-400 text-sm">Được phát triển với</span>
                        <span class="text-red-400 text-lg">❤️</span>
                        <span class="text-gray-400 text-sm">bởi đội ngũ Hiquila</span>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script>
        // Mobile menu toggle
        document.getElementById('mobile-menu-btn')?.addEventListener('click', function() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        });

        // Auto hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('[class*="bg-gradient-to-r"][class*="border-l-4"]');
            alerts.forEach(function(alert) {
                if (alert.parentElement) {
                    alert.style.opacity = '0';
                    alert.style.transform = 'translateY(-20px)';
                    setTimeout(function() {
                        alert.remove();
                    }, 300);
                }
            });
        }, 5000);

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>

    @stack('scripts')
</body>
</html>