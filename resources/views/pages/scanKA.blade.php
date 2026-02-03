@extends('layouts.app')

@section('title')
    Scan KA
@endsection

@push('css')
<style>
    /* ── Filter Bar ── */
    .filter-bar {
        background: linear-gradient(135deg, #001D4B 0%, #001D4B 40%, #0a3d7c 70%, #12527a 100%);
        border-radius: 14px;
        padding: 18px 28px;
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px 20px;
        box-shadow: 0 4px 16px rgba(0, 29, 75, 0.30);
    }

    .filter-bar .filter-label {
        color: #ffffff;
        font-size: 0.88rem;
        font-weight: 600;
        white-space: nowrap;
    }

    .filter-bar select,
    .filter-bar input[type="date"] {
        background: #ffffff;
        border: none;
        border-radius: 8px;
        padding: 9px 14px;
        font-size: 0.88rem;
        color: #1e293b;
        outline: none;
        min-width: 170px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.08);
        transition: box-shadow 0.2s;
    }
    .filter-bar select:focus,
    .filter-bar input[type="date"]:focus {
        box-shadow: 0 0 0 2px rgba(42, 120, 200, 0.35);
    }

    .filter-bar .btn-klik {
        background: #2563eb;
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 9px 24px;
        font-size: 0.88rem;
        font-weight: 700;
        cursor: pointer;
        transition: background 0.2s, transform 0.1s;
        box-shadow: 0 2px 8px rgba(37, 99, 235, 0.35);
        margin-left: auto;
    }
    .filter-bar .btn-klik:hover  { background: #1d4ed8; }
    .filter-bar .btn-klik:active { transform: scale(0.96); }

    /* ── Chart Card ── */
    .chart-card {
        background: #fff;
        border-radius: 18px;
        padding: 28px 24px 20px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.07);
        border: 1px solid #eef2f7;
    }
    .chart-card .chart-title {
        text-align: center;
        font-size: 1.1rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 4px;
    }
    .chart-card .chart-sub {
        text-align: center;
        font-size: 0.82rem;
        color: #94a3b8;
        margin-bottom: 18px;
    }
    .chart-card .canvas-wrap {
        position: relative;
        width: 100%;
        height: 380px;
    }
</style>
@endpush

@section('content')
    @role('Admin')
        <div class="min-h-screen py-6 bg-gray-50/50">

            {{-- Header --}}
            <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex flex-col justify-center">
                    <h1 class="text-3xl font-bold text-gray-900">Scan KA</h1>
                    <p class="mt-1 text-sm text-gray-500">Jumlah scan per kereta berdasarkan periode</p>
                </div>

                <nav class="flex flex-wrap items-center gap-1 lg:gap-2">
                    <a href="#" class="px-4 py-2 text-sm font-semibold text-gray-600 rounded-lg transition-all duration-200 hover:bg-[#001D4B] hover:text-white">
                        Dashboard
                    </a>
                    <a href="#" class="px-4 py-2 text-sm font-semibold bg-[#001D4B] text-white rounded-lg">
                        Scan KA
                    </a>
                    <a href="#" class="px-4 py-2 text-sm font-semibold text-gray-600 rounded-lg transition-all duration-200 hover:bg-[#001D4B] hover:text-white">
                        Scan Per Dinas
                    </a>
                    <a href="#" class="px-4 py-2 text-sm font-semibold text-gray-600 rounded-lg transition-all duration-200 hover:bg-[#001D4B] hover:text-white">
                        Periode Keliling
                    </a>
                    <a href="#" class="px-4 py-2 text-sm font-semibold text-gray-600 rounded-lg transition-all duration-200 hover:bg-[#001D4B] hover:text-white">
                        Rata-rata Keliling
                    </a>
                </nav>
            </div>

            {{-- Filter Bar --}}
            <div class="filter-bar mb-6">
                <span class="filter-label">Nama KA:</span>
                <select id="selectKA">
                    <option value="">Select Nama KA</option>
                    {{-- @foreach ($trains as $train) --}}
                    <option value="1">KA Ekonomi</option>
                    <option value="2">KA Eksekutif</option>
                    <option value="3">KA Bisnis</option>
                    <option value="4">KA Lokal</option>
                    {{-- @endforeach --}}
                </select>

                <span class="filter-label">Start Date:</span>
                <input type="date" id="startDate" value="2026-01-27">

                <span class="filter-label">End Date:</span>
                <input type="date" id="endDate"   value="2026-02-03">

                <button class="btn-klik" id="btnKlik">Klik</button>
            </div>

            {{-- Chart --}}
            <div class="chart-card">
                <p class="chart-title">Jumlah Scan Berdasarkan Nama KA</p>
                <p class="chart-sub">Data per tanggal dalam periode yang dipilih</p>
                <div class="canvas-wrap">
                    <canvas id="scanKAChart"></canvas>
                </div>
            </div>

        </div>
    @else
        {{-- ... kodingan role non-admin ... --}}
    @endrole
