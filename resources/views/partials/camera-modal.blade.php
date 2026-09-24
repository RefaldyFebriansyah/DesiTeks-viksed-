<!-- Camera Modal (Ultra-Modern Dark Mobile Scanner for Faktur / Surat Jalan) -->
<style>
    .camera-modal-content {
        background: #0b0f19 !important;
        color: #f8fafc;
        border-radius: 24px !important;
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
        box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.7) !important;
    }
    
    /* Native Smartphone Shutter Button */
    .shutter-wrapper {
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        user-select: none;
        -webkit-tap-highlight-color: transparent;
    }
    .shutter-ring {
        width: 66px;
        height: 66px;
        border-radius: 50%;
        border: 3.5px solid #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1), border-color 0.2s ease;
        box-shadow: 0 0 20px rgba(255, 255, 255, 0.25);
    }
    .shutter-wrapper:hover .shutter-ring {
        transform: scale(1.06);
        border-color: #60a5fa;
        box-shadow: 0 0 25px rgba(96, 165, 250, 0.4);
    }
    .shutter-wrapper:active .shutter-ring {
        transform: scale(0.92);
    }
    .shutter-core {
        width: 52px;
        height: 52px;
        border-radius: 50%;
        background: #2563eb;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.2s ease;
    }
    .shutter-wrapper:hover .shutter-core {
        background: #1d4ed8;
    }

    .spin-icon {
        animation: spinFlip 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }
    @keyframes spinFlip {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(180deg); }
    }

    /* Scanner Corner Markers */
    .scanner-corners {
        position: absolute;
        inset: 0;
        pointer-events: none;
    }
    .scanner-corner {
        position: absolute;
        width: 24px;
        height: 24px;
        border-color: #38bdf8;
        border-style: solid;
    }
    .corner-tl { top: 0; left: 0; border-width: 3.5px 0 0 3.5px; border-top-left-radius: 10px; }
    .corner-tr { top: 0; right: 0; border-width: 3.5px 3.5px 0 0; border-top-right-radius: 10px; }
    .corner-bl { bottom: 0; left: 0; border-width: 0 0 3.5px 3.5px; border-bottom-left-radius: 10px; }
    .corner-br { bottom: 0; right: 0; border-width: 0 3.5px 3.5px 0; border-bottom-right-radius: 10px; }
    
    .btn-camera-action {
        background: rgba(255, 255, 255, 0.07) !important;
        color: #f1f5f9 !important;
        border: 1px solid rgba(255, 255, 255, 0.15) !important;
        backdrop-filter: blur(8px);
        transition: all 0.2s ease;
    }
    .btn-camera-action:hover {
        background: rgba(255, 255, 255, 0.16) !important;
        color: #ffffff !important;
        border-color: rgba(255, 255, 255, 0.3) !important;
        transform: translateY(-1px);
    }
</style>

