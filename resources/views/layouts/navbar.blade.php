<header class="bg-[#001D4B] text-white shadow-md transition-all duration-300">
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-20 md:h-28">
            <!-- Logo & Title Section -->
            <div class="flex items-center space-x-3 md:space-x-6">
                <!-- Logo -->
                <div class="bg-white p-1 rounded-3xl shadow-inner shrink-0">
                    <img src="{{ asset('assets/images/kai_logo.png') }}" alt="KAI"
                        class="h-10 w-auto md:h-20 transition-all duration-300">
                </div>

                <!-- Text -->
                <div class="flex flex-col justify-center">
                    <h1 class="text-lg md:text-3xl font-bold leading-tight transition-all duration-300">
                        {{-- <span class="hidden md:block">Sistem Monitoring Kondektur</span> --}}
                        <span class="block md:hidden">Monitoring Scan</span>
                    </h1>
                    <p
                        class="text-[10px] md:text-[18px] font-light text-blue-200 uppercase tracking-widest transition-all duration-300">
                        KAI DAOP 3 Cirebon
                    </p>
                </div>
            </div>

            <!-- User Profile Section -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open"
                    class="flex items-center space-x-2 md:space-x-4 focus:outline-none p-2 md:p-4 rounded-xl md:rounded-2xl transition-all duration-200 hover:bg-white/10 active:bg-white/20"
                    :class="open ? 'bg-white/10' : ''">

                    <div class="text-right hidden sm:block">
                        <p class="text-sm md:text-[20px] font-bold leading-tight text-white">Aulya Sab</p>
                        <p class="text-xs md:text-[15px] font-light text-blue-200 tracking-widest">Kondektur</p>
                    </div>

                    <div
                        class="h-8 w-8 md:h-10 md:w-10 rounded-full bg-white flex items-center justify-center text-blue-700 font-bold border-2 border-blue-400 shrink-0 text-xs md:text-base">
                        AS
                    </div>

                    <svg class="h-4 w-4 fill-current text-blue-200 transition-transform duration-200"
                        :class="{ 'rotate-180': open }" viewBox="0 0 20 20">
                        <path
                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" />
                    </svg>
                </button>

                <!-- Dropdown Menu -->
                <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="transform opacity-0 scale-95"
                    x-transition:enter-end="transform opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-75"
                    x-transition:leave-start="transform opacity-100 scale-100"
                    x-transition:leave-end="transform opacity-0 scale-95"
                    class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-xl py-2 z-50 text-gray-800 border border-gray-100 overflow-hidden"
                    style="display: none;">

                    <!-- Mobile User Info inside Dropdown -->
                    <div class="px-4 py-3 border-b border-gray-100 sm:hidden bg-gray-50">
                        <p class="text-sm font-bold text-gray-900 leading-tight">Aulya Sab</p>
                        <p class="text-xs text-gray-500">Kondektur</p>
                    </div>

                    <a href="#"
                        class="block px-4 py-2 text-sm hover:bg-blue-50 hover:text-blue-700 transition">Profile Saya</a>
                    <div class="border-t border-gray-100 my-1"></div>
                    <form action="#" method="POST">
                        <button type="submit"
                            class="w-full text-left block px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
