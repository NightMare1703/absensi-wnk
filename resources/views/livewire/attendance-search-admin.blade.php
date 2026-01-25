<div>
    {{-- Confirmation Modal --}}
    @if ($showDeleteConfirmation)
        <div class="fixed inset-0 z-50 overflow-y-auto" style="background-color: rgba(0, 0, 0, 0.5);">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div
                    class="bg-white rounded-lg shadow-xl max-w-md w-full overflow-hidden transition-all transform animate-pulse">
                    <div class="bg-red-50 px-6 py-4 border-b border-red-100">
                        <h3 class="text-lg font-semibold text-red-900">Konfirmasi Penghapusan</h3>
                    </div>
                    <div class="px-6 py-4">
                        <p class="text-gray-700 mb-2">Anda yakin ingin menghapus data berikut?</p>
                        <div class="bg-red-50 border-l-4 border-red-500 p-3 mb-4">
                            <p class="text-gray-900 font-medium">
                                @if ($deleteType === 'report')
                                    <span class="block text-sm">Laporan Kerja:</span>
                                @elseif($deleteType === 'attendance')
                                    <span class="block text-sm">Data Absensi:</span>
                                @elseif($deleteType === 'bulk' && $name != '')
                                    <span class="block text-sm">Data Terfilter:</span>
                                @endif
                                <span class="text-lg font-bold text-red-700">{{ $deleteItemLabel }}</span>
                            </p>
                            @if ($deleteType === 'bulk' && $name != '')
                                <p class="text-sm text-red-600 mt-2">Total {{ $bulkDeleteCount }} data akan dihapus</p>
                            @endif
                        </div>
                        <p class="text-sm text-gray-600 mb-4">⚠️ Tindakan ini tidak dapat dibatalkan. Data akan dihapus
                            secara permanen.</p>
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

            {{-- Filter By Name --}}
            <div class="mb-4">
                <label for="name" class="block mb-2.5 text-sm font-medium dark:text-white">Filter Nama</label>
                <select wire:model.live="name" type="text" id="name"
                    class="block w-full xl:w-60 p-3 bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand shadow-xs dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-indigo-600 dark:focus:ring-indigo-600" />
                <option value="">-- Pilih Nama --</option>
                @foreach ($users as $user)
                    <option value="{{ $user->name }}">{{ $user->name }}</option>
                @endforeach
                </select>
            </div>
            {{-- Filter By Name End --}}

            {{-- Filter By Date Between --}}
            <div class="mb-4">
                <label for="date_from" class="block mb-2.5 text-sm font-medium dark:text-white">Dari Tanggal</label>
                <input wire:model.live="date_from" type="date" id="date_from"
                    class="block w-full xl:w-60 p-3 bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand shadow-xs dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-indigo-600 dark:focus:ring-indigo-600" />
            </div>
            <div class="mb-4">
                <label for="date_to" class="block mb-2.5 text-sm font-medium dark:text-white">Sampai Tanggal</label>
                <input wire:model.live="date_to" type="date" id="date_to"
                    class="block w-full xl:w-60 p-3 bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand shadow-xs dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-indigo-600 dark:focus:ring-indigo-600" />
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

            {{-- Delete Filtered Button --}}
            @if ($name != '')
            <div class="mb-4">
                <button wire:click="confirmBulkDelete"
                    class="block w-full xl:w-30 p-2.5 bg-red-600 duration-300 hover:bg-red-700 hover:cursor-pointer text-white font-semibold rounded-base focus:ring-brand focus:border-brand shadow-xs">
                    Hapus Data!
                </button>
            </div>
            @endif
            {{-- Delete Filtered Button End --}}
        </div>
        {{-- Filter and Download End --}}
    </div>

    {{-- Statistics Cards Section --}}
    @if ($date_from != '' && $date_to != '')
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            {{-- Total Salary Card --}}
            <div class="bg-linear-to-br from-green-50 to-green-100 border border-green-200 rounded-lg shadow-md hover:shadow-lg transition-shadow overflow-hidden">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-gray-700 font-semibold text-sm">Total Gaji</h3>
                        <div class="p-3 bg-green-200 rounded-lg">
                            <svg class="w-6 h-6 text-green-700" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                    <p class="text-3xl font-bold text-green-700">Rp {{ number_format($totalSalary, 0, ',', '.') }}</p>
                    <p class="text-green-600 text-xs mt-2">Berdasarkan filter yang dipilih</p>
                </div>
            </div>

            {{-- Total Working Hours Card --}}
            <div class="bg-linear-to-br from-blue-50 to-blue-100 border border-blue-200 rounded-lg shadow-md hover:shadow-lg transition-shadow overflow-hidden">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-gray-700 font-semibold text-sm">Total Jam Kerja</h3>
                        <div class="p-3 bg-blue-200 rounded-lg">
                            <svg class="w-6 h-6 text-blue-700" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v3.586L7.707 9.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 10.586V7z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                    <p class="text-3xl font-bold text-blue-700">{{ $totalHours }} <span class="text-lg">jam</span></p>
                    <p class="text-blue-600 text-xs mt-2">Total durasi kerja</p>
                </div>
            </div>

            {{-- Late Attendance Card --}}
            <div class="bg-linear-to-br from-red-50 to-red-100 border border-red-200 rounded-lg shadow-md hover:shadow-lg transition-shadow overflow-hidden">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-gray-700 font-semibold text-sm">Keterlambatan</h3>
                        <div class="p-3 bg-red-200 rounded-lg">
                            <svg class="w-6 h-6 text-red-700" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                    <p class="text-3xl font-bold text-red-700">{{ $lateCount }} <span class="text-lg">kali</span></p>
                    <p class="text-red-600 text-xs mt-2">Jumlah keterlambatan</p>
                </div>
            </div>
        </div>
    @else
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
                <p class="text-yellow-800 text-sm">Silakan pilih tanggal awal dan akhir untuk melihat statistik filter data</p>
            </div>
        </div>
    @endif
    {{-- Statistics Cards Section End --}}

    {{-- Message delete attendance --}}
    @if (session()->has('message'))
        <div wire:ignore class="mb-4 p-4 bg-green-100 text-green-700 rounded-base">
            {{ session('message') }}
        </div>
        <script>
            setTimeout(function() {
                @this.set('message', '');
            }, 5000);
        </script>
    @endif
    {{-- Message delete attendance End --}}

    {{-- Tables --}}
    <div class="xl:flex xl:justify-evenly gap-4">
        {{-- Reports Table --}}
        <div class="relative mb-5 overflow-x-auto bg-neutral-primary-soft shadow-xs rounded-base border border-default">
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

                                {{-- MODAL UPDATE REPORT --}}
                                @if(auth()->user()->id === $report->user_id)
                                <button
                                    class="text-white bg-success box-border border border-transparent hover:bg-success-strong focus:ring-2 focus:ring-brand-medium shadow-xs font-medium leading-5 rounded-base text-sm px-2 py-2 focus:outline-none mr-1">
                                    <a href="{{ route('edit-job-report', ['jobReport' => $report->id]) }}">
                                        <svg class="w-4 h-4 text-white dark:text-white" aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2"
                                                d="m14.304 4.844 2.852 2.852M7 7H4a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-4.5m2.409-9.91a2.017 2.017 0 0 1 0 2.853l-6.844 6.844L8 14l.713-3.565 6.844-6.844a2.015 2.015 0 0 1 2.852 0Z" />
                                        </svg>
                                    </a>
                                </button>
                                @endif
                                {{-- MODAL UPDATE REPORT --}}

                                {{-- MODAL DELETE REPORT --}}
                                <button wire:click="confirmDeleteReport({{ $report->id }})"
                                    class="text-white bg-danger box-border border border-transparent hover:bg-danger-strong focus:ring-2 focus:ring-brand-medium shadow-xs font-medium leading-5 rounded-base text-sm px-2 py-2 focus:outline-none"
                                    type="button">
                                    <svg class="w-4 h-4 text-white dark:text-white" aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                        viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M5 7h14m-9 3v8m4-8v8M10 3h4a1 1 0 0 1 1 1v3H9V4a1 1 0 0 1 1-1ZM6 7h12v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V7Z" />
                                    </svg>
                                </button>
                                {{-- MODAL DELETE REPORT --}}

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
                        <tr wire:key="attendance-{{ $attendance->id }}" class="bg-neutral-primary-soft border-b border-default hover:bg-gray-200">
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
                                        @if ($attendance->status == 'Terlambat') text-danger
                                        @elseif ($attendance->status == 'Hampir Terlambat')
                                            text-warning
                                        @else
                                            text-success @endif
                                        font-medium whitespace-nowrap">
                                {{ $attendance->status }}
                            </td>
                            <td class="px-3 py-2 font-medium text-heading whitespace-nowrap">
                                {{-- MODAL UPDATE ATTENDANCE --}}
                                {{-- @can('update', $attendance) --}}
                                <button
                                    class="text-white bg-success box-border border border-transparent hover:bg-success-strong focus:ring-2 focus:ring-brand-medium shadow-xs font-medium leading-5 rounded-base text-sm px-2 py-2 focus:outline-none mr-1">
                                    <a href="{{ route('edit-attendance', ['attendance' => $attendance->id]) }}">
                                        <svg class="w-4 h-4 text-white dark:text-white" aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2"
                                                d="m14.304 4.844 2.852 2.852M7 7H4a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-4.5m2.409-9.91a2.017 2.017 0 0 1 0 2.853l-6.844 6.844L8 14l.713-3.565 6.844-6.844a2.015 2.015 0 0 1 2.852 0Z" />
                                        </svg>
                                    </a>
                                </button>
                                {{-- @endcan --}}
                                {{-- MODAL UPDATE ATTENDANCE --}}
                                <button wire:click="confirmDeleteAttendance({{ $attendance->id }})"
                                    class="text-white bg-danger box-border border border-transparent hover:bg-danger-strong focus:ring-4 focus:ring-brand-medium shadow-xs font-medium leading-5 rounded-base text-sm px-2 py-2 focus:outline-none"
                                    type="button">
                                    <svg class="w-4 h-4 text-white dark:text-white" aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M5 7h14m-9 3v8m4-8v8M10 3h4a1 1 0 0 1 1 1v3H9V4a1 1 0 0 1 1-1ZM6 7h12v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V7Z" />
                                    </svg>
                                </button>
                            </td>
                            <td class="px-1 py-1 font-medium text-center">
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
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{-- Attendances Table End --}}
    </div>
    {{-- Tables End --}}

    {{-- Pagination Links --}}
    <div class="flex justify-center">
        {{ $attendances->links() }}
    </div>
</div>
