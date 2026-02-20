<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RailAttend - Presentation Command Center</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'JetBrains Mono', monospace;
        }

        .cyber-grid {
            background-image: linear-gradient(rgba(0, 255, 65, 0.05) 1px, transparent 1px),
                linear-gradient(90deg, rgba(0, 255, 65, 0.05) 1px, transparent 1px);
            background-size: 20px 20px;
        }
    </style>
</head>

<body
    class="bg-gray-900 text-green-400 min-h-screen flex items-center justify-center relative overflow-hidden cyber-grid">

    <!-- Ambient Glow -->
    <div
        class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] bg-green-500/10 blur-[100px] rounded-full pointer-events-none">
    </div>

    <div class="relative z-10 w-full max-w-4xl px-4">

        <!-- Header -->
        <div class="text-center mb-12">
            <h1
                class="text-4xl md:text-5xl font-bold tracking-tighter mb-4 text-white drop-shadow-[0_0_10px_rgba(0,255,65,0.5)]">
                PRESENTATION MODE
            </h1>
            <div class="flex items-center justify-center gap-2 text-sm text-green-500/80">
                <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                SYSTEM READY
            </div>
        </div>

        <!-- Alert System -->
        @if (session('success'))
            <div
                class="mb-8 p-4 bg-green-500/10 border border-green-500 text-green-400 rounded-lg text-center backdrop-blur-sm animate-fade-in-down">
                <span class="font-bold">✓ SUCCESS:</span> {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div
                class="mb-8 p-4 bg-red-500/10 border border-red-500 text-red-400 rounded-lg text-center backdrop-blur-sm animate-fade-in-down">
                <span class="font-bold">⚠ ERROR:</span> {{ session('error') }}
            </div>
        @endif

        <!-- The Magic Actions -->
        <div x-data="{ loading: false, resetting: false }" class="flex flex-col md:flex-row items-center justify-center gap-8">

            <!-- SEED BUTTON -->
            <div class="text-center w-full md:w-auto">
                <form action="{{ route('presentation.seed') }}" method="POST" @submit="loading = true">
                    @csrf
                    <button type="submit" :disabled="loading || resetting"
                        class="group relative inline-flex items-center justify-center w-full md:w-80 h-32 text-xl font-bold text-black transition-all duration-200 bg-green-500 hover:bg-green-400 focus:outline-none focus:ring-4 focus:ring-green-500/50 disabled:opacity-50 disabled:cursor-not-allowed clip-path-polygon">

                        <div
                            class="absolute inset-0 w-full h-full bg-white opacity-0 group-hover:opacity-20 transition-opacity">
                        </div>

                        <div class="flex flex-col items-center gap-2">
                            <span x-show="!loading" class="flex flex-col items-center">
                                <svg class="w-10 h-10 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                                EXECUTE SEEDER
                            </span>

                            <span x-show="loading" class="flex flex-col items-center" style="display: none;">
                                <svg class="animate-spin w-10 h-10 mb-2 text-black" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                PROCESSING...
                            </span>
                        </div>
                    </button>
                    <p class="mt-3 text-xs text-gray-500">Generates 20 Users & Simulation Data</p>
                </form>
            </div>

            <!-- RESET BUTTON -->
            <div class="text-center w-full md:w-auto">
                <form action="{{ route('presentation.reset') }}" method="POST" @submit="resetting = true"
                    onsubmit="return confirm('WARNING: THIS WILL DELETE ALL DATA. ARE YOU SURE?');">
                    @csrf
                    <button type="submit" :disabled="loading || resetting"
                        class="group relative inline-flex items-center justify-center w-full md:w-80 h-32 text-xl font-bold text-red-500 transition-all duration-200 bg-transparent border-2 border-red-500 hover:bg-red-500 hover:text-black focus:outline-none focus:ring-4 focus:ring-red-500/50 disabled:opacity-50 disabled:cursor-not-allowed">

                        <div class="flex flex-col items-center gap-2">
                            <span x-show="!resetting" class="flex flex-col items-center">
                                <svg class="w-10 h-10 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                    </path>
                                </svg>
                                RESET DATABASE
                            </span>

                            <span x-show="resetting" class="flex flex-col items-center" style="display: none;">
                                <svg class="animate-spin w-10 h-10 mb-2" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                WIPING DATA...
                            </span>
                        </div>
                    </button>
                    <p class="mt-3 text-xs text-gray-500">Deletes Users, Trains, Schedules</p>
                </form>
            </div>

        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-16 border-t border-gray-800 pt-8">
            <div class="text-center">
                <div class="text-2xl font-bold text-white">20</div>
                <div class="text-xs text-green-500/60 uppercase tracking-widest">Users</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-white">4</div>
                <div class="text-xs text-green-500/60 uppercase tracking-widest">Trains</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-white">16</div>
                <div class="text-xs text-green-500/60 uppercase tracking-widest">Schedules</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-white">~3k</div>
                <div class="text-xs text-green-500/60 uppercase tracking-widest">Verifications</div>
            </div>
        </div>

    </div>

    <!-- Scanline Effect -->
    <div
        class="pointer-events-none absolute inset-0 bg-[linear-gradient(rgba(18,16,16,0)_50%,rgba(0,0,0,0.25)_50%),linear-gradient(90deg,rgba(255,0,0,0.06),rgba(0,255,0,0.02),rgba(0,0,255,0.06))] z-50 bg-[length:100%_2px,3px_100%] opacity-20">
    </div>

</body>

</html>
