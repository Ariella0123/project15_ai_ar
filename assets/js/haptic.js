class HapticManager {
    static trigger(pattern) {
        const enabled = localStorage.getItem('hapticEnabled') !== 'false';
        if (enabled && 'vibrate' in navigator) {
            navigator.vibrate(pattern);
        }
    }

    static focus() { this.trigger(30); }
    static success() { this.trigger([30, 50, 30]); }
    static error() { this.trigger([100, 50, 100]); }
}