@extends('layouts.app')

@section('title')
    Periode Keliling
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
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.08);
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

        .filter-bar .btn-klik:hover {
            background: #1d4ed8;
        }

        .filter-bar .btn-klik:active {
            transform: scale(0.96);
        }

        /* ── Chart Card ── */
        .chart-card {
            background: #fff;
            border-radius: 18px;
            padding: 28px 24px 20px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.07);
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
                    <h1 class="text-3xl font-bold text-gray-900">Periode Keliling</h1>
                    <p class="mt-1 text-sm text-gray-500">Statistik scan per kereta berdasarkan periode dan jabatan</p>
                </div>

                @include('pages.dashboard.nav')
            </div>

            {{-- Filter Bar --}}
            <form action="{{ route('dashboard.periodeKeliling') }}" method="GET" class="filter-bar mb-6" id="filterForm">
                <span class="filter-label">Nama KA:</span>
                <select name="train_id" id="selectKA">
                    <option value="">Semua Kereta</option>
                    @foreach ($trains as $train)
                        <option value="{{ $train->id }}" {{ $trainId == $train->id ? 'selected' : '' }}>
                            {{ $train->name }}
                        </option>
                    @endforeach
                </select>

                <span class="filter-label">Start Date:</span>
                <input type="date" name="start_date" id="startDate" value="{{ $startDate }}">

                <span class="filter-label">End Date:</span>
                <input type="date" name="end_date" id="endDate" value="{{ $endDate }}">

                <button type="submit" class="btn-klik">FILTER</button>
            </form>

            {{-- Hybrid Dashboard: Chart + Detail Table --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- LEFT: Stacked Bar Chart for Trend --}}
                <div class="lg:col-span-2 chart-card">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="chart-title text-left">Total Scan Per Kereta</p>
                            <p class="chart-sub text-left">Grafik untuk melihat trend perbandingan</p>
                        </div>
                    </div>
                    <div class="canvas-wrap">
                        <canvas id="periodeKelilingChart"></canvas>
                    </div>
                </div>

                {{-- RIGHT: Detail Table --}}
                <div class="chart-card overflow-hidden">
                    <div class="mb-4">
                        <p class="chart-title text-left">Rincian Per Tanggal</p>
                        <p class="chart-sub text-left">Detail scan per hari per jabatan</p>
                    </div>
                    <div class="overflow-y-auto max-h-[380px]" id="detailTableContainer">
                        <table class="w-full text-xs">
                            <thead class="bg-gray-50 sticky top-0">
                                <tr>
                                    <th class="text-left px-3 py-2 font-semibold text-gray-700">KA</th>
                                    <th class="text-left px-3 py-2 font-semibold text-[#FC5D02]">Polsuska</th>
                                    <th class="text-left px-3 py-2 font-semibold text-[#041C4D]">Kondektur</th>
                                    <th class="text-left px-3 py-2 font-semibold text-[#1767E8]">TKA</th>
                                </tr>
                            </thead>
                            <tbody id="detailTableBody" class="divide-y divide-gray-100">
                                {{-- Populated by JS --}}
                            </tbody>
                        </table>
                        <div id="noDataMessage" class="hidden text-center py-8 text-gray-400 text-sm">
                            Tidak ada data untuk periode ini
                        </div>
                    </div>
                </div>

            </div>

        </div>
    @else
        <script>
            window.location = "{{ route('dashboard') }}";
        </script>
    @endrole
@endsection


