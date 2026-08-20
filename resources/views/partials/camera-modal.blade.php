<!-- Camera Modal -->
<div class="modal fade" id="cameraModal" tabindex="-1" aria-labelledby="cameraModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow" style="border-radius: var(--dt-radius); overflow: hidden;">
            <div class="modal-header py-3" style="background-color: var(--dt-navy); color: var(--dt-white);">
                <h5 class="modal-title fw-600 fs-5 d-flex align-items-center gap-2" id="cameraModalLabel">
                    <i class="bi bi-camera-fill"></i> Ambil Foto Struk / Nota / Surat Jalan
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" onclick="stopCamera()"></button>
            </div>
            <div class="modal-body p-4" style="background-color: var(--dt-bg);">
                <!-- Video stream container -->
                <div class="position-relative bg-black rounded shadow-sm overflow-hidden mb-3" style="aspect-ratio: 4/3; max-height: 450px; border: 1.5px solid var(--dt-border);">
                    <!-- Loading state -->
                    <div id="cameraLoading" class="position-absolute top-50 start-50 translate-middle text-center text-white">
                        <div class="spinner-border text-light mb-2" role="status"></div>
                        <div class="small opacity-75">Mengaktifkan Kamera...</div>
                    </div>
                    <!-- Error State (Hidden by default) -->
                    <div id="cameraError" class="position-absolute top-50 start-50 translate-middle text-center text-white d-none w-75">
                        <i class="bi bi-exclamation-triangle-fill text-warning fs-1 mb-2"></i>
                        <div class="fw-600">Izin Kamera Diperlukan</div>
                        <div class="small opacity-75 mt-1" id="cameraErrorMessage">Aplikasi memerlukan izin untuk menggunakan kamera Anda. Silakan periksa pengaturan browser Anda.</div>
                    </div>
                    <!-- Live video -->
                    <video id="cameraVideo" autoplay playsinline style="width: 100%; height: 100%; object-fit: cover; display: none;"></video>
                    <!-- Captured image canvas -->
                    <canvas id="cameraCanvas" style="display: none; width: 100%; height: 100%; object-fit: cover;"></canvas>
                </div>

                <!-- Camera selection (only shown if multiple cameras are available) -->
                <div id="cameraSelectContainer" class="mb-2 d-none">
                    <label for="cameraSelect" class="form-label small fw-700 text-navy mb-1" style="color: var(--dt-navy);">
                        <i class="bi bi-arrow-left-right me-1"></i> Pilih Kamera / Sumber Video:
                    </label>
                    <select id="cameraSelect" class="form-select form-select-sm" style="font-size: 13px; border-color: var(--dt-border); border-radius: 6px;"></select>
                </div>
            </div>
            <div class="modal-footer bg-white border-0 py-3 d-flex justify-content-between">
                <div>
                    <button type="button" class="dt-btn dt-btn-outline" data-bs-dismiss="modal" onclick="stopCamera()">Batal</button>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="dt-btn dt-btn-gold" id="btnCapture" onclick="capturePhoto()" style="display: none;">
                        <i class="bi bi-camera me-1"></i> Ambil Foto
                    </button>
                    <button type="button" class="dt-btn dt-btn-outline" id="btnRetake" style="display: none;" onclick="retakePhoto()">
                        <i class="bi bi-arrow-clockwise me-1"></i> Foto Ulang
                    </button>
                    <button type="button" class="dt-btn dt-btn-primary" id="btnUsePhoto" style="display: none;" onclick="usePhoto()">
                        <i class="bi bi-check-lg me-1"></i> Gunakan Foto
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

function openCameraModal(inputId) {
    targetFileInputId = inputId || 'foto_lampiran_input';

    const modalEl = document.getElementById('cameraModal');
    const myModal = new bootstrap.Modal(modalEl);
    myModal.show();
    
    // Reset modal UI states
    document.getElementById('cameraVideo').style.display = 'none';
    document.getElementById('cameraCanvas').style.display = 'none';
    document.getElementById('cameraLoading').classList.remove('d-none');
    document.getElementById('cameraError').classList.add('d-none');
    document.getElementById('btnCapture').style.display = 'none';
    document.getElementById('btnRetake').style.display = 'none';
    document.getElementById('btnUsePhoto').style.display = 'none';
    
    startCamera();
}

