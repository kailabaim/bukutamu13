document.addEventListener('DOMContentLoaded', function () {
    console.log('Script loaded successfully');

    // === Set tanggal & waktu default ===
    const dateInput = document.querySelector('#tanggal, #tanggal_kunjungan');
    const timeInput = document.querySelector('#waktu, #waktu_kunjungan');

    if (dateInput && timeInput) {
        const now = new Date();
        dateInput.value = now.toISOString().split('T')[0];
        timeInput.value = now.getHours().toString().padStart(2, '0') + ':' + now.getMinutes().toString().padStart(2, '0');
    }

    // === KAMERA ===
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
        console.log('startCamera function called');

        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            const errorMsg = 'Browser Anda tidak mendukung akses kamera. Gunakan browser modern seperti Chrome, Firefox, atau Edge.';
            console.error(errorMsg);
            if (cameraWarning) {
                cameraWarning.innerHTML = `<i class="fas fa-exclamation-triangle"></i> ${errorMsg}`;
                cameraWarning.style.display = 'block';
            } else {
                alert(errorMsg);
            }
            return;
        }

        try {
            if (cameraWarning) {
                cameraWarning.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mengaktifkan kamera...';
                cameraWarning.style.display = 'block';
            }

            console.log('Requesting camera access...');

            cameraStream = await navigator.mediaDevices.getUserMedia({
                video: { 
                    width: { ideal: 640 }, 
                    height: { ideal: 480 }, 
                    facingMode: 'user' 
                }
            });

            console.log('Camera access granted');

            if (cameraWarning) cameraWarning.style.display = 'none';
            
            if (camera) {
                camera.srcObject = cameraStream;
                camera.style.display = 'block';
                camera.style.transform = 'scaleX(-1)';
                
                camera.onloadedmetadata = () => {
                    console.log('Camera video loaded');
                    camera.play();
                };
            }

            if (startCameraBtn) startCameraBtn.style.display = 'none';
            if (takePhotoBtn) takePhotoBtn.style.display = 'inline-flex';
            if (stopCameraBtn) stopCameraBtn.style.display = 'inline-flex';
            
            if (photoPreview) {
                const placeholder = photoPreview.querySelector('.camera-placeholder');
                if (placeholder) placeholder.style.display = 'none';
            }
            
            cameraStarted = true;
            console.log('Camera started successfully');

        } catch (error) {
            console.error('Error accessing camera:', error);

            let msg = 'Tidak dapat mengakses kamera. ';
            
            if (error.name === 'NotAllowedError' || error.name === 'PermissionDeniedError') {
                msg += 'Anda menolak akses kamera. Silakan izinkan akses kamera di pengaturan browser Anda.';
            } else if (error.name === 'NotFoundError' || error.name === 'DevicesNotFoundError') {
                msg += 'Kamera tidak ditemukan. Pastikan perangkat Anda memiliki kamera.';
            } else if (error.name === 'NotReadableError' || error.name === 'TrackStartError') {
                msg += 'Kamera sedang digunakan oleh aplikasi lain. Tutup aplikasi tersebut dan coba lagi.';
            } else if (error.name === 'OverconstrainedError') {
                msg += 'Kamera tidak memenuhi spesifikasi yang diminta.';
            } else if (error.name === 'TypeError') {
                msg += 'Browser tidak mendukung akses kamera atau halaman tidak diakses melalui HTTPS.';
            } else {
                msg += error.message;
            }

            if (cameraWarning) {
                cameraWarning.innerHTML = `<i class="fas fa-exclamation-triangle"></i> ${msg}`;
                cameraWarning.style.display = 'block';
            } else {
                alert(msg);
            }
        }
    }

    function takePhoto() {
        console.log('takePhoto function called');

        if (!cameraStarted || !camera || !canvas || !photoPreview || !fotoDataInput) {
            alert('Kamera belum siap! Pastikan kamera sudah aktif.');
            return;
        }

        try {
            canvas.width = camera.videoWidth;
            canvas.height = camera.videoHeight;

            const ctx = canvas.getContext('2d');
            ctx.setTransform(-1, 0, 0, 1, canvas.width, 0);
            ctx.drawImage(camera, 0, 0, canvas.width, canvas.height);
            ctx.setTransform(1, 0, 0, 1, 0, 0);

            const dataURL = canvas.toDataURL('image/jpeg', 0.8);
            console.log('Photo captured');

            const img = document.createElement('img');
            img.src = dataURL;
            img.style.maxWidth = '100%';
            img.style.borderRadius = '8px';
            
            photoPreview.innerHTML = '';
            photoPreview.appendChild(img);
            fotoDataInput.value = dataURL;

            stopCamera();
            if (retakePhotoBtn) retakePhotoBtn.style.display = 'inline-flex';

        } catch (error) {
            console.error('Error taking photo:', error);
            alert('Gagal mengambil foto: ' + error.message);
        }
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
    if (startCameraBtn) {
        startCameraBtn.addEventListener('click', function(e) {
            e.preventDefault();
            startCamera();
        });
    }

    if (takePhotoBtn) {
        takePhotoBtn.addEventListener('click', function(e) {
            e.preventDefault();
            takePhoto();
        });
    }

    if (retakePhotoBtn) {
        retakePhotoBtn.addEventListener('click', function(e) {
            e.preventDefault();
            retakePhoto();
        });
    }

    if (stopCameraBtn) {
        stopCameraBtn.addEventListener('click', function(e) {
            e.preventDefault();
            stopCamera();
        });
    }

    // === SUBMIT FORM DENGAN AJAX - FIXED VERSION ===
    const formIds = ['umumForm', 'ortuForm', 'instansiForm'];
    formIds.forEach(formId => {
        const form = document.getElementById(formId);
        if (!form) return;

        form.addEventListener('submit', function (e) {
            e.preventDefault();

            console.log('Form submit triggered:', formId);

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

            // Ambil CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                              document.querySelector('input[name="_token"]')?.value;

            console.log('CSRF Token:', csrfToken ? 'Found' : 'NOT FOUND');
            console.log('Form Action:', form.action);

            // Kirim via AJAX dengan error handling yang lebih baik
            const formData = new FormData(form);
            
            // Debug: Log form data
            console.log('Form Data:');
            for (let pair of formData.entries()) {
                if (pair[0] === 'foto_data') {
                    console.log(pair[0] + ': [Base64 Image Data]');
                } else {
                    console.log(pair[0] + ': ' + pair[1]);
                }
            }

            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken || '',
                    'Accept': 'application/json',
                },
                body: formData,
                credentials: 'same-origin'
            })
            .then(response => {
                console.log('Response status:', response.status);
                console.log('Response OK:', response.ok);

                // Cek apakah response adalah JSON
                const contentType = response.headers.get('content-type');
                console.log('Content-Type:', contentType);

                if (!response.ok) {
                    // Jika error, coba baca response sebagai text dulu
                    return response.text().then(text => {
                        console.error('Error response text:', text);
                        
                        // Coba parse sebagai JSON
                        try {
                            const json = JSON.parse(text);
                            throw json;
                        } catch (e) {
                            // Jika bukan JSON, throw error dengan info status
                            throw new Error(`Server error: ${response.status} ${response.statusText}`);
                        }
                    });
                }

                // Response OK, parse JSON
                return response.json();
            })
            .then(data => {
                console.log('Success response:', data);
                
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;

                if (data.success) {
                    alert('✅ ' + (data.message || 'Data berhasil dikirim!'));
                    form.reset();
                    // Reset kamera
                    if (typeof retakePhoto === 'function') {
                        retakePhoto();
                    }
                    
                    // Redirect jika ada
                    if (data.redirect) {
                        setTimeout(() => {
                            window.location.href = data.redirect;
                        }, 1000);
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
                
                let msg = 'Terjadi kesalahan saat mengirim data.\n\n';
                
                if (error.message) {
                    msg += 'Detail: ' + error.message;
                } else if (error.errors) {
                    msg += 'Errors: ' + JSON.stringify(error.errors);
                } else {
                    msg += 'Silakan coba lagi atau hubungi administrator.';
                }
                
                // Tambahan info untuk debugging
                msg += '\n\nTips:\n';
                msg += '1. Pastikan koneksi internet stabil\n';
                msg += '2. Coba refresh halaman (F5)\n';
                msg += '3. Buka browser console (F12) untuk detail error';
                
                alert('❌ ' + msg);
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