<div class="modal fade" id="cameraModal" tabindex="-1" aria-labelledby="cameraModalLabel" aria-hidden="true" data-bs-backdrop="static" style="z-index: 1065;">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 600px; z-index: 1066; position: relative;">
        <div class="modal-content camera-modal-content">
            <!-- Header -->
            <div class="modal-header px-4 py-3 border-bottom border-white border-opacity-10 align-items-center justify-content-between" style="background: #0b0f19;">
                <div class="d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center rounded-circle flex-shrink-0" style="width: 38px; height: 38px; background: rgba(37, 99, 235, 0.15); color: #60a5fa;">
                        <i class="bi bi-camera-fill" style="font-size: 18px;"></i>
                    </div>
                    <div>
                        <h6 class="modal-title fw-bold text-white mb-0" id="cameraModalLabel" style="font-size: 15px; letter-spacing: -0.2px;">Foto Bukti Faktur / Surat Jalan</h6>
                        <span style="font-size: 11.5px; color: #94a3b8;">Posisikan nota atau dokumen di dalam area kamera</span>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" onclick="stopCamera()" style="font-size: 12px; opacity: 0.8;"></button>
            </div>

            <!-- Body: Camera Viewport -->
            <div class="modal-body p-0 bg-black position-relative">
                <!-- Video stream container -->
                <div class="position-relative overflow-hidden d-flex align-items-center justify-content-center mx-auto" style="height: 390px; width: 100%; background: #000000;">
                    <!-- Loading state -->
                    <div id="cameraLoading" class="position-absolute top-50 start-50 translate-middle text-center text-white z-2">
                        <div class="spinner-border spinner-border-sm text-info mb-2" role="status"></div>
                        <div class="small opacity-75" style="font-size: 12.5px; color: #cbd5e1;">Mengaktifkan Kamera HP...</div>
                    </div>

                    <!-- Error State -->
                    <div id="cameraError" class="position-absolute top-50 start-50 translate-middle text-center text-white d-none w-85 p-3 z-2">
                        <i class="bi bi-exclamation-triangle-fill text-warning fs-2 mb-2"></i>
                        <div class="fw-semibold text-white" style="font-size: 14px;">Kamera Tidak Dapat Diakses</div>
                        <div class="small mt-1" id="cameraErrorMessage" style="font-size: 12px; line-height: 1.4; color: #94a3b8;">Pastikan izin kamera di browser Anda telah diizinkan.</div>
                        <button type="button" class="btn btn-sm btn-outline-light mt-3 px-3 py-1.5 rounded-pill fw-semibold" onclick="startCamera(activeCameraId)" style="font-size: 12px;">
                            <i class="bi bi-arrow-clockwise me-1"></i> Coba Lagi
                        </button>
                    </div>

                    <!-- Shutter Flash Effect -->
                    <div id="cameraFlash" class="position-absolute w-100 h-100 bg-white opacity-0" style="pointer-events: none; transition: opacity 0.15s ease; z-index: 5;"></div>

                    <!-- Top Overlay Controls (Floating in Camera View) -->
                    <div id="cameraTopOverlay" class="position-absolute top-0 start-0 end-0 p-3 d-flex align-items-center justify-content-between z-3" style="pointer-events: none;">
                        <span id="camModeBadge" class="badge bg-dark bg-opacity-75 text-white px-3 py-1.5 rounded-pill border border-white border-opacity-15 shadow-sm" style="font-size: 11px; font-weight: 500; backdrop-filter: blur(8px);">
                            <i class="bi bi-camera me-1.5 text-info"></i> Kamera Belakang
                        </span>

                        <div class="d-flex align-items-center gap-2" style="pointer-events: auto;">
                            <!-- Flip Camera Button (Depan / Belakang) -->
                            <button type="button" id="btnFlipCamera" onclick="switchCamera()" class="btn btn-sm btn-camera-action rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm" style="width: 40px; height: 40px;" title="Putar Kamera Depan / Belakang">
                                <i class="bi bi-camera-revert fs-5"></i>
                            </button>

                            <!-- Mirror Toggle Button -->
                            <button type="button" id="btnToggleMirror" onclick="toggleMirror()" class="btn btn-sm btn-camera-action rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm" style="width: 40px; height: 40px;" title="Balik Gambar / Cermin">
                                <i class="bi bi-symmetry-vertical fs-5"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Document Viewfinder Overlay (Live Camera Guide) -->
                    <div id="cameraViewfinder" class="position-absolute d-flex flex-column align-items-center justify-content-between p-3" style="inset: 16px; border: 2px dashed rgba(56, 189, 248, 0.4); border-radius: 16px; pointer-events: none; z-index: 3;">
                        <div class="scanner-corners">
                            <div class="scanner-corner corner-tl"></div>
                            <div class="scanner-corner corner-tr"></div>
                            <div class="scanner-corner corner-bl"></div>
                            <div class="scanner-corner corner-br"></div>
                        </div>

                        <span class="badge bg-dark bg-opacity-75 text-white px-3.5 py-1.5 rounded-pill shadow-sm mt-5" style="font-size: 11.5px; font-weight: 500; backdrop-filter: blur(8px); border: 1px solid rgba(255,255,255,0.15);">
                            <i class="bi bi-bounding-box-circles me-1.5 text-cyan"></i> Posisikan Nota / Faktur di Dalam Kotak
                        </span>
                        
                        <div class="d-flex justify-content-between w-100 text-white opacity-75 mb-1" style="font-size: 11px;">
                            <span><i class="bi bi-aspect-ratio me-1 text-info"></i>Bukti Fisik</span>
                            <span id="camQualityBadge"><i class="bi bi-check2-circle text-success me-1"></i>HD Mode</span>
                        </div>
                    </div>

                    <!-- Live Video Stream -->
                    <video id="cameraVideo" autoplay playsinline muted style="width: 100%; height: 100%; object-fit: contain; transform: scaleX(1); transition: transform 0.2s ease; display: none;"></video>

                    <!-- Captured Preview Canvas -->
                    <canvas id="cameraCanvas" style="display: none; width: 100%; height: 100%; object-fit: contain;"></canvas>
                </div>

                <!-- Desktop Device Select (Fallback Bar) -->
                <div id="cameraSelectContainer" class="p-2.5 bg-dark border-top border-white border-opacity-10 d-none">
                    <div class="d-flex align-items-center justify-content-between gap-2 px-2">
                        <label for="cameraSelect" class="form-label small fw-medium mb-0 flex-shrink-0" style="font-size: 12px; color: #cbd5e1;">
                            <i class="bi bi-camera-video me-1.5 text-info"></i>Pilih Perangkat Kamera:
                        </label>
                        <select id="cameraSelect" class="form-select form-select-sm bg-slate-900 text-white border-white border-opacity-20" style="font-size: 12px; border-radius: 8px; max-width: 250px; background-color: #0f172a;"></select>
                    </div>
                </div>
            </div>

            <!-- Footer: Mobile Shutter & Action Bar -->
            <div class="modal-footer px-4 py-3.5 border-top border-white border-opacity-10 align-items-center" style="background: #0b0f19; position: relative; z-index: 10;">
                
                <!-- Live Camera Mobile Controls Bar -->
                <div class="w-100 d-flex align-items-center justify-content-between" id="liveCameraButtons">
                    <!-- Left: Switch Camera (Putar Kamera Depan/Belakang) -->
                    <button type="button" class="btn btn-camera-action rounded-pill px-3.5 py-2 fw-semibold d-inline-flex align-items-center gap-2" onclick="switchCamera()" title="Putar Kamera Depan / Belakang" style="font-size: 12.5px;">
                        <i class="bi bi-camera-revert text-info fs-6"></i>
                        <span class="d-none d-sm-inline">Putar Kamera</span>
                    </button>

                    <!-- Center: iPhone / Android Native Shutter Button -->
                    <div class="shutter-wrapper" id="btnCapture" onclick="capturePhoto()" title="Ambil Foto Dokumen">
                        <div class="shutter-ring">
                            <div class="shutter-core">
                                <i class="bi bi-camera-fill text-white fs-5"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Gallery Upload -->
                    <button type="button" class="btn btn-camera-action rounded-pill px-3.5 py-2 fw-semibold d-inline-flex align-items-center gap-2" onclick="triggerFileInputFromModal()" title="Pilih foto dari galeri HP" style="font-size: 12.5px;">
                        <i class="bi bi-folder2-open text-info fs-6"></i>
                        <span class="d-none d-sm-inline">Galeri HP</span>
                    </button>
                </div>

                <!-- Preview Photo Actions Bar (After Capture) -->
                <div class="w-100 align-items-center justify-content-between d-none" id="previewPhotoButtons">
                    <button type="button" class="btn btn-camera-action rounded-pill px-3.5 py-2 fw-medium d-inline-flex align-items-center gap-1.5" id="btnRetake" onclick="retakePhoto()" style="font-size: 12.5px;">
                        <i class="bi bi-arrow-clockwise fs-6 text-warning"></i> Foto Ulang
                    </button>
                    <button type="button" class="btn btn-camera-action rounded-pill px-3.5 py-2 fw-medium d-inline-flex align-items-center gap-1.5" id="btnRotate" onclick="rotatePhoto()" title="Putar 90 Derajat" style="font-size: 12.5px;">
                        <i class="bi bi-arrow-repeat fs-6 text-info"></i> Putar 90°
                    </button>
                    <button type="button" class="btn btn-success px-4 py-2 rounded-pill fw-bold text-white shadow-md d-inline-flex align-items-center gap-1.5" id="btnUsePhoto" onclick="usePhoto()" style="font-size: 13px; background: #059669; border: none;">
                        <i class="bi bi-check-circle-fill fs-6"></i> Gunakan Foto
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
let rawCapturedCanvas = null;
let currentFacingMode = 'environment'; // 'environment' (belakang) or 'user' (depan)
let availableVideoDevices = [];
let activeDeviceIndex = -1;

