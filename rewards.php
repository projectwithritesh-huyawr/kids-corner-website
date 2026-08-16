<?php
// rewards.php — Star rewards & badges for kids
declare(strict_types=1);
$pageTitle    = 'My Rewards — Kids World';
$sectionTitle = 'My Rewards';
$extraJs      = ['rewards'];
include 'includes/kids-header.php';
?>

<section class="section-head reveal">
    <h1 class="section-head__title">My Rewards! ⭐</h1>
    <p class="section-head__sub">Earn stars by playing games and solving puzzles!</p>
</section>

<section class="rewards reveal">
    <div class="rewards-total panel">
        <div class="rewards-total__star" id="totalStarDisplay">⭐</div>
        <div class="rewards-total__num" id="totalStars">0</div>
        <div class="rewards-total__label">Total Stars</div>
    </div>

    <div class="badges">
        <div class="badge badge--bronze" data-needed="10">
            <span class="badge__emoji">🥉</span>
            <span class="badge__name">Bronze Star</span>
            <span class="badge__need">10 stars</span>
        </div>
        <div class="badge badge--silver" data-needed="25">
            <span class="badge__emoji">🥈</span>
            <span class="badge__name">Silver Star</span>
            <span class="badge__need">25 stars</span>
        </div>
        <div class="badge badge--gold" data-needed="50">
            <span class="badge__emoji">🥇</span>
            <span class="badge__name">Gold Superstar</span>
            <span class="badge__need">50 stars</span>
        </div>
    </div>
</section>

<section class="scores panel reveal">
    <h2 class="scores__title">My Best Scores 🏆</h2>
    <div class="scores__list" id="scoresList"></div>
    <p class="scores__hint">Finish a game to earn your first stars!</p>
</section>

<div class="toast" id="toast" role="status"></div>

<?php include 'includes/kids-footer.php'; ?>
