// js/music.js — Web Audio music studio (no mp3 needed)
'use strict';

(function () {
    const NOTES = [
        { note: 'C', freq: 261.63 },
        { note: 'D', freq: 293.66 },
        { note: 'E', freq: 329.63 },
        { note: 'F', freq: 349.23 },
        { note: 'G', freq: 392.0 },
        { note: 'A', freq: 440.0 },
        { note: 'B', freq: 493.88 },
        { note: 'C2', freq: 523.25 }
    ];

    let audioCtx = null;
    let volume = 0.7;
    let melodyTimer = null;
    let melodyIndex = 0;
    let melodyNotes = [];

    /* ---------- Audio context (must be created/resumed in a user gesture) ---------- */
    function getAudio() {
        try {
            const AC = window.AudioContext || window.webkitAudioContext;
            if (!AC) return null;
            if (!audioCtx) audioCtx = new AC();
            if (audioCtx.state === 'suspended') {
                audioCtx.resume().catch(function () {});
            }
            return audioCtx;
        } catch (e) {
            return null;
        }
    }

    function unlockAudio() {
        getAudio();
    }

    // Browsers block audio until a real user gesture happens. Unlock the context
    // on the very first interaction anywhere on the page.
    ['pointerdown', 'touchstart', 'keydown', 'click'].forEach(function (type) {
        document.addEventListener(type, unlockAudio);
    });

    /* ---------- Instruments ---------- */
    function playTone(freq, dur, type, vol) {
        const ctx = getAudio();
        if (!ctx || volume <= 0) return;
        const o = ctx.createOscillator();
        const g = ctx.createGain();
        o.type = type || 'triangle';
        o.frequency.setValueAtTime(freq, ctx.currentTime);
        const v = (vol === undefined ? 0.35 : vol) * volume;
        g.gain.setValueAtTime(v, ctx.currentTime);
        g.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + dur);
        o.connect(g).connect(ctx.destination);
        o.start();
        o.stop(ctx.currentTime + dur + 0.05);
    }

    function playKick() {
        const ctx = getAudio();
        if (!ctx || volume <= 0) return;
        const o = ctx.createOscillator();
        const g = ctx.createGain();
        o.type = 'sine';
        o.frequency.setValueAtTime(150, ctx.currentTime);
        o.frequency.exponentialRampToValueAtTime(40, ctx.currentTime + 0.12);
        g.gain.setValueAtTime(0.5 * volume, ctx.currentTime);
        g.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.14);
        o.connect(g).connect(ctx.destination);
        o.start();
        o.stop(ctx.currentTime + 0.16);
    }

    function noiseBurst(dur, filterFreq, type, vol) {
        const ctx = getAudio();
        if (!ctx || volume <= 0) return;
        const bufferSize = Math.max(1, Math.floor(ctx.sampleRate * dur));
        const buffer = ctx.createBuffer(1, bufferSize, ctx.sampleRate);
        const data = buffer.getChannelData(0);
        for (let i = 0; i < bufferSize; i++) data[i] = Math.random() * 2 - 1;
        const src = ctx.createBufferSource();
        src.buffer = buffer;
        const f = ctx.createBiquadFilter();
        f.type = type;
        f.frequency.value = filterFreq;
        const g = ctx.createGain();
        g.gain.setValueAtTime((vol === undefined ? 0.4 : vol) * volume, ctx.currentTime);
        g.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + dur);
        src.connect(f).connect(g).connect(ctx.destination);
        src.start();
    }

    const DRUMS = {
        kick: () => playKick(),
        snare: () => noiseBurst(0.16, 1800, 'highpass', 0.45),
        hat: () => noiseBurst(0.08, 7000, 'highpass', 0.3),
        clap: () => noiseBurst(0.12, 1200, 'bandpass', 0.5)
    };

    /* ---------- Piano keys (sound on press, best latency) ---------- */
    const piano = document.getElementById('piano');
    NOTES.forEach((n) => {
        const key = document.createElement('button');
        key.className = 'key';
        key.textContent = n.note;
        key.setAttribute('aria-label', 'Key ' + n.note);

        let lastPress = 0;
        const press = () => {
            const now = Date.now();
            if (now - lastPress < 120) return;
            lastPress = now;
            playTone(n.freq, 0.5, 'triangle');
            key.classList.add('is-pressed');
            setTimeout(() => key.classList.remove('is-pressed'), 160);
        };

        key.addEventListener('pointerdown', (e) => {
            if (e.button !== undefined && e.button !== 0) return;
            e.preventDefault();
            press();
        });
        key.addEventListener('click', press);
        piano.appendChild(key);
    });

    /* ---------- Drum pads ---------- */
    document.querySelectorAll('[data-drum]').forEach((pad) => {
        pad.addEventListener('click', () => {
            const fn = DRUMS[pad.dataset.drum];
            if (fn) fn();
            pad.classList.add('is-pressed');
            setTimeout(() => pad.classList.remove('is-pressed'), 150);
        });
    });

    /* ---------- Volume ---------- */
    const volumeEl = document.getElementById('volume');
    if (volumeEl) {
        volumeEl.addEventListener('input', () => {
            volume = parseInt(volumeEl.value, 10) / 100;
        });
    }

    /* ---------- Melodies ---------- */
    const HAPPY = [
        261.63, 329.63, 392.0, 523.25, 392.0, 329.63,
        293.66, 349.23, 440.0, 349.23, 293.66,
        261.63, 329.63, 392.0, 523.25, 659.25, 523.25, 392.0
    ];
    const STAR = [
        523.25, 523.25, 523.25, 523.25, 523.25, 523.25,
        659.25, 523.25, 440.0, 392.0, 392.0, 392.0, 392.0,
        440.0, 440.0, 392.0, 523.25, 523.25
    ];

    function playMelody() {
        stopMelody();
        unlockAudio();
        const walk = document.getElementById('noteWalk');
        if (walk) walk.classList.add('is-playing');
        melodyIndex = 0;
        melodyTimer = setInterval(() => {
            if (melodyIndex >= melodyNotes.length) {
                stopMelody();
                return;
            }
            playTone(melodyNotes[melodyIndex], 0.28, 'triangle', 0.32);
            melodyIndex++;
        }, 320);
    }

    function stopMelody() {
        if (melodyTimer) {
            clearInterval(melodyTimer);
            melodyTimer = null;
        }
        const walk = document.getElementById('noteWalk');
        if (walk) walk.classList.remove('is-playing');
    }

    const tuneHappy = document.getElementById('tuneHappy');
    if (tuneHappy) {
        tuneHappy.addEventListener('click', () => {
            unlockAudio();
            melodyNotes = HAPPY;
            playMelody();
        });
    }

    const tuneStar = document.getElementById('tuneStar');
    if (tuneStar) {
        tuneStar.addEventListener('click', () => {
            unlockAudio();
            melodyNotes = STAR;
            playMelody();
        });
    }

    const stopBtn = document.getElementById('stopBtn');
    if (stopBtn) stopBtn.addEventListener('click', stopMelody);
})();
