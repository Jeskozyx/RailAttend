@extends('layouts.app')

@section('title')
    Scan Per Dinas
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
            height: 500px;
            /* Taller for many datasets */
        }
    </style>
@endpush

@section('content')
    @role('Admin')
        <div class="min-h-screen py-6 bg-gray-50/50">

            {{-- Header --}}
            <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex flex-col justify-center">
                    <h1 class="text-3xl font-bold text-gray-900">Scan Per Dinas</h1>
                    <p class="mt-1 text-sm text-gray-500">Jumlah scan per dinasan (Jadwal KA) berdasarkan periode</p>
                </div>

                @include('pages.dashboard.nav')
            </div>

            {{-- Filter Bar --}}
            <form action="{{ route('dashboard.scanPerDinas') }}" method="GET" class="filter-bar mb-6">
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
                <p class="chart-title">Jumlah Scan Berdasarkan Dinasan (Jadwal KA)</p>
                <p class="chart-sub">Detail per jadwal dalam periode yang dipilih</p>
                <div class="canvas-wrap">
                    <canvas id="scanPerDinasChart"></canvas>
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

            // ── Real Data from Controller ──
            const chartData = @json($chartData);

            // ── Init Chart ──
            const ctx = document.getElementById('scanPerDinasChart').getContext('2d');

            const scanChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: chartData.labels,
                    datasets: chartData.datasets
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
                            display: true,
                            position: 'top',
                            labels: {
                                usePointStyle: true,
                                pointStyle: 'rectRounded',
                                padding: 10,
                                font: {
                                    size: 11,
                                    weight: '600'
                                },
                                color: '#374151',
                                boxWidth: 10
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
        });
    </script>
@endpush
