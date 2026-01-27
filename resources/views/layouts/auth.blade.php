<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - {{ env('APP_NAME') }}</title>
    @vite('resources/css/app.css')
    <style>
        @keyframes fadeOut {
            from { opacity: 1; }
            to { opacity: 0; }
        }
        
        @keyframes slideUp {
            from { 
                opacity: 0;
                transform: translateY(20px);
            }
            to { 
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        .fade-out {
            animation: fadeOut 0.5s ease-out forwards;
        }

        .slide-up {
            animation: slideUp 0.6s ease-out;
        }

        .logo-pulse {
            animation: pulse 2s ease-in-out infinite;
        }

        .spinner {
            animation: spin 1s linear infinite;
        }

        .bounce-anim {
            animation: bounce 1.5s ease-in-out infinite;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-100 via-slate-200 to-slate-300 min-h-screen" x-data="{ 
    showSplash: true,
    init() {
        setTimeout(() => {
            this.showSplash = false;
        }, 3000);
    }
}">
    
    @include('components.splash')

    <!-- Main Login Content -->
    <div x-show="!showSplash" 
         x-transition:enter="transition ease-out duration-700"
         x-transition:enter-start="opacity-0 transform scale-95"
         x-transition:enter-end="opacity-100 transform scale-100">
        
        <!-- Decorative Background -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute top-0 right-0 w-96 h-96 bg-[#FF7300] rounded-full mix-blend-multiply filter blur-3xl opacity-10"></div>
            <div class="absolute bottom-0 left-0 w-96 h-96 bg-[#001D4B] rounded-full mix-blend-multiply filter blur-3xl opacity-10"></div>
        </div>

        <div class="min-h-screen flex items-center justify-center p-4 relative z-10">
            <div class="w-full max-w-md">
                
                <!-- Logo & Title -->
                <div class="text-center mb-5 slide-up">
                    <div class="relative inline-block">
                        <div class="absolute inset-0 bg-[#FF7300] rounded-2xl blur-xl opacity-20"></div>
                        <div class="relative inline-flex items-center justify-center w-20 h-20">
                            <img src="{{ asset('assets/images/kai_logo.png') }}" alt="KAI" class="w-30 h-20">
                        </div>
                    </div>
                    <h1 class="text-3xl font-bold text-[#001D4B] mb-2">RailAttend</h1>
                    <p class="text-slate-600 font-medium">Sistem Monitoring Kondektur</p>
                </div>

                <!-- Login Card -->
                @yield('content')

                <!-- Copyright -->
                <p class="text-center text-sm text-slate-600 mt-8 font-medium slide-up" style="animation-delay: 0.4s;">
                    © {{ date('Y') }} KAI DAOP 3 Cirebon. All rights reserved.
                </p>
            </div>
        </div>
    </div>
    
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>