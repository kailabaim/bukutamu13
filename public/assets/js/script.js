document.addEventListener('DOMContentLoaded', function () {
    // === Set tanggal & waktu default (hanya untuk form yang punya input #tanggal dan #waktu) ===
    const dateInput = document.querySelector('#tanggal, #tanggal_kunjungan');
    const timeInput = document.querySelector('#waktu, #waktu_kunjungan');

    if (dateInput && timeInput) {
        const now = new Date();
        dateInput.value = now.toISOString().split('T')[0];
        timeInput.value = now.getHours().toString().padStart(2, '0') + ':' + now.getMinutes().toString().padStart(2, '0');
    }

    // === KAMERA (asumsi hanya ada satu form per halaman, jadi ID global aman) ===
    let cameraStream = null;
    let cameraStarted = false;

    const camera = document.getElementById('camera');
    const canvas = document.getElementById('canvas');
    const photoPreview = document.getElementById('photo-preview');
    const startCameraBtn = document.getElementById('start-camera');
    const takePhotoBtn = document.getElementById('take-photo');
    const retakePhotoBtn = document.getElementById('retake-photo');
    const stopCameraBtn = document.getElementById('stop-camera');
    const fotoDataInput = document.getElementById('foto_data');
    const cameraWarning = document.getElementById('camera-warning');

    async function startCamera() {
        try {
            cameraWarning.style.display = 'block';
            cameraStream = await navigator.mediaDevices.getUserMedia({
                video: { width: { ideal: 640 }, height: { ideal: 480 }, facingMode: 'user' },
            });

            cameraWarning.style.display = 'none';
            camera.srcObject = cameraStream;
            camera.style.display = 'block';
            camera.style.transform = 'scaleX(-1)';

            if (startCameraBtn) startCameraBtn.style.display = 'none';
            if (takePhotoBtn) takePhotoBtn.style.display = 'inline-flex';
            if (stopCameraBtn) stopCameraBtn.style.display = 'inline-flex';
            if (photoPreview) {
                const placeholder = photoPreview.querySelector('.camera-placeholder');
                if (placeholder) placeholder.style.display = 'none';
            }
            cameraStarted = true;
        } catch (error) {
            console.error('Error accessing camera:', error);
            let msg = 'Tidak dapat mengakses kamera. ';
            if (error.name === 'NotAllowedError') {
                msg += 'Izinkan akses kamera di browser Anda.';
            } else if (error.name === 'NotFoundError') {
                msg += 'Kamera tidak ditemukan.';
            } else {
                msg += error.message;
            }
            if (cameraWarning) {
                cameraWarning.innerHTML = `<i class="fas fa-exclamation-triangle"></i> ${msg}`;
                cameraWarning.style.display = 'block';
            }
        }
    }

    function takePhoto() {
        if (!cameraStarted || !camera || !canvas || !photoPreview || !fotoDataInput) {
            alert('Kamera belum siap!');
            return;
        }

        canvas.width = camera.videoWidth;
        canvas.height = camera.videoHeight;
        const ctx = canvas.getContext('2d');
        ctx.setTransform(-1, 0, 0, 1, canvas.width, 0);
        ctx.drawImage(camera, 0, 0, canvas.width, canvas.height);
        ctx.setTransform(1, 0, 0, 1, 0, 0);

        const dataURL = canvas.toDataURL('image/jpeg', 0.8);
        const img = document.createElement('img');
        img.src = dataURL;
        photoPreview.innerHTML = '';
        photoPreview.appendChild(img);
        fotoDataInput.value = dataURL;

        stopCamera();
        if (retakePhotoBtn) retakePhotoBtn.style.display = 'inline-flex';
    }

    function retakePhoto() {
        if (!photoPreview || !fotoDataInput || !startCameraBtn || !retakePhotoBtn) return;

        photoPreview.innerHTML =
            '<div class="camera-placeholder"><i class="fas fa-user-circle"></i><p>Klik tombol kamera untuk mengambil foto</p></div>';
        fotoDataInput.value = '';
        retakePhotoBtn.style.display = 'none';
        startCameraBtn.style.display = 'inline-flex';
    }

    function stopCamera() {
        if (cameraStream) {
            cameraStream.getTracks().forEach(track => track.stop());
            cameraStream = null;
        }
        if (camera) {
            camera.style.display = 'none';
            camera.srcObject = null;
            camera.style.transform = 'none';
        }

        if (startCameraBtn) startCameraBtn.style.display = 'inline-flex';
        if (takePhotoBtn) takePhotoBtn.style.display = 'none';
        if (stopCameraBtn) stopCameraBtn.style.display = 'none';
        cameraStarted = false;
    }

    // Pasang event listener kamera
    if (startCameraBtn) startCameraBtn.addEventListener('click', startCamera);
    if (takePhotoBtn) takePhotoBtn.addEventListener('click', takePhoto);
    if (retakePhotoBtn) retakePhotoBtn.addEventListener('click', retakePhoto);
    if (stopCameraBtn) stopCameraBtn.addEventListener('click', stopCamera);

    // === SUBMIT FORM DENGAN AJAX (untuk semua form yang relevan) ===
    const formIds = ['umumForm', 'ortuForm', 'instansiForm'];
    formIds.forEach(formId => {
        const form = document.getElementById(formId);
        if (!form) return;

        form.addEventListener('submit', function (e) {
            e.preventDefault();

            // Reset error styling
            form.querySelectorAll('.input-error').forEach(el => el.classList.remove('input-error'));

            // Validasi field required
            let isValid = true;
            const requiredFields = form.querySelectorAll('[required]');
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.classList.add('input-error');
                    isValid = false;
                }
            });

            if (!isValid) {
                alert('Mohon lengkapi semua field yang wajib diisi!');
                return;
            }

            // Loading button
            const submitBtn = form.querySelector('button[type="submit"]');
            if (!submitBtn) return;

            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mengirim Data...';
            submitBtn.disabled = true;

            // Kirim via AJAX
            const formData = new FormData(form);
            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                        document.querySelector('input[name="_token"]')?.value
                },
                body: formData,
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => { throw err; });
                }
                return response.json();
            })
            .then(data => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;

                if (data.success) {
                    alert('✅ ' + data.message);
                    form.reset();
                    // Reset kamera
                    if (typeof retakePhoto === 'function') {
                        retakePhoto();
                    }
                } else {
                    let errorMsg = data.message || 'Terjadi kesalahan';
                    if (data.errors) {
                        if (Array.isArray(data.errors)) {
                            errorMsg = data.errors.join('\n');
                        } else if (typeof data.errors === 'object') {
                            errorMsg = Object.values(data.errors).flat().join('\n');
                        }
                    }
                    alert('❌ Gagal mengirim:\n' + errorMsg);
                }
            })
            .catch(error => {
                console.error('AJAX Error:', error);
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
                let msg = 'Terjadi kesalahan jaringan atau server.';
                if (error.message) msg = error.message;
                alert('❌ Gagal mengirim: ' + msg);
            });
        });
    });

    // Auto-resize textarea
    document.querySelectorAll('textarea').forEach(ta => {
        ta.addEventListener('input', () => {
            ta.style.height = 'auto';
            ta.style.height = (ta.scrollHeight) + 'px';
        });
    });

    // Bersihkan kamera saat keluar
    window.addEventListener('beforeunload', () => {
        if (cameraStream) {
            cameraStream.getTracks().forEach(track => track.stop());
        }
    });
});