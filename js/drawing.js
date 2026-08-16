// js/drawing.js — Drawing pad
'use strict';

(function () {
    const canvas = document.getElementById('drawCanvas');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');

    const COLORS = ['#ff5e9c', '#ff9f45', '#ffd166', '#06d6a0', '#2ec4b6', '#4d96ff', '#8b5cf6', '#ff6b6b', '#ff8fab', '#3ddc97', '#ffd93d', '#5b3a00'];

    const swatchesWrap = document.getElementById('swatches');
    const brushRange = document.getElementById('brushRange');
    const brushValue = document.getElementById('brushValue');
    const eraserBtn = document.getElementById('eraserBtn');
    const undoBtn = document.getElementById('undoBtn');
    const clearBtn = document.getElementById('clearBtn');
    const saveBtn = document.getElementById('saveBtn');

    let drawing = false;
    let color = '#ff5e9c';
    let size = 8;
    let eraser = false;
    let lastX = 0;
    let lastY = 0;
    const history = [];
    const MAX_HISTORY = 20;

    /* ---------- Setup canvas ---------- */
    function resizeCanvas() {
        const rect = canvas.getBoundingClientRect();
        const dpr = window.devicePixelRatio || 1;
        canvas.width = Math.round(rect.width * dpr);
        canvas.height = Math.round(rect.height * dpr);
        ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
        ctx.lineCap = 'round';
        ctx.lineJoin = 'round';
        if (history.length) {
            ctx.drawImage(history[history.length - 1].img, 0, 0, rect.width, rect.height);
        } else {
            ctx.fillStyle = '#ffffff';
            ctx.fillRect(0, 0, rect.width, rect.height);
        }
    }

    window.addEventListener('resize', () => resizeCanvas());
    resizeCanvas();
    snapshot();

    /* ---------- Color swatches ---------- */
    COLORS.forEach((c) => {
        const sw = document.createElement('button');
        sw.className = 'swatch';
        sw.style.background = c;
        sw.setAttribute('aria-label', 'Color ' + c);
        sw.addEventListener('click', () => {
            setColor(c);
            document.querySelectorAll('.swatch').forEach((s) => s.classList.remove('is-active'));
            sw.classList.add('is-active');
        });
        swatchesWrap.appendChild(sw);
    });
    swatchesWrap.firstChild.classList.add('is-active');

    function setColor(c) {
        color = c;
        eraser = false;
        eraserBtn.classList.remove('btn-fun--green');
        brushRange.style.accentColor = c;
    }

    /* ---------- Brush size ---------- */
    brushRange.addEventListener('input', () => {
        size = parseInt(brushRange.value, 10);
        brushValue.textContent = size;
    });

    /* ---------- Drawing with pointer events ---------- */
    function pos(e) {
        const rect = canvas.getBoundingClientRect();
        return {
            x: e.clientX - rect.left,
            y: e.clientY - rect.top
        };
    }

    canvas.addEventListener('pointerdown', (e) => {
        e.preventDefault();
        canvas.setPointerCapture(e.pointerId);
        const p = pos(e);
        drawing = true;
        lastX = p.x;
        lastY = p.y;
        ctx.beginPath();
        ctx.moveTo(p.x, p.y);
    });

    canvas.addEventListener('pointermove', (e) => {
        if (!drawing) return;
        const p = pos(e);
        ctx.strokeStyle = eraser ? '#ffffff' : color;
        ctx.lineWidth = eraser ? size * 3 : size;
        ctx.lineTo(p.x, p.y);
        ctx.stroke();
        lastX = p.x;
        lastY = p.y;
    });

    canvas.addEventListener('pointerup', () => {
        if (!drawing) return;
        drawing = false;
        snapshot();
    });

    canvas.addEventListener('pointercancel', () => {
        drawing = false;
    });

    /* ---------- Undo / clear / save ---------- */
    function snapshot() {
        const img = new Image();
        img.src = canvas.toDataURL();
        history.push({ img });
        if (history.length > MAX_HISTORY) history.shift();
    }

    function restore(last) {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        if (last) {
            const rect = canvas.getBoundingClientRect();
            ctx.drawImage(last.img, 0, 0, rect.width, rect.height);
        } else {
            ctx.fillStyle = '#ffffff';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
        }
    }

    undoBtn.addEventListener('click', () => {
        if (history.length > 1) {
            history.pop();
            restore(history[history.length - 1]);
        }
    });

    clearBtn.addEventListener('click', () => {
        ctx.fillStyle = '#ffffff';
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        snapshot();
    });

    eraserBtn.addEventListener('click', () => {
        eraser = !eraser;
        eraserBtn.classList.toggle('btn-fun--green', eraser);
        if (!eraser) {
            setColor(color);
        }
    });

    saveBtn.addEventListener('click', () => {
        const a = document.createElement('a');
        a.download = 'my-drawing.png';
        a.href = canvas.toDataURL('image/png');
        a.click();
        toast('Saved! Check your downloads. 🎉');
    });

    /* ---------- Toast ---------- */
    let toastTimer = null;
    function toast(msg) {
        const t = document.getElementById('toast');
        if (!t) return;
        t.textContent = msg;
        t.classList.add('is-visible');
        clearTimeout(toastTimer);
        toastTimer = setTimeout(() => t.classList.remove('is-visible'), 2400);
    }
})();