async function startCamera(deviceId = null) {
    const video = document.getElementById('cameraVideo');
    const loading = document.getElementById('cameraLoading');
    const errorEl = document.getElementById('cameraError');
    const btnCapture = document.getElementById('btnCapture');
    
    if (currentStream) {
        currentStream.getTracks().forEach(track => track.stop());
    }
    
    loading.classList.remove('d-none');
    errorEl.classList.add('d-none');
    video.style.display = 'none';
    btnCapture.style.display = 'none';
    
    const constraints = {
        video: deviceId ? { deviceId: { exact: deviceId } } : { facingMode: 'environment' }
    };
    
    try {
        currentStream = await navigator.mediaDevices.getUserMedia(constraints);
        video.srcObject = currentStream;
        video.style.display = 'block';
        btnCapture.style.display = 'inline-block';
        loading.classList.add('d-none');
        
        // Once successfully started, let's load all camera options
        await updateCameraDevices();
    } catch (err) {
        console.error("Gagal mengakses kamera:", err);
        loading.classList.add('d-none');
        
        // If exact device failed (like environment fallback bug on some browsers), fallback to default facingMode
        if (deviceId) {
            startCamera(); 
        } else {
            errorEl.classList.remove('d-none');
            const errorMsg = document.getElementById('cameraErrorMessage');
            if (err.name === 'NotAllowedError') {
                errorMsg.textContent = "Akses kamera ditolak. Silakan berikan izin akses kamera pada browser Anda lalu coba kembali.";
            } else if (err.name === 'NotFoundError' || err.name === 'DevicesNotFoundError') {
                errorMsg.textContent = "Kamera tidak ditemukan pada perangkat Anda.";
            } else {
                errorMsg.textContent = "Gagal memuat kamera: " + err.message;
            }
        }
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
            
            // Handle change camera source
            select.onchange = function() {
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
    const canvas = document.getElementById('cameraCanvas');
    const context = canvas.getContext('2d');
    
    // Match dimensions to video natural stream dimensions
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    
    // Draw the current video frame to canvas
    context.drawImage(video, 0, 0, canvas.width, canvas.height);
    
    // Show preview canvas, hide live video
    video.style.display = 'none';
    canvas.style.display = 'block';
    
    // Swap buttons
    document.getElementById('btnCapture').style.display = 'none';
    document.getElementById('btnRetake').style.display = 'inline-block';
    document.getElementById('btnUsePhoto').style.display = 'inline-block';
}

function retakePhoto() {
    const video = document.getElementById('cameraVideo');
    const canvas = document.getElementById('cameraCanvas');
    
    video.style.display = 'block';
    canvas.style.display = 'none';
    
    document.getElementById('btnCapture').style.display = 'inline-block';
    document.getElementById('btnRetake').style.display = 'none';
    document.getElementById('btnUsePhoto').style.display = 'none';
}

function usePhoto() {
    const canvas = document.getElementById('cameraCanvas');
    canvas.toBlob((blob) => {
        if (!blob) {
            alert("Gagal memproses gambar, silakan coba foto kembali.");
            return;
        }
        
        const timestamp = new Date().getTime();
        const file = new File([blob], `struk_kamera_${timestamp}.jpg`, { type: "image/jpeg" });
        
        // Put the file into the file input field
        const fileInput = document.getElementById(targetFileInputId);
        if (fileInput) {
            const container = new DataTransfer();
            container.items.add(file);
            fileInput.files = container.files;
            
            // Trigger change event manually so the form preview updates
            const event = new Event('change', { bubbles: true });
            fileInput.dispatchEvent(event);
        }
        
        stopCamera();
        
        // Hide modal
        const modalEl = document.getElementById('cameraModal');
        const modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) {
            modal.hide();
        }
    }, 'image/jpeg', 0.9);
}

// Ensure camera stops when clicking outside of modal to dismiss it or closing it
document.addEventListener('DOMContentLoaded', () => {
    const modalEl = document.getElementById('cameraModal');
    if (modalEl) {
        modalEl.addEventListener('hidden.bs.modal', stopCamera);
    }
});
</script>
