@extends('layouts.app')

@section('title')
    Presentation Manager
@endsection

@section('content')
    <div class="min-h-screen py-8 bg-gray-50/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Header --}}
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Presentation Manager</h1>
                <p class="mt-1 text-sm text-gray-500">Kelola data presentasi dan demo aplikasi</p>
            </div>

            {{-- Alert Messages --}}
            @if (session('success'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl flex items-center gap-3">
                    <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span class="text-green-700 font-medium">{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl flex items-center gap-3">
                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                    <span class="text-red-700 font-medium">{{ session('error') }}</span>
                </div>
            @endif

            {{-- Main Card --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-8">
                    <div class="flex items-start gap-6">
                        <div class="p-4 bg-red-50 rounded-2xl">
                            <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z">
                                </path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Reset & Generate Data Presentasi</h3>
                            <p class="text-gray-500 mb-6 leading-relaxed">
                                Tindakan ini akan <strong>menghapus</strong> data demo sebelumnya (User: Kondektur Demo,
                                Jadwal: KA-DEMO hari ini) dan
                                <strong>membuat ulang</strong> data baru yang segar. Gunakan fitur ini sebelum melakukan
                                presentasi untuk memastikan data bersih.
                            </p>

                            <div class="bg-gray-50 rounded-xl p-4 mb-6 border border-gray-200">
                                <h4 class="text-sm font-bold text-gray-700 mb-3 uppercase tracking-wider">Data yang akan
                                    dibuat:</h4>
                                <ul class="space-y-2 text-sm text-gray-600">
                                    <li class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        User: <strong>Kondektur Demo</strong> (NIPP: DEMO123)
                                    </li>
                                    <li class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Jadwal: <strong>KA-DEMO</strong> (Jakarta - Surabaya) untuk <strong>HARI
                                            INI</strong>
                                    </li>
                                    <li class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Data Scan: <strong>3 Putaran</strong> (Normal, Warning, Danger)
                                    </li>
                                </ul>
                            </div>

                            <form action="{{ route('presentation.reset') }}" method="POST"
                                onsubmit="return confirm('Apakah Anda yakin ingin mereset data presentasi?');">
                                @csrf
                                <button type="submit"
                                    class="inline-flex items-center justify-center gap-2 px-6 py-4 bg-red-600 text-white rounded-xl text-base font-bold hover:bg-red-700 active:bg-red-800 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 w-full sm:w-auto">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                        </path>
                                    </svg>
                                    Reset & Generate Data Baru
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-8 py-4 border-t border-gray-200">
                    <p class="text-xs text-center text-gray-500">
                        Halaman ini hanya dapat diakses oleh Administrator.
                    </p>
                </div>
            </div>

        </div>
    </div>
@endsection
