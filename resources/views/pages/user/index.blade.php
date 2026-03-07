@extends('layouts.app')

@section('title', 'Data User')

@push('css')
    <style>
        .dropdown-menu.show {
            display: block !important;
        }
    </style>
@endpush

@section('content')
    <div class="min-h-screen py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Header -->
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between mb-8 gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Data User</h1>
                    <p class="mt-1 text-sm text-gray-500">Kelola data pengguna sistem</p>
                </div>

                <!-- List / Grid View Toggles -->
                <div class="flex items-center bg-gray-100 p-1 rounded-xl border border-gray-200">
                    <a href="{{ route('user.index') }}"
                        class="px-5 py-2 rounded-lg text-sm font-medium text-gray-600 hover:text-gray-900 transition-all">List
                        View</a>
                    <button
                        class="px-5 py-2 rounded-lg bg-white text-gray-900 text-sm font-medium shadow-sm transition-all border border-gray-200">Grid
                        View</button>
                </div>
            </div>

            @include('components.alert.success')

            <!-- Search, Filter & Actions Container -->
            <div
                class="mb-8 p-6 bg-white rounded-2xl shadow-sm border border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <form method="GET" action="{{ route('user.index') }}"
                    class="flex-1 flex flex-col md:flex-row gap-4 w-full">
                    <!-- Search -->
                    <div class="relative w-full md:max-w-md">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" name="search" value="{{ request('search') ?? '' }}"
                            placeholder="Cari nama atau email..."
                            class="block w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl leading-5 text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all text-sm">
                    </div>

                    <!-- Sort Dropdown -->
                    <div class="flex items-center space-x-3 flex-shrink-0">
                        <label class="text-sm font-medium text-gray-700">Tampilkan:</label>
                        <select name="sort" id="sort" onchange="this.form.submit()"
                            class="px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-700 text-sm transition-all">
                            <option value="8" {{ request('sort') == 8 ? 'selected' : '' }}>8</option>
                            <option value="16" {{ request('sort') == 16 ? 'selected' : '' }}>16</option>
                            <option value="24" {{ request('sort') == 24 ? 'selected' : '' }}>24</option>
                            <option value="32" {{ request('sort') == 32 ? 'selected' : '' }}>32</option>
                        </select>
                    </div>
                </form>

                <a href="{{ route('user.create') }}"
                    class="flex items-center justify-center space-x-2 px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-semibold rounded-xl hover:from-blue-700 hover:to-blue-800 transition-all duration-200 shadow-lg hover:shadow-xl flex-shrink-0 text-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Tambah User</span>
                </a>
            </div>

            <!-- Grid Container -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @forelse ($user as $item)
                    @php
                        $role = $item->roles()->first();
                        $roleName = $role?->name ?? 'Tidak Ada Jabatan';

                        // Original badge colors based on role
                        $roleBadgeClass = match ($roleName) {
                            'Admin' => 'bg-purple-100 text-purple-800',
                            'Tidak Ada Jabatan' => 'bg-red-100 text-red-800',
                            default => 'bg-blue-100 text-blue-800',
                        };

                        $borderColors = [
                            'border-blue-400',
                            'border-purple-400',
                            'border-pink-400',
                            'border-teal-400',
                            'border-indigo-400',
                            'border-orange-400',
                        ];
                        $borderColor = $borderColors[$loop->index % count($borderColors)];
                    @endphp

                    <div
                        class="bg-white rounded-[1.5rem] p-6 shadow-sm hover:shadow-md border border-gray-100 transition-all duration-300 relative group flex flex-col items-center">

                        <!-- Top Actions inside card -->
                        <div class="w-full flex justify-end items-start mb-4">
                            <div class="relative dropdown-container">
                                <button
                                    class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 p-1.5 rounded-lg transition-colors toggle-dropdown">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <circle cx="5" cy="12" r="1.5" />
                                        <circle cx="12" cy="12" r="1.5" />
                                        <circle cx="19" cy="12" r="1.5" />
                                    </svg>
                                </button>
                                <!-- Dropdown Menu -->
                                <div
                                    class="dropdown-menu absolute right-0 mt-2 w-40 bg-white border border-gray-200 rounded-xl shadow-lg z-20 hidden overflow-hidden py-1">
                                    <a href="{{ route('user.edit', ['id' => $item->id]) }}"
                                        class="flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                        <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        Edit Profil
                                    </a>
                                    <form action="{{ route('user.destroy', ['id' => $item->id]) }}" method="POST"
                                        class="delete-form m-0">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button"
                                            class="btn-delete w-full flex items-center px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                            <svg class="w-4 h-4 mr-2 text-red-500" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            Hapus User
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Avatar -->
                        <div class="relative w-24 h-24 mb-4" data-user-status="{{ $item->id }}">
                            <!-- Colored ring -->
                            <div class="w-full h-full rounded-full border-[3px] {{ $borderColor }} p-[3px]">
                                <!-- Image wrapper -->
                                <div
                                    class="w-full h-full rounded-full bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center text-gray-500 font-bold text-2xl overflow-hidden border border-gray-200">
                                    @if ($item->avatar_url)
                                        <img src="{{ $item->avatar_url }}" alt="{{ $item->name }}"
                                            class="w-full h-full object-cover">
                                    @else
                                        {{ strtoupper(substr($item->name, 0, 2)) }}
                                    @endif
                                </div>
                            </div>

                            <!-- Online Status Dot -->
                            <div class="status-indicator">
                                @if ($item->is_online)
                                    <span
                                        class="absolute bottom-1 right-1 w-4 h-4 bg-green-500 border-2 border-white rounded-full z-10 shadow-sm"></span>
                                @else
                                    <span
                                        class="absolute bottom-1 right-1 w-4 h-4 bg-gray-400 border-2 border-white rounded-full z-10 shadow-sm"></span>
                                @endif
                            </div>
                        </div>

                        <!-- User Info -->
                        <h3 class="text-gray-900 font-bold text-lg text-center leading-tight truncate w-full px-2"
                            title="{{ $item->name }}">{{ $item->name }}</h3>
                        <div class="mt-2 mb-1">
                            <span
                                class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $roleBadgeClass }}">
                                {{ $roleName }}
                            </span>
                        </div>

                        @if ($item->nipp)
                            <p class="text-gray-500 text-[12px] mt-2 font-medium tracking-wider text-center">
                                {{ $item->nipp }}</p>
                        @endif

                        @if ($item->email)
                            <p class="text-gray-400 text-[12px] mt-1 text-center truncate w-full px-2">{{ $item->email }}
                            </p>
                        @endif
                    </div>
                @empty
                    <div class="col-span-full">
                        <div
                            class="flex flex-col items-center justify-center text-center py-20 bg-white rounded-2xl border border-gray-100 shadow-sm">
                            <div
                                class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-4 border border-gray-100">
                                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M12 4v16m8-8H4" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Tidak Ada Data User</h3>
                            <p class="text-sm text-gray-500 max-w-sm mb-6">Belum ada data user yang tersedia untuk
                                ditampilkan.</p>
                            <a href="{{ route('user.create') }}"
                                class="px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-semibold text-sm rounded-xl hover:from-blue-700 hover:to-blue-800 transition-all shadow-md">Tambah
                                User Pertama</a>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if ($user->hasPages())
                <div class="mt-8 mb-8 pb-4">
                    <div class="bg-white px-6 py-4 border border-gray-200 rounded-2xl shadow-sm">
                        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                            <!-- Info -->
                            <div class="text-sm text-gray-700">
                                Menampilkan
                                <span class="font-semibold">{{ $user->firstItem() ?? 0 }}</span>
                                sampai
                                <span class="font-semibold">{{ $user->lastItem() ?? 0 }}</span>
                                dari
                                <span class="font-semibold">{{ $user->total() }}</span>
                                data
                            </div>

                            <!-- Pagination Buttons -->
                            <div class="flex items-center space-x-2">
                                {{-- Previous Button --}}
                                @if ($user->onFirstPage())
                                    <button disabled
                                        class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-400 bg-gray-100 cursor-not-allowed transition-all duration-200">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 19l-7-7 7-7" />
                                        </svg>
                                    </button>
                                @else
                                    <a href="{{ $user->appends(request()->query())->previousPageUrl() }}"
                                        class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-all duration-200">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 19l-7-7 7-7" />
                                        </svg>
                                    </a>
                                @endif

                                {{-- Page Numbers --}}
                                @php
                                    $start = max($user->currentPage() - 2, 1);
                                    $end = min($user->currentPage() + 2, $user->lastPage());
                                @endphp

                                @if ($start > 1)
                                    <a href="{{ $user->appends(request()->query())->url(1) }}"
                                        class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-all duration-200">
                                        1
                                    </a>
                                    @if ($start > 2)
                                        <span class="px-2 text-gray-400">...</span>
                                    @endif
                                @endif

                                @foreach (range($start, $end) as $page)
                                    @if ($page == $user->currentPage())
                                        <button
                                            class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium transition-all duration-200 shadow-sm">
                                            {{ $page }}
                                        </button>
                                    @else
                                        <a href="{{ $user->appends(request()->query())->url($page) }}"
                                            class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-all duration-200">
                                            {{ $page }}
                                        </a>
                                    @endif
                                @endforeach

                                @if ($end < $user->lastPage())
                                    @if ($end < $user->lastPage() - 1)
                                        <span class="px-2 text-gray-400">...</span>
                                    @endif
                                    <a href="{{ $user->appends(request()->query())->url($user->lastPage()) }}"
                                        class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-all duration-200">
                                        {{ $user->lastPage() }}
                                    </a>
                                @endif

                                {{-- Next Button --}}
                                @if ($user->hasMorePages())
                                    <a href="{{ $user->appends(request()->query())->nextPageUrl() }}"
                                        class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-all duration-200">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5l7 7-7 7" />
                                        </svg>
                                    </a>
                                @else
                                    <button disabled
                                        class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-400 bg-gray-100 cursor-not-allowed transition-all duration-200">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5l7 7-7 7" />
                                        </svg>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>
