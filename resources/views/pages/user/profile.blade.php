@extends('layouts.app')

@section('title')
    Profile Saya
@endsection

@section('content')
    <div class="min-h-screen" style="background: repeating-linear-gradient(45deg, #ff0000, #ff0000 10px, #00ff00 10px, #00ff00 20px, #0000ff 20px, #0000ff 30px); animation: bgMove 1s infinite linear;">

    <style>
    @keyframes bgMove {
        0% { background-position: 0 0; }
        100% { background-position: 50px 50px; }
    }
    @keyframes rainbow {
        0% { color: red; }
        25% { color: yellow; }
        50% { color: lime; }
        75% { color: cyan; }
        100% { color: magenta; }
    }
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-10px); }
        75% { transform: translateX(10px); }
    }
    @keyframes blink {
        0%, 100% { opacity: 1; }
        50% { opacity: 0; }
    }
    </style>

        <div class="max-w-2xl mx-auto bg-white rounded-lg border-8 border-double border-transparent"
            style="background: repeating-radial-gradient(circle, pink, pink 10px, lightblue 10px, lightblue 20px);
                   border-image: url('data:image/svg+xml,<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"100\" height=\"100\"><rect width=\"100\" height=\"100\" fill=\"none\" stroke=\"%23ff00ff\" stroke-width=\"10\" stroke-dasharray=\"10,5\"/></svg>') 30 stretch;
                   animation: shake 0.3s infinite;">

            <div class="relative h-32" style="background: linear-gradient(90deg, rgba(255,0,0,0.7), rgba(0,255,0,0.7), rgba(0,0,255,0.7));">
                <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,<svg width=\"60\" height=\"60\" xmlns=\"http://www.w3.org/2000/svg\"><text x=\"0\" y=\"45\" font-family=\"Comic Sans MS\" font-size=\"40\" fill=\"%23ffff00\">💩</text></svg>'); background-size: 60px;">
                </div>
                <div class="absolute top-3 right-3">
                    <div class="text-4xl animate-spin" style="animation-duration: 0.5s;">🌀</div>
                </div>
            </div>

            <div class="relative -mt-20 flex justify-center">
                <div class="relative">
                    <div class="h-40 w-40 rounded-full p-3" 
                         style="background: conic-gradient(red, yellow, lime, cyan, blue, magenta, red);
                                border: 6px dotted black;
                                box-shadow: 0 0 30px 10px rgba(255,255,0,0.8),
                                            inset 0 0 20px 5px rgba(0,0,0,0.5);">
                        <img src="{{ $user->avatar_url }}" alt="User Avatar" id="avatar-preview"
                            class="h-full w-full object-cover rounded-full border-4"
                            style="border-style: groove;
                                   border-color: #ff00ff #00ffff #ffff00 #ff0000;
                                   filter: contrast(200%) saturate(300%) hue-rotate(45deg);">
                    </div>
                    <label for="avatar-input"
                        class="absolute -bottom-2 -right-2 p-3 rounded-full cursor-pointer"
                        style="background: radial-gradient(circle, red, orange, yellow, green, blue, indigo, violet);
                               border: 5px solid black;
                               box-shadow: 0 0 15px 5px rgba(255,0,0,0.7);
                               animation: blink 0.2s infinite;">
                        <div class="text-2xl">📸</div>
                    </label>
                </div>
            </div>

            <div class="text-center mt-6 mb-6 px-2">
                <h2 class="text-5xl font-black mb-2" style="text-shadow: 3px 3px 0 red, -3px -3px 0 blue, 0 0 20px yellow;
                                                           animation: rainbow 0.5s infinite;
                                                           font-family: 'Comic Sans MS', cursive;">
                    {{ strtoupper($user->name) }}
                </h2>
                <div class="mt-4 inline-block px-6 py-2 rounded-full text-lg font-extrabold"
                     style="background: linear-gradient(45deg, #ff0000, #ffff00, #00ff00, #00ffff, #0000ff, #ff00ff);
                            border: 4px dashed black;
                            transform: skewX(-20deg);
                            animation: shake 0.2s infinite alternate;">
                    <span class="inline-block animate-bounce" style="animation-duration: 0.3s;">👑</span>
                    {{ $user->jabatan ?? 'USER ROLE' }}
                    <span class="inline-block animate-bounce" style="animation-duration: 0.4s;">💎</span>
                </div>
            </div>

            <form action="{{ route('profile.update') }}" method="POST" class="px-6 pb-8" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="file" name="avatar" id="avatar-input" class="hidden" accept="image/*"
                    onchange="previewImage(this)">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6" style="gap: 30px !important;">

                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-bold mb-3 px-2 py-1 rounded"
                                   style="background: linear-gradient(to right, red, orange);
                                          color: white;
                                          text-shadow: 2px 2px 0 black;
                                          transform: rotate(-1deg);">
                                🔢 NIPP (NOMOR INDUK)
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center">
                                    <div class="w-8 h-8 rounded-full animate-pulse"
                                         style="background: linear-gradient(45deg, #ff0000, #00ff00);
                                                animation-duration: 0.5s;"></div>
                                </div>
                                <input type="text" value="{{ $user->nipp }}" disabled
                                    class="block w-full pl-14 pr-4 py-4 rounded-lg text-xl font-mono font-black"
                                    style="background: repeating-linear-gradient(0deg, #ccc, #ccc 2px, #fff 2px, #fff 4px);
                                           border: 3px wavy #ff0000;
                                           color: #0000ff;
                                           text-shadow: 1px 1px 0 #ffff00;">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <div class="text-2xl animate-spin" style="animation-duration: 1s;">🔒</div>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-bold mb-3 px-2 py-1 rounded"
                                   style="background: linear-gradient(to right, #00ff00, #0000ff);
                                          color: white;
                                          text-shadow: 2px 2px 0 black;
                                          transform: rotate(1deg);">
                                💼 JABATAN SEKARANG
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center">
                                    <div class="text-2xl">👔</div>
                                </div>
                                <input type="text" value="{{ $user->jabatan }}" disabled
                                    class="block w-full pl-12 pr-3 py-4 rounded-lg text-lg font-bold italic"
                                    style="background: radial-gradient(circle, #ffff00, #ff00ff);
                                           border: 4px double #00ffff;
                                           color: #000;
                                           box-shadow: inset 0 0 10px rgba(0,0,0,0.5);">
                            </div>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div>
                            <label class="block text-base font-black mb-2 px-3 py-2 rounded-lg"
                                   style="background: linear-gradient(45deg, #ff00ff, #ffff00);
                                          border: 3px dotted #ff0000;
                                          color: #0000ff;
                                          animation: blink 0.8s infinite;">
                                🏷️ NAMA LENGKAP
                            </label>
                            <input type="text" name="name" value="{{ $user->name }}"
                                class="block w-full px-5 py-4 rounded-md text-lg font-black tracking-wider"
                                style="background: linear-gradient(135deg, #ffcccc, #ccffcc, #ccccff);
                                       border: 5px outset #ff9900;
                                       color: #ff0000;
                                       text-transform: uppercase;
                                       letter-spacing: 3px;
                                       transition: all 0.1s;">
                        </div>

                        <div>
                            <label class="block text-base font-black mb-2 px-3 py-2 rounded-lg"
                                   style="background: linear-gradient(45deg, #00ffff, #ff00ff);
                                          border: 3px ridge #ffff00;
                                          color: #ff0000;
                                          text-decoration: underline wavy;">
                                📧 EMAIL AKTIF
                            </label>
                            <input type="email" name="email" value="{{ $user->email }}"
                                class="block w-full px-5 py-4 rounded-md text-lg font-black"
                                style="background: repeating-linear-gradient(-45deg, transparent, transparent 5px, rgba(255,255,0,0.3) 5px, rgba(255,255,0,0.3) 10px);
                                       border: 4px groove #ff0000;
                                       color: #0000ff;
                                       font-style: italic;">
                        </div>
                    </div>
                </div>

                <div class="relative my-10">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t-4" style="border-style: dashed dotted solid double; border-color: red green blue yellow;"></div>
                    </div>
                    <div class="relative flex justify-center">
                        <span class="px-6 py-2 text-xl font-black rounded-full"
                              style="background: conic-gradient(from 0deg, red, orange, yellow, green, blue, indigo, violet, red);
                                     color: white;
                                     text-shadow: 0 0 10px black;
                                     border: 3px solid black;
                                     animation: shake 0.3s infinite;">
                            🔐🔓🔏 KEAMANAN AKUN 🔐🔓🔏
                        </span>
                    </div>
                </div>

                <div class="rounded-xl p-6 mb-6"
                     style="background: linear-gradient(90deg, rgba(255,0,0,0.3), rgba(0,255,0,0.3), rgba(0,0,255,0.3));
                            border: 6px ridge #ff9900;
                            box-shadow: 0 0 20px rgba(255,0,255,0.7),
                                        inset 0 0 20px rgba(0,255,255,0.7);">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-lg font-black mb-3"
                                   style="color: #ff0000;
                                          text-shadow: 2px 2px 0 #ffff00,
                                                       -2px -2px 0 #00ffff;">
                                🔑 PASSWORD BARU
                            </label>
                            <input type="password" name="password" placeholder="KOSONGIN AJA KALO MALAS GANTI"
                                class="block w-full px-5 py-4 rounded-lg text-center font-bold"
                                style="background: repeating-linear-gradient(90deg, #fff, #fff 5px, #000 5px, #000 10px);
                                       border: 4px inset #ff0000;
                                       color: #00ff00;
                                       letter-spacing: 5px;">
                        </div>
                        <div>
                            <label class="block text-lg font-black mb-3"
                                   style="color: #00ff00;
                                          text-shadow: 2px 2px 0 #ff00ff,
                                                       -2px -2px 0 #ffff00;">
                                🔁 ULANGI PASSWORD
                            </label>
                            <input type="password" name="password_confirmation" placeholder="SAMAIN AJA YANG ATAS"
                                class="block w-full px-5 py-4 rounded-lg text-center font-bold"
                                style="background: repeating-linear-gradient(-90deg, #000, #000 5px, #fff 5px, #fff 10px);
                                       border: 4px outset #00ff00;
                                       color: #ff0000;
                                       letter-spacing: 5px;">
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex items-center justify-end space-x-6">
                    <button type="button"
                        class="px-8 py-4 rounded-lg text-xl font-black"
                        style="background: linear-gradient(45deg, #ff0000, #000000);
                               border: 5px double #ffff00;
                               color: #ffffff;
                               text-shadow: 0 0 10px #ff0000;
                               transform: skewX(-15deg);
                               animation: blink 0.5s infinite;">
                        🚫 BATAL DONG
                    </button>
                    <button type="submit"
                        class="px-10 py-4 rounded-lg text-2xl font-black relative overflow-hidden"
                        style="background: linear-gradient(90deg, #ff0000, #ffff00, #00ff00, #00ffff, #0000ff, #ff00ff, #ff0000);
                               background-size: 400% 100%;
                               animation: rainbow 2s infinite linear;
                               border: 6px groove #ffffff;
                               color: #000000;
                               text-shadow: 0 0 5px #ffffff;
                               box-shadow: 0 0 30px rgba(255,255,0,0.8);
                               transform: scale(1.1);">
                        <span class="relative z-10">💾 SIMPAN NIH 💾</span>
                        <div class="absolute inset-0" style="background: repeating-linear-gradient(45deg, transparent, transparent 10px, rgba(255,255,255,0.3) 10px, rgba(255,255,255,0.3) 20px);"></div>
                    </button>
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
                    const avatar = document.getElementById('avatar-preview');
                    avatar.src = e.target.result;
                    
                    // Efek EXTREME
                    avatar.style.filter = 'hue-rotate(90deg) saturate(500%) contrast(200%) invert(100%)';
                    avatar.style.transform = 'rotate(180deg) scale(1.2)';
                    avatar.style.border = '8px dotted #ff00ff';
                    
                    // Flash effect
                    document.body.style.backgroundColor = '#ffff00';
                    setTimeout(() => {
                        document.body.style.backgroundColor = '';
                        avatar.style.filter = 'contrast(200%) saturate(300%) hue-rotate(45deg)';
                        avatar.style.transform = 'rotate(0deg) scale(1)';
                    }, 300);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }
        
        // Tambah efek ketikan
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('input[type="text"], input[type="email"], input[type="password"]');
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.style.transform = 'scale(1.2) rotate(2deg)';
                    this.style.boxShadow = '0 0 30px 10px rgba(255,0,255,0.7)';
                });
                input.addEventListener('blur', function() {
                    this.style.transform = '';
                    this.style.boxShadow = '';
                });
            });
        });
    </script>
@endpush