@endsection


@push('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.5.0/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Dummy data (ganti dengan AJAX / controller response) ──
    const dummyData = {
        labels: [
            '27 Jan 2026','28 Jan 2026','29 Jan 2026','30 Jan 2026',
            '31 Jan 2026','01 Feb 2026','02 Feb 2026','03 Feb 2026'
        ],
        polsuska:   [153, 120, 142, 158, 67,  137, 145, 146],
        kondektur:  [71,  40,  47,  31,  28,  49,  78,  46],
        tka:        [43,  16,  17,  39,  27,  48,  22,  51],
        it:         [10,  0,   0,   0,   0,   0,   0,   0]
    };

    // ── Warna dataset (senada palet KAI) ──
    const colors = {
        polsuska:  { bg: '#001D4B', border: '#001D4B' }, // Navy KAI
        kondektur: { bg: '#2563eb', border: '#2563eb' }, // Blue Bold
        tka:       { bg: '#60a5fa', border: '#60a5fa' }, // Sky Blue
    };

    // ── Init Chart ──
    const ctx = document.getElementById('scanKAChart').getContext('2d');

    const scanChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: dummyData.labels,
            datasets: [
                {
                    label: 'Polsuska',
                    data: dummyData.polsuska,
                    backgroundColor: colors.polsuska.bg,
                    borderRadius: {
                        topLeft: 4,
                        topRight: 4,
                    },                    borderSkipped: false
                },
                {
                    label: 'Kondektur',
                    data: dummyData.kondektur,
                    backgroundColor: colors.kondektur.bg,
                    borderRadius: {
                        topLeft: 4,
                        topRight: 4,
                    }, 
                    borderSkipped: false
                },
                {
                    label: 'TKA',
                    data: dummyData.tka,
                    backgroundColor: colors.tka.bg,
                    borderRadius: {
                        topLeft: 4,
                        topRight: 4,
                    }, 
                    borderSkipped: false
                },
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        pointStyle: 'rectRounded',
                        padding: 20,
                        font: { size: 13, weight: '600' },
                        color: '#374151'
                    }
                },
                tooltip: {
                    backgroundColor: '#1e293b',
                    titleColor: '#fff',
                    bodyColor: '#cbd5e1',
                    titleFont: { size: 13, weight: '600' },
                    bodyFont: { size: 12 },
                    padding: 12,
                    cornerRadius: 8,
                    displayColors: true,
                    boxPadding: 4
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: {
                        color: '#64748b',
                        font: { size: 11, weight: '600' },
                        maxRotation: 0
                    }
                },
                y: {
                    beginAtZero: true,
                    max: 350, 
                    grid: {
                        color: '#f1f5f9',
                        drawBorder: false
                    },
                    ticks: {
                        color: '#94a3b8',
                        font: { size: 11 },
                        stepSize: 50
                    }
                }
            }
        }
    });

    // ── Tombol Klik — refresh chart (placeholder untuk AJAX) ──
    document.getElementById('btnKlik').addEventListener('click', function () {
        const ka        = document.getElementById('selectKA').value;
        const startDate = document.getElementById('startDate').value;
        const endDate   = document.getElementById('endDate').value;


        console.log('Filter:', { ka, startDate, endDate });
    });
});
</script>
@endpush