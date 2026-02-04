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
        {{-- ... kodingan role non-admin ... --}}

        <div class="mb-12 text-center">
            <h2 class="text-4xl font-black text-[#001D4B] mb-3">Daftar Kereta</h2>
            <p class="text-slate-600 text-lg">Pilih kereta untuk verifikasi keliling 30 menit</p>
            <div class="w-24 h-1 bg-gradient-to-r from-transparent via-[#FF7300] to-transparent mx-auto mt-4"></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach ($train as $kereta)
                <div
                    class="group bg-white rounded-3xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-500 hover:-translate-y-2">

                    <!-- Image/Gradient Header -->
                    <div class="relative h-40 bg-gradient-to-br from-[#001D4B] to-[#003D7A] overflow-hidden">
                        <!-- Decorative Pattern -->
                        <div class="absolute inset-0 opacity-10">
                            <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                                <path d="M0,0 Q50,20 100,0 L100,100 L0,100 Z" fill="white" />
                            </svg>
                        </div>

                        <!-- Train Icon -->
                        <div class="absolute inset-0 flex items-center justify-center">
                            <svg class="w-20 h-20 text-white opacity-90 group-hover:scale-125 transition-transform duration-500"
                                fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M12 2c-4 0-8 .5-8 4v9.5C4 17.43 5.57 19 7.5 19L6 20.5v.5h12v-.5L16.5 19c1.93 0 3.5-1.57 3.5-3.5V6c0-3.5-4-4-8-4zM7.5 17c-.83 0-1.5-.67-1.5-1.5S6.67 14 7.5 14s1.5.67 1.5 1.5S8.33 17 7.5 17zm3.5-6H6V6h5v5zm7 4.5c0 .83-.67 1.5-1.5 1.5s-1.5-.67-1.5-1.5.67-1.5 1.5-1.5 1.5.67 1.5 1.5zm0-4.5h-5V6h5v5z" />
                            </svg>
                        </div>

                        <!-- Badge -->
                        <div class="absolute top-4 right-4">
                            <span class="px-3 py-1 bg-white/90 backdrop-blur-sm rounded-full text-xs font-bold text-[#001D4B]">
                                {{ $loop->iteration }}
                            </span>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="p-6">
                        <h3 class="text-2xl font-bold text-[#001D4B] mb-4 text-center">{{ $kereta['name'] }}</h3>

                        <!-- Button -->
                        <a href="{{ url('/jadwal') }}?train_id={{ $kereta['id'] }}"
                            class="w-full bg-[#001D4B] hover:bg-[#FF7300] text-white font-bold py-4 rounded-xl flex items-center justify-center space-x-2 transition-all duration-300 group-hover:shadow-lg">
                            <span class="text-sm">PILIH KERETA</span>
                            <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endunlessrole
@endsection
