<?php
require_once __DIR__ . '/includes/AppConfig.php';
$config = AppConfig::get();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - AI + AR System</title>
    <link rel="stylesheet" href="assets/css/app.css">
    <style>
        .settings-container { padding: 20px; overflow-y: auto; height: 100vh; }
        .section { background: rgba(255,255,255,0.05); padding: 15px; border-radius: 12px; margin-bottom: 20px; }
        label { display: block; margin: 10px 0 5px; }
        select, input[type="range"] { width: 100%; padding: 8px; border-radius: 6px; background: #1a2035; color: #fff; border: 1px solid #333; }
        .btn-test { margin-top: 10px; padding: 8px 16px; background: #00e5ff; border: none; border-radius: 6px; cursor: pointer; }
    </style>
</head>
<body>
    <div class="settings-container">
        <a href="index.php" style="color: #00e5ff;"><i class="fas fa-arrow-left"></i> Back to Scanner</a>
        <h1 style="margin: 15px 0;">Settings</h1>

        <div class="section">
            <h3>Camera Settings</h3>
            <label>Resolution</label>
            <select id="setting-resolution">
                <option value="720p">720p</option>
                <option value="1080p" selected>1080p (Full HD)</option>
                <option value="high">1440p (High Res)</option>
            </select>
        </div>

        <div class="section">
            <h3>Voice Settings</h3>
            <label>
                <input type="checkbox" id="setting-autospeak" checked> Auto Speak
            </label>
            <label>Volume</label>
            <input type="range" id="setting-volume" min="0" max="1" step="0.1" value="1">
            <button class="btn-test" id="test-voice-btn">Test Voice</button>
        </div>

        <div class="section">
            <h3>Haptic Settings</h3>
            <label>
                <input type="checkbox" id="setting-haptic" checked> Enable Haptic Feedback
            </label>
        </div>
    </div>

    <script src="assets/js/voice.js"></script>
    <script>
        const voice = new VoiceManager();
        document.getElementById('test-voice-btn').addEventListener('click', () => {
            voice.speak("This is a voice test for the AI Object Recognition System.");
        });

        // 存储持久化设置
        document.getElementById('setting-resolution').addEventListener('change', (e) => {
            localStorage.setItem('cameraResolution', e.target.value);
        });
        document.getElementById('setting-autospeak').addEventListener('change', (e) => {
            localStorage.setItem('autoSpeak', e.target.checked);
        });
        document.getElementById('setting-volume').addEventListener('change', (e) => {
            localStorage.setItem('voiceVolume', e.target.value);
        });
        document.getElementById('setting-haptic').addEventListener('change', (e) => {
            localStorage.setItem('hapticEnabled', e.target.checked);
        });
    </script>
</body>
</html>