<div>
    {{-- Confirmation Modal --}}
    @if($showDeleteConfirmation)
        <div class="fixed inset-0 z-50 overflow-y-auto" style="background-color: rgba(0, 0, 0, 0.5);">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="bg-white rounded-lg shadow-xl max-w-md w-full overflow-hidden transition-all transform animate-pulse">
                    <div class="bg-red-50 px-6 py-4 border-b border-red-100">
                        <h3 class="text-lg font-semibold text-red-900">Konfirmasi Penghapusan</h3>
                    </div>
                    <div class="px-6 py-4">
                        <p class="text-gray-700 mb-2">Anda yakin ingin menghapus data berikut?</p>
                        <div class="bg-red-50 border-l-4 border-red-500 p-3 mb-4">
                            <p class="text-gray-900 font-medium">
                                @if($deleteType === 'report')
                                    <span class="block text-sm">Laporan Kerja:</span>
                                @elseif($deleteType === 'attendance')
                                    <span class="block text-sm">Data Absensi:</span>
                                @endif
                                <span class="text-lg font-bold text-red-700">{{ $deleteItemLabel }}</span>
                            </p>
                        </div>
                        <p class="text-sm text-gray-600 mb-4">⚠️ Tindakan ini tidak dapat dibatalkan. Semua data akan dihapus secara permanen.</p>
                    </div>
                    <div class="bg-gray-50 px-6 py-4 flex gap-3 justify-end border-t">
                        <button wire:click="cancelDelete"
                            class="px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 transition-colors font-medium">
                            Batal
                        </button>
                        <button wire:click="proceedDelete"
                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium">
                            Hapus Sekarang
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
    {{-- Confirmation Modal End --}}

    <div class="container flex-col lg:flex lg:flex-wrap justify-center mb-5">

        {{-- Filter and Download --}}
        <div class="filter flex flex-col max-w-md md:flex-row md:items-end md:space-x-4 mb-4">

            {{-- Filter By Date Between --}}
            <div class="mb-4">
                <label for="date_from" class="block mb-2.5 text-sm font-medium text-heading">Dari Tanggal</label>
                <input wire:model.live="date_from" type="date" id="date_from"
                    class="block w-full xl:w-60 p-3 bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand shadow-xs" />
            </div>
            <div class="mb-4">
                <label for="date_to" class="block mb-2.5 text-sm font-medium text-heading">Sampai Tanggal</label>
                <input wire:model.live="date_to" type="date" id="date_to"
                    class="block w-full xl:w-60 p-3 bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand shadow-xs" />
            </div>
            {{-- Filter By Date Between End --}}

            {{-- Export Button --}}
            <div class="mb-4">
                <button wire:click="exportExcel"
                    class="block w-full xl:w-60 p-2.5 bg-gray-700 duration-300 hover:bg-gray-500 hover:cursor-pointer text-white text- font-semibold rounded-base focus:ring-brand focus:border-brand shadow-xs">
                    Download Excel
                </button>
            </div>
            {{-- Export Button End --}}
        </div>
        {{-- Filter and Download End --}}
    </div>

    {{-- Total Salary --}}
    <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-base">
        @php
            $totalSalary = 0;
            if (isset($reports) && $date_from != '' && $date_to != '') {
                foreach ($reports as $report) {
                    $totalSalary += $report->hours * 10000;
                }
            } else {
                $totalSalary = 0;
            }
        @endphp
        Total Gaji : <strong>Rp {{ number_format($totalSalary, 0, ',', '.') }}</strong>
    </div>
    {{-- Total Salary End --}}

    {{-- Message delete attendance --}}
    @if (session()->has('message'))
        <div wire:ignore class="mb-4 p-4 bg-green-100 text-green-700 rounded-base">
            {{ session('message') }}
        </div>
        <script>
            setTimeout(function() {
                @this.set('message', '');
            }, 3000);
        </script>
    @endif
    {{-- Message delete attendance End --}}

    {{-- Tables --}}
    <div class="xl:flex xl:justify-evenly gap-4">
        {{-- Reports Table --}}
        <div
            class="relative mb-5 overflow-x-auto bg-neutral-primary-soft shadow-xs rounded-base border border-default">
            <table class="w-full text-sm text-left rtl:text-center text-body">
                <thead class="text-md text-body bg-gray-300 border-b border-default-medium">
                    <tr class="w-20">
                        <th scope="col" class="p-3 font-strong text-center">
                            NO
                        </th>
                        <th scope="col" class="p-3 font-strong text-center">
                            TANGGAL
                        </th>
                        <th scope="col" class="p-3 font-strong text-center">
                            KERJA
                        </th>
                        <th scope="col" class="p-3 font-strong text-center">
                            GAJI
                        </th>
                        <th scope="col" class="p-3 font-strong text-center">
                            AKSI
                        </th>
                        <th scope="col" class="p-3 font-strong text-center">
                            KETERANGAN
                        </th>
                    </tr>
                </thead>
                <tbody>
                        @forelse ($reports as $report)
                            <tr wire:key="report-{{ $report->id }}"
                                class="bg-neutral-primary-soft border-b border-default hover:bg-gray-200">
                                <td scope="row" class="px-3 py-2 font-medium text-heading whitespace-nowrap">
                                    {{ $loop->iteration }}
                                </td>
                                <td scope="row" class="px-3 py-2 font-medium text-heading whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($report->work_date)->locale('id')->isoFormat('DD MMMM YYYY') }}
                                </td>
                                <td scope="row" class="px-3 py-2 font-medium text-heading whitespace-nowrap">
                                    {{ $report->hours }} jam
                                </td>
                                <td scope="row" class="px-3 py-2 font-medium text-heading whitespace-nowrap">
                                    Rp {{ number_format($report->total_salary, 0, ',', '.') }}
                                </td>
                                <td class="px-3 py-2 font-medium text-heading whitespace-nowrap w-20">

                                    {{-- MODAL DELETE REPORT --}}
                                    <button
                                        class="text-white bg-success box-border border border-transparent hover:bg-success-strong focus:ring-2 focus:ring-brand-medium shadow-xs font-medium leading-5 rounded-base text-sm px-2 py-2 focus:outline-none">
                                        <a href="{{ route('edit-job-report', ['jobReport' => $report->id]) }}">
                                            <svg class="w-4 h-4 text-white dark:text-white" aria-hidden="true"
                                                xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                fill="none" viewBox="0 0 24 24">
                                                <path stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2"
                                                    d="m14.304 4.844 2.852 2.852M7 7H4a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-4.5m2.409-9.91a2.017 2.017 0 0 1 0 2.853l-6.844 6.844L8 14l.713-3.565 6.844-6.844a2.015 2.015 0 0 1 2.852 0Z" />
                                            </svg>
                                        </a>
                                    </button>
                                    {{-- MODAL DELETE REPORT --}}

                                    {{-- MODAL UPDATE REPORT --}}
                                    <button
                                        wire:click="confirmDeleteReport({{ $report->id }})"
                                        class="text-white bg-danger box-border border border-transparent hover:bg-danger-strong focus:ring-2 focus:ring-brand-medium shadow-xs font-medium leading-5 rounded-base text-sm px-2 py-2 focus:outline-none"
                                        type="button">
                                        <svg class="w-4 h-4 text-white dark:text-white" aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round"
                                                stroke-linejoin="round" stroke-width="2"
                                                d="M5 7h14m-9 3v8m4-8v8M10 3h4a1 1 0 0 1 1 1v3H9V4a1 1 0 0 1 1-1ZM6 7h12v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V7Z" />
                                        </svg>
                                    </button>
                                    {{-- MODAL UPDATE REPORT --}}

                                </td>
                                <td class="px-6 py-4 font-medium text-heading whitespace-nowrap w-20">
                                    {{ $report->description }}
                                </td>
                            </tr>
                        @empty
                            <tr class="bg-neutral-primary-soft border-b border-default hover:bg-gray-200">
                                <td colspan="7"
                                    class="px-3 py-2 font-medium text-danger whitespace-nowrap text-center">
                                    Tidak ada data laporan.
                                </td>
                            </tr>
                        @endforelse
                </tbody>

            </table>
            {{-- Job Report Total Salary --}}
        </div>
        {{-- Reports Table End --}}

        {{-- Attendances Table --}}
        <div
            class="relative mb-5 overflow-x-auto bg-neutral-primary-soft shadow-xs rounded-base border border-default">
            <table class="w-full text-sm text-left rtl:text-center text-body">
                <thead class="text-md text-body bg-gray-300 border-b border-default-medium">
                    <tr>
                        <th scope="col" class="p-3 font-strong text-center">
                            NO
                        </th>
                        <th scope="col" class="p-3 font-strong text-center">
                            TANGGAL
                        </th>
                        <th scope="col" class="p-3 font-strong text-center">
                            SHIFT
                        </th>
                        <th scope="col" class="p-3 font-strong text-center">
                            LOKASI
                        </th>
                        <th scope="col" class="p-3 font-strong text-center">
                            KEHADIRAN
                        </th>
                        <th scope="col" class="p-3 font-strong text-center">
                            KETERANGAN
                        </th>
                        <th scope="col" class="p-3 font-strong text-center">
                            AKSI
                        </th>
                        <th scope="col" class="p-3 font-strong text-center">
                            FOTO
                        </th>
                    </tr>
                </thead>
                <tbody>
                        @forelse ($attendances as $attendance)
                            <tr wire:key="attendance-{{ $attendance->id }}"
                                class="bg-neutral-primary-soft border-b border-default hover:bg-gray-200">
                                <td scope="row" class="px-3 py-2 font-medium text-heading whitespace-nowrap">
                                    {{ $loop->iteration }}
                                </td>
                                <td scope="row" class="px-3 py-2 font-medium text-heading whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($attendance->date)->locale('id')->isoFormat('DD MMMM YYYY') }}
                                </td>
                                <td scope="row" class="px-3 py-2 font-medium text-heading whitespace-nowrap">
                                    {{ $attendance->shift->shift }}
                                </td>
                                <td scope="row" class="px-3 py-2 font-medium text-heading whitespace-nowrap">
                                    {{ $attendance->location->location }}
                                </td>
                                <td class="px-3 py-2 font-medium text-heading whitespace-nowrap">
                                    {{ $attendance->check_in }}
                                </td>
                                <td scope="row"
                                    class="px-3 py-2
                                        @if ($attendance->status == 'Terlambat') 
                                            text-danger
                                        @elseif ($attendance->status == 'Hampir Terlambat')
                                            text-warning
                                        @else
                                            text-success
                                        @endif
                                        font-medium whitespace-nowrap">
                                    {{ $attendance->status }}
                                </td>
                                <td class="px-3 py-2 font-medium text-center">
                                    <button wire:click="confirmDeleteAttendance({{ $attendance->id }})"
                                        class="text-white bg-danger box-border border border-transparent hover:bg-danger-strong focus:ring-4 focus:ring-brand-medium shadow-xs font-medium leading-5 rounded-base text-sm px-2 py-2 focus:outline-none"
                                        type="button">
                                        <svg class="w-4 h-4 text-white dark:text-white" aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round"
                                                stroke-linejoin="round" stroke-width="2"
                                                d="M5 7h14m-9 3v8m4-8v8M10 3h4a1 1 0 0 1 1 1v3H9V4a1 1 0 0 1 1-1ZM6 7h12v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V7Z" />
                                        </svg>
                                    </button>
                                </td>
                                <td class="px-3 py-2 font-medium text-center">
                                    @if ($attendance->picture_check_in)
                                        <img style="transform:scaleX(-1);"
                                            src="{{ asset('storage/' . $attendance->picture_check_in) }}"
                                            class="w-20 rounded">
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr class="bg-neutral-primary-soft border-b border-default hover:bg-gray-200">
                                <td colspan="7"
                                    class="px-3 py-2 font-medium text-danger whitespace-nowrap text-center">
                                    Tidak ada data absensi.
                                </td>
                            </tr>
                        @endforelse
                </tbody>
            </table>
        </div>
        {{-- Attendances Table End --}}
    </div>
    {{-- Tables End --}}
    @if ($attendances->hasPages())
        <div class="mt-4">
            {{ $attendances->links() }}
        </div>
    @endif
</div>
