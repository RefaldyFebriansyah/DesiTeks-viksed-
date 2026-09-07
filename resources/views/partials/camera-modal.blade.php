<!-- Camera Modal (Modern Document Scanner for Faktur / Struk) -->
<div class="modal fade" id="cameraModal" tabindex="-1" aria-labelledby="cameraModalLabel" aria-hidden="true" data-bs-backdrop="static" style="z-index: 1065;">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 620px; z-index: 1066; position: relative;">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 18px; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(15,23,42,0.25) !important;">
            <!-- Header -->
            <div class="modal-header px-4 py-3 bg-white border-bottom align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center rounded-circle flex-shrink-0" style="width: 40px; height: 40px; background: #eff6ff; color: #2563eb;">
                        <i class="bi bi-camera" style="font-size: 19px;"></i>
                    </div>
                    <div>
                        <h6 class="modal-title fw-bold text-navy mb-0" id="cameraModalLabel" style="font-size: 15px; letter-spacing: -0.2px;">Foto Bukti Faktur / Surat Jalan</h6>
                        <span class="text-muted" style="font-size: 12px;">Arahkan kamera ke nota atau dokumen faktur untuk bukti penerimaan</span>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="stopCamera()" style="font-size: 11px; opacity: 0.6;"></button>
            </div>

            <!-- Body: Camera Viewport -->
            <div class="modal-body p-3 p-sm-4 bg-light">
                <!-- Video stream container -->
                <div class="position-relative bg-dark rounded-3 overflow-hidden border shadow-sm d-flex align-items-center justify-content-center mx-auto" style="height: 360px; width: 100%; background: #0b0f19;">
                    <!-- Loading state -->
                    <div id="cameraLoading" class="position-absolute top-50 start-50 translate-middle text-center text-white z-2">
                        <div class="spinner-border spinner-border-sm text-light mb-2" role="status"></div>
                        <div class="small opacity-75" style="font-size: 12.5px;">Mengaktifkan Kamera...</div>
                    </div>

                    <!-- Error State -->
                    <div id="cameraError" class="position-absolute top-50 start-50 translate-middle text-center text-white d-none w-85 p-3 z-2">
                        <i class="bi bi-exclamation-triangle-fill text-warning fs-2 mb-2"></i>
                        <div class="fw-semibold" style="font-size: 14px;">Kamera Tidak Dapat Diakses</div>
                        <div class="small opacity-75 mt-1" id="cameraErrorMessage" style="font-size: 12px; line-height: 1.4;">Pastikan izin kamera di browser Anda telah diizinkan.</div>
                        <button type="button" class="btn btn-sm btn-light mt-3 px-3 py-1.5 rounded-pill fw-semibold" onclick="startCamera(activeCameraId)" style="font-size: 12px;">
                            <i class="bi bi-arrow-clockwise me-1"></i> Coba Lagi
                        </button>
                    </div>

                    <!-- Shutter Flash Effect -->
                    <div id="cameraFlash" class="position-absolute w-100 h-100 bg-white opacity-0" style="pointer-events: none; transition: opacity 0.15s ease; z-index: 5;"></div>

                    <!-- Document Viewfinder Overlay (Live Camera Guide) -->
                    <div id="cameraViewfinder" class="position-absolute d-flex flex-column align-items-center justify-content-between p-3" style="inset: 12px; border: 2px dashed rgba(255,255,255,0.45); border-radius: 12px; pointer-events: none; z-index: 3;">
                        <span class="badge bg-dark bg-opacity-75 text-white px-2.5 py-1 rounded-pill shadow-xs" style="font-size: 11px; font-weight: 500;">
                            <i class="bi bi-file-earmark-text me-1 text-info"></i> Posisikan Kertas Faktur / Nota di Dalam Kotak
                        </span>
                        <div class="d-flex justify-content-between w-100 text-white opacity-75" style="font-size: 11px;">
                            <span><i class="bi bi-aspect-ratio me-1"></i>Bukti Fisik</span>
                            <span id="camQualityBadge"><i class="bi bi-check2-circle text-success me-1"></i>HD Mode</span>
                        </div>
                    </div>

                    <!-- Live Video Stream -->
                    <video id="cameraVideo" autoplay playsinline muted style="width: 100%; height: 100%; object-fit: contain; transform: scaleX(1); transition: transform 0.2s ease; display: none;"></video>

                    <!-- Captured Preview Canvas -->
                    <canvas id="cameraCanvas" style="display: none; width: 100%; height: 100%; object-fit: contain;"></canvas>

                    <!-- Floating Quick Control Buttons (Top Right of Camera) -->
                    <div id="cameraFloatControls" class="position-absolute top-0 end-0 m-2 d-flex gap-1" style="z-index: 4;">
                        <button type="button" class="btn btn-sm btn-dark bg-opacity-75 text-white border-0 rounded-circle d-flex align-items-center justify-content-center" id="btnToggleMirror" onclick="toggleMirror()" title="Balik Gambar / Cermin (jika tulisan terbalik)" style="width: 34px; height: 34px;">
                            <i class="bi bi-symmetry-vertical" style="font-size: 15px;"></i>
                        </button>
                    </div>
                </div>

                <!-- Camera device select & Controls row -->
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mt-2.5">
                    <div id="cameraSelectContainer" class="d-flex align-items-center gap-1.5 d-none">
                        <label for="cameraSelect" class="form-label small fw-semibold text-secondary mb-0 flex-shrink-0" style="font-size: 12px;">
                            <i class="bi bi-camera-video me-1"></i>Kamera:
                        </label>
                        <select id="cameraSelect" class="form-select form-select-sm" style="font-size: 12px; border-radius: 6px; max-width: 240px;"></select>
                    </div>
                    <div class="small text-muted ms-auto" id="mirrorStatusText" style="font-size: 11.5px;">
                        <i class="bi bi-info-circle me-1"></i>Klik ikon <i class="bi bi-symmetry-vertical text-dark"></i> jika tulisan terbalik
                    </div>
                </div>
            </div>

            <!-- Footer: Actions -->
            <div class="modal-footer px-4 py-3 bg-white border-top d-flex justify-content-between align-items-center" style="position: relative; z-index: 10;">
                <button type="button" class="btn btn-sm btn-light border px-3.5 py-1.5 rounded-3 fw-medium text-secondary" data-bs-dismiss="modal" onclick="stopCamera()" style="font-size: 12.5px; position: relative; z-index: 11;">Batal</button>
                
                <!-- Live Camera Actions -->
                <div class="d-flex align-items-center gap-2" id="liveCameraButtons" style="position: relative; z-index: 11;">
                    <button type="button" class="btn btn-sm btn-primary px-4 py-2 rounded-3 fw-semibold shadow-xs" id="btnCapture" onclick="capturePhoto()" style="display: none; font-size: 13px;">
                        <i class="bi bi-camera-fill me-1.5"></i> Ambil Foto Faktur
                    </button>
                </div>

                <!-- Preview Photo Actions -->
                <div class="align-items-center gap-2 d-none" id="previewPhotoButtons" style="position: relative; z-index: 11;">
                    <button type="button" class="btn btn-sm btn-light border px-3 py-2 rounded-3 fw-medium text-dark" id="btnRetake" onclick="retakePhoto()" style="font-size: 12.5px;">
                        <i class="bi bi-arrow-clockwise me-1"></i> Foto Ulang
                    </button>
                    <button type="button" class="btn btn-sm btn-light border px-3 py-2 rounded-3 fw-medium text-dark" id="btnRotate" onclick="rotatePhoto()" title="Putar 90 Derajat" style="font-size: 12.5px;">
                        <i class="bi bi-arrow-repeat me-1"></i> Putar 90°
                    </button>
                    <button type="button" class="btn btn-sm btn-success px-4 py-2 rounded-3 fw-semibold text-white shadow-xs" id="btnUsePhoto" onclick="usePhoto()" style="font-size: 13px;">
                        <i class="bi bi-check-lg me-1.5"></i> Gunakan Foto Ini
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let currentStream = null;
let activeCameraId = null;
let targetFileInputId = 'foto_lampiran_input';
let isMirrored = false;
let currentRotation = 0; // 0, 90, 180, 270
let rawCapturedCanvas = null; // Buffer offscreen untuk rotasi lossless

