// js/rewards.js — Rewards dashboard
'use strict';

(function () {
    const STARS_KEY = 'kidsWorldStars';

    function getStarsData() {
        try {
            return JSON.parse(localStorage.getItem(STARS_KEY)) || {};
        } catch (e) {
            return {};
        }
    }

    const totalEl = document.getElementById('totalStars');
    const scoresList = document.getElementById('scoresList');

    const GAMES = [
        ['memory', 'Memory Match', '🃏'],
        ['stars', 'Catch the Stars', '⭐'],
        ['mole', 'Whack-a-Mole', '🐹'],
        ['ttt', 'Tic-Tac-Toe', '⭕'],
        ['rps', 'Rock Paper Scissors', '✊✋✌️'],
        ['tiles', 'Slide the Tiles', '🧩'],
        ['odd', 'Spot the Odd One', '🕵️']
    ];

    const data = getStarsData();
    const total = data.total || 0;

    /* ---------- Confetti ---------- */
    const COLORS = ['#ff5e9c', '#ff9f45', '#ffd166', '#06d6a0', '#2ec4b6', '#4d96ff', '#8b5cf6', '#ff6b6b'];

    function burstConfetti(count) {
        const wrap = document.getElementById('confetti');
        if (!wrap) return;
        for (let i = 0; i < count; i++) {
            const p = document.createElement('span');
            p.className = 'confetti-piece';
            p.style.left = Math.random() * 100 + 'vw';
            p.style.setProperty('--color', COLORS[Math.floor(Math.random() * COLORS.length)]);
            p.style.setProperty('--size', (7 + Math.random() * 7).toFixed(1) + 'px');
            p.style.setProperty('--radius', Math.random() > 0.5 ? '50%' : '2px');
            p.style.setProperty('--dur', (2.2 + Math.random() * 2).toFixed(2) + 's');
            p.style.setProperty('--delay', (Math.random() * 0.8).toFixed(2) + 's');
            p.style.setProperty('--sway', (Math.random() * 220 - 110).toFixed(0) + 'px');
            p.style.setProperty('--rot', (Math.random() * 1080 - 540).toFixed(0) + 'deg');
            wrap.appendChild(p);
            p.addEventListener('animationend', () => p.remove(), { once: true });
        }
    }

    /* ---------- Total ---------- */
    if (totalEl) totalEl.textContent = total;
    if (total >= 50) burstConfetti(140);
    else if (total >= 25) burstConfetti(100);
    else if (total >= 10) burstConfetti(70);

    /* ---------- Badges ---------- */
    document.querySelectorAll('.badge').forEach((b) => {
        const need = parseInt(b.dataset.needed, 10);
        if (total >= need) {
            b.classList.add('is-unlocked');
        }
    });

    /* ---------- Scores list ---------- */
    const played = GAMES.filter((g) => (data[g[0]] || 0) > 0);

    if (played.length) {
        scoresList.innerHTML = '';
        played.forEach((g) => {
            const row = document.createElement('div');
            row.className = 'score-row';
            const stars = data[g[0]];
            row.innerHTML =
                '<span class="score-row__emoji">' + g[2] + '</span>' +
                '<span class="score-row__name">' + g[1] + '</span>' +
                '<span class="score-row__stars">' + '⭐'.repeat(stars) + '</span>';
            scoresList.appendChild(row);
        });
        document.querySelector('.scores__hint').style.display = 'none';
    } else {
        scoresList.innerHTML = '';
    }
})();