function triggerFileInputFromModal() {
    stopCamera();
    const modalEl = document.getElementById('cameraModal');
    if (modalEl) {
        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        if (modal) modal.hide();
    }
    setTimeout(() => {
        const fileInput = document.getElementById(targetFileInputId);
        if (fileInput) fileInput.click();
    }, 200);
}

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

    const topControls = document.getElementById('cameraTopOverlay');
    if (topControls) topControls.style.display = 'flex';
    
    startCamera();
}

async function startCamera(deviceId = null) {
    const video = document.getElementById('cameraVideo');
    const loading = document.getElementById('cameraLoading');
    const errorEl = document.getElementById('cameraError');
    const btnCapture = document.getElementById('btnCapture');
    const camModeBadge = document.getElementById('camModeBadge');
    
    if (currentStream) {
        currentStream.getTracks().forEach(track => track.stop());
        currentStream = null;
    }
    
    loading.classList.remove('d-none');
    errorEl.classList.add('d-none');
    video.style.display = 'none';
    if (btnCapture) btnCapture.style.display = 'none';
    
    let videoConstraint = {};
    if (deviceId) {
        videoConstraint = { deviceId: { exact: deviceId } };
    } else {
        videoConstraint = { facingMode: { ideal: currentFacingMode } };
    }

    const constraints = {
        video: {
            ...videoConstraint,
            width: { ideal: 1920, min: 1280 },
            height: { ideal: 1080, min: 720 }
        }
    };
    
    try {
        currentStream = await navigator.mediaDevices.getUserMedia(constraints);
        video.srcObject = currentStream;
        await video.play();
        
        video.style.display = 'block';
        if (btnCapture) btnCapture.style.display = 'flex';
        loading.classList.add('d-none');
        
        if (!deviceId) {
            isMirrored = (currentFacingMode === 'user');
        }
        updateMirrorStyle();
        
        if (camModeBadge) {
            if (currentFacingMode === 'user' || isMirrored) {
                camModeBadge.innerHTML = '<i class="bi bi-person-bounding-box me-1.5 text-info"></i> Kamera Depan';
            } else {
                camModeBadge.innerHTML = '<i class="bi bi-camera me-1.5 text-info"></i> Kamera Belakang';
            }
        }
        
        await updateCameraDevices();
    } catch (err) {
        console.warn("Retrying camera with fallback constraints...", err);
        try {
            const fallbackConstraints = {
                video: deviceId ? { deviceId: { exact: deviceId } } : { facingMode: currentFacingMode }
            };
            currentStream = await navigator.mediaDevices.getUserMedia(fallbackConstraints);
            video.srcObject = currentStream;
            await video.play();
            video.style.display = 'block';
            if (btnCapture) btnCapture.style.display = 'flex';
            loading.classList.add('d-none');
            updateMirrorStyle();
            await updateCameraDevices();
        } catch (fallbackErr) {
            console.error("Camera access failed:", fallbackErr);
            loading.classList.add('d-none');
            errorEl.classList.remove('d-none');
            const errorMsg = document.getElementById('cameraErrorMessage');
            if (fallbackErr.name === 'NotAllowedError') {
                errorMsg.textContent = "Akses kamera ditolak. Silakan berikan izin akses kamera di browser Anda.";
            } else if (fallbackErr.name === 'NotFoundError' || fallbackErr.name === 'DevicesNotFoundError') {
                errorMsg.textContent = "Kamera tidak terdeteksi pada perangkat Anda.";
            } else {
                errorMsg.textContent = "Gagal memuat kamera: " + (fallbackErr.message || 'Kendala izin browser.');
            }
        }
    }
}

