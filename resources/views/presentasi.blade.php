<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RailAttend - Presentation Mode</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'JetBrains Mono', monospace;
        }

        .loader {
            border: 3px solid rgba(255, 255, 255, 0.1);
            border-left-color: #3b82f6;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Animasi Glitch untuk tombol reset */
        .glitch-hover:hover {
            animation: glitch 0.3s cubic-bezier(.25, .46, .45, .94) both infinite;
            color: #ef4444;
        }

        @keyframes glitch {
            0% {
                transform: translate(0)
            }

            20% {
                transform: translate(-2px, 2px)
            }

            40% {
                transform: translate(-2px, -2px)
            }

            60% {
                transform: translate(2px, 2px)
            }

            80% {
                transform: translate(2px, -2px)
            }

            100% {
                transform: translate(0)
            }
        }
    </style>
</head>

<body class="bg-gray-900 text-gray-200 h-screen flex flex-col items-center justify-center overflow-hidden relative">

    <div class="absolute inset-0 opacity-10 pointer-events-none"
        style="background-image: linear-gradient(#374151 1px, transparent 1px), linear-gradient(90deg, #374151 1px, transparent 1px); background-size: 40px 40px;">
    </div>

    <div class="relative z-10 w-full max-w-2xl px-4 text-center">

        <div class="mb-10">
            <h1
                class="text-5xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-cyan-300 tracking-tighter mb-4">
                SYSTEM OVERRIDE
            </h1>
            <p class="text-gray-400 text-lg">Presentation Data Generator Module v1.0</p>
        </div>

        @if (session('success'))
            <div class="mb-8 p-4 bg-green-500/10 border border-green-500/50 rounded-lg text-green-400 animate-bounce">
                <span class="font-bold">✓ SYSTEM STATUS:</span> {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-8 p-4 bg-red-500/10 border border-red-500/50 rounded-lg text-red-400">
                <span class="font-bold">✕ SYSTEM FAILURE:</span> {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('presentasi.seed') }}" method="POST" onsubmit="return startProcess()" class="mb-12">
            @csrf

            <button type="submit" id="magicBtn"
                class="group relative inline-flex items-center justify-center w-64 h-64 rounded-full bg-gray-800 border-4 border-gray-700 shadow-[0_0_50px_rgba(59,130,246,0.1)] hover:shadow-[0_0_80px_rgba(59,130,246,0.4)] hover:border-blue-500 transition-all duration-500 focus:outline-none">

                <span
                    class="absolute inset-4 rounded-full border border-gray-600 border-dashed opacity-50 group-hover:rotate-180 transition-transform duration-[10s]"></span>

                <div class="flex flex-col items-center justify-center z-20">
                    <div id="btnIcon" class="text-blue-500 mb-2 transform group-hover:scale-110 transition-transform">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                        </svg>
                    </div>
                    <span id="btnText"
                        class="text-xl font-bold text-gray-100 tracking-widest group-hover:text-blue-400 transition-colors">
                        INITIATE
                    </span>
                    <div id="loader" class="hidden mt-2">
                        <div class="loader"></div>
                    </div>
                </div>
            </button>
        </form>

        <form action="{{ route('presentasi.reset') }}" method="POST" onsubmit="return confirmReset()">
            @csrf
            <button type="submit"
                class="group flex items-center justify-center space-x-2 mx-auto text-gray-600 hover:text-red-500 transition-colors duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 group-hover:animate-pulse" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                <span
                    class="text-sm font-bold tracking-widest border-b border-transparent group-hover:border-red-500 glitch-hover">
                    PURGE ALL DUMMY DATA
                </span>
            </button>
        </form>

        <div class="mt-12 grid grid-cols-3 gap-6 border-t border-gray-800 pt-8">
            <div>
                <div class="text-3xl font-bold text-white">{{ isset($stats['users']) ? $stats['users'] : '-' }}</div>
                <div class="text-xs text-gray-500 uppercase mt-1">Total Users</div>
            </div>
            <div>
                <div class="text-3xl font-bold text-blue-400">{{ isset($stats['scans']) ? $stats['scans'] : '-' }}</div>
                <div class="text-xs text-gray-500 uppercase mt-1">Scan Records</div>
            </div>
            <div>
                <div class="text-3xl font-bold text-green-400">READY</div>
                <div class="text-xs text-gray-500 uppercase mt-1">System Status</div>
            </div>
        </div>
    </div>

    <script>
        function startProcess() {
            const btn = document.getElementById('magicBtn');
            const icon = document.getElementById('btnIcon');
            const text = document.getElementById('btnText');
            const loader = document.getElementById('loader');

            btn.classList.add('cursor-not-allowed', 'opacity-75');
            btn.classList.remove('hover:shadow-[0_0_80px_rgba(59,130,246,0.4)]', 'hover:border-blue-500');
            icon.classList.add('hidden');
            text.innerText = "PROCESSING";
            loader.classList.remove('hidden');

            return true;
        }

        function confirmReset() {
            return confirm(
                '⚠️ PERINGATAN KERAS ⚠️\n\nAnda akan menghapus SEMUA data dummy (Users, Jadwal, Scan).\n\nLanjutkan pemusnahan data?'
                );
        }
    </script>
</body>

</html>
