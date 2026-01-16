(async function () {
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
        if(!preview.classList.contains('block')){
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
                location:location,
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
            status.textContent = `Gagal: server mengembalikan halaman HTML (status ${res.status}).`;
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
