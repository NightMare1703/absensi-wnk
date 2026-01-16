<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Attendance') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h1 class="font-semibold">Absensi Karyawan</h1>

                    {{-- <form action="/absensi" method="post"> --}}
                    {{-- ! Name --}}

                    <label ><h1 class="text-xl font-semibold mb-5">{{ auth()->user()->name }}</h1></label>

                    {{-- ! End name --}}

                    {{-- Select shift --}}
                    <div class="mb-5">
                        <label for="shift"
                            class="block mb-1 text-gray-900 dark:text-gray-400 text-md font-semibold">Pilih
                            Shift</label>
                        <select id="shift" name="shift"
                            class="bg-gray-50 lg:w-80 border border-gray-300 text-gray-900 text-sm rounded-md focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            <option value="" class="text-md font-semibold" selected disabled>--Pilih Shift--</option>
                            @foreach ($shifts as $shift)
                            <option value="{{ $shift->id }}">{{ $shift->shift }}</option>
                            @endforeach
                        </select>
                    </div>
                    {{-- End select shift --}}

                    {{-- Select location --}}
                    <div class="mb-5">
                        <label for="location"
                            class="block mb-1 text-gray-900 dark:text-gray-400 text-md font-semibold">Pilih
                            Lokasi & No. Meja</label>
                        <select id="location" name="location"
                            class="bg-gray-50 lg:w-80 border border-gray-300 text-gray-900 text-sm rounded-md focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            <option value="" class="text-md font-semibold" selected disabled>--Pilih Lokasi & No. Meja--</option>
                            @foreach ($locations as $location)
                                
                            <option value="{{ $location->id }}">{{ $location->location }}</option>
                            @endforeach
                        </select>
                    </div>
                    {{-- End select location --}}

                    {{-- Video Preview --}}
                    <div class="video rounded-xl">
                        <video class="rounded-xl" id="video" width="320" height="240" autoplay
                        style="transform:scaleX(-1);"></video>
                        <canvas id="canvas" width="320" height="240" style="display:none;"></canvas>
                    </div>
                    {{-- End Video Preview --}}

                    <div class="mb-5">
                        <img id="preview" alt="preview" class="hidden rounded-xl" width="320px"
                            style="margin-top:10px;max-width:320px;transform:scaleX(-1);" />
                    </div>

                    <div class="lg:block flex justify-center gap-4 mb-5">
                        <button class="px-4 py-2 bg-gray-600 duration-300 text-white font-semibold rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:bg-blue-500 dark:hover:bg-blue-600 dark:focus:ring-offset-gray-800" id="capture" type="button">Ambil Gambar</button>
                        <div class="inline-block">
                            <button class="px-4 py-2 bg-gray-800 text-white font-semibold rounded-md duration-300 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:bg-gray-700 dark:hover:bg-gray-800 dark:focus:ring-offset-gray-800" id="submit">Kirim Absensi</button>
                        </div>
                    </div>

                    <p class="text-md font-semibold text-green-600" id="status"></p>
                    {{-- </form> --}}
                </div>
            </div>
        </div>
    </div>
    @vite(['resources/js/attendance.js'])
</x-app-layout>