function openCameraModal(inputId) {
    targetFileInputId = inputId || 'foto_lampiran_input';

    const modalEl = document.getElementById('cameraModal');
    if (modalEl && modalEl.parentElement !== document.body) {
        document.body.appendChild(modalEl);
    }
    const myModal = bootstrap.Modal.getOrCreateInstance(modalEl);
    myModal.show();
    
    // Reset state UI
    currentRotation = 0;
    rawCapturedCanvas = null;
    document.getElementById('cameraVideo').style.display = 'none';
    document.getElementById('cameraCanvas').style.display = 'none';
    document.getElementById('cameraViewfinder').style.display = 'flex';
    document.getElementById('cameraLoading').classList.remove('d-none');
    document.getElementById('cameraError').classList.add('d-none');
    document.getElementById('btnCapture').style.display = 'none';

    const liveBtns = document.getElementById('liveCameraButtons');
    const prevBtns = document.getElementById('previewPhotoButtons');
    if (liveBtns) {
        liveBtns.classList.remove('d-none');
        liveBtns.classList.add('d-flex');
    }
    if (prevBtns) {
        prevBtns.classList.remove('d-flex');
        prevBtns.classList.add('d-none');
    }

    document.getElementById('btnToggleMirror').style.display = 'inline-flex';
    
    startCamera();
}