@endsection

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Dropdown Logic
            const dropdownToggles = document.querySelectorAll('.toggle-dropdown');
            dropdownToggles.forEach(toggle => {
                toggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    // close other dropdowns
                    document.querySelectorAll('.dropdown-menu').forEach(menu => {
                        if (menu !== this.nextElementSibling) {
                            menu.classList.remove('show');
                        }
                    });

                    const menu = this.nextElementSibling;
                    menu.classList.toggle('show');
                });
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!e.target.closest('.dropdown-container')) {
                    document.querySelectorAll('.dropdown-menu').forEach(menu => {
                        menu.classList.remove('show');
                    });
                }
            });

            // Delete confirmation
            document.querySelectorAll('.btn-delete').forEach(button => {
                button.addEventListener('click', function() {
                    const form = this.closest('form');
                    Swal.fire({
                        title: 'Yakin hapus?',
                        text: 'Data yang dihapus tidak bisa dikembalikan!',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc2626',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Ya, hapus',
                        cancelButtonText: 'Batal',
                        customClass: {
                            popup: 'rounded-2xl'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });

            // Realtime Polling
            function updateOnlineStatus() {
                fetch("{{ route('user.onlineStatus') }}")
                    .then(response => response.json())
                    .then(users => {
                        users.forEach(user => {
                            const statusContainer = document.querySelector(
                                `[data-user-status="${user.id}"] .status-indicator`);
                            if (statusContainer) {
                                if (user.is_online) {
                                    statusContainer.innerHTML = `
                                    <span class="absolute bottom-1 right-1 w-4 h-4 bg-green-500 border-2 border-white rounded-full z-10 shadow-sm"></span>
                                `;
                                } else {
                                    statusContainer.innerHTML = `
                                    <span class="absolute bottom-1 right-1 w-4 h-4 bg-gray-400 border-2 border-white rounded-full z-10 shadow-sm"></span>
                                `;
                                }
                            }
                        });
                    })
                    .catch(error => console.error('Error fetching status:', error));
            }

            setInterval(updateOnlineStatus, 5000);
        });
    </script>
@endpush
