@extends('layouts.app')

@section('title')
    Dashboard
@endsection

@section('content')
    @role('Admin')
        <div class="min-h-screen py-6 bg-gray-50/50">
            <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex flex-col justify-center">
                    <h1 class="text-3xl font-bold text-gray-900">Dashboard Admin</h1>
                    <p class="mt-1 text-sm text-gray-500">Ringkasan data sistem monitoring konduktur</p>
                </div>

                @include('pages.dashboard.nav')
            </div>

            @include('pages.dashboard.index')

        </div>
    @endrole

    @unlessrole('Admin')
        <div class="py-10 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="mb-10 md:flex md:items-center md:justify-between">
                <div>
                    <div class="flex items-center text-[#FF7300] mb-2 gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        <span class="text-xs font-bold uppercase tracking-widest">Sistem Verifikasi</span>
                    </div>
                    <h2 class="text-3xl md:text-3xl font-extrabold text-[#001D4B] tracking-tight">
                        Daftar Kereta
                    </h2>
                    <p class="mt-2 text-sm text-slate-500 max-w-xl">
                        Pilih kereta yang akan Anda jalankan untuk memulai proses verifikasi perjalanan dinas.
                    </p>
                </div>
            </div>

            <!-- Grid Container -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach ($train as $kereta)
                    <!-- Simple Clean Card -->
                    <div class="group bg-white rounded-2xl border border-slate-200 hover:border-[#FF7300] hover:shadow-xl hover:shadow-[#FF7300]/10 transition-all duration-300 flex flex-col overflow-hidden">
                        <div class="p-5 flex flex-col flex-grow">
                            <!-- Card Header -->
                            <div class="flex justify-between items-start mb-5">
                                <div class="w-12 h-12 bg-slate-50 rounded-xl flex items-center justify-center text-[#001D4B] group-hover:bg-[#001D4B] group-hover:text-white transition-colors duration-300 border border-slate-100">
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2c-4 0-8 .5-8 4v9.5C4 17.43 5.57 19 7.5 19L6 20.5v.5h12v-.5L16.5 19c1.93 0 3.5-1.57 3.5-3.5V6c0-3.5-4-4-8-4zM7.5 17c-.83 0-1.5-.67-1.5-1.5S6.67 14 7.5 14s1.5.67 1.5 1.5S8.33 17 7.5 17zm3.5-6H6V6h5v5zm7 4.5c0 .83-.67 1.5-1.5 1.5s-1.5-.67-1.5-1.5.67-1.5 1.5-1.5 1.5.67 1.5 1.5zm0-4.5h-5V6h5v5z" />
                                    </svg>
                                </div>
                                <span class="px-3 py-1 bg-slate-100/80 text-slate-500 text-xs font-bold rounded-full border border-slate-200/60">
                                    #{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}
                                </span>
                            </div>

                            <!-- Train Info -->
                            <div>
                                <h3 class="text-lg font-bold text-slate-800 group-hover:text-[#FF7300] transition-colors line-clamp-1 mb-1.5">
                                    {{ $kereta['name'] }}
                                </h3>
                                <div class="flex items-center text-slate-500 text-xs mb-5">
                                    <svg class="w-3.5 h-3.5 mr-1.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Tersedia hari ini
                                </div>
                            </div>

                            <!-- Status -->
                            <div class="mt-auto pt-4 border-t border-slate-100/80">
                                <div class="flex items-center font-medium">
                                    <span class="relative flex h-2.5 w-2.5 mr-2">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                                    </span>
                                    <span class="text-emerald-600 text-[13px] tracking-wide">Status: Siap Jalan</span>
                                </div>
                            </div>
                        </div>

                        <!-- Action Button -->
                        <div class="px-5 pb-5">
                            <a href="{{ url('/jadwal') }}?train_id={{ $kereta['id'] }}"
                                class="flex items-center justify-center w-full bg-slate-50 hover:bg-[#001D4B] text-[#001D4B] hover:text-white border border-slate-200 hover:border-[#001D4B] font-semibold py-2.5 px-4 rounded-xl transition-all duration-300 group/btn">
                                <span class="text-xs tracking-widest uppercase">Pilih Kereta</span>
                                <svg class="w-4 h-4 ml-2 group-hover/btn:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endunlessrole
@endsection
