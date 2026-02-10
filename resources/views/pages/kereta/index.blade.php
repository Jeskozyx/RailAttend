@extends('layouts.app')

@section('title')
    Data Kereta
@endsection

@push('css')
@endpush

@section('content')
    <div class="min-h-screen py-2">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Data Kereta</h1>
                        <p class="mt-1 text-sm text-gray-500">Kelola data kereta dan rangkaian kereta</p>
                    </div>
                    <a href="{{ route('train.create') }}"
                        class="px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl hover:from-blue-700 hover:to-blue-800 transition-all duration-200 shadow-lg hover:shadow-xl flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Tambah Kereta</span>
                    </a>
                </div>
            </div>

            @include('components.alert.success')

            <!-- Search & Filter Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
                <form method="GET" action="{{ route('train.index') }}">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <!-- Search -->
                        <div class="relative flex-1">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input type="text" name="search" value="{{ request('search') ?? '' }}"
                                placeholder="Cari nama kereta..."
                                class="block w-full pl-10 pr-3 py-3 border border-gray-200 rounded-xl leading-5 bg-gray-50 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                        </div>

                        <!-- Sort Dropdown -->
                        <div class="flex items-center space-x-3">
                            <label class="text-sm font-medium text-gray-600 whitespace-nowrap">Tampilkan:</label>
                            <select name="sort" id="sort" onchange="this.form.submit()"
                                class="pl-4 pr-10 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white text-sm text-gray-700 transition-all duration-200">
                                <option value="10" {{ request('sort') == 10 ? 'selected' : '' }}>10</option>
                                <option value="25" {{ request('sort') == 25 ? 'selected' : '' }}>25</option>
                                <option value="50" {{ request('sort') == 50 ? 'selected' : '' }}>50</option>
                                <option value="100" {{ request('sort') == 100 ? 'selected' : '' }}>100</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Trains Accordion List -->
            <div class="space-y-4">
                @forelse ($trains as $train)
                    <div x-data="{ expanded: false }"
                        class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow duration-200">
                        <!-- Accordion Header -->
                        <div class="flex items-center justify-between px-6 py-5 bg-white">
                            <button @click="expanded = !expanded"
                                class="flex-1 flex items-center space-x-4 text-left hover:bg-gray-50 transition-colors duration-200 -m-2 p-2 rounded-lg">
                                <div class="bg-blue-100 p-2 rounded-lg text-blue-600">
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M12 2c-4 0-8 .5-8 4v9.5C4 17.43 5.57 19 7.5 19L6 20.5v.5h12v-.5L16.5 19c1.93 0 3.5-1.57 3.5-3.5V6c0-3.5-4-4-8-4zM7.5 17c-.83 0-1.5-.67-1.5-1.5S6.67 14 7.5 14s1.5.67 1.5 1.5S8.33 17 7.5 17zm3.5-6H6V6h5v5zm7 4.5c0 .83-.67 1.5-1.5 1.5s-1.5-.67-1.5-1.5.67-1.5 1.5-1.5 1.5.67 1.5 1.5zm0-4.5h-5V6h5v5z" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-lg font-bold text-gray-900">{{ $train->name }}</h3>
                                    <p class="text-sm text-gray-500">
                                        <span class="font-semibold text-[#FF7300]">{{ $train->rangkaians_count }}</span>
                                        Rangkaian Kereta
                                    </p>
                                </div>
                                <svg class="w-5 h-5 text-gray-400 transform transition-transform duration-200"
                                    :class="{ 'rotate-180': expanded }" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <!-- Action Buttons -->
                            <div class="flex items-center space-x-2 ml-4">
                                <a href="{{ route('rangkaian.print', ['train_id' => $train->id]) }}"
                                    class="text-blue-600 hover:text-blue-900 bg-blue-100 hover:bg-blue-100 p-2 rounded-lg transition-colors border border-blue-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                                    </svg>
                                </a>
                                <a href="{{ route('train.edit', ['id' => $train->id]) }}"
                                    class="text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 p-2 rounded-lg transition-colors border border-blue-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>
                                <form action="{{ route('train.destroy', ['id' => $train->id]) }}" method="POST"
                                    class="delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button"
                                        class="btn-delete text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 p-2 rounded-lg transition-colors border border-red-200">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Accordion Body - Rangkaian List -->
                        <div x-show="expanded" x-collapse style="display: none;">
                            <div class="p-6 bg-gray-50/50 border-t border-gray-100">
                                @if ($train->rangkaians->count() > 0)
                                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                                        @foreach ($train->rangkaians as $rangkaian)
                                            @php
                                                $typeConfig = match ($rangkaian->type) {
                                                    'LUX' => [
                                                        'bg' => 'bg-amber-100',
                                                        'text' => 'text-amber-700',
                                                        'border' => 'border-amber-200',
                                                        'label' => 'LUXURY',
                                                    ],
                                                    'EKS' => [
                                                        'bg' => 'bg-blue-100',
                                                        'text' => 'text-blue-700',
                                                        'border' => 'border-blue-200',
                                                        'label' => 'EKSEKUTIF',
                                                    ],
                                                    'EKO' => [
                                                        'bg' => 'bg-green-100',
                                                        'text' => 'text-green-700',
                                                        'border' => 'border-green-200',
                                                        'label' => 'EKONOMI',
                                                    ],
                                                    'KMP' => [
                                                        'bg' => 'bg-purple-100',
                                                        'text' => 'text-purple-700',
                                                        'border' => 'border-purple-200',
                                                        'label' => 'KERETA MAKAN',
                                                    ],
                                                    'BP' => [
                                                        'bg' => 'bg-gray-100',
                                                        'text' => 'text-gray-700',
                                                        'border' => 'border-gray-300',
                                                        'label' => 'PEMBANGKIT',
                                                    ],
                                                    default => [
                                                        'bg' => 'bg-slate-100',
                                                        'text' => 'text-slate-700',
                                                        'border' => 'border-slate-200',
                                                        'label' => $rangkaian->type,
                                                    ],
                                                };
                                            @endphp
                                            <div
                                                class="bg-white rounded-xl p-4 border {{ $typeConfig['border'] }} shadow-sm">
                                                <div class="flex items-center justify-between mb-2">
                                                    <span
                                                        class="px-2 py-0.5 {{ $typeConfig['bg'] }} {{ $typeConfig['text'] }} rounded text-[10px] font-bold">
                                                        {{ $typeConfig['label'] }}
                                                    </span>
                                                    <span class="text-xs text-gray-400">#{{ $rangkaian->urutan }}</span>
                                                </div>
                                                <h4 class="font-bold text-gray-900 text-sm">{{ $rangkaian->name }}</h4>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-8">
                                        <div
                                            class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3 text-gray-300">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                            </svg>
                                        </div>
                                        <p class="text-sm text-gray-500 font-medium">Belum ada rangkaian gerbong</p>
                                        <p class="text-xs text-gray-400 mt-1">Tambahkan melalui menu Kereta → Atur
                                            Rangkaian</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12">
                        <div class="flex flex-col items-center justify-center text-center">
                            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                <svg class="w-10 h-10 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M12 2c-4 0-8 .5-8 4v9.5C4 17.43 5.57 19 7.5 19L6 20.5v.5h12v-.5L16.5 19c1.93 0 3.5-1.57 3.5-3.5V6c0-3.5-4-4-8-4z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Tidak Ada Data Kereta</h3>
                            <p class="text-sm text-gray-500 mb-6 max-w-sm">
                                Belum ada data kereta yang tersedia. Silakan tambahkan kereta baru untuk memulai.
                            </p>
                            <a href="{{ route('train.create') }}"
                                class="px-6 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors font-medium">
                                Tambah Kereta Baru
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if ($trains->hasPages())
                <div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-200 px-6 py-4">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="text-sm text-gray-700">
                            Menampilkan
                            <span class="font-semibold">{{ $trains->firstItem() ?? 0 }}</span>
                            sampai
                            <span class="font-semibold">{{ $trains->lastItem() ?? 0 }}</span>
                            dari
                            <span class="font-semibold">{{ $trains->total() }}</span> data
                        </div>
                        <div class="flex items-center space-x-2">
                            @if ($trains->onFirstPage())
                                <button disabled
                                    class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-400 bg-gray-100 cursor-not-allowed">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 19l-7-7 7-7" />
                                    </svg>
                                </button>
                            @else
                                <a href="{{ $trains->appends(request()->query())->previousPageUrl() }}"
                                    class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-all">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 19l-7-7 7-7" />
                                    </svg>
                                </a>
                            @endif

                            @foreach ($trains->getUrlRange(1, $trains->lastPage()) as $page => $url)
                                @if ($page == $trains->currentPage())
                                    <button
                                        class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium">{{ $page }}</button>
                                @else
                                    <a href="{{ $trains->appends(request()->query())->url($page) }}"
                                        class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-all">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach

                            @if ($trains->hasMorePages())
                                <a href="{{ $trains->appends(request()->query())->nextPageUrl() }}"
                                    class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-all">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            @else
                                <button disabled
                                    class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-400 bg-gray-100 cursor-not-allowed">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('modal')
@endpush

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.btn-delete').forEach(button => {
                button.addEventListener('click', function() {
                    const form = this.closest('form');

                    Swal.fire({
                        title: 'Yakin hapus?',
                        text: 'Data kereta dan semua rangkaian akan dihapus!',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc2626',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Ya, hapus',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>
@endpush
