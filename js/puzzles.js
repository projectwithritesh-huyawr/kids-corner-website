// js/puzzles.js — Sliding tiles + spot the odd one
'use strict';

(function () {
    const STARS_KEY = 'kidsWorldStars';

    /* ---------- Stars storage ---------- */
    function getStarsData() {
        try {
            return JSON.parse(localStorage.getItem(STARS_KEY)) || {};
        } catch (e) {
            return {};
        }
    }

    function saveStarsData(d) {
        try {
            localStorage.setItem(STARS_KEY, JSON.stringify(d));
        } catch (e) { /* ignore */ }
    }

    function awardStars(game, stars) {
        const d = getStarsData();
        d[game] = Math.max(d[game] || 0, stars);
        d.total = 0;
        ['memory', 'stars', 'mole', 'ttt', 'rps', 'tiles', 'odd'].forEach((k) => {
            d.total += d[k] || 0;
        });
        saveStarsData(d);
        refreshStars();
    }

    function refreshStars() {
        const el = document.getElementById('totalStars');
        if (el) el.textContent = getStarsData().total || 0;
    }

    /* ---------- Toast ---------- */
    let toastTimer = null;
    function showToast(msg) {
        const t = document.getElementById('toast');
        if (!t) return;
        t.textContent = msg;
        t.classList.add('is-visible');
        clearTimeout(toastTimer);
        toastTimer = setTimeout(() => t.classList.remove('is-visible'), 2600);
    }

    /* ---------- Confetti ---------- */
    const COLORS = ['#ff5e9c', '#ff9f45', '#ffd166', '#06d6a0', '#2ec4b6', '#4d96ff', '#8b5cf6', '#ff6b6b'];
    function burstConfetti(count) {
        const wrap = document.getElementById('confetti');
        if (!wrap) return;
        const n = count || 90;
        for (let i = 0; i < n; i++) {
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

    /* ---------- Sound ---------- */
    let audioCtx = null;
    function popSound() {
        try {
            if (!audioCtx) audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            if (audioCtx.state === 'suspended') audioCtx.resume();
            const o = audioCtx.createOscillator();
            const g = audioCtx.createGain();
            o.type = 'triangle';
            o.frequency.setValueAtTime(420 + Math.random() * 220, audioCtx.currentTime);
            g.gain.setValueAtTime(0.12, audioCtx.currentTime);
            g.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 0.12);
            o.connect(g).connect(audioCtx.destination);
            o.start();
            o.stop(audioCtx.currentTime + 0.13);
        } catch (e) { /* ignore */ }
    }

    /* ---------- Navigation ---------- */
    const lobby = document.getElementById('puzzleLobby');
    let tileTimer = null;

    document.querySelectorAll('[data-puzzle]').forEach((card) => {
        card.addEventListener('click', () => {
            lobby.classList.add('is-hidden');
            const id = 'puzzle-' + card.dataset.puzzle;
            document.querySelectorAll('.puzzle-view').forEach((v) => v.classList.remove('is-active'));
            document.getElementById(id).classList.add('is-active');
            if (id === 'puzzle-tiles') initTiles();
            if (id === 'puzzle-odd') resetOdd();
        });
    });

    document.querySelectorAll('[data-back]').forEach((btn) => {
        btn.addEventListener('click', () => {
            clearTileTimer();
            document.querySelectorAll('.puzzle-view').forEach((v) => v.classList.remove('is-active'));
            lobby.classList.remove('is-hidden');
        });
    });

    /* ============================================================
       Sliding tiles
       ============================================================ */
    const TILE_EMOJIS_3 = ['😀', '😍', '😎', '🤩', '🥳', '😜', '🤗', '😇'];
    const TILE_EMOJIS_4 = ['😀', '😍', '😎', '🤩', '🥳', '😜', '🤗', '😇', '🤠', '😺', '🙃', '😋', '🤓', '👾', '🐸'];
    const boardEl = document.getElementById('tileBoard');

    let tileState = {
        size: 3,
        tiles: [],
        empty: -1,
        moves: 0,
        seconds: 0,
        solved: false
    };

    function initTiles() {
        clearTileTimer();
        buildTiles(tileState.size);
    }

    function buildTiles(size) {
        const emojis = size === 3 ? TILE_EMOJIS_3 : TILE_EMOJIS_4;
        tileState = {
            size,
            tiles: [...emojis, null],
            empty: emojis.length,
            moves: 0,
            seconds: 0,
            solved: false
        };
        boardEl.dataset.size = size;
        document.getElementById('tileMoves').textContent = '0';
        document.getElementById('tileTime').textContent = '0';
        document.getElementById('tileResult').textContent = '';
        renderTiles();
        shuffleTiles();
        startTileTimer();
    }

    function renderTiles() {
        boardEl.innerHTML = '';
        tileState.tiles.forEach((t, i) => {
            const b = document.createElement('button');
            b.className = 'tile' + (t === null ? ' is-empty' : '');
            b.textContent = t === null ? '' : t;
            b.setAttribute('aria-label', t === null ? 'Empty' : 'Tile');
            if (t !== null) {
                b.addEventListener('click', () => moveTile(i));
            }
            boardEl.appendChild(b);
        });
    }

    function shuffleTiles() {
        const size = tileState.size;
        for (let i = 0; i < 200; i++) {
            const neighbors = getNeighbors(tileState.empty);
            const pick = neighbors[Math.floor(Math.random() * neighbors.length)];
            swapTile(pick);
        }
        tileState.moves = 0;
        document.getElementById('tileMoves').textContent = '0';
    }

    function getNeighbors(emptyIdx) {
        const size = tileState.size;
        const res = [];
        const row = Math.floor(emptyIdx / size);
        const col = emptyIdx % size;
        if (row > 0) res.push(emptyIdx - size);
        if (row < size - 1) res.push(emptyIdx + size);
        if (col > 0) res.push(emptyIdx - 1);
        if (col < size - 1) res.push(emptyIdx + 1);
        return res;
    }

    function moveTile(i) {
        if (tileState.solved) return;
        if (!getNeighbors(tileState.empty).includes(i)) return;
        swapTile(i);
        popSound();
        tileState.moves++;
        document.getElementById('tileMoves').textContent = tileState.moves;
        if (isSolved()) winTiles();
    }

    function swapTile(i) {
        const e = tileState.empty;
        [tileState.tiles[e], tileState.tiles[i]] = [tileState.tiles[i], tileState.tiles[e]];
        tileState.empty = i;
        renderTiles();
    }

    function isSolved() {
        const arr = tileState.tiles;
        for (let i = 0; i < arr.length - 1; i++) {
            if (arr[i] !== (tileState.size === 3 ? TILE_EMOJIS_3[i] : TILE_EMOJIS_4[i])) return false;
        }
        return arr[arr.length - 1] === null;
    }

    function winTiles() {
        tileState.solved = true;
        clearTileTimer();
        const fast = document.getElementById('fastMode').checked;
        let stars = 1;
        if (tileState.size === 3) {
            stars = tileState.moves <= 25 ? 3 : tileState.moves <= 45 ? 2 : 1;
        } else {
            stars = tileState.moves <= 90 ? 3 : tileState.moves <= 150 ? 2 : 1;
        }
        if (fast && tileState.seconds <= 60) stars = Math.max(stars, 3);
        awardStars('tiles', stars);
        burstConfetti();
        document.getElementById('tileResult').textContent =
            'Yay! You solved it in ' + tileState.moves + ' moves and ' + tileState.seconds + 's! ⭐' + stars;
        showToast('Puzzle solved! You earned ' + stars + ' stars!');
    }

    function startTileTimer() {
        clearTileTimer();
        tileTimer = setInterval(() => {
            tileState.seconds++;
            document.getElementById('tileTime').textContent = tileState.seconds;
        }, 1000);
    }

    function clearTileTimer() {
        if (tileTimer) {
            clearInterval(tileTimer);
            tileTimer = null;
        }
    }

    document.getElementById('shuffleBtn').addEventListener('click', () => {
        tileState.solved = false;
        tileState.moves = 0;
        tileState.seconds = 0;
        document.getElementById('tileMoves').textContent = '0';
        document.getElementById('tileTime').textContent = '0';
        document.getElementById('tileResult').textContent = '';
        startTileTimer();
        shuffleTiles();
        renderTiles();
    });

    document.getElementById('tileNewBtn').addEventListener('click', initTiles);

    document.querySelectorAll('[data-size]').forEach((btn) => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('[data-size]').forEach((b) => b.classList.remove('is-active'));
            btn.classList.add('is-active');
            tileState.size = parseInt(btn.dataset.size, 10);
            buildTiles(tileState.size);
        });
    });

    /* ============================================================
       Spot the odd one
       ============================================================ */
    const oddBoard = document.getElementById('oddBoard');
    const ODD_EMOJIS = ['🍎', '🍌', '🍇', '🍓', '🥕', '🌻', '🐞', '🦋', '🐥', '⭐', '🌈', '🎈'];
    let oddState = { round: 1, score: 0, locked: false };

    function resetOdd() {
        oddState = { round: 1, score: 0, locked: false };
        document.getElementById('oddRound').textContent = '1';
        document.getElementById('oddScore').textContent = '0';
        document.getElementById('oddResult').textContent = '';
        oddBoard.innerHTML = '<p class="game__result">Tap Start to begin! 🚀</p>';
    }

    function buildOddRound() {
        oddState.locked = false;
        const base = ODD_EMOJIS[Math.floor(Math.random() * ODD_EMOJIS.length)];
        let odd = ODD_EMOJIS[Math.floor(Math.random() * ODD_EMOJIS.length)];
        if (odd === base) odd = ODD_EMOJIS[(ODD_EMOJIS.indexOf(base) + 1) % ODD_EMOJIS.length];

        const cells = [];
        for (let i = 0; i < 8; i++) cells.push(base);
        const oddIdx = Math.floor(Math.random() * 9);
        cells.splice(oddIdx, 0, odd);

        oddBoard.innerHTML = '';
        cells.forEach((emoji, i) => {
            const c = document.createElement('button');
            c.className = 'odd-cell';
            c.textContent = emoji;
            c.setAttribute('aria-label', 'Emoji');
            c.addEventListener('click', () => {
                if (oddState.locked) return;
                if (i === oddIdx) {
                    oddState.locked = true;
                    c.classList.add('is-found');
                    oddState.score++;
                    document.getElementById('oddScore').textContent = oddState.score;
                    popSound();
                    setTimeout(() => {
                        if (oddState.round >= 3) endOdd();
                        else {
                            oddState.round++;
                            document.getElementById('oddRound').textContent = oddState.round;
                            buildOddRound();
                        }
                    }, 800);
                } else {
                    c.classList.add('is-wrong');
                    setTimeout(() => c.classList.remove('is-wrong'), 250);
                }
            });
            oddBoard.appendChild(c);
        });
    }

    document.getElementById('oddStartBtn').addEventListener('click', () => {
        oddState = { round: 1, score: 0, locked: false };
        document.getElementById('oddRound').textContent = '1';
        document.getElementById('oddScore').textContent = '0';
        document.getElementById('oddResult').textContent = '';
        buildOddRound();
    });

    function endOdd() {
        const stars = oddState.score === 3 ? 3 : oddState.score === 2 ? 2 : 1;
        awardStars('odd', stars);
        document.getElementById('oddResult').textContent =
            'You found ' + oddState.score + ' of 3! ⭐' + stars;
        if (oddState.score === 3) burstConfetti();
        showToast('You earned ' + stars + ' stars!');
        oddBoard.innerHTML = '<p class="game__result">Great job! Play again!</p>';
    }

    refreshStars();
})();
