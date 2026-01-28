@extends('layouts.app')

@section('title', 'Verifikasi Gerbong')

@section('content')
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
        }

        .scanner-box {
            position: relative;
            overflow: hidden;
            border-radius: 1.5rem;
            background: #000;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }

        .animated-line {
            position: absolute;
            width: 100%;
            height: 2px;
            background: linear-gradient(90deg, transparent, #6366f1, transparent);
            top: 0;
            animation: move-line 2.5s infinite linear;
            z-index: 10;
        }

        @keyframes move-line {

            0%,
            100% {
                top: 10%;
            }

            50% {
                top: 90%;
            }
        }

        #reader__dashboard_section_csr,
        #reader__dashboard_section_swaplink {
            display: none !important;
        }

        #reader video {
            width: 100% !important;
            height: 100% !important;
            object-fit: cover !important;
        }
    </style>

    <div class="min-h-screen pb-12">
        <nav class="sticky top-0 z-30 bg-white/80 backdrop-blur-md border-b border-slate-200 px-4 py-4">
            <div class="max-w-xl mx-auto flex items-center justify-between">
                <a href="{{ route('gerbong.show', $schedule->id) }}" class="p-2 hover:bg-slate-100 rounded-full transition-colors">
                    <svg class="w-6 h-6 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <h1 class="text-lg font-bold text-slate-800">Verifikasi Rangkaian</h1>
                <div class="w-10"></div>
            </div>
        </nav>

        <div class="max-w-xl mx-auto px-4 mt-6">
            <div class="bg-white rounded-[2rem] p-4 shadow-sm border border-slate-200">
                <div class="scanner-box aspect-square w-full">
                    <div id="reader" class="w-full h-full"></div>
                    <div class="animated-line"></div>
                </div>
                <div class="mt-4 flex items-center justify-center space-x-2 text-slate-500 text-sm italic">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                    </svg>
                    <span>Scan QR di gerbong <strong>{{ $selectedGerbong->name }}</strong></span>
                </div>
            </div>

            <div class="mt-8 flex items-end justify-between px-2">
                <div>
                    <span class="text-xs font-bold text-indigo-600 uppercase tracking-widest">Kereta Aktif</span>
                    <h2 class="text-2xl font-extrabold text-slate-900">{{ $schedule->train->name }}</h2>
                </div>
                <div class="text-right">
                    <span class="bg-emerald-100 text-emerald-700 px-3 py-1 rounded-full text-xs font-bold uppercase">On
                        Duty</span>
                </div>
            </div>

            <div class="mt-6 space-y-3" id="list-gerbong">
                @foreach ($schedule->train->rangkaians as $gerbong)
                    <div id="card-{{ $gerbong->id }}"
                        class="group relative bg-white border {{ $gerbong->id == $selectedGerbong->id ? 'border-indigo-500 ring-4 ring-indigo-50' : 'border-slate-200' }} 
                        rounded-2xl p-4 transition-all duration-300 {{ $gerbong->is_verified ? 'bg-emerald-50/50 border-emerald-200' : '' }}">

                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div id="icon-{{ $gerbong->id }}"
                                    class="w-12 h-12 rounded-xl flex items-center justify-center transition-all duration-500
                                    {{ $gerbong->is_verified ? 'bg-emerald-500 text-white' : 'bg-slate-100 text-slate-400' }}">
                                    @if ($gerbong->is_verified)
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                                d="M5 13l4 4L19 7" />
                                        </svg>
                                    @else
                                        <span class="font-bold text-sm">{{ $loop->iteration }}</span>
                                    @endif
                                </div>
                                <div>
                                    <h4 class="font-bold {{ $gerbong->is_verified ? 'text-slate-900' : 'text-slate-700' }}">
                                        {{ $gerbong->name }}</h4>
                                    <p class="text-xs font-medium text-slate-400 font-mono">{{ $gerbong->qr_code }}</p>
                                </div>
                            </div>
                            <div id="time-{{ $gerbong->id }}"
                                class="text-xs font-bold text-emerald-600 bg-emerald-100 px-2 py-1 rounded-md transition-opacity duration-500 {{ $gerbong->verified_at ? 'opacity-100' : 'opacity-0' }}">
                                {{ $gerbong->verified_at ? \Carbon\Carbon::parse($gerbong->verified_at)->format('H:i') : '' }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-8 pb-10">
                <form action="{{ route('kondektur.submit_report') }}" method="POST">
                    @csrf
                    <input type="hidden" name="schedule_id" value="{{ $schedule->id }}">
                    <button type="submit"
                        class="w-full py-4 rounded-2xl font-black tracking-widest text-white transition-all shadow-lg
                    {{ $schedule->train->rangkaians->where('is_verified', false)->count() > 0 ? 'bg-slate-300 cursor-not-allowed' : 'bg-indigo-600 hover:bg-indigo-700 active:scale-95 shadow-indigo-200' }}"
                        {{ $schedule->train->rangkaians->where('is_verified', false)->count() > 0 ? 'disabled' : '' }}>
                        KIRIM LAPORAN DINASAN
                    </button>
                </form>
            </div>
        </div>
    </div>

    <audio id="beep-sound" preload="auto">
        <source src="https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3" type="audio/mpeg">
    </audio>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const beep = document.getElementById('beep-sound');
            const csrfToken = "{{ csrf_token() }}";
            let isScanning = true;

            function onScanSuccess(decodedText) {
                if (!isScanning) return;
                isScanning = false;

                fetch("{{ route('kondektur.process_scan') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": csrfToken
                        },
                        body: JSON.stringify({
                            qr_code: decodedText,
                            train_id: "{{ $schedule->train_id }}",
                            rangkaian_id: "{{ $selectedGerbong->id }}",
                            schedule_id: "{{ $schedule->id }}"
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            // Play Sound
                            if (beep) {
                                beep.play().catch(e => console.log("Sound error:", e));
                            }

                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: data.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                // Redirect ke halaman list gerbong setelah sukses
                                window.location.href = "{{ route('gerbong.show', $schedule->id) }}";
                            });
                        } else {
                            throw new Error(data.message);
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: error.message
                        }).then(() => {
                            isScanning = true;
                        });
                    });
            }

            const html5QrCode = new Html5Qrcode("reader");
            const config = {
                fps: 10,
                qrbox: {
                    width: 250,
                    height: 250
                },
                aspectRatio: 1.0
            };
            html5QrCode.start({
                facingMode: "environment"
            }, config, onScanSuccess);
        });
    </script>
@endsection