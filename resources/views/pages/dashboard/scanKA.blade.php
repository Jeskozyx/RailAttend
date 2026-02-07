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
                    <h1 class="text-3xl font-bold text-gray-900">Scan KA</h1>
                    <p class="mt-1 text-sm text-gray-500">Jumlah scan per kereta berdasarkan periode</p>
                </div>

                @include('pages.dashboard.nav')
            </div>

            {{-- Filter Bar --}}
            <form action="{{ route('dashboard.scanKA') }}" method="GET" class="filter-bar mb-6">
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

            {{-- Chart --}}
            <div class="chart-card">
                <p class="chart-title">Jumlah Scan Berdasarkan Nama KA</p>
                <p class="chart-sub">Data per tanggal dalam periode yang dipilih</p>
                <div class="canvas-wrap">
                    <canvas id="scanKAChart"></canvas>
                </div>

                {{-- Dynamic Role Legend --}}
                <div class="flex items-center justify-center gap-8 mt-6 pt-4 border-t border-gray-100">
                    @foreach ($allRoles as $role)
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full flex-shrink-0"
                                style="background-color: {{ $roleColors[$role->name] ?? '#6B7280' }};"></span>
                            <span class="text-sm font-semibold text-gray-700">{{ $role->name }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    @else
        {{-- Redirect or show unauthorized message if needed --}}
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
            const startDate = "{{ $startDate }}";
            const endDate = "{{ $endDate }}";
            const trainId = "{{ $trainId ?? '' }}";

            // ── Build dynamic datasets from roles ──
            function buildDatasets(data) {
                return data.roles.map(role => ({
                    label: role.name,
                    data: data.datasets[role.key]?.data || [],
                    backgroundColor: role.color,
                    borderRadius: 4,
                    borderSkipped: false
                }));
            }

            // ── Init Chart ──
            const ctx = document.getElementById('scanKAChart').getContext('2d');

            const scanChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: chartData.labels,
                    datasets: buildDatasets(chartData)
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'nearest',
                        axis: 'x',
                        intersect: true
                    },
                    plugins: {
                        legend: {
                            display: false
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
                            boxPadding: 6,
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    let value = context.parsed.y;
                                    let dateIndex = context.dataIndex;

                                    // Find role key for this dataset
                                    const role = chartData.roles.find(r => r.name === label);
                                    if (role) {
                                        const details = chartData.datasets[role.key]?.details?.[
                                            dateIndex
                                        ] || [];
                                        if (details.length > 0) {
                                            return label + ': ' + details.join(', ');
                                        }
                                    }
                                    return label + ': ' + value;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: '#64748b',
                                font: {
                                    size: 11,
                                    weight: '600'
                                },
                                maxRotation: 0
                            }
                        },
                        y: {
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
            // AJAX POLLING (Update tiap 5 detik)
            // ==========================================

            // Store last data for comparison
            let lastChartDataJson = JSON.stringify(chartData);

            function fetchScanKAStats() {
                const params = new URLSearchParams({
                    start_date: startDate,
                    end_date: endDate,
                    train_id: trainId
                });

                fetch("{{ route('dashboard.scanKA.stats') }}?" + params.toString())
                    .then(response => response.json())
                    .then(data => {
                        // Compare new data with last data
                        const newChartDataJson = JSON.stringify(data);

                        // Only update if data has changed
                        if (newChartDataJson === lastChartDataJson) {
                            return; // Skip update
                        }

                        lastChartDataJson = newChartDataJson;
                        chartData = data;

                        // Rebuild datasets dynamically
                        scanChart.data.labels = data.labels;
                        scanChart.data.datasets = buildDatasets(data);
                        scanChart.update();
                    })
                    .catch(error => console.error('Error Polling ScanKA:', error));
            }

            // Jalankan polling setiap 5000ms (5 Detik)
            setInterval(fetchScanKAStats, 5000);
        });
    </script>
@endpush
