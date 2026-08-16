<?php
// music.php — Music studio for kids (Web Audio, no mp3 needed)
declare(strict_types=1);
$pageTitle    = 'Music — Kids World';
$sectionTitle = 'Music Studio';
$extraCss     = ['music'];
$extraJs      = ['music'];
include 'includes/kids-header.php';
?>

<section class="section-head reveal">
    <h1 class="section-head__title">Let's Make Music! 🎵</h1>
    <p class="section-head__sub">Tap the keys and drum pads to make your own song!</p>
    <div class="hud">
        <span class="stat-chip">🔊 Volume <input class="volume-range" id="volume" type="range" min="0" max="100" value="70" aria-label="Volume" /></span>
    </div>
</section>

<section class="music-panel panel reveal">
    <div class="music-block">
        <h2 class="music-block__title">🎹 Piano Keys</h2>
        <div class="piano" id="piano" role="group" aria-label="Piano keys"></div>
    </div>

    <div class="music-block">
        <h2 class="music-block__title">🥁 Drum Pads</h2>
        <div class="drum-pads">
            <button class="drum-pad drum-pad--kick" type="button" data-drum="kick">🥁<span>Kick</span></button>
            <button class="drum-pad drum-pad--snare" type="button" data-drum="snare">🎛️<span>Snare</span></button>
            <button class="drum-pad drum-pad--hat" type="button" data-drum="hat">🔔<span>Hi-Hat</span></button>
            <button class="drum-pad drum-pad--clap" type="button" data-drum="clap">👏<span>Clap</span></button>
        </div>
    </div>

    <div class="music-block">
        <h2 class="music-block__title">🎶 Play a Tune</h2>
        <div class="tune-buttons">
            <button class="btn-fun" id="tuneHappy" type="button">Happy Tune 🎵</button>
            <button class="btn-fun btn-fun--blue" id="tuneStar" type="button">Star Song ⭐</button>
            <button class="btn-fun btn-fun--green" id="stopBtn" type="button">Stop ⏹️</button>
        </div>
        <div class="note-walk" id="noteWalk" aria-hidden="true">
            <span class="note">🎵</span><span class="note">🎶</span><span class="note">🎵</span>
        </div>
    </div>
</section>

<div class="toast" id="toast" role="status"></div>

<?php include 'includes/kids-footer.php'; ?>
