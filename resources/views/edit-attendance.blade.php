<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Absensi') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h1 class="text-3xl mb-5">Edit Absensi <span class="block lg:inline font-bold">
                            {{ auth()->user()->name }}
                        </span></h1>
                    <form method="POST" action="{{ route('update-attendance', ['attendance' => $attendance->id]) }}">
                        @csrf
                        @method('PUT')
                        <!-- Hidden field untuk menyimpan attendance ID -->
                        <input type="hidden" name="attendance_id" value="{{ $attendance->id }}"> 
                        <div class="mb-4">
                            <label for="date"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal</label>
                            <input value="{{ old('date', $attendance->date) }}" type="date" name="date" id="date" required
                                class="text-gray-800 mt-1 block w-full lg:w-100 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm  dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-indigo-600 dark:focus:ring-indigo-600" disabled readonly>
                        </div>
                        <div class="mb-4">
                            <label for="location"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Lokasi
                                Kerja</label>
                            <select name="location" id="location" required
                                class="text-gray-800 mt-1 block w-full lg:w-100 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm  dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-indigo-600 dark:focus:ring-indigo-600">
                                @foreach($locations as $location)
                                    <option value="{{ $location->id }}" {{ old('location_id', $attendance->location_id) == $location->id ? 'selected' : '' }}>
                                        {{ $location->location }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-4">
                            <label for="shift"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Shift
                                Kerja</label>
                            <select name="shift" id="shift" required
                                class="text-gray-800 mt-1 block w-full lg:w-100 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm  dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-indigo-600 dark:focus:ring-indigo-600">
                                @foreach($shifts as $shift)
                                    <option value="{{ $shift->id }}" {{ old('shift_id', $attendance->shift_id) == $shift->id ? 'selected' : '' }}>
                                        {{ $shift->shift }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Update Absensi
                            </button>
                        </div>
    </div>
</x-app-layout>