async function switchCamera() {
    const flipBtns = document.querySelectorAll('#btnFlipCamera i, button[onclick="switchCamera()"] i');
    flipBtns.forEach(icon => icon.classList.add('spin-icon'));
    
    if (availableVideoDevices.length > 1) {
        if (activeDeviceIndex === -1) {
            activeDeviceIndex = 0;
        }
        activeDeviceIndex = (activeDeviceIndex + 1) % availableVideoDevices.length;
        const targetDevice = availableVideoDevices[activeDeviceIndex];
        activeCameraId = targetDevice.deviceId;
        
        const label = (targetDevice.label || '').toLowerCase();
        if (label.includes('front') || label.includes('depan') || label.includes('user') || label.includes('selfie')) {
            currentFacingMode = 'user';
            isMirrored = true;
        } else {
            currentFacingMode = 'environment';
            isMirrored = false;
        }
        await startCamera(activeCameraId);
    } else {
        currentFacingMode = (currentFacingMode === 'environment') ? 'user' : 'environment';
        isMirrored = (currentFacingMode === 'user');
        activeCameraId = null;
        await startCamera();
    }

    setTimeout(() => {
        flipBtns.forEach(icon => icon.classList.remove('spin-icon'));
    }, 400);
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
        if (isMirrored) {
            mirrorBtn.style.background = 'rgba(37, 99, 235, 0.6)';
            mirrorBtn.style.borderColor = '#60a5fa';
        } else {
            mirrorBtn.style.background = 'rgba(255, 255, 255, 0.07)';
            mirrorBtn.style.borderColor = 'rgba(255, 255, 255, 0.15)';
        }
    }
}

