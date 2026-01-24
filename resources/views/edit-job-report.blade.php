<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Report') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h1 class="text-3xl mb-5">Laporan Kerja <span class="block lg:inline font-bold">
                            {{ auth()->user()->name }}
                        </span></h1>
                    <form method="POST" action="{{ route('update-job-report', ['jobReport' => $jobReport->id]) }}">
                        @csrf
                        @method('PUT')
                        <!-- Hidden field untuk menyimpan job report ID -->
                        <input type="hidden" name="job_report_id" value="{{ $jobReport->id }}"> 
                        <div class="mb-4">
                            <label for="date"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal</label>
                            <input value="{{ old('date', $jobReport->work_date) }}" type="date" name="date" id="date" required
                                class="text-gray-800 mt-1 block w-full lg:w-100 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>

                        <div class="mb-4">
                            <label for="hours"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Jumlah
                                Jam</label>
                            <input value="{{ old('hours', $jobReport->hours) }}" type="number" name="hours" id="hours" required
                                class="text-gray-800 mt-1 block w-full lg:w-100 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>

                        <div class="mb-4">
                            <label for="description"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Deskripsi
                                Pekerjaan</label>
                            <textarea name="description" id="description" rows="4"
                                class="text-gray-800 mt-1 block w-full lg:w-100 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('description', $jobReport->description) }}</textarea>
                        </div>

                        <div>
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Submit Report
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
