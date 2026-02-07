@push('css')
    <style>
        /* ── Stat Card Base ── */
        .stat-card {
            position: relative;
            overflow: hidden;
            background: linear-gradient(135deg, #001D4B 0%, #001D4B 35%, #0a3d7c 60%, #12527a 85%, #1a6fa0 100%);
            border-radius: 1rem;
            padding: 1.5rem;
            min-height: 110px;
            box-shadow: 0 6px 20px rgba(0, 29, 75, 0.30);
            border: none;
        }

        /* ── Geometric Hexagon Decorations ── */
        .stat-card .geo-bottom {
            position: absolute;
            right: -16px;
            bottom: -20px;
            width: 165px;
            height: 165px;
            opacity: 0.17;
            pointer-events: none;
        }

        .stat-card .geo-top {
            position: absolute;
            right: 10px;
            top: -26px;
            width: 105px;
            height: 105px;
            opacity: 0.09;
            pointer-events: none;
        }

        .stat-card .geo-mid {
            position: absolute;
            right: 28px;
            bottom: 16px;
            width: 68px;
            height: 68px;
            opacity: 0.12;
            pointer-events: none;
        }

        /* ── Card Inner Layout ── */
        .stat-card .card-inner {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        /* ── Icon Circle ── */
        .stat-card .icon-wrap {
            width: 3.2rem;
            height: 3.2rem;
            min-width: 3.2rem;
            border-radius: 9999px;
            background: rgba(255, 255, 255, 0.11);
            border: 1px solid rgba(255, 255, 255, 0.20);
            backdrop-filter: blur(6px);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 3px 12px rgba(0, 0, 0, 0.18);
        }

        .stat-card .icon-wrap svg {
            width: 1.6rem;
            height: 1.6rem;
            stroke: #ffffff;
            fill: none;
        }

        /* ── Text ── */
        .stat-card .card-label {
            font-size: 0.82rem;
            font-weight: 500;
            color: rgba(255, 255, 255, 0.60);
            letter-spacing: 0.02em;
            margin-bottom: 3px;
        }

        .stat-card .card-value {
            font-size: 1.70rem;
            font-weight: 700;
            color: #ffffff;
            line-height: 1.2;
        }
    </style>
@endpush

{{-- ════════════════════════════════════════════ --}}
{{-- STAT CARDS (Gradient + Geometric Hexagon)    --}}
{{-- ════════════════════════════════════════════ --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

    {{-- Total Admin --}}
    <div class="stat-card">
        <svg class="geo-bottom" viewBox="0 0 165 165" fill="none">
            <polygon points="82,8 158,50 158,134 82,176 6,134 6,50" stroke="white" stroke-width="1.3" fill="none"
                transform="translate(17,-11) scale(0.82)" />
            <polygon points="82,8 158,50 158,134 82,176 6,134 6,50" stroke="white" stroke-width="0.9" fill="none"
                transform="translate(60,27) scale(0.48)" />
            <polygon points="82,8 158,50 158,134 82,176 6,134 6,50" stroke="white" stroke-width="0.6" fill="none"
                transform="translate(4,56) scale(0.33)" />
        </svg>
        <svg class="geo-top" viewBox="0 0 105 105" fill="none">
            <polygon points="52,4 101,30 101,75 52,101 3,75 3,30" stroke="white" stroke-width="0.7" fill="none" />
        </svg>
        <svg class="geo-mid" viewBox="0 0 68 68" fill="none">
            <polygon points="34,3 65,19 65,49 34,65 3,49 3,19" stroke="white" stroke-width="0.6" fill="none" />
        </svg>

        <div class="card-inner">
            <div class="icon-wrap">
                <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path
                        d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                </svg>
            </div>
            <div>
                <p class="card-label">Total Admin</p>
                <h3 class="card-value">{{ $totalAdmin ?? '0' }}</h3>
            </div>
        </div>
    </div>

    {{-- Total Users --}}
    <div class="stat-card">
        <svg class="geo-bottom" viewBox="0 0 165 165" fill="none">
            <polygon points="82,8 158,50 158,134 82,176 6,134 6,50" stroke="white" stroke-width="1.3" fill="none"
                transform="translate(17,-11) scale(0.82)" />
            <polygon points="82,8 158,50 158,134 82,176 6,134 6,50" stroke="white" stroke-width="0.9" fill="none"
                transform="translate(60,27) scale(0.48)" />
            <polygon points="82,8 158,50 158,134 82,176 6,134 6,50" stroke="white" stroke-width="0.6" fill="none"
                transform="translate(4,56) scale(0.33)" />
        </svg>
        <svg class="geo-top" viewBox="0 0 105 105" fill="none">
            <polygon points="52,4 101,30 101,75 52,101 3,75 3,30" stroke="white" stroke-width="0.7" fill="none" />
        </svg>
        <svg class="geo-mid" viewBox="0 0 68 68" fill="none">
            <polygon points="34,3 65,19 65,49 34,65 3,49 3,19" stroke="white" stroke-width="0.6" fill="none" />
        </svg>

        <div class="card-inner">
            <div class="icon-wrap">
                <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path
                        d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                </svg>
            </div>
            <div>
                <p class="card-label">Total Users</p>
                {{-- TAMBAHAN: ID="total-users" --}}
                <h3 class="card-value" id="total-users">{{ $totalUsers ?? '0' }}</h3>
            </div>
        </div>
    </div>

    {{-- Total KA --}}
    <div class="stat-card">
        <svg class="geo-bottom" viewBox="0 0 165 165" fill="none">
            <polygon points="82,8 158,50 158,134 82,176 6,134 6,50" stroke="white" stroke-width="1.3" fill="none"
                transform="translate(17,-11) scale(0.82)" />
            <polygon points="82,8 158,50 158,134 82,176 6,134 6,50" stroke="white" stroke-width="0.9" fill="none"
                transform="translate(60,27) scale(0.48)" />
            <polygon points="82,8 158,50 158,134 82,176 6,134 6,50" stroke="white" stroke-width="0.6" fill="none"
                transform="translate(4,56) scale(0.33)" />
        </svg>
        <svg class="geo-top" viewBox="0 0 105 105" fill="none">
            <polygon points="52,4 101,30 101,75 52,101 3,75 3,30" stroke="white" stroke-width="0.7"
                fill="none" />
        </svg>
        <svg class="geo-mid" viewBox="0 0 68 68" fill="none">
            <polygon points="34,3 65,19 65,49 34,65 3,49 3,19" stroke="white" stroke-width="0.6" fill="none" />
        </svg>

        <div class="card-inner">
            <div class="icon-wrap">
                <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path
                        d="M9.5 22H14.5M8 2H16M12 5V2M4 12H20M17 19L18.5 22M7 19L5.5 22M8.5 15.5H8.51M15.5 15.5H15.51M8.8 19H15.2C16.8802 19 17.7202 19 18.362 18.673C18.9265 18.3854 19.3854 17.9265 19.673 17.362C20 16.7202 20 15.8802 20 14.2V9.8C20 8.11984 20 7.27976 19.673 6.63803C19.3854 6.07354 18.9265 5.6146 18.362 5.32698C17.7202 5 16.8802 5 15.2 5H8.8C7.11984 5 6.27976 5 5.63803 5.32698C5.07354 5.6146 4.6146 6.07354 4.32698 6.63803C4 7.27976 4 8.11984 4 9.8V14.2C4 15.8802 4 16.7202 4.32698 17.362C4.6146 17.9265 5.07354 18.3854 5.63803 18.673C6.27976 19 7.11984 19 8.8 19ZM9 15.5C9 15.7761 8.77614 16 8.5 16C8.22386 16 8 15.7761 8 15.5C8 15.2239 8.22386 15 8.5 15C8.77614 15 9 15.2239 9 15.5ZM16 15.5C16 15.7761 15.7761 16 15.5 16C15.2239 16 15 15.7761 15 15.5C15 15.2239 15.2239 15 15.5 15C15.7761 15 16 15.2239 16 15.5Z" />
                </svg>
            </div>
            <div>
                <p class="card-label">Total KA</p>
                <h3 class="card-value">{{ $trains->count() }}</h3>
            </div>
        </div>
    </div>

    {{-- Total Sarana --}}
    <div class="stat-card">
        <svg class="geo-bottom" viewBox="0 0 165 165" fill="none">
            <polygon points="82,8 158,50 158,134 82,176 6,134 6,50" stroke="white" stroke-width="1.3" fill="none"
                transform="translate(17,-11) scale(0.82)" />
            <polygon points="82,8 158,50 158,134 82,176 6,134 6,50" stroke="white" stroke-width="0.9" fill="none"
                transform="translate(60,27) scale(0.48)" />
            <polygon points="82,8 158,50 158,134 82,176 6,134 6,50" stroke="white" stroke-width="0.6" fill="none"
                transform="translate(4,56) scale(0.33)" />
        </svg>
        <svg class="geo-top" viewBox="0 0 105 105" fill="none">
            <polygon points="52,4 101,30 101,75 52,101 3,75 3,30" stroke="white" stroke-width="0.7"
                fill="none" />
        </svg>
        <svg class="geo-mid" viewBox="0 0 68 68" fill="none">
            <polygon points="34,3 65,19 65,49 34,65 3,49 3,19" stroke="white" stroke-width="0.6" fill="none" />
        </svg>

        <div class="card-inner">
            <div class="icon-wrap">
                <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 13c0 -3.87 -3.37 -7 -10 -7h-8" />
                    <path d="M3 15h16a2 2 0 0 0 2 -2" />
                    <path d="M3 6v5h17.5" />
                    <path d="M3 11v4" />
                    <path d="M8 11v-5" />
                    <path d="M13 11v-4.5" />
                    <path d="M3 19h18" />
                </svg>
            </div>
            <div>
                <p class="card-label">Total Sarana</p>
                {{-- TAMBAHAN: ID="total-sarana" --}}
                <h3 class="card-value" id="total-sarana">{{ $totalSarana ?? '0' }}</h3>
            </div>
        </div>
    </div>

</div>

{{-- ════════════════════════════════════════════ --}}
{{-- GRAFIK + PIE CHART (tetap sama seperti asli) --}}
{{-- ════════════════════════════════════════════ --}}
<div class="flex flex-col md:flex-row gap-6 mb-8 items-stretch">
    <div class="w-full md:w-[60%] bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex flex-col">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-lg font-bold text-gray-900">Statistik Scan per Kereta</h3>
                <p class="text-xs text-gray-500">Jumlah scan verifikasi per Nomor KA (Jadwal)</p>
            </div>
            <div class="flex items-center gap-2">
                <select id="sortTrainStats"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-1.5 cursor-pointer">
                    <option value="name">Nama (A-Z)</option>
                    <option value="most">Paling Banyak</option>
                    <option value="least">Paling Sedikit</option>
                </select>
                <div class="bg-blue-50 p-2 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-[#001D4B]" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
            </div>
        </div>
        <div class="relative h-[350px] w-full">
            <canvas id="trainTypeChart"></canvas>
        </div>
    </div>

    {{-- Pie Chart --}}
    <div class="w-full md:w-[40%] bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex flex-col">
        <div class="mb-4">
            <h3 class="text-lg font-bold text-gray-900">Frekuensi Scan</h3>
            <p class="text-xs text-gray-500">Distribusi berdasarkan kedinasan</p>
        </div>

        <div class="flex items-center justify-between flex-grow gap-2">
            <div class="relative w-[180px] h-[180px] mx-auto">
                <canvas id="dinasPieChart"></canvas>
            </div>

            <div class="w-1/2 space-y-3" id="pie-chart-legend">
                {{-- Dynamic role legend --}}
                @foreach ($allRoles as $index => $role)
                    <div class="flex items-center justify-between group">
                        <div class="flex items-center">
                            <span class="w-3 h-3 rounded-full mr-2 flex-shrink-0"
                                style="background-color: {{ $chartPieColors[$index] ?? '#6B7280' }};"></span>
                            <span class="text-xs font-semibold text-gray-600">{{ $role->name }}</span>
                        </div>
                        <span id="perc-role-{{ $loop->index }}"
                            class="text-xs font-bold text-gray-900 bg-gray-50 px-2 py-1 rounded">{{ $chartPiePercentages[$index] ?? 0 }}%</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

</div>

@push('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.5.0/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0"></script>

    <script>
        // Global variables for Chart Instance
        let barChartInstance = null;
        let pieChartInstance = null;

        document.addEventListener('DOMContentLoaded', function() {
            // ==========================================
            // 1. SETUP CHART AWAL (Tampilan Awal)
            // ==========================================
            const ctxBar = document.getElementById('trainTypeChart').getContext('2d');
            const labels = @json($chartTrainTypeLabels);
            const values = @json($chartTrainTypeValues);

            const blueGradient = ctxBar.createLinearGradient(0, 0, 0, 320);
            blueGradient.addColorStop(0, '#001D4B');
            blueGradient.addColorStop(0.5, '#0a3d7c');
            blueGradient.addColorStop(1, '#1a6fa0');

            const shadowGradient = ctxBar.createLinearGradient(0, 0, 0, 320);
            shadowGradient.addColorStop(0, 'rgba(0, 29, 75, 0.25)');
            shadowGradient.addColorStop(0.6, 'rgba(10, 61, 124, 0.12)');
            shadowGradient.addColorStop(1, 'rgba(10, 61, 124, 0)');

            barChartInstance = new Chart(ctxBar, {
                plugins: [ChartDataLabels],
                data: {
                    labels: labels,
                    datasets: [{
                        type: 'line',
                        label: 'Trend',
                        data: values,
                        borderColor: '#1a6fa0',
                        borderWidth: 2,
                        pointRadius: 0,
                        tension: 0.4,
                        fill: true,
                        backgroundColor: shadowGradient,
                        datalabels: {
                            display: false
                        },
                        order: 2
                    }, {
                        type: 'bar',
                        label: 'Jumlah Scan',
                        data: values,
                        backgroundColor: blueGradient,
                        borderRadius: 6,
                        barThickness: 45,
                        datalabels: {
                            anchor: 'end',
                            align: 'top',
                            color: '#4B5563',
                            font: {
                                weight: 'bold',
                                size: 12
                            },
                            offset: 4
                        }
                    }]
                },
                options: {
                    aspectRatio: 2,
                    responsive: true,
                    maintainAspectRatio: false,
                    layout: {
                        padding: {
                            top: 30
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: Math.max(...values) + 5,
                            ticks: {
                                stepSize: 20
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });

            const ctxPie = document.getElementById('dinasPieChart').getContext('2d');
            pieChartInstance = new Chart(ctxPie, {
                type: 'doughnut',
                data: {
                    labels: @json($chartPieLabels),
                    datasets: [{
                        data: @json($chartPieValues),
                        backgroundColor: @json($chartPieColors),
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '75%',
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });

            // ==========================================
            // 2. LOGIKA AJAX POLLING (Update tiap 5 detik)
            // ==========================================

            let lastDashboardData = null;

            // Event listener for sort dropdown
            const sortDropdown = document.getElementById('sortTrainStats');
            if (sortDropdown) {
                sortDropdown.addEventListener('change', function() {
                    lastDashboardData = null; // Force update on sort change
                    fetchDashboardStats();
                });
            }

            function fetchDashboardStats() {
                const sort = sortDropdown ? sortDropdown.value : 'name';
                fetch("{{ route('dashboard.stats') }}?sort=" + sort)
                    .then(response => response.json())
                    .then(data => {
                        // Compare new data with last data
                        const currentDataJson = JSON.stringify(data);
                        if (currentDataJson === lastDashboardData) {
                            return; // No change, skip update
                        }

                        lastDashboardData = currentDataJson;

                        // A. Update Angka Text (DOM ID)
                        if (document.getElementById('total-users'))
                            document.getElementById('total-users').innerText = data.totalUsers;

                        if (document.getElementById('total-sarana'))
                            document.getElementById('total-sarana').innerText = data.totalSarana;

                        // B. Update Persentase Text (Dynamic)
                        if (data.chartPiePercentages) {
                            data.chartPiePercentages.forEach((perc, index) => {
                                const el = document.getElementById('perc-role-' + index);
                                if (el) el.innerText = perc + '%';
                            });
                        }

                        // C. Update Bar Chart
                        if (barChartInstance) {
                            barChartInstance.data.labels = data.chartTrainLabels;
                            barChartInstance.data.datasets[0].data = data.chartTrainValues;
                            barChartInstance.data.datasets[1].data = data.chartTrainValues;
                            barChartInstance.options.scales.y.max = Math.max(...data.chartTrainValues) + 5;
                            barChartInstance.update();
                        }

                        // D. Update Pie Chart (with dynamic colors)
                        if (pieChartInstance) {
                            pieChartInstance.data.labels = data.chartPieLabels;
                            pieChartInstance.data.datasets[0].data = data.chartPieValues;
                            pieChartInstance.data.datasets[0].backgroundColor = data.chartPieColors;
                            pieChartInstance.update();
                        }
                    })
                    .catch(error => console.error('Error Polling:', error));
            }

            // Jalankan polling setiap 5000ms (5 Detik)
            setInterval(fetchDashboardStats, 5000);
        });
    </script>
@endpush
