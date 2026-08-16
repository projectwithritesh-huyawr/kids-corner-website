// js/kids.js — Kids World interactions
'use strict';

document.addEventListener('DOMContentLoaded', () => {
    const loader = document.getElementById('loader');
    const sky = document.getElementById('sky');
    const particles = document.getElementById('particles');
    const confettiWrap = document.getElementById('confetti');
    const glow = document.getElementById('cursorGlow');

    const COLORS = ['#ff5e9c', '#ff9f45', '#ffd166', '#06d6a0', '#2ec4b6', '#4d96ff', '#8b5cf6', '#ff6b6b'];

    /* ---------- Loader ---------- */
    let loaderDone = false;
    const hideLoader = () => {
        if (loaderDone || !loader) return;
        loaderDone = true;
        loader.classList.add('loader--hidden');
        document.body.classList.add('is-ready');
        setTimeout(() => loader.remove(), 700);
        setTimeout(() => confetti(160), 500);
    };
    window.addEventListener('load', () => setTimeout(hideLoader, 650));
    setTimeout(hideLoader, 3500);

    /* ---------- Confetti ---------- */
    const confetti = (count) => {
        if (!confettiWrap) return;
        for (let i = 0; i < count; i++) {
            const p = document.createElement('span');
            p.className = 'confetti-piece';
            p.style.left = Math.random() * 100 + 'vw';
            p.style.setProperty('--color', COLORS[Math.floor(Math.random() * COLORS.length)]);
            p.style.setProperty('--size', (7 + Math.random() * 7).toFixed(1) + 'px');
            p.style.setProperty('--radius', Math.random() > 0.5 ? '50%' : '2px');
            p.style.setProperty('--dur', (2.4 + Math.random() * 2.2).toFixed(2) + 's');
            p.style.setProperty('--delay', (Math.random() * 1.2).toFixed(2) + 's');
            p.style.setProperty('--sway', (Math.random() * 220 - 110).toFixed(0) + 'px');
            p.style.setProperty('--rot', (Math.random() * 1080 - 540).toFixed(0) + 'deg');
            confettiWrap.appendChild(p);
            p.addEventListener('animationend', () => p.remove(), { once: true });
        }
    };

    /* ---------- Floating particles ---------- */
    const spawnParticles = () => {
        if (!particles) return;
        for (let i = 0; i < 18; i++) {
            const p = document.createElement('span');
            p.className = 'particle';
            p.style.left = Math.random() * 100 + 'vw';
            p.style.top = (30 + Math.random() * 70) + 'vh';
            const size = 4 + Math.random() * 7;
            p.style.width = size + 'px';
            p.style.height = size + 'px';
            p.style.background = COLORS[Math.floor(Math.random() * COLORS.length)];
            p.style.animationDuration = (8 + Math.random() * 10).toFixed(1) + 's';
            p.style.animationDelay = (-Math.random() * 12).toFixed(1) + 's';
            particles.appendChild(p);
        }
    };
    spawnParticles();

    /* ---------- Mouse parallax ---------- */
    const layers = Array.from(document.querySelectorAll('[data-depth]'));
    let targetX = 0;
    let targetY = 0;
    let curX = 0;
    let curY = 0;

    window.addEventListener('mousemove', (e) => {
        targetX = e.clientX / window.innerWidth - 0.5;
        targetY = e.clientY / window.innerHeight - 0.5;
        if (glow) {
            glow.style.left = e.clientX + 'px';
            glow.style.top = e.clientY + 'px';
            glow.classList.add('is-active');
        }
    });

    const animateParallax = () => {
        curX += (targetX - curX) * 0.07;
        curY += (targetY - curY) * 0.07;

        layers.forEach((layer) => {
            const depth = parseFloat(layer.dataset.depth) || 0;
            layer.style.transform =
                'translate3d(' + (curX * depth * 60).toFixed(1) + 'px,' + (curY * depth * 60).toFixed(1) + 'px,0)';
        });

        if (glow && glow.classList.contains('is-active')) {
            glow.style.transform = 'translate(-50%, -50%) translate3d(' + (curX * 30).toFixed(1) + 'px,' + (curY * 30).toFixed(1) + 'px,0)';
        }

        requestAnimationFrame(animateParallax);
    };
    animateParallax();

    document.addEventListener('mouseleave', () => {
        if (glow) glow.classList.remove('is-active');
    });

    /* ---------- Activity cards: tilt ---------- */
    const cards = Array.from(document.querySelectorAll('.fun-card'));

    cards.forEach((card) => {
        card.addEventListener('mousemove', (e) => {
            const r = card.getBoundingClientRect();
            const px = (e.clientX - r.left) / r.width - 0.5;
            const py = (e.clientY - r.top) / r.height - 0.5;
            card.style.transform =
                'perspective(650px) rotateY(' + (px * 12).toFixed(1) + 'deg) rotateX(' + (-py * 12).toFixed(1) + 'deg) translateY(-8px) scale(1.04)';
        });

        card.addEventListener('mouseleave', () => {
            card.style.transform = '';
        });
    });

    /* ---------- Reveal on scroll ---------- */
    const revealEls = document.querySelectorAll('.reveal');
    if ('IntersectionObserver' in window) {
        const io = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    io.unobserve(entry.target);
                }
            });
        }, { threshold: 0.15 });
        revealEls.forEach((el) => io.observe(el));
    } else {
        revealEls.forEach((el) => el.classList.add('is-visible'));
    }
});
