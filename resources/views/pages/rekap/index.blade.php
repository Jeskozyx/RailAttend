@extends('layouts.app')

@section('title')
    Rekap Waktu
@endsection

@section('content')
    @role('Admin')
        <div class="min-h-screen py-8 bg-gray-50/50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

                {{-- Header --}}
                <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Rekap Waktu</h1>
                        <p class="mt-1 text-sm text-gray-500">Detail waktu keliling per Putaran dengan rincian scan gerbong</p>
                    </div>
                </div>

                {{-- Filter Toolbar --}}
                <div class="mb-8 p-1">
                    <form action="" method="GET" class="flex flex-col lg:flex-row lg:items-end gap-4 overflow-x-auto pb-2">
                        {{-- Date Range Group --}}
                        <div class="flex items-center gap-2 bg-white p-1.5 rounded-xl border border-gray-200 shadow-sm">
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-4 w-4 text-gray-400 group-hover:text-blue-500 transition-colors"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <input type="date" name="date_from" value="{{ $dateFrom ?? now()->format('Y-m-d') }}"
                                    class="pl-9 pr-3 py-2 bg-transparent border-0 text-sm font-medium text-gray-700 focus:ring-0 focus:text-blue-600 cursor-pointer placeholder-gray-400"
                                    title="Dari Tanggal">
                            </div>
                            <span class="text-gray-300">|</span>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-4 w-4 text-gray-400 group-hover:text-blue-500 transition-colors"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <input type="date" name="date_to" value="{{ $dateTo ?? now()->format('Y-m-d') }}"
                                    class="pl-9 pr-3 py-2 bg-transparent border-0 text-sm font-medium text-gray-700 focus:ring-0 focus:text-blue-600 cursor-pointer placeholder-gray-400"
                                    title="Sampai Tanggal">
                            </div>
                        </div>

                        {{-- Dropdowns Group --}}
                        <div class="flex flex-1 gap-3 overflow-x-auto">
                            <div class="relative min-w-[140px]">
                                <select name="user_id"
                                    class="w-full pl-3 pr-8 py-3.5 bg-white border border-gray-200 rounded-xl text-sm font-medium text-gray-700 focus:ring-2 focus:ring-blue-100 focus:border-blue-400 transition-all appearance-none cursor-pointer hover:border-blue-300">
                                    <option value="">Semua User</option>
                                    @foreach ($users ?? [] as $user)
                                        <option value="{{ $user->id }}"
                                            {{ ($userId ?? '') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </div>
                            </div>

                            <div class="relative min-w-[160px]">
                                <select name="schedule_id"
                                    class="w-full pl-3 pr-8 py-3.5 bg-white border border-gray-200 rounded-xl text-sm font-medium text-gray-700 focus:ring-2 focus:ring-blue-100 focus:border-blue-400 transition-all appearance-none cursor-pointer hover:border-blue-300">
                                    <option value="">Semua Jadwal</option>
                                    @foreach ($schedules ?? [] as $schedule)
                                        <option value="{{ $schedule->id }}"
                                            {{ ($scheduleId ?? '') == $schedule->id ? 'selected' : '' }}>
                                            {{ $schedule->train?->name ?? '-' }} ({{ $schedule->no_ka }})
                                        </option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </div>
                            </div>

                            <div class="relative min-w-[140px]">
                                <select name="role_id"
                                    class="w-full pl-3 pr-8 py-3.5 bg-white border border-gray-200 rounded-xl text-sm font-medium text-gray-700 focus:ring-2 focus:ring-blue-100 focus:border-blue-400 transition-all appearance-none cursor-pointer hover:border-blue-300">
                                    <option value="">Semua Jabatan</option>
                                    @foreach ($roles ?? [] as $role)
                                        <option value="{{ $role->id }}"
                                            {{ ($roleId ?? '') == $role->id ? 'selected' : '' }}>
                                            {{ $role->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        {{-- Action Button --}}
                        <button type="submit"
                            class="shrink-0 inline-flex items-center justify-center gap-2 px-6 py-3.5 bg-gray-900 text-white rounded-xl text-sm font-semibold hover:bg-gray-800 active:bg-black transition-all shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                            </svg>
                            Filter
                        </button>

                        <button type="submit" formaction="{{ route('rekap.export') }}"
                            class="shrink-0 inline-flex items-center justify-center gap-2 px-6 py-3.5 bg-green-600 text-white rounded-xl text-sm font-semibold hover:bg-green-700 active:bg-green-800 transition-all shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                            Export Excel
                        </button>
                    </form>
                </div>

                {{-- Table Card --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden mb-8">
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse">
                            <thead>
                                <tr class="bg-gray-50/80 border-b border-gray-200">
                                    <th class="px-4 py-4 text-left w-12"></th>
                                    <th
                                        class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        Putaran</th>
                                    <th
                                        class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        User</th>
                                    <th
                                        class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        Jabatan</th>
                                    <th
                                        class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        Waktu</th>
                                    <th
                                        class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        Durasi</th>
                                    <th
                                        class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        Jarak Waktu</th>
                                    <th
                                        class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        KA & SF</th>
                                    <th
                                        class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        Status</th>
                                </tr>
                            </thead>
                            <tbody id="rekap-table-body" class="divide-y divide-gray-100">
                                @include('pages.rekap.partials.table_rows')
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Summary Section --}}
                <div id="rekap-summary">
                    @include('pages.rekap.partials.summary_cards')
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
    <script>
        function toggleDetail(index, event) {
            // Stop propagation to prevent double-firing if row click logic exists
            if (event) event.stopPropagation();

            const childRow = document.getElementById(`detail-${index}`);
            const parentRow = document.querySelector(`[data-target="detail-${index}"]`);
            if (!childRow || !parentRow) return;

            const toggleIcon = parentRow.querySelector('.toggle-btn svg');
            const toggleBtn = parentRow.querySelector('.toggle-btn');

            if (!toggleIcon || !toggleBtn) return;

            // Toggle Logic
            const isHidden = childRow.classList.contains('hidden');

            if (isHidden) {
                // Show
                childRow.classList.remove('hidden');

                // Active State Styling
                parentRow.classList.add('bg-blue-50/50');
                toggleBtn.classList.remove('bg-gray-100', 'text-gray-400');
                toggleBtn.classList.add('bg-blue-100', 'text-blue-600');
                toggleIcon.classList.add('rotate-180');
            } else {
                // Hide
                childRow.classList.add('hidden');

                // Inactive State Styling
                parentRow.classList.remove('bg-blue-50/50');
                toggleBtn.classList.add('bg-gray-100', 'text-gray-400');
                toggleBtn.classList.remove('bg-blue-100', 'text-blue-600');
                toggleIcon.classList.remove('rotate-180');
            }
        }

        // Event Delegation for Table Rows
        document.addEventListener('click', function(e) {
            // Check if clicked element is inside a parent-row
            const parentRow = e.target.closest('.parent-row');
            if (parentRow) {
                const target = parentRow.getAttribute('data-target');
                if (target) {
                    const index = target.replace('detail-', '');
                    toggleDetail(index, e);
                }
            }
        });

        // Realtime Polling
        const POLL_INTERVAL = 5000; // 5 seconds

        function fetchData() {
            // Keep current query params for filtering
            const url = window.location.href;

            // Save currently expanded rows
            const expandedRows = [];
            document.querySelectorAll('.child-row:not(.hidden)').forEach(row => {
                expandedRows.push(row.id.replace('detail-', ''));
            });

            fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    if (data.html_table) {
                        const tableBody = document.getElementById('rekap-table-body');
                        if (tableBody) {
                            tableBody.innerHTML = data.html_table;

                            // Restore expanded state
                            expandedRows.forEach(id => {
                                const childRow = document.getElementById(`detail-${id}`);
                                const parentRow = document.querySelector(`[data-target="detail-${id}"]`);

                                if (childRow && parentRow) {
                                    const toggleIcon = parentRow.querySelector('.toggle-btn svg');
                                    const toggleBtn = parentRow.querySelector('.toggle-btn');

                                    // Show Child
                                    childRow.classList.remove('hidden');

                                    // Active State Styling for Parent
                                    parentRow.classList.add('bg-blue-50/50');
                                    if (toggleBtn) {
                                        toggleBtn.classList.remove('bg-gray-100', 'text-gray-400');
                                        toggleBtn.classList.add('bg-blue-100', 'text-blue-600');
                                    }
                                    if (toggleIcon) {
                                        toggleIcon.classList.add('rotate-180');
                                    }
                                }
                            });
                        }
                    }
                    if (data.html_summary) {
                        const summaryContainer = document.getElementById('rekap-summary');
                        if (summaryContainer) summaryContainer.innerHTML = data.html_summary;
                    }
                })
                .catch(error => console.error('Error polling data:', error));
        }

        // Start polling if not disabled
        setInterval(fetchData, POLL_INTERVAL);
    </script>
@endpush
