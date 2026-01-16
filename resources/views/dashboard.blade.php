<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-5">
        <div class="max-w-8xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h1 class="text-3xl mb-5">Data Absensi <span class="font-bold">
                            @if (auth()->user()->role == 'admin')
                                Host
                            @elseif (auth()->user()->role == 'employee')
                                {{ auth()->user()->name }}
                            @endif
                        </span></h1>

                    @if (auth()->user()->role == 'admin')
                        <div class="mb-4 p-4 bg-green-100 text-success rounded-md">
                            Anda masuk sebagai <strong>Admin</strong>. Berikut adalah data absensi host.
                        </div>


                        {{-- Work Location Setting --}}
                        <div class="lokasi-live mb-4">
                            <h3 class="text-sm mb-2 font-medium">Setting Lokasi</h3>
                            <livewire:work-location-crud />
                        </div>
                        {{-- Work Location Setting End --}}
                        {{-- Work Shift Setting --}}
                        <div class="shift mb-4">
                            <h3 class="text-sm mb-2 font-medium">Setting Jam Kerja</h3>
                            <livewire:work-shift-crud />
                        </div>
                        {{-- Work Shift Setting End --}}

                        {{-- Attendance Search Admin --}}
                        <livewire:attendance-search-admin />

                    @elseif (auth()->user()->role == 'employee')
                        <div class="mb-4 p-4 bg-blue-100 text-blue-800 rounded-md">
                            Anda masuk sebagai <strong>Host</strong>. Berikut absensi kehadiran anda.
                        </div>

                        
                        {{-- Attendance Search --}}
                        <livewire:attendance-search />
                        
                    @endif

                </div>

            </div>
        </div>
    </div>
</x-app-layout>