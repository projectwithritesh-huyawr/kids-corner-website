// js/games.js — 5 mini-games for Kids World
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
        const n = count || 80;
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

    /* ---------- Tiny pop sound (Web Audio) ---------- */
    let audioCtx = null;
    function getAudio() {
        try {
            if (!audioCtx) audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            if (audioCtx.state === 'suspended') audioCtx.resume();
            return audioCtx;
        } catch (e) {
            return null;
        }
    }

    function popSound() {
        const ctx = getAudio();
        if (!ctx) return;
        const o = ctx.createOscillator();
        const g = ctx.createGain();
        o.type = 'triangle';
        o.frequency.setValueAtTime(520 + Math.random() * 300, ctx.currentTime);
        g.gain.setValueAtTime(0.15, ctx.currentTime);
        g.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.12);
        o.connect(g).connect(ctx.destination);
        o.start();
        o.stop(ctx.currentTime + 0.14);
    }

    /* ---------- Navigation ---------- */
    const lobby = document.getElementById('lobby');
    const gameView = document.getElementById('gameView');
    const timers = [];

    function openGame(id) {
        lobby.classList.add('is-hidden');
        gameView.classList.add('is-active');
        document.querySelectorAll('.game').forEach((g) => g.classList.remove('is-active'));
        const g = document.getElementById('game-' + id);
        if (g) g.classList.add('is-active');
        initGame(id);
    }

    function backToLobby() {
        clearTimers();
        document.querySelectorAll('.game').forEach((g) => g.classList.remove('is-active'));
        gameView.classList.remove('is-active');
        lobby.classList.remove('is-hidden');
    }

    function clearTimers() {
        while (timers.length) {
            clearInterval(timers.pop());
        }
    }

    document.querySelectorAll('.lobby-card').forEach((card) => {
        card.addEventListener('click', () => openGame(card.dataset.game));
    });

    document.querySelectorAll('[data-back]').forEach((btn) => {
        btn.addEventListener('click', backToLobby);
    });

    /* ---------- Game 1: Memory Match ---------- */
    const memoryEmojis = ['🐶', '🐱', '🦊', '🐻', '🐸', '🦄'];
    let memoryState = { moves: 0, pairs: 0, first: null, lock: false };

    function initMemory() {
        memoryState = { moves: 0, pairs: 0, first: null, lock: false };
        document.getElementById('memoryMoves').textContent = '0';
        document.getElementById('memoryResult').textContent = '';
        const grid = document.getElementById('memoryGrid');
        grid.innerHTML = '';
        const deck = shuffle([...memoryEmojis, ...memoryEmojis]);
        deck.forEach((emoji) => {
            const card = document.createElement('button');
            card.className = 'mem-card';
            card.setAttribute('aria-label', 'Memory card');
            const inner = document.createElement('span');
            inner.className = 'mem-card__inner';
            inner.innerHTML = '<span class="mem-card__front">?</span><span class="mem-card__back">' + emoji + '</span>';
            card.appendChild(inner);
            card.addEventListener('click', () => flipMemory(card));
            grid.appendChild(card);
        });
    }

    function flipMemory(card) {
        if (memoryState.lock || card.classList.contains('is-flipped') || card.classList.contains('is-matched')) return;
        card.classList.add('is-flipped');
        popSound();
        if (!memoryState.first) {
            memoryState.first = card;
            return;
        }
        memoryState.moves++;
        document.getElementById('memoryMoves').textContent = memoryState.moves;
        const first = memoryState.first;
        const same = first.querySelector('.mem-card__back').textContent === card.querySelector('.mem-card__back').textContent;
        if (same) {
            first.classList.add('is-matched');
            card.classList.add('is-matched');
            memoryState.pairs++;
            memoryState.first = null;
            if (memoryState.pairs === 6) winMemory();
        } else {
            memoryState.lock = true;
            memoryState.first = null;
            setTimeout(() => {
                first.classList.remove('is-flipped');
                card.classList.remove('is-flipped');
                memoryState.lock = false;
            }, 750);
        }
    }

    function winMemory() {
        const stars = memoryState.moves <= 14 ? 3 : memoryState.moves <= 22 ? 2 : 1;
        awardStars('memory', stars);
        burstConfetti();
        document.getElementById('memoryResult').textContent =
            'Yay! You found all pairs in ' + memoryState.moves + ' moves! ⭐' + stars;
        showToast('Memory Match done! You earned ' + stars + ' star' + (stars > 1 ? 's' : '') + '!');
    }

    /* ---------- Game 2: Catch the Stars ---------- */
    const starArea = document.getElementById('starArea');
    let starState = { score: 0, time: 30 };

    function initStars() {
        clearTimers();
        starState = { score: 0, time: 30 };
        document.getElementById('starScore').textContent = '0';
        document.getElementById('starTime').textContent = '30';
        document.getElementById('starResult').textContent = '';
        starArea.innerHTML = '<p class="play-area__hint">Tap the stars before they fall! ⭐</p>';
        timers.push(setInterval(() => {
            starState.time--;
            document.getElementById('starTime').textContent = starState.time;
            if (starState.time <= 0) endStars();
        }, 1000));
        spawnStar();
        timers.push(setInterval(spawnStar, 650));
    }

    function spawnStar() {
        if (starState.time <= 0) return;
        const s = document.createElement('button');
        s.className = 'fall-star';
        s.textContent = Math.random() > 0.85 ? '✨' : '⭐';
        s.style.left = (Math.random() * 88 + 4) + '%';
        s.style.animationDuration = (2.2 + Math.random() * 1.8).toFixed(2) + 's';
        s.addEventListener('click', () => {
            if (!s.parentNode || s.classList.contains('is-caught')) return;
            s.classList.add('is-caught');
            starState.score++;
            document.getElementById('starScore').textContent = starState.score;
            popSound();
            setTimeout(() => s.remove(), 320);
        });
        s.addEventListener('animationend', () => {
            if (!s.classList.contains('is-caught')) s.remove();
        });
        starArea.appendChild(s);
    }

    function endStars() {
        clearTimers();
        const stars = starState.score >= 20 ? 3 : starState.score >= 10 ? 2 : 1;
        awardStars('stars', stars);
        document.getElementById('starResult').textContent =
            'Time up! You caught ' + starState.score + ' stars! ⭐' + stars;
        if (starState.score >= 10) burstConfetti();
        showToast('You earned ' + stars + ' star' + (stars > 1 ? 's' : '') + '!');
    }

    /* ---------- Game 3: Whack-a-Mole ---------- */
    const moleGrid = document.getElementById('moleGrid');
    let moleState = { score: 0, time: 30 };

    function initMole() {
        clearTimers();
        moleState = { score: 0, time: 30 };
        document.getElementById('moleScore').textContent = '0';
        document.getElementById('moleTime').textContent = '30';
        document.getElementById('moleResult').textContent = '';
        moleGrid.innerHTML = '';
        for (let i = 0; i < 9; i++) {
            const hole = document.createElement('div');
            hole.className = 'mole-hole';
            const mole = document.createElement('button');
            mole.className = 'mole';
            mole.setAttribute('aria-label', 'Mole');
            mole.textContent = '🐹';
            mole.addEventListener('click', () => whackMole(mole));
            hole.appendChild(mole);
            moleGrid.appendChild(hole);
        }
        timers.push(setInterval(() => {
            moleState.time--;
            document.getElementById('moleTime').textContent = moleState.time;
            if (moleState.time <= 0) endMole();
        }, 1000));
        timers.push(setInterval(popMoles, 850));
        popMoles();
    }

    function popMoles() {
        const moles = moleGrid.querySelectorAll('.mole');
        moles.forEach((m) => m.classList.remove('is-up'));
        const count = 1 + (Math.random() > 0.7 ? 1 : 0);
        const idxs = shuffle([0, 1, 2, 3, 4, 5, 6, 7, 8]).slice(0, count);
        idxs.forEach((i) => {
            if (moles[i]) moles[i].classList.add('is-up');
        });
    }

    function whackMole(mole) {
        if (!mole.classList.contains('is-up')) return;
        mole.classList.remove('is-up');
        mole.classList.add('is-whacked');
        moleState.score++;
        document.getElementById('moleScore').textContent = moleState.score;
        popSound();
        setTimeout(() => mole.classList.remove('is-whacked'), 220);
    }

    function endMole() {
        clearTimers();
        const stars = moleState.score >= 18 ? 3 : moleState.score >= 10 ? 2 : 1;
        awardStars('mole', stars);
        document.getElementById('moleResult').textContent =
            'Time up! You whacked ' + moleState.score + ' moles! ⭐' + stars;
        if (moleState.score >= 10) burstConfetti();
        showToast('You earned ' + stars + ' star' + (stars > 1 ? 's' : '') + '!');
    }

    /* ---------- Game 4: Tic-Tac-Toe ---------- */
    let tttState = { board: [], over: false };

    function initTTT() {
        tttState = { board: Array(9).fill(null), over: false };
        document.getElementById('tttResult').textContent = '';
        const board = document.getElementById('tttBoard');
        board.innerHTML = '';
        for (let i = 0; i < 9; i++) {
            const cell = document.createElement('button');
            cell.className = 'ttt-cell';
            cell.setAttribute('aria-label', 'Cell');
            cell.addEventListener('click', () => tttPlay(cell, i));
            board.appendChild(cell);
        }
    }

    function tttPlay(cell, i) {
        if (tttState.over || tttState.board[i]) return;
        tttState.board[i] = 'X';
        cell.textContent = '😀';
        cell.disabled = true;
        popSound();
        if (checkTTTWin('X')) return tttEnd('X');
        if (tttState.board.every(Boolean)) return tttEnd(null);
        setTimeout(() => {
            const ai = bestTTTMove();
            if (ai === null) return;
            tttState.board[ai] = 'O';
            const cells = tttBoardCells();
            cells[ai].textContent = '🤖';
            cells[ai].disabled = true;
            if (checkTTTWin('O')) return tttEnd('O');
            if (tttState.board.every(Boolean)) return tttEnd(null);
        }, 450);
    }

    function tttBoardCells() {
        return Array.from(document.querySelectorAll('.ttt-cell'));
    }

    const TTT_LINES = [
        [0, 1, 2], [3, 4, 5], [6, 7, 8],
        [0, 3, 6], [1, 4, 7], [2, 5, 8],
        [0, 4, 8], [2, 4, 6]
    ];

    function checkTTTWin(player) {
        for (const line of TTT_LINES) {
            if (line.every((i) => tttState.board[i] === player)) {
                tttBoardCells().forEach((c, i) => {
                    if (line.includes(i)) c.classList.add('is-win');
                });
                return true;
            }
        }
        return false;
    }

    function bestTTTMove() {
        const b = tttState.board;
        const empty = b.map((v, i) => (v === null ? i : -1)).filter((i) => i >= 0);
        if (!empty.length) return null;
        const winMove = (p) => empty.find((i) => {
            const copy = b.slice();
            copy[i] = p;
            return TTT_LINES.some((line) => line.every((j) => copy[j] === p));
        });
        return winMove('O') !== undefined ? winMove('O')
            : winMove('X') !== undefined ? winMove('X')
            : b[4] === null ? 4
            : [0, 2, 6, 8].find((i) => b[i] === null) !== undefined ? [0, 2, 6, 8].find((i) => b[i] === null)
            : empty[Math.floor(Math.random() * empty.length)];
    }

    function tttEnd(winner) {
        tttState.over = true;
        const result = document.getElementById('tttResult');
        if (winner === 'X') {
            const stars = 3;
            awardStars('ttt', stars);
            burstConfetti();
            result.textContent = 'You won! 🎉 Great job! ⭐' + stars;
            showToast('Tic-Tac-Toe win! +' + stars + ' stars!');
        } else if (winner === 'O') {
            awardStars('ttt', 1);
            result.textContent = 'Robot wins this time! Try again! ⭐1';
            showToast('You still get 1 star!');
        } else {
            awardStars('ttt', 1);
            result.textContent = 'It is a draw! Great game! ⭐1';
            showToast('Draw! You get 1 star!');
        }
    }

    /* ---------- Game 5: Rock Paper Scissors ---------- */
    const RPS_MAP = { rock: '✊', paper: '✋', scissors: '✌️' };
    const RPS_BEAT = { rock: 'scissors', paper: 'rock', scissors: 'paper' };
    let rpsState = { you: 0, robot: 0, round: 1, over: false };

    function initRPS() {
        rpsState = { you: 0, robot: 0, round: 1, over: false };
        updateRPS();
        document.getElementById('rpsYourPick').textContent = '❓';
        document.getElementById('rpsRobotPick').textContent = '❓';
        document.getElementById('rpsResult').textContent = '';
    }

    function updateRPS() {
        document.getElementById('rpsYou').textContent = rpsState.you;
        document.getElementById('rpsRobot').textContent = rpsState.robot;
        document.getElementById('rpsRound').textContent = rpsState.round;
    }

    document.querySelectorAll('[data-move]').forEach((btn) => {
        btn.addEventListener('click', () => playRPS(btn.dataset.move));
    });

    function playRPS(move) {
        if (rpsState.over) return;
        const moves = ['rock', 'paper', 'scissors'];
        const robot = moves[Math.floor(Math.random() * 3)];
        const yourEl = document.getElementById('rpsYourPick');
        const robotEl = document.getElementById('rpsRobotPick');

        yourEl.classList.add('is-shaking');
        robotEl.classList.add('is-shaking');
        yourEl.textContent = RPS_MAP[move];
        robotEl.textContent = RPS_MAP[robot];
        popSound();

        setTimeout(() => {
            yourEl.classList.remove('is-shaking');
            robotEl.classList.remove('is-shaking');

            let msg;
            if (move === robot) {
                msg = 'It is a tie! 🤝';
            } else if (RPS_BEAT[move] === robot) {
                rpsState.you++;
                msg = 'You win this round! 🎉';
            } else {
                rpsState.robot++;
                msg = 'Robot wins this round! 🤖';
            }
            rpsState.round++;
            updateRPS();

            if (rpsState.you >= 3 || rpsState.robot >= 3) {
                rpsState.over = true;
                const stars = rpsState.you >= 3 ? 3 : 2;
                awardStars('rps', stars);
                document.getElementById('rpsResult').textContent =
                    (rpsState.you >= 3 ? 'You win the game! 🏆 ' : 'Robot wins the game! 🤖 ') + 'Final: You ' + rpsState.you + ' - Robot ' + rpsState.robot + ' ⭐' + stars;
                if (rpsState.you >= 3) burstConfetti();
                showToast('You earned ' + stars + ' stars!');
            } else {
                document.getElementById('rpsResult').textContent = msg + ' Round ' + rpsState.round + ' of 5';
            }
        }, 500);
    }

    /* ---------- Restart buttons ---------- */
    document.querySelectorAll('[data-restart]').forEach((btn) => {
        btn.addEventListener('click', () => initGame(btn.dataset.restart));
    });

    function initGame(id) {
        if (id === 'memory') initMemory();
        if (id === 'stars') initStars();
        if (id === 'mole') initMole();
        if (id === 'ttt') initTTT();
        if (id === 'rps') initRPS();
    }

    /* ---------- Helpers ---------- */
    function shuffle(arr) {
        const a = arr.slice();
        for (let i = a.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [a[i], a[j]] = [a[j], a[i]];
        }
        return a;
    }

    refreshStars();
})();
