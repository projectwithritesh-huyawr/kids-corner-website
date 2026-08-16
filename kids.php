<?php
// kids.php — Kids World: a fun, colorful 3D animated homepage for children
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Protect this page — must be logged in
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

// Show the logged-in child's name
$childName = trim($_SESSION['name'] ?? '');
if ($childName === '') {
    $childName = trim($_SESSION['identifier'] ?? '');
}
if ($childName === '') {
    $childName = 'Friend';
}

$sections = [
    'Games'      => ['emoji' => '🎮', 'class' => 'card--games',   'url' => 'games.php'],
    'Drawing'    => ['emoji' => '🎨', 'class' => 'card--drawing', 'url' => 'drawing.php'],
    'Music'      => ['emoji' => '🎵', 'class' => 'card--music',   'url' => 'music.php'],
    'Puzzles'    => ['emoji' => '🧩', 'class' => 'card--puzzles', 'url' => 'puzzles.php'],
    'My Rewards' => ['emoji' => '⭐', 'class' => 'card--rewards', 'url' => 'rewards.php'],
];
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="Kids World — a fun place for kids!" />
    <title>Kids World 🌈</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@600;700;800&family=Quicksand:wght@500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="css/kids.css" />
</head>
<body>

    <!-- Fun loading screen -->
    <div class="loader" id="loader" role="status" aria-label="Loading">
        <div class="loader__ring">
            <span class="loader__star">🌟</span>
        </div>
        <p class="loader__text">Loading fun...</p>
    </div>

    <!-- Bright sky background -->
    <div class="sky" id="sky" aria-hidden="true">
        <div class="sky__gradient"></div>

        <!-- Sun -->
        <div class="layer" data-depth="0.15">
            <div class="floaty floaty--sun">
                <svg width="130" height="130" viewBox="0 0 120 120">
                    <g class="sun-rays">
                        <path d="M60 8v14M60 98v14M8 60h14M98 60h14M25 25l10 10M85 85l10 10M95 25l-10 10M35 85l-10 10" stroke="#ffd93d" stroke-width="8" stroke-linecap="round"/>
                    </g>
                    <circle cx="60" cy="60" r="34" fill="#ffd93d" />
                    <circle cx="60" cy="60" r="34" fill="url(#sunFace)"/>
                    <defs>
                        <radialGradient id="sunFace" cx="0.35" cy="0.3" r="0.9">
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

        <!-- Clouds -->
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

        <div class="layer" data-depth="0.25">
            <div class="floaty floaty--cloud2">
                <svg width="110" height="66" viewBox="0 0 110 66">
                    <g fill="#ffffff" opacity="0.9">
                        <ellipse cx="34" cy="46" rx="30" ry="19"/>
                        <ellipse cx="72" cy="43" rx="26" ry="16"/>
                        <ellipse cx="52" cy="30" rx="24" ry="18"/>
                    </g>
                    <circle cx="47" cy="41" r="3" fill="#8a7cc0"/>
                    <circle cx="62" cy="41" r="3" fill="#8a7cc0"/>
                    <path d="M47 49 Q55 55 62 49" stroke="#8a7cc0" stroke-width="3" fill="none" stroke-linecap="round"/>
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

        <!-- Stars -->
        <div class="layer" data-depth="0.45">
            <div class="floaty floaty--star"><span class="star">⭐</span></div>
        </div>
        <div class="layer" data-depth="0.6">
            <div class="floaty floaty--star2"><span class="star">🌟</span></div>
        </div>
        <div class="layer" data-depth="0.3">
            <div class="floaty floaty--star3"><span class="star">✨</span></div>
        </div>

        <!-- Balloons -->
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

        <!-- Colorful shapes -->
        <div class="layer" data-depth="0.7">
            <div class="floaty floaty--shape"><span class="shape shape--square"></span></div>
        </div>
        <div class="layer" data-depth="0.5">
            <div class="floaty floaty--shape2"><span class="shape shape--triangle"></span></div>
        </div>
        <div class="layer" data-depth="0.65">
            <div class="floaty floaty--shape3"><span class="shape shape--circle"></span></div>
        </div>

        <!-- Rainbow -->
        <div class="layer" data-depth="0.2">
            <div class="floaty floaty--rainbow">
                <svg width="120" height="60" viewBox="0 0 120 60">
                    <path d="M10 60 a50 50 0 0 1 100 0" fill="none" stroke="#ff6b6b" stroke-width="10"/>
                    <path d="M22 60 a38 38 0 0 1 76 0" fill="none" stroke="#ffd166" stroke-width="10"/>
                    <path d="M34 60 a26 26 0 0 1 52 0" fill="none" stroke="#06d6a0" stroke-width="10"/>
                    <path d="M46 60 a14 14 0 0 1 28 0" fill="none" stroke="#4d96ff" stroke-width="10"/>
                </svg>
            </div>
        </div>

        <div id="particles" class="particles"></div>
    </div>

    <!-- Mouse-follow glow -->
    <div class="cursor-glow" id="cursorGlow" aria-hidden="true"></div>

    <!-- Confetti layer -->
    <div class="confetti" id="confetti" aria-hidden="true"></div>

    <!-- Main content -->
    <main class="content" id="content">

        <header class="topbar glass">
            <div class="brand"><span class="brand__emoji">🌈</span> RITESH KIDS CORNER</div>
            <a class="btn-logout" href="kids.php?logout=1">Logout</a>
        </header>

        <section class="hero">
            <div class="hero__text">
                <p class="hero__hi">Hi, <span class="hero__name" id="childName"><?php echo htmlspecialchars($childName, ENT_QUOTES, 'UTF-8'); ?></span>! 👋</p>
                <h1 class="hero__title">Welcome to <span class="hero__title--grad">Kids World!</span> 🎉</h1>
                <p class="hero__sub">Pick something fun below and let's play together!</p>
                <a class="btn-cta" href="#activities">Let's Play! 🎈</a>
            </div>

            <div class="hero__scene scene">
                <div class="scene__plane">
                    <div class="char char--buddy" aria-hidden="true">
                        <svg width="150" height="150" viewBox="0 0 120 120">
                            <ellipse cx="60" cy="104" rx="26" ry="8" fill="rgba(43,43,94,0.15)"/>
                            <ellipse cx="60" cy="92" rx="40" ry="44" fill="url(#buddyBody)"/>
                            <circle cx="44" cy="80" r="5" fill="#2b2b5e"/>
                            <circle cx="76" cy="80" r="5" fill="#2b2b5e"/>
                            <path d="M48 96 Q60 108 72 96" stroke="#2b2b5e" stroke-width="4" fill="none" stroke-linecap="round"/>
                            <circle cx="40" cy="92" r="7" fill="#ff8fab" opacity="0.9"/>
                            <circle cx="80" cy="92" r="7" fill="#ff8fab" opacity="0.9"/>
                            <path d="M96 74 q14 0 14 14 q0 14 -14 14" fill="#ff5e9c" stroke="#ff5e9c" stroke-width="2" stroke-linecap="round">
                                <animateTransform attributeName="transform" type="rotate" values="0 96 88;-12 96 88;0 96 88" dur="1.4s" repeatCount="indefinite"/>
                            </path>
                            <defs>
                                <radialGradient id="buddyBody" cx="0.35" cy="0.3" r="1">
                                    <stop offset="0" stop-color="#ffd166"/>
                                    <stop offset="1" stop-color="#ff9f45"/>
                                </radialGradient>
                            </defs>
                        </svg>
                    </div>

                    <div class="char char--star" aria-hidden="true">
                        <svg width="120" height="120" viewBox="0 0 100 100">
                            <polygon points="50,6 61,36 93,38 68,57 77,89 50,70 23,89 32,57 7,38 39,36" fill="#ffd93d" stroke="#f5b700" stroke-width="3" stroke-linejoin="round"/>
                            <circle cx="40" cy="46" r="4" fill="#5b3a00"/>
                            <circle cx="60" cy="46" r="4" fill="#5b3a00"/>
                            <path d="M40 58 Q50 67 60 58" stroke="#5b3a00" stroke-width="3.5" fill="none" stroke-linecap="round"/>
                        </svg>
                    </div>

                    <div class="char char--cloud" aria-hidden="true">
                        <svg width="120" height="80" viewBox="0 0 120 80">
                            <g fill="#ffffff" opacity="0.95">
                                <ellipse cx="40" cy="56" rx="36" ry="22"/>
                                <ellipse cx="82" cy="52" rx="30" ry="19"/>
                                <ellipse cx="60" cy="36" rx="28" ry="20"/>
                            </g>
                            <circle cx="52" cy="50" r="3.5" fill="#7a6bb8"/>
                            <circle cx="68" cy="50" r="3.5" fill="#7a6bb8"/>
                            <path d="M52 58 Q60 65 68 58" stroke="#7a6bb8" stroke-width="3" fill="none" stroke-linecap="round"/>
                        </svg>
                    </div>

                    <div class="char char--ball" aria-hidden="true">
                        <span class="bounce-ball"></span>
                    </div>
                </div>
            </div>
        </section>

        <section class="activities" id="activities">
            <h2 class="activities__title">Choose an Activity 🌟</h2>
            <p class="activities__sub">Tap a card to start playing!</p>

            <div class="grid">
                <?php foreach ($sections as $name => $s): ?>
                    <a class="fun-card reveal <?php echo htmlspecialchars($s['class'], ENT_QUOTES, 'UTF-8'); ?>"
                       href="<?php echo htmlspecialchars($s['url'], ENT_QUOTES, 'UTF-8'); ?>"
                       aria-label="<?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?>">
                        <span class="fun-card__emoji"><?php echo $s['emoji']; ?></span>
                        <span class="fun-card__name"><?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </section>

        <footer class="foot">
            <p>Made with <span class="foot__heart">❤️</span> for awesome kids</p>
        </footer>

    </main>

    <script src="js/kids.js" defer></script>
</body>
</html>