async function startCamera(deviceId = null) {
    const video = document.getElementById('cameraVideo');
    const loading = document.getElementById('cameraLoading');
    const errorEl = document.getElementById('cameraError');
    const btnCapture = document.getElementById('btnCapture');
    
    if (currentStream) {
        currentStream.getTracks().forEach(track => track.stop());
        currentStream = null;
    }
    
    loading.classList.remove('d-none');
    errorEl.classList.add('d-none');
    video.style.display = 'none';
    btnCapture.style.display = 'none';
    
    // Request resolusi tinggi agar teks nota / faktur terbaca tajam
    const constraints = {
        video: {
            deviceId: deviceId ? { exact: deviceId } : undefined,
            facingMode: deviceId ? undefined : { ideal: 'environment' },
            width: { ideal: 1920, min: 1280 },
            height: { ideal: 1080, min: 720 }
        }
    };
    
    try {
        currentStream = await navigator.mediaDevices.getUserMedia(constraints);
        video.srcObject = currentStream;
        await video.play();
        
        video.style.display = 'block';
        btnCapture.style.display = 'inline-block';
        loading.classList.add('d-none');
        
        // Cek apakah kamera hadap depan secara default (jika depan, bisa terbalik teksnya)
        updateMirrorStyle();
        
        // Muat daftar perangkat kamera yang tersedia
        await updateCameraDevices();
    } catch (err) {
        console.error("Gagal mengakses kamera:", err);
        
        // Fallback jika resolusi tinggi atau constraint ditolak browser
        if (constraints.video.width) {
            try {
                const fallbackConstraints = {
                    video: deviceId ? { deviceId: { exact: deviceId } } : true
                };
                currentStream = await navigator.mediaDevices.getUserMedia(fallbackConstraints);
                video.srcObject = currentStream;
                await video.play();
                video.style.display = 'block';
                btnCapture.style.display = 'inline-block';
                loading.classList.add('d-none');
                updateMirrorStyle();
                await updateCameraDevices();
                return;
            } catch (fallbackErr) {
                console.error("Fallback kamera juga gagal:", fallbackErr);
            }
        }
        
        loading.classList.add('d-none');
        errorEl.classList.remove('d-none');
        const errorMsg = document.getElementById('cameraErrorMessage');
        if (err.name === 'NotAllowedError') {
            errorMsg.textContent = "Akses kamera ditolak. Silakan berikan izin kamera pada ikon gembok/kamera di samping URL browser.";
        } else if (err.name === 'NotFoundError' || err.name === 'DevicesNotFoundError') {
            errorMsg.textContent = "Kamera tidak terdeteksi pada perangkat Anda.";
        } else {
            errorMsg.textContent = "Gagal memuat kamera: " + (err.message || 'Kendala izin browser.');
        }
    }
}

