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

                    <label>
                        <h1 class="text-xl font-semibold mb-5">{{ auth()->user()->name }}</h1>
                    </label>

                    {{-- ! End name --}}

                    {{-- Select shift --}}
                    <div class="mb-5">
                        <label for="shift"
                            class="block mb-1 text-gray-900 dark:text-gray-400 text-md font-semibold">Pilih
                            Shift</label>
                        <select id="shift" name="shift"
                            class="bg-gray-50 lg:w-80 border border-gray-300 text-gray-900 text-sm rounded-md focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            <option value="" class="text-md font-semibold" selected disabled>--Pilih Shift--
                            </option>
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
                            <option value="" class="text-md font-semibold" selected disabled>--Pilih Lokasi & No.
                                Meja--</option>
                            @foreach ($locations as $location)
                                <option value="{{ $location->id }}">{{ $location->location }}</option>
                            @endforeach
                        </select>
                    </div>
                    {{-- End select location --}}
                    <div class="photos xl:block flex flex-col items-center mb-5 w-full lg:w-96">

                        {{-- Video Preview --}}
                        <div class="rounded-xl mb-5 w-full aspect-video bg-black">
                            <video class="rounded-xl w-full h-full object-cover" id="video" autoplay
                                style="transform:scaleX(-1);"></video>
                            <canvas id="canvas" class="rounded-xl w-full h-full object-cover" style="display:none;transform:scaleX(-1);"></canvas>
                        </div>
                        {{-- End Video Preview --}}

                        {{-- <div class="mb-5"> --}}
                            <img id="preview" alt="preview" class="hidden rounded-xl w-full h-60 object-cover"
                                style="transform:scaleX(-1);" />
                        {{-- </div> --}}
                    </div>

                    <div class="lg:block flex justify-center gap-4 mb-5">
                        <button
                            class="px-4 py-2 bg-gray-600 duration-300 text-white font-semibold rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:bg-blue-500 dark:hover:bg-blue-600 dark:focus:ring-offset-gray-800"
                            id="capture" type="button">Ambil Gambar</button>
                        <div class="inline-block">
                            <button
                                class="px-4 py-2 bg-gray-800 text-white font-semibold rounded-md duration-300 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:bg-gray-700 dark:hover:bg-gray-800 dark:focus:ring-offset-gray-800"
                                id="submit">Kirim Absensi</button>
                        </div>
                    </div>

                    <p class="text-md font-semibold text-green-600" id="status"></p>
                    {{-- </form> --}}
                </div>
            </div>
        </div>
    </div>
    {{-- @vite(['resources/js/attendance.js']) --}}
    <script>
        (async function() {
            const video = document.getElementById("video");
            const canvas = document.getElementById("canvas");
            const preview = document.getElementById("preview");
            const captureBtn = document.getElementById("capture");
            const submitBtn = document.getElementById("submit");
            const status = document.getElementById("status");

            // minta permission camera
            try {
                const stream = await navigator.mediaDevices.getUserMedia({
                    video: true,
                    audio: false,
                });
                video.srcObject = stream;
            } catch (err) {
                status.textContent = "Tidak dapat mengakses kamera: " + err.message;
                console.error(err);
                return;
            }

            // ambil foto
            captureBtn.addEventListener("click", () => {
                const ctx = canvas.getContext("2d");
                ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                const dataUrl = canvas.toDataURL("image/png");
                if (!preview.classList.contains('block')) {
                    preview.classList.replace('hidden', 'block')
                }
                preview.src = dataUrl;
            });

            // kirim data absensi
            submitBtn.addEventListener("click", async () => {
                // const userId = document.getElementById("employeeId").value.trim();
                const shift = document.getElementById("shift").value.trim();
                const location = document.getElementById("location").value.trim();

                if (!preview.src) {
                    alert("Ambil foto terlebih dahulu");
                    return;
                }

                if (!shift) {
                    alert("Pilih shift terlebih dahulu!");
                    return;
                }
                if (!location) {
                    alert("Pilih lokasi terlebih dahulu!");
                    return;
                }

                // tampilkan status
                status.textContent = "Mengirim...";

                // kirim data ke server
                const res = await fetch("/attendance", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector(
                            'meta[name="csrf-token"]'
                        ).content,
                    },
                    credentials: "same-origin", // penting
                    body: JSON.stringify({
                        // user_id: userId,
                        shift: shift,
                        location: location,
                        picture_check_in: preview.src,
                    }),
                });

                // Periksa header Content-Type sebelum parsing
                const contentType = res.headers.get("content-type") || "";

                let data;
                if (contentType.includes("application/json")) {
                    try {
                        data = await res.json();
                    } catch (err) {
                        console.error("Gagal mem-parse JSON:", err);
                        // fallback: baca text untuk debugging
                        const text = await res.text();
                        console.error("Response text:", text);
                        status.textContent =
                            "Gagal memproses respons server (JSON tidak valid).";
                        return;
                    }
                } else {
                    // Respons bukan JSON — ambil text untuk melihat apa yang dikembalikan server
                    const text = await res.text();
                    console.warn("Server returned non-JSON response:", text);
                    status.textContent =
                        `Gagal: server mengembalikan halaman HTML (status ${res.status}).`;
                    return;
                }

                // handle response
                if (res.ok) {
                    status.textContent = data.message || "Sukses";
                    window.location.href = data.redirect || "/dashboard";
                    // reset
                } else {
                    status.textContent = data.message || "Gagal: " + res.status;
                }

            });
        })();
    </script>
</x-app-layout>
