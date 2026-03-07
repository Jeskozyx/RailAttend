<header class="bg-gradient-to-r from-[#001D4B] to-[#002B6B] text-white shadow-lg" x-data="{ open: false, mobileMenu: false, rekapDropdown: false, userDropdown: false, keretaDropdown: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-20">
            <!-- Logo & Title Section -->
            <div class="flex items-center space-x-3 flex-shrink-0">
                <!-- Logo -->
                <div class="relative">
                    <div
                        class="bg-white p-1 rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-0.5">
                        <img src="{{ asset('assets/images/kai_logo.png') }}" alt="KAI"
                            class="h-10 w-auto transition-transform hover:scale-105 duration-300">
                    </div>
                </div>

                <!-- Text -->
                <div class="hidden lg:flex flex-col justify-center">
                    <h1 class="text-2xl font-bold leading-tight tracking-tight">
                        RailAttend
                    </h1>
                    <p class="text-[11px] font-medium text-blue-200/80 uppercase tracking-widest">
                        KAI DAOP 3 Cirebon
                    </p>
                </div>
            </div>

            @role('Admin')
                <!-- Navigation Menu - Center -->
                <nav class="hidden md:flex items-center space-x-1">
                    <!-- Dashboard -->
                    <a href="{{ route('dashboard') }}"
                        class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 hover:bg-white/10 active:bg-white/20 flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        <span>Dashboard</span>
                    </a>

                    <!-- Kereta -->
                    <!-- Kereta Dropdown -->
                    <div class="relative" @mouseenter="keretaDropdown = true" @mouseleave="keretaDropdown = false">
                        <button
                            class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 hover:bg-white/10 flex items-center space-x-2"
                            :class="keretaDropdown ? 'bg-white/10' : ''">
                            <svg class="w-5 h-5 text-white opacity-90 transition-transform duration-500" fill="currentColor"
                                viewBox="0 0 24 24">
                                <path
                                    d="M12 2c-4 0-8 .5-8 4v9.5C4 17.43 5.57 19 7.5 19L6 20.5v.5h12v-.5L16.5 19c1.93 0 3.5-1.57 3.5-3.5V6c0-3.5-4-4-8-4zM7.5 17c-.83 0-1.5-.67-1.5-1.5S6.67 14 7.5 14s1.5.67 1.5 1.5S8.33 17 7.5 17zm3.5-6H6V6h5v5zm7 4.5c0 .83-.67 1.5-1.5 1.5s-1.5-.67-1.5-1.5.67-1.5 1.5-1.5 1.5.67 1.5 1.5zm0-4.5h-5V6h5v5z" />
                            </svg>
                            <span>Kereta</span>
                            <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': keretaDropdown }"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <!-- Dropdown Menu -->
                        <div x-show="keretaDropdown" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 transform scale-95"
                            x-transition:enter-end="opacity-100 transform scale-100"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 transform scale-100"
                            x-transition:leave-end="opacity-0 transform scale-95"
                            class="absolute left-0 mt-2 w-56 bg-white rounded-xl shadow-2xl py-2 z-50 border border-gray-100"
                            style="display: none;">

                            <a href="{{ route('train.index') }}"
                                class="flex items-center space-x-3 px-4 py-3 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                <span>Data Kereta</span>
                            </a>

                            <a href="{{ route('schedule.index') }}"
                                class="flex items-center space-x-3 px-4 py-3 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span>Jadwal</span>
                            </a>
                        </div>
                    </div>

                    <!-- Rekap Dropdown -->
                    <div class="relative" @mouseenter="rekapDropdown = true" @mouseleave="rekapDropdown = false">
                        <button
                            class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 hover:bg-white/10 flex items-center space-x-2"
                            :class="rekapDropdown ? 'bg-white/10' : ''">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            <span>Rekap</span>
                            <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': rekapDropdown }"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <!-- Dropdown Menu -->
                        <div x-show="rekapDropdown" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 transform scale-95"
                            x-transition:enter-end="opacity-100 transform scale-100"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 transform scale-100"
                            x-transition:leave-end="opacity-0 transform scale-95"
                            class="absolute left-0 mt-2 w-56 bg-white rounded-xl shadow-2xl py-2 z-50 border border-gray-100"
                            style="display: none;">

                            <a href="{{ route('rekap.index') }}"
                                class="flex items-center space-x-3 px-4 py-3 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                <span>Rekap Kereta dan Waktu</span>
                            </a>

                            {{-- <a href="#"
                                class="flex items-center space-x-3 px-4 py-3 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Rekap Per Waktu</span>
                            </a> --}}

                            <a href="#"
                                class="flex items-center space-x-3 px-4 py-3 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                <span>Catatan Dinas</span>
                            </a>
                        </div>
                    </div>

                    <!-- User Dropdown -->
                    <div class="relative" @mouseenter="userDropdown = true" @mouseleave="userDropdown = false">
                        <button
                            class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 hover:bg-white/10 flex items-center space-x-2"
                            :class="userDropdown ? 'bg-white/10' : ''">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <span>User</span>
                            <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': userDropdown }"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <!-- Dropdown Menu -->
                        <div x-show="userDropdown" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 transform scale-95"
                            x-transition:enter-end="opacity-100 transform scale-100"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 transform scale-100"
                            x-transition:leave-end="opacity-0 transform scale-95"
                            class="absolute left-0 mt-2 w-56 bg-white rounded-xl shadow-2xl py-2 z-50 border border-gray-100"
                            style="display: none;">

                            <a href="{{ route('role.index') }}"
                                class="flex items-center space-x-3 px-4 py-3 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                <span>Jabatan</span>
                            </a>

                            <a href="{{ route('user.index') }}"
                                class="flex items-center space-x-3 px-4 py-3 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <span>Data User</span>
                            </a>
                        </div>
                    </div>
                </nav>
            @endrole

            <!-- Right Section: User Profile & Mobile Menu -->
            <div class="flex items-center space-x-3">
                <!-- Mobile Menu Button -->
                @role('Admin')
                    <button @click="mobileMenu = !mobileMenu"
                        class="md:hidden p-2 rounded-lg hover:bg-white/10 transition-colors focus:outline-none focus:ring-2 focus:ring-white/20">
                        <svg class="w-6 h-6" :class="{ 'hidden': mobileMenu }" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <svg class="w-6 h-6" :class="{ 'hidden': !mobileMenu }" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                @endrole

                <!-- User Profile Dropdown -->
                <div class="relative">
                    <button @click="open = !open"
                        class="flex items-center space-x-3 focus:outline-none px-3 py-2 rounded-xl transition-all duration-200 hover:bg-white/10"
                        :class="open ? 'bg-white/10' : ''">

                        <div class="text-right hidden sm:block">
                            <p class="text-sm font-semibold leading-tight text-white">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-blue-200">{{ Auth::user()->roles->first()->name ?? 'User' }}</p>
                        </div>

                        <div
                            class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white font-bold shadow-lg overflow-hidden">
                            <img src="{{ Auth::user()->avatar_url }}" alt="User Avatar"
                                class="h-full w-full object-cover">
                        </div>

                        <svg class="h-4 w-4 fill-current text-white transition-transform duration-200"
                            :class="{ 'rotate-180': open }" viewBox="0 0 20 20">
                            <path
                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" />
                        </svg>
                    </button>

                    <!-- Dropdown Menu -->
                    <div x-show="open" @click.away="open = false"
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="transform opacity-0 scale-95"
                        x-transition:enter-end="transform opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="transform opacity-100 scale-100"
                        x-transition:leave-end="transform opacity-0 scale-95"
                        class="absolute right-0 mt-3 w-56 bg-white rounded-xl shadow-2xl py-2 z-50 border border-gray-100"
                        style="display: none;">

                        <!-- Mobile User Info -->
                        <div class="px-4 py-3 border-b border-gray-100 sm:hidden">
                            <p class="text-sm font-bold text-gray-900">{{ Auth::user()->name }}</p>
                            {{-- <p class="text-xs font-bold text-gray-900">{{ Auth::user()->nipp }}</p> --}}
                            <p class="text-xs text-gray-500">{{ Auth::user()->roles->first()->name ?? 'User' }}</p>
                        </div>

                        @unlessrole('Admin')
                            <a href="{{ route('profile.edit') }}"
                                class="flex items-center space-x-3 px-4 py-3 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <span>Profile Saya</span>
                            </a>
                        @endunless

                        <a href="{{ route('presentasi.index') }}"
                            class="flex items-center space-x-3 px-4 py-3 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span>Pengaturan</span>
                        </a>

                        <div class="border-t border-gray-100 my-1"></div>
                        <a href="{{ route('logout') }}"
                            class="flex items-center space-x-3 w-full text-left px-4 py-3 text-sm text-red-600 hover:bg-red-50 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            <span>Logout</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @role('Admin')
        <!-- Mobile Navigation Menu -->
        <div x-show="mobileMenu" @click.away="mobileMenu = false" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-2"
            class="md:hidden border-t border-white/10 bg-[#001D4B]/95 backdrop-blur-sm" style="display: none;">

            <nav class="px-4 py-3 space-y-1">
                <a href="{{ route('dashboard') }}"
                    class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-white/10 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span class="font-medium">Dashboard</span>
                </a>

                <!-- Kereta Dropdown Mobile -->
                <div x-data="{ keretaMobile: false }">
                    <button @click="keretaMobile = !keretaMobile"
                        class="w-full flex items-center justify-between px-4 py-3 rounded-lg hover:bg-white/10 transition-colors">
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M12 2c-4 0-8 .5-8 4v9.5C4 17.43 5.57 19 7.5 19L6 20.5v.5h12v-.5L16.5 19c1.93 0 3.5-1.57 3.5-3.5V6c0-3.5-4-4-8-4zM7.5 17c-.83 0-1.5-.67-1.5-1.5S6.67 14 7.5 14s1.5.67 1.5 1.5S8.33 17 7.5 17zm3.5-6H6V6h5v5zm7 4.5c0 .83-.67 1.5-1.5 1.5s-1.5-.67-1.5-1.5.67-1.5 1.5-1.5 1.5.67 1.5 1.5zm0-4.5h-5V6h5v5z" />
                            </svg>
                            <span class="font-medium">Kereta</span>
                        </div>
                        <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': keretaMobile }" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="keretaMobile" x-collapse class="ml-8 mt-1 space-y-1">
                        <a href="{{ route('train.index') }}"
                            class="flex items-center space-x-3 px-4 py-2 rounded-lg hover:bg-white/10 transition-colors text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            <span>Kereta</span>
                        </a>
                        <a href="{{ route('schedule.index') }}"
                            class="flex items-center space-x-3 px-4 py-2 rounded-lg hover:bg-white/10 transition-colors text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span>Jadwal</span>
                        </a>
                    </div>
                </div>

                <!-- Rekap Mobile -->
                <div x-data="{ rekapMobile: false }">
                    <button @click="rekapMobile = !rekapMobile"
                        class="w-full flex items-center justify-between px-4 py-3 rounded-lg hover:bg-white/10 transition-colors">
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            <span class="font-medium">Rekap</span>
                        </div>
                        <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': rekapMobile }" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="rekapMobile" x-collapse class="ml-8 mt-1 space-y-1">
                        <a href="{{ route('rekap.index') }}"
                            class="flex items-center space-x-3 px-4 py-2 rounded-lg hover:bg-white/10 transition-colors text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            <span>Rekap Kereta dan Waktu</span>
                        </a>
                        {{-- <a href="#"
                            class="flex items-center space-x-3 px-4 py-2 rounded-lg hover:bg-white/10 transition-colors text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Rekap Per Waktu</span>
                        </a> --}}
                        <a href="#"
                            class="flex items-center space-x-3 px-4 py-2 rounded-lg hover:bg-white/10 transition-colors text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            <span>Catatan Dinas</span>
                        </a>
                    </div>
                </div>

                <!-- User Mobile -->
                <div x-data="{ userMobile: false }">
                    <button @click="userMobile = !userMobile"
                        class="w-full flex items-center justify-between px-4 py-3 rounded-lg hover:bg-white/10 transition-colors">
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <span class="font-medium">User</span>
                        </div>
                        <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': userMobile }" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="userMobile" x-collapse class="ml-8 mt-1 space-y-1">
                        <a href="{{ route('role.index') }}"
                            class="flex items-center space-x-3 px-4 py-2 rounded-lg hover:bg-white/10 transition-colors text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <span>Jabatan</span>
                        </a>
                        <a href="{{ route('user.index') }}"
                            class="flex items-center space-x-3 px-4 py-2 rounded-lg hover:bg-white/10 transition-colors text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span>Data User</span>
                        </a>
                    </div>
                </div>
            </nav>
        </div>
    @endrole
</header>
