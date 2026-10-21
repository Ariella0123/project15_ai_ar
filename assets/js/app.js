document.addEventListener('DOMContentLoaded', async () => {
    const video = document.getElementById('camera-preview');
    const viewport = document.getElementById('viewport');
    const scanBtn = document.getElementById('scan-btn');
    const torchBtn = document.getElementById('torch-btn');
    const switchBtn = document.getElementById('switch-btn');
    const arCard = document.getElementById('ar-card');
    const focusReticle = document.getElementById('focus-reticle');

    const camera = new CameraManager(video);
    const voice = new VoiceManager();
    const ar = new AROverlay(viewport, arCard);

    let torchState = false;

    await camera.init(localStorage.getItem('cameraResolution') || '1080p');

    // 触控对焦动画
    viewport.addEventListener('click', (e) => {
        if (e.target.classList.contains('ar-box')) return;
        
        const rect = viewport.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;

        focusReticle.style.left = `${x}px`;
        focusReticle.style.top = `${y}px`;
        focusReticle.classList.add('active');

        HapticManager.focus();
        setTimeout(() => focusReticle.classList.remove('active'), 1500);
    });

    // 手电筒
    torchBtn.addEventListener('click', async () => {
        torchState = !torchState;
        const success = await camera.toggleTorch(torchState);
        if (!success) torchState = !torchState;
    });

    // 切换摄像头
    switchBtn.addEventListener('click', async () => {
        await camera.switchCamera();
    });

    // 扫描识别
    scanBtn.addEventListener('click', async () => {
        scanBtn.disabled = true;
        scanBtn.innerText = 'SCANNING...';
        ar.clear();
        voice.stop();

        const imageBase64 = camera.captureFrame();

        try {
            const res = await fetch('api/recognize.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ image: imageBase64 })
            });
            const response = await res.json();

            if (response.ok) {
                HapticManager.success();
                ar.render(response.data.boundingBox, response.data);
                voice.speakResult(response.data);
            } else {
                HapticManager.error();
                alert(response.error || 'Recognition failed');
            }
        } catch (err) {
            HapticManager.error();
            alert('Server network error');
        } finally {
            scanBtn.disabled = false;
            scanBtn.innerText = 'SCAN OBJECT';
        }
    });
});