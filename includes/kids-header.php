<?php
// includes/kids-header.php — Shared layout for Kids World section pages
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Protect section pages — must be logged in
if (empty($_SESSION['logged_in'])) {
    header('Location: login.php');
    exit;
}

// Logout
if (isset($_GET['logout'])) {
    $_SESSION = [];
    session_destroy();
    header('Location: login.php');
    exit;
}

// Child's name
$childName = trim($_SESSION['name'] ?? '');
if ($childName === '') {
    $childName = trim($_SESSION['identifier'] ?? '');
}
if ($childName === '') {
    $childName = 'Friend';
}

$pageTitle    = $pageTitle ?? 'Kids World';
$sectionTitle = $sectionTitle ?? 'Kids World';
$extraCss     = $extraCss ?? [];
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="Kids World — fun activities for kids" />
    <title><?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@600;700;800&family=Quicksand:wght@500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="css/kids.css" />
    <link rel="stylesheet" href="css/sections.css" />
    <?php foreach ($extraCss as $css): ?>
        <link rel="stylesheet" href="css/<?php echo htmlspecialchars($css, ENT_QUOTES, 'UTF-8'); ?>.css" />
    <?php endforeach; ?>
</head>
<body>

    <!-- Fun loading screen -->
    <div class="loader" id="loader" role="status" aria-label="Loading">
        <div class="loader__ring"><span class="loader__star">🌟</span></div>
        <p class="loader__text">Loading fun...</p>
    </div>

    <!-- Bright sky background -->
    <div class="sky" id="sky" aria-hidden="true">
        <div class="sky__gradient"></div>

        <div class="layer" data-depth="0.15">
            <div class="floaty floaty--sun">
                <svg width="120" height="120" viewBox="0 0 120 120">
                    <g class="sun-rays">
                        <path d="M60 8v14M60 98v14M8 60h14M98 60h14M25 25l10 10M85 85l10 10M95 25l-10 10M35 85l-10 10" stroke="#ffd93d" stroke-width="8" stroke-linecap="round"/>
                    </g>
                    <circle cx="60" cy="60" r="34" fill="#ffd93d" />
                    <circle cx="60" cy="60" r="34" fill="url(#sunFaceSec)"/>
                    <defs>
                        <radialGradient id="sunFaceSec" cx="0.35" cy="0.3" r="0.9">
                            <stop offset="0" stop-color="#fff1b8"/>
                            <stop offset="1" stop-color="#ffd93d"/>
                        </radialGradient>
                    </defs>
                    <circle cx="50" cy="56" r="4" fill="#5b3a00"/>
                    <circle cx="70" cy="56" r="4" fill="#5b3a00"/>
                    <path d="M48 68 Q60 78 72 68" stroke="#5b3a00" stroke-width="4" fill="none" stroke-linecap="round"/>
                    <circle cx="44" cy="66" r="5" fill="#ff8fab"/>
                    <circle cx="76" cy="66" r="5" fill="#ff8fab"/>
                </svg>
            </div>
        </div>

        <div class="layer" data-depth="0.35">
            <div class="floaty floaty--cloud">
                <svg width="150" height="90" viewBox="0 0 150 90">
                    <g fill="#ffffff" opacity="0.92">
                        <ellipse cx="45" cy="62" rx="42" ry="26"/>
                        <ellipse cx="95" cy="58" rx="34" ry="22"/>
                        <ellipse cx="68" cy="40" rx="32" ry="24"/>
                    </g>
                    <circle cx="60" cy="55" r="4" fill="#7a6bb8"/>
                    <circle cx="78" cy="55" r="4" fill="#7a6bb8"/>
                    <path d="M60 64 Q69 72 78 64" stroke="#7a6bb8" stroke-width="3.5" fill="none" stroke-linecap="round"/>
                </svg>
            </div>
        </div>

        <div class="layer" data-depth="0.5">
            <div class="floaty floaty--cloud3">
                <svg width="180" height="100" viewBox="0 0 180 100">
                    <g fill="#ffffff" opacity="0.95">
                        <ellipse cx="52" cy="70" rx="50" ry="30"/>
                        <ellipse cx="112" cy="66" rx="42" ry="26"/>
                        <ellipse cx="80" cy="46" rx="38" ry="28"/>
                    </g>
                    <circle cx="70" cy="64" r="4" fill="#7a6bb8"/>
                    <circle cx="92" cy="64" r="4" fill="#7a6bb8"/>
                    <path d="M70 74 Q81 82 92 74" stroke="#7a6bb8" stroke-width="3.5" fill="none" stroke-linecap="round"/>
                </svg>
            </div>
        </div>

        <div class="layer" data-depth="0.45">
            <div class="floaty floaty--star"><span class="star">⭐</span></div>
        </div>
        <div class="layer" data-depth="0.6">
            <div class="floaty floaty--star2"><span class="star">🌟</span></div>
        </div>

        <div class="layer" data-depth="0.55">
            <div class="floaty floaty--balloon">
                <div class="balloon balloon--pink"></div>
            </div>
        </div>
        <div class="layer" data-depth="0.4">
            <div class="floaty floaty--balloon2">
                <div class="balloon balloon--blue"></div>
            </div>
        </div>

        <div class="layer" data-depth="0.7">
            <div class="floaty floaty--shape"><span class="shape shape--square"></span></div>
        </div>
        <div class="layer" data-depth="0.65">
            <div class="floaty floaty--shape3"><span class="shape shape--circle"></span></div>
        </div>

        <div id="particles" class="particles"></div>
    </div>

    <!-- Mouse-follow glow -->
    <div class="cursor-glow" id="cursorGlow" aria-hidden="true"></div>

    <!-- Confetti layer -->
    <div class="confetti" id="confetti" aria-hidden="true"></div>

    <main class="content content--section">

        <header class="topbar topbar--section glass">
            <a class="btn-back" href="kids.php">⬅ Back to Home</a>
            <div class="brand brand--section"><span class="brand__emoji">🌈</span> <span class="brand__text">RITESH KIDS CORNER</span></div>
            <div class="topbar__right">
                <span class="topbar__hi">Hi, <strong><?php echo htmlspecialchars($childName, ENT_QUOTES, 'UTF-8'); ?></strong>! 👋</span>
                <a class="btn-logout" href="kids.php?logout=1">Logout</a>
            </div>
        </header>
