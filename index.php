<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI + AR Smart Object Recognition</title>
    <link rel="stylesheet" href="assets/css/app.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="app-container">
        <header class="header">
            <h2>AI+AR Vision</h2>
            <a href="settings.php" class="btn-icon"><i class="fas fa-cog"></i></a>
        </header>

        <main class="viewport" id="viewport">
            <video id="camera-preview" autoplay playsinline muted></video>
            <div class="scan-reticle"></div>
            <div class="focus-indicator" id="focus-reticle"></div>
            <div class="ar-card" id="ar-card"></div>
        </main>

        <footer class="controls">
            <div class="quick-tools">
                <button class="btn-circle" id="torch-btn"><i class="fas fa-bolt"></i></button>
                <button class="btn-circle" id="switch-btn"><i class="fas fa-sync-alt"></i></button>
            </div>
            <button class="btn-scan" id="scan-btn">SCAN OBJECT</button>
        </footer>
    </div>

    <script src="assets/js/haptic.js"></script>
    <script src="assets/js/camera.js"></script>
    <script src="assets/js/voice.js"></script>
    <script src="assets/js/ar-overlay.js"></script>
    <script src="assets/js/app.js"></script>
</body>
</html>