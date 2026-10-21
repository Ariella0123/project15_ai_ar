class VoiceManager {
    constructor() {
        this.synth = 'speechSynthesis' in window ? window.speechSynthesis : null;
        this.voices = [];
        this.initVoices();
    }

    initVoices() {
        if (!this.synth) return;
        const load = () => { this.voices = this.synth.getVoices(); };
        load();
        if (this.synth.onvoiceschanged !== undefined) {
            this.synth.onvoiceschanged = load;
        }
    }

    speakResult(data) {
        if (!this.synth) return;

        const autoSpeak = localStorage.getItem('autoSpeak') !== 'false';
        if (!autoSpeak) return;

        const text = `This is a ${data.manufacturer || ''} ${data.productName}. ${data.description || ''}`;
        this.speak(text);
    }

    speak(text) {
        if (!this.synth) return;
        this.stop();

        const utterance = new SpeechSynthesisUtterance(text);
        const volume = parseFloat(localStorage.getItem('voiceVolume') || '1.0');
        const selectedVoiceName = localStorage.getItem('selectedVoice');

        utterance.volume = volume;

        if (selectedVoiceName) {
            const voice = this.voices.find(v => v.name === selectedVoiceName);
            if (voice) utterance.voice = voice;
        }

        this.synth.speak(utterance);
    }

    stop() {
        if (this.synth && this.synth.speaking) {
            this.synth.cancel();
        }
    }
}