@push('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.5.0/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // ── Real Data from Controller (mutable for polling) ──
            let chartData = @json($chartData);

            // ── Filter Parameters for AJAX ──
            let startDate = "{{ $startDate }}";
            let endDate = "{{ $endDate }}";
            let trainId = "{{ $trainId ?? '' }}";

            // ── Warna dataset (senada palet KAI) ──
            const colors = {
                polsuska: {
                    bg: '#FC5D02',
                    border: '#FC5D02'
                },
                kondektur: {
                    bg: '#041C4D',
                    border: '#041C4D'
                },
                tka: {
                    bg: '#1767E8',
                    border: '#1767E8'
                },
            };

            // ── Load & Resize Icons for Legend ──
            const iconSize = 25;

            function createResizedIcon(src) {
                const img = new Image();
                img.src = src;
                const canvas = document.createElement('canvas');
                canvas.width = iconSize;
                canvas.height = iconSize;
                const ctx = canvas.getContext('2d');

                img.onload = function() {
                    ctx.drawImage(img, 0, 0, iconSize, iconSize);
                    periodeChart.update();
                };
                return canvas;
            }

            const polsuskaIcon = createResizedIcon("{{ asset('assets/images/profile/polsuska_icons.png') }}");
            const kondekturIcon = createResizedIcon("{{ asset('assets/images/profile/kondektur_icons.png') }}");
            const tkaIcon = createResizedIcon("{{ asset('assets/images/profile/TKA_icons.png') }}");

            // ── Init Chart (Stacked for trend comparison) ──
            const ctx = document.getElementById('periodeKelilingChart').getContext('2d');

            const periodeChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: chartData.labels,
                    datasets: [{
                            label: 'Polsuska',
                            data: chartData.polsuska,
                            backgroundColor: colors.polsuska.bg,
                            borderRadius: 4,
                            borderSkipped: false,
                            pointStyle: polsuskaIcon,
                        },
                        {
                            label: 'Kondektur',
                            data: chartData.kondektur,
                            backgroundColor: colors.kondektur.bg,
                            borderRadius: 4,
                            borderSkipped: false,
                            pointStyle: kondekturIcon,
                        },
                        {
                            label: 'TKA',
                            data: chartData.tka,
                            backgroundColor: colors.tka.bg,
                            borderRadius: 4,
                            borderSkipped: false,
                            pointStyle: tkaIcon,
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
                                padding: 20,
                                font: {
                                    size: 13,
                                    weight: '600'
                                },
                                color: '#374151'
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(30, 41, 59, 0.95)',
                            titleColor: '#fff',
                            bodyColor: '#cbd5e1',
                            titleFont: {
                                size: 14,
                                weight: 'bold'
                            },
                            bodyFont: {
                                size: 12
                            },
                            padding: 12,
                            cornerRadius: 6,
                            displayColors: true,
                            boxPadding: 6
                        }
                    },
                    scales: {
                        x: {
                            stacked: true,
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: '#64748b',
                                font: {
                                    size: 10,
                                    weight: '600'
                                },
                                maxRotation: 45,
                                minRotation: 45
                            }
                        },
                        y: {
                            stacked: true,
                            beginAtZero: true,
                            grid: {
                                color: '#f1f5f9',
                                drawBorder: false
                            },
                            ticks: {
                                color: '#94a3b8',
                                font: {
                                    size: 11
                                },
                                precision: 0
                            }
                        }
                    }
                }
            });

            // ==========================================
            // Detail Table Population
            // ==========================================
            const detailTableBody = document.getElementById('detailTableBody');
            const noDataMessage = document.getElementById('noDataMessage');

            function populateDetailTable(data) {
                detailTableBody.innerHTML = '';

                if (!data.labels || data.labels.length === 0) {
                    noDataMessage.classList.remove('hidden');
                    return;
                }
                noDataMessage.classList.add('hidden');

                data.labels.forEach((trainLabel, index) => {
                    const polDetails = (data.polsuskaDetails[index] || []).join('<br>') || '-';
                    const konDetails = (data.kondekturDetails[index] || []).join('<br>') || '-';
                    const tkaDetails = (data.tkaDetails[index] || []).join('<br>') || '-';

                    const row = document.createElement('tr');
                    row.className = 'hover:bg-gray-50';
                    row.innerHTML = `
                        <td class="px-3 py-2 font-medium text-gray-800 align-top">${trainLabel}</td>
                        <td class="px-3 py-2 text-gray-600 align-top">${polDetails}</td>
                        <td class="px-3 py-2 text-gray-600 align-top">${konDetails}</td>
                        <td class="px-3 py-2 text-gray-600 align-top">${tkaDetails}</td>
                    `;
                    detailTableBody.appendChild(row);
                });
            }

            // Initial table population
            populateDetailTable(chartData);

            // ==========================================
            // AJAX POLLING (Update tiap 5 detik)
            // ==========================================

            let lastChartDataJson = JSON.stringify(chartData);

            function fetchPeriodeKelilingStats() {
                const params = new URLSearchParams({
                    start_date: startDate,
                    end_date: endDate,
                    train_id: trainId
                });

                fetch("{{ route('dashboard.periodeKeliling.stats') }}?" + params.toString())
                    .then(response => response.json())
                    .then(data => {
                        const newChartDataJson = JSON.stringify(data);

                        if (newChartDataJson === lastChartDataJson) {
                            return; // Skip update
                        }

                        console.log('Data changed! Updating chart and table...');
                        lastChartDataJson = newChartDataJson;

                        // Update chartData reference
                        chartData = data;

                        // Update chart
                        periodeChart.data.labels = data.labels;
                        periodeChart.data.datasets[0].data = data.polsuska;
                        periodeChart.data.datasets[1].data = data.kondektur;
                        periodeChart.data.datasets[2].data = data.tka;
                        periodeChart.update();

                        // Update table
                        populateDetailTable(data);
                    })
                    .catch(error => console.error('Error Polling Periode Keliling:', error));
            }

            // Polling setiap 5 detik
            setInterval(fetchPeriodeKelilingStats, 5000);

            // ==========================================
            // Live filter update (tanpa reload)
            // ==========================================
            document.getElementById('filterForm').addEventListener('submit', function(e) {
                e.preventDefault();

                startDate = document.getElementById('startDate').value;
                endDate = document.getElementById('endDate').value;
                trainId = document.getElementById('selectKA').value;

                lastChartDataJson = null; // Force refresh
                fetchPeriodeKelilingStats();
            });
        });
    </script>
@endpush
