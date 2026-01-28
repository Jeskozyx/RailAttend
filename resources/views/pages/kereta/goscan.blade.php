<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak QR - {{ $train->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <style>
        @media print {
            .no-print {
                display: none !important;
            }

            body {
                background: white;
            }

            .page-break {
                page-break-inside: avoid;
            }

            .qr-card {
                border: 1px solid #ccc;
            }
        }
    </style>
</head>

<body class="bg-slate-100 min-h-screen p-8">

    <div class="max-w-6xl mx-auto mb-8 flex justify-between items-center no-print">
        <div>
            <h1 class="text-2xl font-black text-[#001D4B]">QR Code Generator</h1>
            <p class="text-slate-500">Rangkaian: {{ $train->name }} ({{ $train->rangkaians->count() }} Gerbong)</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('train.index') }}"
                class="bg-slate-200 hover:bg-slate-300 text-slate-700 px-4 py-3 rounded-xl font-bold transition-all">
                KEMBALI
            </a>
            <button onclick="downloadAll()"
                class="bg-[#001D4B] text-white px-4 py-3 rounded-xl font-bold transition-all">
                ⬇ DOWNLOAD SEMUA (.ZIP)
            </button>
            <button onclick="window.print()"
                class="bg-[#FF7300] hover:bg-[#e66800] text-white px-6 py-3 rounded-xl font-bold shadow-lg transition-all">
                🖨 CETAK KERTAS
            </button>
        </div>
    </div>

    @if ($train->rangkaians->count() > 0)
        <div class="max-w-6xl mx-auto grid grid-cols-2 md:grid-cols-4 gap-6">
            @foreach ($train->rangkaians as $gerbong)
                <div class="flex flex-col items-center gap-2">

                    <div id="qr-target-{{ $gerbong->id }}"
                        class="qr-card bg-white p-6 border-2 border-dashed border-slate-300 flex flex-col items-center justify-center text-center relative w-[300px] h-[350px] overflow-hidden page-break">

                        <div class="mb-4 relative z-10">
                            <span class="text-[10px] font-bold tracking-[0.2em] text-slate-400 uppercase">RAILATTEND
                                SYSTEM</span>
                        </div>

                        <div class="relative w-[200px] h-[200px]">
                            {!! QrCode::size(200)->margin(1)->format('svg')->errorCorrection('H')->generate($gerbong->qr_code) !!}

                            <div class="absolute inset-0 flex items-center justify-center">
                                <div class="bg-white p-1.5 rounded-md shadow-sm">
                                    <img src="{{ asset('assets/images/kai_logo.png') }}"
                                        onerror="this.style.display='none'" class="w-8 h-auto" alt="Logo">
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 relative z-10">
                            {{-- <h2 class="text-xl font-black text-[#001D4B] uppercase mb-1 leading-none">
                                {{ $gerbong->name }}</h2> --}}
                            <p class="text-sm font-bold text-[#FF7300] mb-2">{{ $train->name }}</p>
                            <p class="text-sm font-black text-black font-mono tracking-widest uppercase">
                                {{ $gerbong->name }} {{ $gerbong->qr_code }}
                            </p>
                        </div>

                        <div class="absolute top-0 right-0 w-16 h-16 bg-[#001D4B]/5 rounded-bl-full -mr-8 -mt-8 z-0">
                        </div>
                        <div class="absolute bottom-0 left-0 w-16 h-16 bg-[#FF7300]/10 rounded-tr-full -ml-8 -mb-8 z-0">
                        </div>
                    </div>

                    <button onclick="downloadQR('qr-target-{{ $gerbong->id }}', '{{ $gerbong->qr_code }}')"
                        class="mt-3 text-sm text-blue-600 font-bold hover:underline no-print flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Download PNG
                    </button>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-12">
            <p class="text-slate-500">Belum ada gerbong.</p>
        </div>
    @endif

    <script>
        function downloadQR(elementId, filename) {
            const element = document.getElementById(elementId);

            html2canvas(element, {
                scale: 2,
                backgroundColor: "#ffffff",
                useCORS: true
            }).then(canvas => {
                const link = document.createElement('a');
                link.download = filename + '.png';
                link.href = canvas.toDataURL("image/png");
                link.click();
            });
        }

        function downloadAll() {
            alert('Fitur download semua sedang diproses satu per satu...');
            @foreach ($train->rangkaians as $g)
                setTimeout(() => {
                    downloadQR('qr-target-{{ $g->id }}', '{{ $g->qr_code }}');
                }, {{ $loop->index * 500 }});
            @endforeach
        }
    </script>
</body>

</html>
