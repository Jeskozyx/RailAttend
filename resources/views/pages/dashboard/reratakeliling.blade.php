@extends('layouts.app')

@section('title')
    Rata-rata Keliling
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
            font-size: 1.1rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 4px;
        }

        .chart-card .chart-sub {
            font-size: 0.82rem;
            color: #94a3b8;
            margin-bottom: 18px;
        }

        .chart-card .canvas-wrap {
            position: relative;
            width: 100%;
            height: 380px;
        }

        .over-limit {
            color: #dc2626;
            font-weight: 600;
        }
    </style>
@endpush

@section('content')
    @role('Admin')
        <div class="min-h-screen py-6 bg-gray-50/50">

            {{-- Header --}}
            <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex flex-col justify-center">
                    <h1 class="text-3xl font-bold text-gray-900">Rata-rata Keliling</h1>
                    <p class="mt-1 text-sm text-gray-500">Durasi scan per kereta (menit) - Limit: 30 menit</p>
                </div>

                @include('pages.dashboard.nav')
            </div>

            {{-- Filter Bar --}}
            <form action="{{ route('dashboard.rerataKeliling') }}" method="GET" class="filter-bar mb-6" id="filterForm">
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

                {{-- LEFT: Grouped Bar Chart for Duration --}}
                <div class="lg:col-span-2 chart-card">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="chart-title">Durasi Keliling Per Kereta</p>
                            <p class="chart-sub">Garis merah = Limit 30 menit</p>
                        </div>
                    </div>
                    <div class="canvas-wrap">
                        <canvas id="rerataKelilingChart"></canvas>
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

                {{-- RIGHT: Detail Table --}}
                <div class="chart-card overflow-hidden">
                    <div class="mb-4">
                        <p class="chart-title">Rincian Per Tanggal</p>
                        <p class="chart-sub">Durasi per hari per jabatan (menit)</p>
                    </div>
                    <div class="overflow-y-auto max-h-[380px]" id="detailTableContainer">
                        <table class="w-full text-xs">
                            <thead class="bg-gray-50 sticky top-0">
                                <tr>
                                    <th class="text-left px-3 py-2 font-semibold text-gray-700">KA</th>
                                    @foreach ($allRoles as $role)
                                        <th class="text-left px-3 py-2 font-semibold"
                                            style="color: {{ $roleColors[$role->name] ?? '#6B7280' }};">{{ $role->name }}</th>
                                    @endforeach
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
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-annotation@3.0.1/dist/chartjs-plugin-annotation.min.js">
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // ── Real Data from Controller ──
            let chartData = @json($chartData);

            // ── Filter Parameters ──
            let startDate = "{{ $startDate }}";
            let endDate = "{{ $endDate }}";
            let trainId = "{{ $trainId ?? '' }}";

            // ── Limit Line Value ──
            const LIMIT_MINUTES = 30;

            // ── Format Duration: >60m becomes hours ──
            function formatDuration(mins) {
                if (mins >= 60) {
                    const hours = Math.floor(mins / 60);
                    const remainMins = mins % 60;
                    return remainMins > 0 ? `${hours}j ${remainMins}m` : `${hours}j`;
                }
                return `${mins}m`;
            }

            // ── Build dynamic datasets from roles ──
            function buildDatasets(data) {
                return data.roles.map(role => ({
                    label: role.name,
                    data: data.datasets[role.key]?.data || [],
                    backgroundColor: role.color,
                    borderRadius: 4
                }));
            }

            // ── Init Chart ──
            const ctx = document.getElementById('rerataKelilingChart').getContext('2d');

            const rerataChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: chartData.labels,
                    datasets: buildDatasets(chartData)
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
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    let value = context.parsed.y;
                                    let suffix = value > LIMIT_MINUTES ? ' ⚠️ OVER LIMIT!' : '';
                                    return label + ': ' + formatDuration(value) + suffix;
                                }
                            }
                        },
                        annotation: {
                            annotations: {
                                limitLine: {
                                    type: 'line',
                                    yMin: LIMIT_MINUTES,
                                    yMax: LIMIT_MINUTES,
                                    borderColor: '#dc2626',
                                    borderWidth: 2,
                                    borderDash: [6, 4],
                                    label: {
                                        display: true,
                                        content: 'Limit 30m',
                                        position: 'end',
                                        backgroundColor: '#dc2626',
                                        color: '#fff',
                                        font: {
                                            size: 10,
                                            weight: 'bold'
                                        }
                                    }
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            stacked: false,
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
                            stacked: false,
                            beginAtZero: true,
                            suggestedMax: 45,
                            grid: {
                                color: '#f1f5f9',
                                drawBorder: false
                            },
                            ticks: {
                                color: '#94a3b8',
                                font: {
                                    size: 11
                                },
                                callback: function(value) {
                                    return value + 'm';
                                }
                            },
                            title: {
                                display: true,
                                text: 'Durasi (Menit)',
                                color: '#64748b',
                                font: {
                                    size: 12,
                                    weight: '600'
                                }
                            }
                        }
                    }
                }
            });

            // ==========================================
            // Detail Table Population - DYNAMIC ROLES
            // ==========================================
            const detailTableBody = document.getElementById('detailTableBody');
            const noDataMessage = document.getElementById('noDataMessage');

            function formatDetails(details) {
                return details.map(d => {
                    // Parse "04 Feb: 90m" format
                    const parts = d.split(': ');
                    const dateStr = parts[0];
                    const mins = parseInt(parts[1]);
                    const isOver = mins > LIMIT_MINUTES;
                    const formatted = dateStr + ': ' + formatDuration(mins);
                    return isOver ? `<span class="over-limit">${formatted}</span>` : formatted;
                }).join('<br>') || '-';
            }

            function populateDetailTable(data) {
                detailTableBody.innerHTML = '';

                if (!data.labels || data.labels.length === 0) {
                    noDataMessage.classList.remove('hidden');
                    return;
                }
                noDataMessage.classList.add('hidden');

                data.labels.forEach((trainLabel, index) => {
                    const row = document.createElement('tr');
                    row.className = 'hover:bg-gray-50';

                    // Start with train label column
                    let rowHtml =
                        `<td class="px-3 py-2 font-medium text-gray-800 align-top">${trainLabel}</td>`;

                    // Add column for each role dynamically
                    data.roles.forEach(role => {
                        const roleDetails = formatDetails(data.datasets[role.key]?.details?.[
                            index] || []);
                        rowHtml +=
                            `<td class="px-3 py-2 text-gray-600 align-top">${roleDetails}</td>`;
                    });

                    row.innerHTML = rowHtml;
                    detailTableBody.appendChild(row);
                });
            }

            populateDetailTable(chartData);

            // ==========================================
            // AJAX POLLING
            // ==========================================
            let lastChartDataJson = JSON.stringify(chartData);

            function fetchRerataKelilingStats() {
                const params = new URLSearchParams({
                    start_date: startDate,
                    end_date: endDate,
                    train_id: trainId
                });

                fetch("{{ route('dashboard.rerataKeliling.stats') }}?" + params.toString())
                    .then(response => response.json())
                    .then(data => {
                        const newChartDataJson = JSON.stringify(data);

                        if (newChartDataJson === lastChartDataJson) {
                            return;
                        }

                        lastChartDataJson = newChartDataJson;
                        chartData = data;

                        // Update chart with dynamic datasets
                        rerataChart.data.labels = data.labels;
                        rerataChart.data.datasets = buildDatasets(data);
                        rerataChart.update();

                        populateDetailTable(data);
                    })
                    .catch(error => console.error('Error Polling Rerata Keliling:', error));
            }

            setInterval(fetchRerataKelilingStats, 5000);

            // ==========================================
            // Live filter
            // ==========================================
            document.getElementById('filterForm').addEventListener('submit', function(e) {
                e.preventDefault();
                startDate = document.getElementById('startDate').value;
                endDate = document.getElementById('endDate').value;
                trainId = document.getElementById('selectKA').value;
                lastChartDataJson = null;
                fetchRerataKelilingStats();
            });
        });
    </script>
@endpush