function toggleMirror() {
    isMirrored = !isMirrored;
    updateMirrorStyle();
}

function updateMirrorStyle() {
    const video = document.getElementById('cameraVideo');
    const mirrorBtn = document.getElementById('btnToggleMirror');
    if (video) {
        video.style.transform = isMirrored ? 'scaleX(-1)' : 'scaleX(1)';
    }
    if (mirrorBtn) {
        mirrorBtn.className = isMirrored 
            ? 'btn btn-sm btn-primary border-0 rounded-circle d-flex align-items-center justify-content-center' 
            : 'btn btn-sm btn-dark bg-opacity-75 text-white border-0 rounded-circle d-flex align-items-center justify-content-center';
    }
}

async function updateCameraDevices() {
    try {
        if (!navigator.mediaDevices || !navigator.mediaDevices.enumerateDevices) return;
        
        const devices = await navigator.mediaDevices.enumerateDevices();
        const videoDevices = devices.filter(device => device.kind === 'videoinput');
        const select = document.getElementById('cameraSelect');
        const container = document.getElementById('cameraSelectContainer');
        
        if (videoDevices.length > 1) {
            container.classList.remove('d-none');
            select.innerHTML = '';
            
            videoDevices.forEach((device, index) => {
                const option = document.createElement('option');
                option.value = device.deviceId;
                option.text = device.label || `Kamera ${index + 1}`;
                
                if (currentStream) {
                    const activeTrack = currentStream.getVideoTracks()[0];
                    if (activeTrack && activeTrack.label === device.label) {
                        option.selected = true;
                        activeCameraId = device.deviceId;
                    }
                }
                select.appendChild(option);
            });
            
            select.onchange = function() {
                activeCameraId = this.value;
                startCamera(this.value);
            };
        } else {
            container.classList.add('d-none');
        }
    } catch (e) {
        console.warn("Gagal mendeteksi daftar kamera:", e);
    }
}

function stopCamera() {
    if (currentStream) {
        currentStream.getTracks().forEach(track => track.stop());
        currentStream = null;
    }
}

function capturePhoto() {
    const video = document.getElementById('cameraVideo');
    const flash = document.getElementById('cameraFlash');
    
    // Animasi Flash Shutter
    if (flash) {
        flash.style.opacity = '0.85';
        setTimeout(() => { flash.style.opacity = '0'; }, 150);
    }

    const w = video.videoWidth || 1280;
    const h = video.videoHeight || 720;

    // Buat raw snapshot di offscreen canvas
    rawCapturedCanvas = document.createElement('canvas');
    rawCapturedCanvas.width = w;
    rawCapturedCanvas.height = h;
    const ctx = rawCapturedCanvas.getContext('2d');

    if (isMirrored) {
        ctx.translate(w, 0);
        ctx.scale(-1, 1);
    }
    ctx.drawImage(video, 0, 0, w, h);

    currentRotation = 0;
    renderCanvasPreview();

    // Sembunyikan video live, tampilkan canvas preview
    video.style.display = 'none';
    document.getElementById('cameraCanvas').style.display = 'block';
    document.getElementById('cameraViewfinder').style.display = 'none';
    document.getElementById('btnToggleMirror').style.display = 'none';

    // Ganti tombol aksi
    const liveBtns = document.getElementById('liveCameraButtons');
    const prevBtns = document.getElementById('previewPhotoButtons');
    if (liveBtns) {
        liveBtns.classList.remove('d-flex');
        liveBtns.classList.add('d-none');
    }
    if (prevBtns) {
        prevBtns.classList.remove('d-none');
        prevBtns.classList.add('d-flex');
    }
}

