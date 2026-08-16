<?php
// games.php — 5 mini-games for kids
declare(strict_types=1);
$pageTitle    = 'Games — Kids World';
$sectionTitle = 'Games Zone';
$extraCss     = ['games'];
$extraJs      = ['games'];
include 'includes/kids-header.php';
?>

<section class="section-head reveal">
    <h1 class="section-head__title">Let's Play Games! 🎮</h1>
    <p class="section-head__sub">Pick a game, have fun and earn stars! ⭐</p>
    <div class="hud">
        <span class="stat-chip">⭐ Total Stars: <strong id="totalStars">0</strong></span>
    </div>
</section>

<!-- Game lobby -->
<div class="lobby reveal" id="lobby">
    <button class="lobby-card lobby-card--memory" type="button" data-game="memory">
        <span class="lobby-card__emoji">🃏</span>
        <span class="lobby-card__name">Memory Match</span>
        <span class="lobby-card__hint">Find the pairs!</span>
    </button>
    <button class="lobby-card lobby-card--stars" type="button" data-game="stars">
        <span class="lobby-card__emoji">⭐</span>
        <span class="lobby-card__name">Catch the Stars</span>
        <span class="lobby-card__hint">Tap the falling stars!</span>
    </button>
    <button class="lobby-card lobby-card--mole" type="button" data-game="mole">
        <span class="lobby-card__emoji">🐹</span>
        <span class="lobby-card__name">Whack-a-Mole</span>
        <span class="lobby-card__hint">Tap the moles!</span>
    </button>
    <button class="lobby-card lobby-card--ttt" type="button" data-game="ttt">
        <span class="lobby-card__emoji">⭕</span>
        <span class="lobby-card__name">Tic-Tac-Toe</span>
        <span class="lobby-card__hint">Beat the robot!</span>
    </button>
    <button class="lobby-card lobby-card--rps" type="button" data-game="rps">
        <span class="lobby-card__emoji">✊✋✌️</span>
        <span class="lobby-card__name">Rock Paper Scissors</span>
        <span class="lobby-card__hint">Best of 5 rounds!</span>
    </button>
</div>

<!-- Game views -->
<div class="game-view" id="gameView">

    <!-- Memory Match -->
    <section class="game panel" id="game-memory">
        <div class="game__head">
            <button class="btn-back" type="button" data-back>⬅ Games</button>
            <h2 class="game__title">🃏 Memory Match</h2>
            <span class="stat-chip">Moves: <strong id="memoryMoves">0</strong></span>
        </div>
        <div class="memory-grid" id="memoryGrid"></div>
        <p class="game__result" id="memoryResult"></p>
        <button class="btn-fun" type="button" data-restart="memory">Play Again 🔄</button>
    </section>

    <!-- Catch the Stars -->
    <section class="game panel" id="game-stars">
        <div class="game__head">
            <button class="btn-back" type="button" data-back>⬅ Games</button>
            <h2 class="game__title">⭐ Catch the Stars</h2>
            <span class="stat-chip">Score: <strong id="starScore">0</strong></span>
        </div>
        <div class="hud">
            <span class="stat-chip">⏱️ <strong id="starTime">30</strong></span>
        </div>
        <div class="play-area" id="starArea"><p class="play-area__hint">Tap the stars before they fall! ⭐</p></div>
        <p class="game__result" id="starResult"></p>
        <button class="btn-fun" type="button" data-restart="stars">Play Again 🔄</button>
    </section>

    <!-- Whack-a-Mole -->
    <section class="game panel" id="game-mole">
        <div class="game__head">
            <button class="btn-back" type="button" data-back>⬅ Games</button>
            <h2 class="game__title">🐹 Whack-a-Mole</h2>
            <span class="stat-chip">Score: <strong id="moleScore">0</strong></span>
        </div>
        <div class="hud">
            <span class="stat-chip">⏱️ <strong id="moleTime">30</strong></span>
        </div>
        <div class="mole-grid" id="moleGrid"></div>
        <p class="game__result" id="moleResult"></p>
        <button class="btn-fun" type="button" data-restart="mole">Play Again 🔄</button>
    </section>

    <!-- Tic-Tac-Toe -->
    <section class="game panel" id="game-ttt">
        <div class="game__head">
            <button class="btn-back" type="button" data-back>⬅ Games</button>
            <h2 class="game__title">⭕ Tic-Tac-Toe</h2>
        </div>
        <p class="game__sub">You are <strong>😀</strong>, the robot is <strong>🤖</strong>.</p>
        <div class="ttt-board" id="tttBoard"></div>
        <p class="game__result" id="tttResult"></p>
        <button class="btn-fun" type="button" data-restart="ttt">Play Again 🔄</button>
    </section>

    <!-- Rock Paper Scissors -->
    <section class="game panel" id="game-rps">
        <div class="game__head">
            <button class="btn-back" type="button" data-back>⬅ Games</button>
            <h2 class="game__title">✊✋✌️ Rock Paper Scissors</h2>
        </div>
        <div class="hud">
            <span class="stat-chip">You: <strong id="rpsYou">0</strong></span>
            <span class="stat-chip">Robot: <strong id="rpsRobot">0</strong></span>
            <span class="stat-chip">Round: <strong id="rpsRound">1</strong>/5</span>
        </div>
        <div class="rps-arena">
            <div class="rps-choice" id="rpsYourPick">❓</div>
            <div class="rps-vs">VS</div>
            <div class="rps-choice" id="rpsRobotPick">❓</div>
        </div>
        <div class="rps-buttons">
            <button class="rps-btn" type="button" data-move="rock" aria-label="Rock">✊</button>
            <button class="rps-btn" type="button" data-move="paper" aria-label="Paper">✋</button>
            <button class="rps-btn" type="button" data-move="scissors" aria-label="Scissors">✌️</button>
        </div>
        <p class="game__result" id="rpsResult"></p>
        <button class="btn-fun" type="button" data-restart="rps">Play Again 🔄</button>
    </section>

</div>

<div class="toast" id="toast" role="status"></div>

<?php include 'includes/kids-footer.php'; ?>
