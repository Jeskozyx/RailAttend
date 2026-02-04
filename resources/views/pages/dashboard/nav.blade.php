<nav class="flex flex-wrap items-center gap-1 lg:gap-2">
    <a href="{{ route('dashboard') }}"
        class="px-4 py-2 text-sm font-semibold rounded-lg transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-[#001D4B] text-white' : 'text-gray-600 hover:bg-[#001D4B] hover:text-white' }}">
        Dashboard
    </a>
    <a href="{{ route('dashboard.scanKA') }}"
        class="px-4 py-2 text-sm font-semibold rounded-lg transition-all duration-200 {{ request()->routeIs('dashboard.scanKA') ? 'bg-[#001D4B] text-white' : 'text-gray-600 hover:bg-[#001D4B] hover:text-white' }}">
        Scan KA
    </a>
    <a href="{{ route('dashboard.scanPerDinas') }}"
        class="px-4 py-2 text-sm font-semibold rounded-lg transition-all duration-200 {{ request()->routeIs('dashboard.scanPerDinas') ? 'bg-[#001D4B] text-white' : 'text-gray-600 hover:bg-[#001D4B] hover:text-white' }}">
        Scan Per Dinas
    </a>
    <a href="#"
        class="px-4 py-2 text-sm font-semibold rounded-lg transition-all duration-200 text-gray-600 hover:bg-[#001D4B] hover:text-white">
        Periode Keliling
    </a>
    <a href="#"
        class="px-4 py-2 text-sm font-semibold rounded-lg transition-all duration-200 text-gray-600 hover:bg-[#001D4B] hover:text-white">
        Rata-rata Keliling
    </a>
</nav>