async function updateCameraDevices() {
    try {
        if (!navigator.mediaDevices || !navigator.mediaDevices.enumerateDevices) return;
        
        const devices = await navigator.mediaDevices.enumerateDevices();
        availableVideoDevices = devices.filter(device => device.kind === 'videoinput');
        
        const select = document.getElementById('cameraSelect');
        const container = document.getElementById('cameraSelectContainer');
        
        if (availableVideoDevices.length > 1) {
            if (container) container.classList.remove('d-none');
            if (select) {
                select.innerHTML = '';
                availableVideoDevices.forEach((device, index) => {
                    const option = document.createElement('option');
                    option.value = device.deviceId;
                    option.text = device.label || `Kamera ${index + 1}`;
                    
                    if (currentStream) {
                        const activeTrack = currentStream.getVideoTracks()[0];
                        if (activeTrack && (activeTrack.label === device.label || activeTrack.getSettings().deviceId === device.deviceId)) {
                            option.selected = true;
                            activeCameraId = device.deviceId;
                            activeDeviceIndex = index;
                        }
                    }
                    select.appendChild(option);
                });
                
                select.onchange = function() {
                    activeCameraId = this.value;
                    activeDeviceIndex = availableVideoDevices.findIndex(d => d.deviceId === this.value);
                    startCamera(this.value);
                };
            }
        } else {
            if (container) container.classList.add('d-none');
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
    
    if (flash) {
        flash.style.opacity = '0.85';
        setTimeout(() => { flash.style.opacity = '0'; }, 150);
    }

    const w = video.videoWidth || 1280;
    const h = video.videoHeight || 720;

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

    video.style.display = 'none';
    document.getElementById('cameraCanvas').style.display = 'block';
    document.getElementById('cameraViewfinder').style.display = 'none';
    
    const topControls = document.getElementById('cameraTopOverlay');
    if (topControls) topControls.style.display = 'none';

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
    
    const topControls = document.getElementById('cameraTopOverlay');
    if (topControls) topControls.style.display = 'flex';
    
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
        
        const fileInput = document.getElementById(targetFileInputId);
        if (fileInput) {
            try {
                const container = new DataTransfer();
                container.items.add(file);
                fileInput.files = container.files;
            } catch (err) {
                console.warn("DataTransfer tidak didukung:", err);
            }

            const previewContainer = document.getElementById('foto_preview_container');
            const previewImg = document.getElementById('foto_preview_img');
            const filenameText = document.getElementById('foto_filename_text');

            if (filenameText) filenameText.textContent = filename;
            if (previewImg) previewImg.src = canvas.toDataURL('image/jpeg', 0.92);
            if (previewContainer) previewContainer.classList.remove('d-none');

            fileInput.dispatchEvent(new Event('change', { bubbles: true }));
        }
        
        stopCamera();
        
        const modalEl = document.getElementById('cameraModal');
        if (modalEl) {
            const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            if (modal) modal.hide();
        }

        setTimeout(() => {
            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
            document.body.classList.remove('modal-open');
            document.body.style = '';
        }, 250);

    }, 'image/jpeg', 0.92);
}

document.addEventListener('DOMContentLoaded', () => {
    const modalEl = document.getElementById('cameraModal');
    if (modalEl) {
        modalEl.addEventListener('hidden.bs.modal', stopCamera);
    }
});
</script>
