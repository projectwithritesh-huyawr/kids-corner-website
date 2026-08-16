<?php
// puzzles.php — Puzzles for kids (sliding tiles + spot the odd one)
declare(strict_types=1);
$pageTitle    = 'Puzzles — Kids World';
$sectionTitle = 'Puzzle Corner';
$extraCss     = ['puzzles'];
$extraJs      = ['puzzles'];
include 'includes/kids-header.php';
?>

<section class="section-head reveal">
    <h1 class="section-head__title">Let's Solve Puzzles! 🧩</h1>
    <p class="section-head__sub">Slide the tiles or find the odd emoji — can you beat the timer?</p>
    <div class="hud">
        <span class="stat-chip">⭐ Total Stars: <strong id="totalStars">0</strong></span>
    </div>
</section>

<!-- Puzzle lobby -->
<div class="puzzle-lobby reveal" id="puzzleLobby">
    <button class="puzzle-lobby-card" type="button" data-puzzle="tiles">
        <span class="puzzle-lobby-card__emoji">🧩</span>
        <span class="puzzle-lobby-card__name">Slide the Tiles</span>
        <span class="puzzle-lobby-card__hint">Slide emojis to complete the picture!</span>
    </button>
    <button class="puzzle-lobby-card" type="button" data-puzzle="odd">
        <span class="puzzle-lobby-card__emoji">🕵️</span>
        <span class="puzzle-lobby-card__name">Spot the Odd One</span>
        <span class="puzzle-lobby-card__hint">Find the different emoji fast!</span>
    </button>
</div>

<!-- Sliding tiles puzzle -->
<section class="puzzle-view panel reveal" id="puzzle-tiles">
    <div class="game__head">
        <button class="btn-back" type="button" data-back>⬅ Puzzles</button>
        <h2 class="game__title">🧩 Slide the Tiles</h2>
    </div>

    <div class="tile-controls">
        <div class="tile-options">
            <button class="chip-btn is-active" type="button" data-size="3">Easy (3x3)</button>
            <button class="chip-btn" type="button" data-size="4">Hard (4x4)</button>
        </div>
        <label class="chip-toggle">
            <input type="checkbox" id="fastMode" />
            <span>⚡ Fast Mode</span>
        </label>
    </div>

    <div class="hud">
        <span class="stat-chip">Moves: <strong id="tileMoves">0</strong></span>
        <span class="stat-chip">⏱️ <strong id="tileTime">0</strong></span>
    </div>

    <div class="tile-board" id="tileBoard"></div>
    <p class="game__result" id="tileResult"></p>

    <div class="tile-actions">
        <button class="btn-fun" id="shuffleBtn" type="button">Shuffle 🔀</button>
        <button class="btn-fun btn-fun--green" id="tileNewBtn" type="button">New Puzzle ✨</button>
    </div>
</section>

<!-- Spot the odd one -->
<section class="puzzle-view panel reveal" id="puzzle-odd">
    <div class="game__head">
        <button class="btn-back" type="button" data-back>⬅ Puzzles</button>
        <h2 class="game__title">🕵️ Spot the Odd One</h2>
    </div>

    <div class="hud">
        <span class="stat-chip">Round: <strong id="oddRound">1</strong>/3</span>
        <span class="stat-chip">Score: <strong id="oddScore">0</strong></span>
    </div>

    <div class="odd-board" id="oddBoard"></div>
    <p class="game__result" id="oddResult"></p>

    <button class="btn-fun" id="oddStartBtn" type="button">Start 🚀</button>
</section>

<div class="toast" id="toast" role="status"></div>

<?php include 'includes/kids-footer.php'; ?>
