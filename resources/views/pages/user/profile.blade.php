@extends('layouts.app')

@section('title')
    Profile Saya
@endsection

@push('css')
    <style>
        /* ── Stat Card Base (Sama dengan Dashboard) ── */
        .stat-card-profile {
            position: relative;
            overflow: hidden;
            background: linear-gradient(135deg, #001D4B 0%, #001D4B 35%, #0a3d7c 60%, #12527a 85%, #1a6fa0 100%);
            border-radius: 1.25rem;
            padding: 1.5rem;
            min-height: 110px;
            box-shadow: 0 6px 20px rgba(0, 29, 75, 0.25);
            border: none;
            display: flex;
            align-items: center;
        }

        /* ── Dekorasi Hexagon ── */
        .stat-card-profile .geo-bottom {
            position: absolute;
            right: -10px;
            bottom: -15px;
            width: 140px;
            height: 140px;
            opacity: 0.15;
            pointer-events: none;
        }

        .stat-card-profile .geo-top {
            position: absolute;
            right: 10px;
            top: -20px;
            width: 80px;
            height: 80px;
            opacity: 0.1;
            pointer-events: none;
        }

        /* ── Konten ── */
        .stat-card-profile .card-inner {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            gap: 1.25rem;
            width: 100%;
        }

        .stat-card-profile .icon-wrap {
            width: 3.5rem;
            height: 3.5rem;
            border-radius: 1rem;
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(8px);
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .stat-card-profile .card-label {
            font-size: 0.75rem;
            font-weight: 800;
            color: rgba(255, 255, 255, 0.7);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 2px;
        }

        .stat-card-profile .card-value {
            font-size: 1.5rem;
            font-weight: 900;
            color: #ffffff;
            line-height: 1;
        }
    </style>
@endpush

@section('content')
    <div class="min-h-screen py-8 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4">

            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">

                    {{-- --- BAGIAN KIRI: IDENTITAS (DIPERBESAR) --- --}}
                    <div class="lg:col-span-4 space-y-6 lg:sticky lg:top-8"> {{-- Span naik ke 5 agar lebih lebar --}}
                        <div class="bg-white rounded-[2.5rem] shadow-md border border-gray-100 p-12 text-center">
                            {{-- p-12 untuk ruang sangat luas --}}

                            <div class="relative inline-block group mb-6">
                                {{-- Foto Profil Diperbesar ke h-32 w-32 --}}
                                <div
                                    class="h-32 w-32 rounded-full overflow-hidden border-4 border-[#001D4B] shadow-lg mx-auto">
                                    <img src="{{ $user->avatar_url }}" alt="Avatar" id="avatar-preview"
                                        class="h-full w-full object-cover">
                                </div>

                                <label for="avatar-input"
                                    class="absolute -bottom-1 -right-2 bg-white text-[#001D4B] p-3 rounded-full cursor-pointer hover:bg-gray-100 transition-all shadow-md border border-gray-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        <path
                                            d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                    </svg>
                                </label>
                                <input type="file" name="avatar" id="avatar-input" class="hidden" accept="image/*"
                                    onchange="previewImage(this)">
                            </div>

                            <div class="mt-4 mb-10">
                                <h2 class="text-2xl font-black text-gray-900 tracking-tight">{{ strtoupper($user->name) }}
                                </h2>
                                <p class="text-sm font-bold text-blue-600 tracking-[0.2em] uppercase mt-2">
                                    {{ $user->jabatan ?? '-' }}</p>
                            </div>

                            <div class="space-y-8 text-left">
                                <div>
                                    <label class="text-xs font-extrabold text-[#001D4B]/60 uppercase tracking-widest">NIPP
                                        (Nomor Induk)</label>
                                    <p
                                        class="mt-2 text-gray-800 font-mono font-bold text-xl border-b-2 border-gray-50 pb-2">
                                        {{ $user->nipp }}
                                    </p>
                                </div>

                                <div>
                                    <label
                                        class="text-xs font-extrabold text-[#001D4B]/60 uppercase tracking-widest">Jabatan
                                        Sekarang</label>
                                    <p class="mt-2 text-gray-800 font-bold text-xl border-b-2 border-gray-50 pb-2">
                                        {{ $user->jabatan }}
                                    </p>
                                </div>

                                <div>
                                    <label
                                        class="text-xs font-extrabold text-[#001D4B]/60 uppercase tracking-widest mb-3 block">Nama
                                        Lengkap</label>
                                    <input type="text" name="name" value="{{ $user->name }}"
                                        class="block w-full px-6 py-4 bg-gray-50 border border-gray-200 rounded-2xl text-lg font-bold text-gray-800 focus:ring-2 focus:ring-[#001D4B] focus:border-[#001D4B] transition-all">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- --- BAGIAN KANAN: STATISTIK & KEAMANAN (DIPERBESAR) --- --}}
                    <div class="lg:col-span-8 space-y-10"> {{-- Span 7 --}}
                        {{-- Kotak Putih Atas: Ringkasan Aktivitas --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Card 1: Periode Keliling --}}
                            <div class="stat-card-profile">
                                {{-- Dekorasi Hexagon (SVG Copy dari Dashboard) --}}
                                <svg class="geo-bottom" viewBox="0 0 165 165" fill="none">
                                    <polygon points="82,8 158,50 158,134 82,176 6,134 6,50" stroke="white"
                                        stroke-width="1.3" transform="translate(17,-11) scale(0.82)" />
                                </svg>
                                <svg class="geo-top" viewBox="0 0 105 105" fill="none">
                                    <polygon points="52,4 101,30 101,75 52,101 3,75 3,30" stroke="white"
                                        stroke-width="0.7" />
                                </svg>

                                <div class="card-inner">
                                    <div class="icon-wrap">
                                        <svg class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="card-label">Keliling Minggu Ini</p>
                                        <h3 class="card-value">{{ $weeklyRounds }}</h3>
                                    </div>
                                </div>
                            </div>
                            {{-- Card 2: Rata-rata Keliling --}}
                            <div class="stat-card-profile">
                                {{-- Dekorasi Hexagon --}}
                                <svg class="geo-bottom" viewBox="0 0 165 165" fill="none">
                                    <polygon points="82,8 158,50 158,134 82,176 6,134 6,50" stroke="white"
                                        stroke-width="1.3" transform="translate(17,-11) scale(0.82)" />
                                </svg>
                                <div class="card-inner">
                                    <div class="icon-wrap">
                                        <svg class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">

                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="card-label">Rata-rata Keliling</p>
                                        <h3 class="card-value">{{ $averageRounds }} <span
                                                class="text-xs font-normal opacity-60">/minggu</span></h3>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Kotak Putih Bawah: Keamanan Akun --}}
                        <div class="bg-white rounded-[2.5rem] shadow-md border border-gray-100 p-12">
                            <div class="mb-10">
                                <h3 class="text-2xl font-black text-[#001D4B] uppercase tracking-tight">Keamanan Akun</h3>
                                <div class="h-1.5 w-55 bg-[#001D4B] mt-2 rounded-full"></div>
                            </div>

                            <div class="grid grid-cols-1 gap-8">
                                <div>
                                    <label
                                        class="block text-xs font-extrabold text-[#001D4B]/60 uppercase tracking-widest mb-3">Email
                                        Aktif</label>
                                    <input type="email" name="email" value="{{ $user->email }}"
                                        class="w-full px-6 py-5 bg-gray-50 border border-gray-200 rounded-2xl text-xl font-bold text-gray-800 focus:ring-2 focus:ring-[#001D4B] focus:border-[#001D4B] transition-all">
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                    <div>
                                        <label
                                            class="block text-xs font-extrabold text-[#001D4B]/60 uppercase tracking-widest mb-3">Password
                                            Baru</label>
                                        <div class="relative">
                                            <input type="password" name="password" id="password" placeholder="********"
                                                class="w-full px-6 py-5 bg-gray-50 border border-gray-200 rounded-2xl text-xl font-bold text-gray-800 focus:ring-2 focus:ring-[#001D4B] focus:border-[#001D4B] transition-all pr-12">
                                            <button type="button" onclick="togglePassword('password')"
                                                class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400 hover:text-gray-600 focus:outline-none">
                                                <svg id="icon-password" xmlns="http://www.w3.org/2000/svg"
                                                    class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                    <div>
                                        <label
                                            class="block text-xs font-extrabold text-[#001D4B]/60 uppercase tracking-widest mb-3">Konfirmasi
                                            Password</label>
                                        <div class="relative">
                                            <input type="password" name="password_confirmation"
                                                id="password_confirmation" placeholder="********"
                                                class="w-full px-6 py-5 bg-gray-50 border border-gray-200 rounded-2xl text-xl font-bold text-gray-800 focus:ring-2 focus:ring-[#001D4B] focus:border-[#001D4B] transition-all pr-12">
                                            <button type="button" onclick="togglePassword('password_confirmation')"
                                                class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400 hover:text-gray-600 focus:outline-none">
                                                <svg id="icon-password_confirmation" xmlns="http://www.w3.org/2000/svg"
                                                    class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Button Actions --}}
                        <div class="flex items-center justify-end gap-1">
                            <button type="button"
                                class="px-10 py-4 text-lg font-black text-gray-400 hover:text-gray-600 transition-all">
                                Batalkan
                            </button>
                            <button type="submit"
                                class="px-1 py-5 text-lg font-black text-white bg-[#001D4B] hover:bg-[#0a3d7c] rounded-[1.5rem] shadow-2xl shadow-blue-900/40 transition-all uppercase tracking-widest">
                                Simpan Perubahan
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('js')
    <script>
        function previewImage(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('avatar-preview').src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById('icon-' + inputId);

            if (input.type === "password") {
                input.type = "text";
                icon.innerHTML =
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />';
            } else {
                input.type = "password";
                icon.innerHTML =
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11@extends('layouts.app')

@section('title')
    Profile Saya
@endsection

@section('content')
    <div class="min-h-screen py-8 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4">

            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">

                    {{-- --- BAGIAN KIRI: IDENTITAS (DIPERBESAR) --- --}}
                    <div class="lg:col-span-4 space-y-6 lg:sticky lg:top-8">
                        <div class="bg-white rounded-[2.5rem] shadow-md border border-gray-100 p-12 text-center">

                            <div class="relative inline-block group mb-6">
                                {{-- Foto Profil Diperbesar ke h-32 w-32 --}}
                                <div
                                    class="h-32 w-32 rounded-full overflow-hidden border-4 border-[#001D4B] shadow-lg mx-auto">
                                    <img src="{{ $user->avatar_url }}" alt="Avatar" id="avatar-preview"
                                        class="h-full w-full object-cover">
                                </div>

                                <label for="avatar-input"
                                    class="absolute -bottom-1 -right-2 bg-white text-[#001D4B] p-3 rounded-full cursor-pointer hover:bg-gray-100 transition-all shadow-md border border-gray-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        <path
                                            d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                    </svg>
                                </label>
                                <input type="file" name="avatar" id="avatar-input" class="hidden" accept="image/*"
                                    onchange="previewImage(this)">
                            </div>

                            <div class="mt-4 mb-10">
                                <h2 class="text-2xl font-black text-gray-900 tracking-tight">{{ strtoupper($user->name) }}
                                </h2>
                                <p class="text-sm font-bold text-blue-600 tracking-[0.2em] uppercase mt-2">
                                    {{ $user->jabatan ?? '-' }}</p>
                            </div>

                            <div class="space-y-8 text-left">
                                <div>
                                    <label class="text-xs font-extrabold text-[#001D4B]/60 uppercase tracking-widest">NIPP
                                        (Nomor Induk)</label>
                                    <p
                                        class="mt-2 text-gray-800 font-mono font-bold text-xl border-b-2 border-gray-50 pb-2">
                                        {{ $user->nipp }}
                                    </p>
                                </div>

                                <div>
                                    <label
                                        class="text-xs font-extrabold text-[#001D4B]/60 uppercase tracking-widest">Jabatan
                                        Sekarang</label>
                                    <p class="mt-2 text-gray-800 font-bold text-xl border-b-2 border-gray-50 pb-2">
                                        {{ $user->jabatan }}
                                    </p>
                                </div>

                                <div>
                                    <label
                                        class="text-xs font-extrabold text-[#001D4B]/60 uppercase tracking-widest">Divisi</label>
                                    <p class="mt-2 text-gray-800 font-bold text-xl border-b-2 border-gray-50 pb-2">
                                        {{ $user->divisi ?? '-' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Kotak Putih Tengah: Statistik Keliling --}}
                        <div class="bg-white rounded-[2.5rem] shadow-md border border-gray-100 p-12">
                            <div class="mb-10">
                                <h3 class="text-2xl font-black text-[#001D4B] uppercase tracking-tight">Statistik Keliling
                                </h3>
                                <div class="h-1.5 w-40 bg-[#001D4B] mt-2 rounded-full"></div>
                            </div>

                            {{-- Card 1: Keliling Minggu Ini --}}
                            <div class="relative overflow-hidden bg-gradient-to-br from-[#001D4B] via-[#0a3d7c] to-[#1a6fa0] rounded-[1.25rem] p-6 min-h-[110px] shadow-[0_6px_20px_rgba(0,29,75,0.25)] flex items-center mb-6">
                                {{-- Dekorasi Hexagon Bottom --}}
                                <svg class="absolute -right-2.5 -bottom-[15px] w-[140px] h-[140px] opacity-15 pointer-events-none" viewBox="0 0 165 165" fill="none">
                                    <polygon points="82,8 158,50 158,134 82,176 6,134 6,50" stroke="white"
                                        stroke-width="1.5" transform="translate(-3,-11) scale(0.8)" />
                                </svg>
                                
                                {{-- Konten Card --}}
                                <div class="relative z-10 flex items-center gap-5 w-full">
                                    <div class="w-14 h-14 rounded-2xl bg-white/10 backdrop-blur-sm flex items-center justify-center border border-white/20">
                                        <svg class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-xs font-extrabold text-white/70 uppercase tracking-wider mb-0.5">Keliling Minggu Ini</p>
                                        <h3 class="text-2xl font-black text-white leading-none">{{ $weeklyRounds }}</h3>
                                    </div>
                                </div>
                            </div>

                            {{-- Card 2: Rata-rata Keliling --}}
                            <div class="relative overflow-hidden bg-gradient-to-br from-[#001D4B] via-[#0a3d7c] to-[#1a6fa0] rounded-[1.25rem] p-6 min-h-[110px] shadow-[0_6px_20px_rgba(0,29,75,0.25)] flex items-center">
                                {{-- Dekorasi Hexagon Bottom --}}
                                <svg class="absolute -right-2.5 -bottom-[15px] w-[140px] h-[140px] opacity-15 pointer-events-none" viewBox="0 0 165 165" fill="none">
                                    <polygon points="82,8 158,50 158,134 82,176 6,134 6,50" stroke="white"
                                        stroke-width="1.3" transform="translate(17,-11) scale(0.82)" />
                                </svg>
                                
                                {{-- Konten Card --}}
                                <div class="relative z-10 flex items-center gap-5 w-full">
                                    <div class="w-14 h-14 rounded-2xl bg-white/10 backdrop-blur-sm flex items-center justify-center border border-white/20">
                                        <svg class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-xs font-extrabold text-white/70 uppercase tracking-wider mb-0.5">Rata-rata Keliling</p>
                                        <h3 class="text-2xl font-black text-white leading-none">{{ $averageRounds }} <span
                                                class="text-xs font-normal opacity-60">/minggu</span></h3>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Kotak Putih Bawah: Keamanan Akun --}}
                        <div class="bg-white rounded-[2.5rem] shadow-md border border-gray-100 p-12">
                            <div class="mb-10">
                                <h3 class="text-2xl font-black text-[#001D4B] uppercase tracking-tight">Keamanan Akun</h3>
                                <div class="h-1.5 w-55 bg-[#001D4B] mt-2 rounded-full"></div>
                            </div>

                            <div class="grid grid-cols-1 gap-8">
                                <div>
                                    <label
                                        class="block text-xs font-extrabold text-[#001D4B]/60 uppercase tracking-widest mb-3">Email
                                        Aktif</label>
                                    <input type="email" name="email" value="{{ $user->email }}"
                                        class="w-full px-6 py-5 bg-gray-50 border border-gray-200 rounded-2xl text-xl font-bold text-gray-800 focus:ring-2 focus:ring-[#001D4B] focus:border-[#001D4B] transition-all">
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                    <div>
                                        <label
                                            class="block text-xs font-extrabold text-[#001D4B]/60 uppercase tracking-widest mb-3">Password
                                            Baru</label>
                                        <div class="relative">
                                            <input type="password" name="password" id="password" placeholder="********"
                                                class="w-full px-6 py-5 bg-gray-50 border border-gray-200 rounded-2xl text-xl font-bold text-gray-800 focus:ring-2 focus:ring-[#001D4B] focus:border-[#001D4B] transition-all pr-12">
                                            <button type="button" onclick="togglePassword('password')"
                                                class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400 hover:text-gray-600 focus:outline-none">
                                                <svg id="icon-password" xmlns="http://www.w3.org/2000/svg"
                                                    class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                    <div>
                                        <label
                                            class="block text-xs font-extrabold text-[#001D4B]/60 uppercase tracking-widest mb-3">Konfirmasi
                                            Password</label>
                                        <div class="relative">
                                            <input type="password" name="password_confirmation"
                                                id="password_confirmation" placeholder="********"
                                                class="w-full px-6 py-5 bg-gray-50 border border-gray-200 rounded-2xl text-xl font-bold text-gray-800 focus:ring-2 focus:ring-[#001D4B] focus:border-[#001D4B] transition-all pr-12">
                                            <button type="button" onclick="togglePassword('password_confirmation')"
                                                class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400 hover:text-gray-600 focus:outline-none">
                                                <svg id="icon-password_confirmation" xmlns="http://www.w3.org/2000/svg"
                                                    class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Button Actions --}}
                        <div class="flex items-center justify-end gap-1">
                            <button type="button"
                                class="px-10 py-4 text-lg font-black text-gray-400 hover:text-gray-600 transition-all">
                                Batalkan
                            </button>
                            <button type="submit"
                                class="px-1 py-5 text-lg font-black text-white bg-[#001D4B] hover:bg-[#0a3d7c] rounded-[1.5rem] shadow-2xl shadow-blue-900/40 transition-all uppercase tracking-widest">
                                Simpan Perubahan
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('js')
    <script>
        function previewImage(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('avatar-preview').src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById('icon-' + inputId);

            if (input.type === "password") {
                input.type = "text";
                icon.innerHTML =
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />';
            } else {
                input.type = "password";
                icon.innerHTML =
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />';
            }
        }
    </script>
@endpush-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />';
            }
        }
    </script>
@endpush
