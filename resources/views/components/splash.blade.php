<!-- Splash Screen -->
<div x-show="showSplash" 
        x-transition:leave="transition ease-in duration-500"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center bg-gradient-to-br from-slate-50 via-white to-slate-100">
    
    <!-- Decorative Elements -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <!-- Animated Circles -->
        <div class="absolute -top-20 -right-20 w-80 h-80 bg-gradient-to-br from-[#FF7300] to-[#FF9500] rounded-full mix-blend-multiply filter blur-3xl opacity-10 logo-pulse"></div>
        <div class="absolute -bottom-20 -left-20 w-80 h-80 bg-gradient-to-br from-[#001D4B] to-[#003D7A] rounded-full mix-blend-multiply filter blur-3xl opacity-10 logo-pulse" style="animation-delay: 1s;"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-gradient-to-br from-blue-200 to-purple-200 rounded-full mix-blend-multiply filter blur-3xl opacity-5 logo-pulse" style="animation-delay: 0.5s;"></div>
        
        <!-- Grid Pattern -->
        <div class="absolute inset-0 opacity-5">
            <div class="absolute inset-0" style="background-image: radial-gradient(circle, #001D4B 1px, transparent 1px); background-size: 40px 40px;"></div>
        </div>
    </div>

    <!-- Content -->
    <div class="relative z-10 text-center px-4">
        <!-- Logo with Animation -->
        <div class="mb-10 bounce-anim">
            <div class="relative inline-block">
                <!-- Outer glow -->
                <div class="absolute inset-0 bg-gradient-to-br from-[#FF7300] to-[#FF9500] rounded-3xl blur-2xl opacity-20"></div>
                
                <!-- Logo container -->
                <div class="relative bg-white rounded-3xl p-8 shadow-2xl border-4 border-slate-100">
                    <img src="{{ asset('assets/images/kai_logo.png') }}" alt="KAI" class="w-28 h-28">
                </div>
                
                <!-- Decorative rings -->
                <div class="absolute -inset-4 border-4 border-[#FF7300] rounded-3xl opacity-20"></div>
                <div class="absolute -inset-8 border-2 border-[#001D4B] rounded-3xl opacity-10"></div>
            </div>
        </div>

        <!-- App Name -->
        <div class="mb-8">
            <h1 class="text-6xl font-black mb-3">
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#001D4B] to-[#003D7A]">Rail</span><span class="text-transparent bg-clip-text bg-gradient-to-r from-[#FF7300] to-[#FF9500]">Attend</span>
            </h1>
            <p class="text-slate-600 text-xl font-semibold mb-2">Sistem Monitoring Kondektur</p>
        </div>

        <!-- Loading Indicator -->
        <div class="flex flex-col items-center space-y-6">
            <!-- Loading Text with dots animation -->
            <div class="flex items-center space-x-2">
                <p class="text-slate-700 text-base font-semibold">Memuat aplikasi</p>
                <div class="flex space-x-1">
                    <div class="w-2 h-2 bg-[#FF7300] rounded-full bounce-anim"></div>
                    <div class="w-2 h-2 bg-[#FF7300] rounded-full bounce-anim" style="animation-delay: 0.2s;"></div>
                    <div class="w-2 h-2 bg-[#FF7300] rounded-full bounce-anim" style="animation-delay: 0.4s;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="absolute bottom-10 left-0 right-0 text-center px-4">
        <p class="text-slate-500 text-sm font-semibold">PT Kereta Api Indonesia</p>
        <p class="text-slate-400 text-xs font-medium mt-1">DAOP 3 Cirebon</p>
    </div>
</div>