<?php
// drawing.php — Drawing pad for kids
declare(strict_types=1);
$pageTitle    = 'Drawing — Kids World';
$sectionTitle = 'Drawing Studio';
$extraCss     = ['drawing'];
$extraJs      = ['drawing'];
include 'includes/kids-header.php';
?>

<section class="section-head reveal">
    <h1 class="section-head__title">Let's Draw! 🎨</h1>
    <p class="section-head__sub">Pick a color, choose a brush and draw something awesome!</p>
</section>

<section class="draw-panel panel reveal">
    <div class="draw-toolbar">
        <div class="draw-toolbar__group" role="group" aria-label="Colors">
            <span class="draw-toolbar__label">Colors</span>
            <div class="swatches" id="swatches"></div>
        </div>
        <div class="draw-toolbar__group">
            <span class="draw-toolbar__label">Brush <output id="brushValue">8</output></span>
            <input class="brush-range" id="brushRange" type="range" min="2" max="40" value="8" aria-label="Brush size" />
        </div>
        <div class="draw-toolbar__group">
            <button class="btn-fun btn-fun--small" id="eraserBtn" type="button">🧽 Eraser</button>
            <button class="btn-fun btn-fun--small btn-fun--green" id="undoBtn" type="button">↩️ Undo</button>
            <button class="btn-fun btn-fun--small btn-fun--blue" id="clearBtn" type="button">🗑️ Clear</button>
            <button class="btn-fun btn-fun--small btn-fun--green" id="saveBtn" type="button">⬇️ Save</button>
        </div>
    </div>

    <div class="canvas-wrap">
        <canvas id="drawCanvas" aria-label="Drawing canvas"></canvas>
    </div>

    <p class="draw-hint">Draw with your mouse or finger! ✏️</p>
</section>

<div class="toast" id="toast" role="status"></div>

<?php include 'includes/kids-footer.php'; ?>
