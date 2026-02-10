@extends('layouts.app')

@section('title', 'Tambah Jadwal Baru')

@section('content')
    <div class="min-h-screen bg-[#F8FAFC] px-4 sm:px-6 py-8">

        <div class="max-w-4xl mx-auto">
            <div class="flex items-center justify-between mb-8">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('schedule.index') }}"
                        class="bg-white border border-slate-200 text-slate-500 hover:text-[#FF7300] hover:border-[#FF7300] p-2.5 rounded-xl transition-all shadow-sm">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-2xl font-black text-[#001D4B] tracking-tight">Tambah Jadwal</h1>
                        <p class="text-slate-500 text-sm font-medium">Manajemen operasional perjalanan KA</p>
                    </div>
                </div>
            </div>

            @if ($errors->any())
                <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-xl shadow-sm">
                    <div class="flex">
                        <div class="flex-shrink-0 text-red-500">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-bold text-red-800">Periksa kesalahan:</p>
                            <ul class="mt-1 list-disc list-inside text-xs text-red-700">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            @if (session('success'))
                <div
                    class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-xl shadow-sm flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 text-green-500">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <p class="ml-3 text-sm font-bold text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            <form action="{{ route('schedule.store') }}" method="POST" class="space-y-6">
                @csrf

                <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-100 flex items-center space-x-3 bg-slate-50/50">
                        <div
                            class="w-8 h-8 bg-[#FF7300] rounded-lg flex items-center justify-center text-white font-bold text-sm shadow-md shadow-orange-500/20">
                            1</div>
                        <h3 class="font-bold text-[#001D4B]">Identitas Armada</h3>
                    </div>

                    <div class="p-6 sm:p-8 grid grid-cols-1 md:grid-cols-2 gap-5 sm:gap-6">
                        <div class="space-y-2">
                            <label class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Pilih
                                Kereta</label>
                            <div class="relative">
                                <select name="train_id"
                                    class="appearance-none w-full bg-slate-50 border border-slate-200 rounded-2xl py-3.5 px-5 focus:ring-2 focus:ring-[#FF7300] focus:border-[#FF7300] transition-all font-bold text-[#001D4B] text-sm">
                                    <option value="">-- Pilih --</option>
                                    @foreach ($trains as $train)
                                        <option value="{{ $train->id }}"
                                            {{ old('train_id') == $train->id ? 'selected' : '' }}>{{ $train->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div
                                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-500">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                            <p class="text-[10px] text-slate-400 pl-2">*Pastikan gerbong sudah diatur via tombol di atas</p>
                        </div>

                        <div class="space-y-2">
                            <label class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Nomor
                                KA</label>
                            <input type="text" name="no_ka" value="{{ old('no_ka') }}" placeholder="Misal: 67"
                                class="w-full bg-slate-50 border border-slate-200 rounded-2xl py-3.5 px-5 focus:ring-2 focus:ring-[#FF7300] focus:border-[#FF7300] transition-all font-bold text-[#001D4B] text-sm">
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-3xl shadow-sm border border-slate-200">
                    <div class="px-6 py-5 border-b border-slate-100 flex items-center space-x-3 bg-slate-50/50">
                        <div
                            class="w-8 h-8 bg-[#001D4B] rounded-lg flex items-center justify-center text-white font-bold text-sm shadow-md shadow-blue-900/20">
                            2</div>
                        <h3 class="font-bold text-[#001D4B]">Rute & Waktu</h3>
                    </div>
                    <div class="p-6 sm:p-8 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 sm:gap-6">
                            <div class="space-y-2">
                                <label
                                    class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Asal</label>
                                <input type="text" name="origin" value="{{ old('origin') }}"
                                    placeholder="Stasiun Keberangkatan"
                                    class="w-full bg-slate-50 border border-slate-200 rounded-2xl py-3.5 px-5 focus:ring-2 focus:ring-[#FF7300] font-bold text-[#001D4B] text-sm">
                            </div>
                            <div class="space-y-2">
                                <label
                                    class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Tujuan</label>
                                <input type="text" name="destination" value="{{ old('destination') }}"
                                    placeholder="Stasiun Kedatangan"
                                    class="w-full bg-slate-50 border border-slate-200 rounded-2xl py-3.5 px-5 focus:ring-2 focus:ring-[#FF7300] font-bold text-[#001D4B] text-sm">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 sm:gap-6">
                            <div class="space-y-2">
                                <label
                                    class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Tanggal</label>
                                <input type="date" name="date" value="{{ old('date') }}"
                                    class="w-full bg-slate-50 border border-slate-200 rounded-2xl py-3.5 px-5 focus:ring-2 focus:ring-[#FF7300] font-bold text-[#001D4B] text-sm h-[50px]">
                            </div>
                            <div class="space-y-2">
                                <label class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Jam
                                    Berangkat</label>
                                <input type="time" name="departure_time" value="{{ old('departure_time') }}"
                                    class="w-full bg-slate-50 border border-slate-200 rounded-2xl py-3.5 px-5 focus:ring-2 focus:ring-[#FF7300] font-bold text-[#001D4B] text-sm h-[50px]">
                            </div>
                            <div class="space-y-2">
                                <label class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Jam
                                    Tiba</label>
                                <input type="time" name="arrival_time" value="{{ old('arrival_time') }}"
                                    class="w-full bg-slate-50 border border-slate-200 rounded-2xl py-3.5 px-5 focus:ring-2 focus:ring-[#FF7300] font-bold text-[#001D4B] text-sm h-[50px]">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 sm:gap-4 pt-4">
                    <a href="{{ route('schedule.index') }}"
                        class="px-6 py-4 rounded-2xl text-slate-500 font-bold hover:bg-slate-200 transition-colors text-center text-sm">Batal</a>
                    <button type="submit"
                        class="bg-[#001D4B] hover:bg-[#FF7300] text-white px-8 py-4 rounded-2xl font-bold shadow-lg shadow-blue-900/20 active:scale-[0.98] transition-all flex items-center justify-center space-x-2 text-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>SIMPAN JADWAL</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
