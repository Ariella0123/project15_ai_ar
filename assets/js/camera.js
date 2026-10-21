class CameraManager {
    constructor(videoElement) {
        this.video = videoElement;
        this.stream = null;
        this.track = null;
        this.devices = [];
        this.currentDeviceIndex = 0;
    }

    async init(resolution = '1080p') {
        await this.loadDevices();
        await this.startStream(resolution);
    }

    async loadDevices() {
        const devices = await navigator.mediaDevices.enumerateDevices();
        this.devices = devices.filter(d => d.kind === 'videoinput');
    }

    async startStream(resolution = '1080p') {
        if (this.stream) this.stopStream();

        const resMap = {
            '720p': { width: 1280, height: 720 },
            '1080p': { width: 1920, height: 1080 },
            'high': { width: 2560, height: 1440 }
        };

        const targetRes = resMap[resolution] || resMap['1080p'];
        const constraints = {
            video: {
                width: { ideal: targetRes.width },
                height: { ideal: targetRes.height },
                facingMode: { ideal: 'environment' },
                focusMode: { ideal: 'continuous' }
            },
            audio: false
        };

        if (this.devices.length > 0 && this.devices[this.currentDeviceIndex]) {
            constraints.video.deviceId = { exact: this.devices[this.currentDeviceIndex].deviceId };
        }

        try {
            this.stream = await navigator.mediaDevices.getUserMedia(constraints);
            this.video.srcObject = this.stream;
            this.track = this.stream.getVideoTracks()[0];
        } catch (err) {
            console.error('Camera stream access failed:', err);
        }
    }

    stopStream() {
        if (this.stream) {
            this.stream.getTracks().forEach(t => t.stop());
            this.stream = null;
            this.track = null;
        }
    }

    async switchCamera() {
        if (this.devices.length < 2) return;
        this.currentDeviceIndex = (this.currentDeviceIndex + 1) % this.devices.length;
        await this.startStream();
    }

    async toggleTorch(state) {
        if (!this.track || !('getCapabilities' in this.track)) return false;
        const capabilities = this.track.getCapabilities();
        if (!capabilities.torch) return false;

        try {
            await this.track.applyConstraints({ advanced: [{ torch: state }] });
            return true;
        } catch (err) {
            return false;
        }
    }

    captureFrame(maxDim = 1280, quality = 0.7) {
        const canvas = document.createElement('canvas');
        let width = this.video.videoWidth;
        let height = this.video.videoHeight;

        if (width > maxDim || height > maxDim) {
            if (width > height) {
                height = Math.round((height * maxDim) / width);
                width = maxDim;
            } else {
                width = Math.round((width * maxDim) / height);
                height = maxDim;
            }
        }

        canvas.width = width;
        canvas.height = height;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(this.video, 0, 0, width, height);

        return canvas.toDataURL('image/jpeg', quality);
    }
}