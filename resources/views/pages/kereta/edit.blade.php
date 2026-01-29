@extends('layouts.app')

@section('title')
    Edit: {{ $train->name }}
@endsection

@push('css')
@endpush

@section('content')
    <div class="min-h-screen py-3 sm:py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-6 sm:mb-8">
                <div class="flex items-center space-x-3 sm:space-x-4">
                    <a href="{{ route('train.index') }}"
                        class="bg-white border border-slate-200 text-slate-500 hover:text-[#FF7300] hover:border-[#FF7300] p-2.5 rounded-xl transition-all shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Edit Kereta</h1>
                        <p class="mt-1 text-xs sm:text-sm text-gray-500">Kelola data dan rangkaian gerbong</p>
                    </div>
                </div>
            </div>

            @if (session('success'))
                <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-xl shadow-sm flex items-center">
                    <div class="flex-shrink-0 text-green-500">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <p class="ml-3 text-sm font-bold text-green-800">{{ session('success') }}</p>
                </div>
            @endif

            <div class="space-y-6">
                <!-- Form Card - Nama Kereta -->
                <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-gray-100">
                    <div class="px-6 py-4 border-b border-slate-100 flex items-center space-x-3 bg-slate-50/50">
                        <div
                            class="w-8 h-8 bg-[#001D4B] rounded-lg flex items-center justify-center text-white font-bold text-sm shadow-md">
                            1</div>
                        <h3 class="font-bold text-[#001D4B]">Identitas Kereta</h3>
                    </div>
                    <form action="{{ route('train.update', $train->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="p-6 space-y-4">
                            <div>
                                <label for="name"
                                    class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">
                                    Nama Kereta <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="name" id="name" value="{{ old('name', $train->name) }}"
                                    placeholder="Masukkan nama kereta"
                                    class="block w-full px-4 py-3 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all @error('name') border-red-500 @enderror">
                                @error('name')
                                    <p class="mt-2 text-xs text-red-600 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>

                        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end">
                            <button type="submit"
                                class="px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl hover:from-blue-700 hover:to-blue-800 transition-all shadow-lg hover:shadow-xl flex items-center space-x-2 text-sm font-medium">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Simpan Perubahan</span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Rangkaian Card -->
                <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-gray-100">
                    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                        <div class="flex items-center space-x-3">
                            <div
                                class="w-8 h-8 bg-[#FF7300] rounded-lg flex items-center justify-center text-white font-bold text-sm shadow-md shadow-orange-500/20">
                                2</div>
                            <div>
                                <h3 class="font-bold text-[#001D4B]">Rangkaian Gerbong</h3>
                                <p class="text-xs text-slate-500">Total: <span
                                        class="font-semibold text-[#FF7300]">{{ $train->rangkaians->count() }}</span>
                                    gerbong</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            @if ($train->rangkaians->count() > 0)
                                <a href="{{ route('rangkaian.print', $train->id) }}"
                                    class="text-xs font-bold text-white bg-[#001D4B] hover:bg-[#002D6B] px-4 py-2.5 rounded-xl transition-colors flex items-center space-x-2 shadow-lg">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                                    </svg>
                                    <span>BUAT QR</span>
                                </a>
                            @endif
                            <button type="button" onclick="document.getElementById('modalRangkaian').showModal()"
                                class="text-xs font-bold text-white bg-[#FF7300] hover:bg-[#e66800] px-4 py-2.5 rounded-xl transition-colors flex items-center space-x-2 shadow-lg shadow-orange-500/20">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                <span>TAMBAH GERBONG</span>
                            </button>
                        </div>
                    </div>

                    <div class="p-6">
                        @if ($train->rangkaians->count() > 0)
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
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
                                        class="bg-white rounded-xl p-4 border {{ $typeConfig['border'] }} shadow-sm group hover:shadow-md transition-all">
                                        <div class="flex items-center justify-between mb-2">
                                            <span
                                                class="px-2 py-0.5 {{ $typeConfig['bg'] }} {{ $typeConfig['text'] }} rounded text-[10px] font-bold">
                                                {{ $typeConfig['label'] }}
                                            </span>
                                            <div class="flex items-center space-x-1">
                                                <span class="text-xs text-gray-400">#{{ $rangkaian->urutan }}</span>
                                                <form action="{{ route('rangkaian.destroy', $rangkaian->id) }}"
                                                    method="POST" class="inline delete-rangkaian-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button"
                                                        class="btn-delete-rangkaian opacity-0 group-hover:opacity-100 text-red-400 hover:text-red-600 p-1 rounded transition-all">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                        <h4 class="font-bold text-gray-900 text-sm">{{ $rangkaian->name }}</h4>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <div
                                    class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-300">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                </div>
                                <p class="text-sm text-gray-500 font-medium">Belum ada rangkaian gerbong</p>
                                <p class="text-xs text-gray-400 mt-1">Klik tombol "Tambah Gerbong" untuk menambahkan</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Rangkaian Component --}}
    @include('components.modal-rangkaian', ['train' => $train])
@endsection

@push('modal')
@endpush

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.btn-delete-rangkaian').forEach(button => {
                button.addEventListener('click', function() {
                    const form = this.closest('form');

                    Swal.fire({
                        title: 'Hapus gerbong ini?',
                        text: 'Gerbong akan dihapus dari rangkaian!',
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