function rotatePhoto() {
    currentRotation = (currentRotation + 90) % 360;
    renderCanvasPreview();
}

function renderCanvasPreview() {
    if (!rawCapturedCanvas) return;

    const canvas = document.getElementById('cameraCanvas');
    const ctx = canvas.getContext('2d');
    const rad = (currentRotation * Math.PI) / 180;

    if (currentRotation === 90 || currentRotation === 270) {
        canvas.width = rawCapturedCanvas.height;
        canvas.height = rawCapturedCanvas.width;
    } else {
        canvas.width = rawCapturedCanvas.width;
        canvas.height = rawCapturedCanvas.height;
    }

    ctx.clearRect(0, 0, canvas.width, canvas.height);
    ctx.save();
    ctx.translate(canvas.width / 2, canvas.height / 2);
    ctx.rotate(rad);
    ctx.drawImage(rawCapturedCanvas, -rawCapturedCanvas.width / 2, -rawCapturedCanvas.height / 2);
    ctx.restore();
}

function retakePhoto() {
    const video = document.getElementById('cameraVideo');
    const canvas = document.getElementById('cameraCanvas');
    
    video.style.display = 'block';
    canvas.style.display = 'none';
    document.getElementById('cameraViewfinder').style.display = 'flex';
    document.getElementById('btnToggleMirror').style.display = 'inline-flex';
    
    const liveBtns = document.getElementById('liveCameraButtons');
    const prevBtns = document.getElementById('previewPhotoButtons');
    if (liveBtns) {
        liveBtns.classList.remove('d-none');
        liveBtns.classList.add('d-flex');
    }
    if (prevBtns) {
        prevBtns.classList.remove('d-flex');
        prevBtns.classList.add('d-none');
    }
}

function usePhoto() {
    const canvas = document.getElementById('cameraCanvas');
    if (!canvas) return;

    canvas.toBlob((blob) => {
        if (!blob) {
            alert("Gagal memproses gambar foto. Silakan coba klik foto ulang.");
            return;
        }
        
        const timestamp = new Date().getTime();
        const filename = `faktur_kamera_${timestamp}.jpg`;
        const file = new File([blob], filename, { type: "image/jpeg" });
        
        // 1. Simpan file ke input type="file" via DataTransfer
        const fileInput = document.getElementById(targetFileInputId);
        if (fileInput) {
            try {
                const container = new DataTransfer();
                container.items.add(file);
                fileInput.files = container.files;
            } catch (err) {
                console.warn("DataTransfer tidak didukung:", err);
            }

            // 2. Update thumbnail preview langsung di halaman form
            const previewContainer = document.getElementById('foto_preview_container');
            const previewImg = document.getElementById('foto_preview_img');
            const filenameText = document.getElementById('foto_filename_text');

            if (filenameText) filenameText.textContent = filename;
            if (previewImg) previewImg.src = canvas.toDataURL('image/jpeg', 0.92);
            if (previewContainer) previewContainer.classList.remove('d-none');

            // Trigger change event agar event listener form tetap sync
            fileInput.dispatchEvent(new Event('change', { bubbles: true }));
        }
        
        stopCamera();
        
        // Tutup Modal secara aman
        const modalEl = document.getElementById('cameraModal');
        if (modalEl) {
            const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            if (modal) modal.hide();
        }

        // Cleanup backdrop jika tertinggal
        setTimeout(() => {
            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
            document.body.classList.remove('modal-open');
            document.body.style = '';
        }, 250);

    }, 'image/jpeg', 0.92);
}

// Pastikan stream kamera mati saat modal ditutup (lewat tombol X atau klik luar)
document.addEventListener('DOMContentLoaded', () => {
    const modalEl = document.getElementById('cameraModal');
    if (modalEl) {
        modalEl.addEventListener('hidden.bs.modal', stopCamera);
    }
});
</script>
