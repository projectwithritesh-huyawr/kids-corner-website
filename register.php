<?php
// register.php — Premium dark glassmorphism registration page
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Demo accounts that already exist in login.php
$demoUsers = [
    'admin' => ['password' => 'admin123', 'name' => 'Administrator'],
    'user'  => ['password' => 'user123',  'name' => 'Demo User'],
];

// Session-based registered users — replace with a database
if (!isset($_SESSION['registered_users'])) {
    $_SESSION['registered_users'] = [];
}

$error   = '';
$prefill = ['name' => '', 'identifier' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name       = trim($_POST['name'] ?? '');
    $identifier = trim($_POST['identifier'] ?? '');
    $password   = $_POST['password'] ?? '';
    $confirm    = $_POST['confirm'] ?? '';
    $terms      = isset($_POST['terms']);

    $prefill['name']       = $name;
    $prefill['identifier'] = $identifier;

    if ($name === '' || $identifier === '' || $password === '' || $confirm === '') {
        $error = 'Please fill in all fields.';
    } elseif (!preg_match('/^[a-zA-Z0-9._@-]+$/', $identifier)) {
        $error = 'Username/email contains invalid characters.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } elseif (isset($demoUsers[$identifier]) || isset($_SESSION['registered_users'][$identifier])) {
        $error = 'That username/email is already taken.';
    } elseif (!$terms) {
        $error = 'Please accept the terms and conditions.';
    } else {
        $_SESSION['registered_users'][$identifier] = [
            'name'     => $name,
            'password' => password_hash($password, PASSWORD_DEFAULT),
        ];
        header('Location: login.php?registered=1');
        exit;
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="Create your account" />
    <title>Create Account</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Sora:wght@600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="css/style.css" />
</head>
<body>

    <!-- Animated background -->
    <div class="bg" aria-hidden="true">
        <div class="bg-gradient"></div>
        <div class="orb orb--one"></div>
        <div class="orb orb--two"></div>
        <div class="orb orb--three"></div>
        <div class="bg-grid"></div>
    </div>

    <main class="page page--tall">
        <section class="card card--reg" aria-label="Registration form">

            <div class="card__head">
                <div class="logo">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" /><circle cx="12" cy="7" r="4" />
                    </svg>
                </div>
                <h1 class="card__title">Create Account</h1>
                <p class="card__subtitle">Join us and get started in minutes</p>
            </div>

            <?php if ($error !== ''): ?>
                <div class="alert alert--error" role="alert">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                        <circle cx="12" cy="12" r="10" /><line x1="12" y1="8" x2="12" y2="12" /><line x1="12" y1="16" x2="12.01" y2="16" />
                    </svg>
                    <span><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
            <?php endif; ?>

            <form class="form form--reg" method="post" action="register.php" novalidate>

                <div class="field">
                    <label class="field__label" for="name">Full Name</label>
                    <div class="field__control">
                        <svg class="field__icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" /><circle cx="12" cy="7" r="4" />
                        </svg>
                        <input
                            class="field__input"
                            id="name"
                            name="name"
                            type="text"
                            placeholder="Your name"
                            autocomplete="name"
                            required
                            value="<?php echo htmlspecialchars($prefill['name'], ENT_QUOTES, 'UTF-8'); ?>"
                        />
                    </div>
                </div>

                <div class="field">
                    <label class="field__label" for="identifier">Email or Username</label>
                    <div class="field__control">
                        <svg class="field__icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="5" width="18" height="14" rx="2" /><path d="M3 7l9 6 9-6" />
                        </svg>
                        <input
                            class="field__input"
                            id="identifier"
                            name="identifier"
                            type="text"
                            placeholder="you@example.com"
                            autocomplete="username"
                            required
                            value="<?php echo htmlspecialchars($prefill['identifier'], ENT_QUOTES, 'UTF-8'); ?>"
                        />
                    </div>
                </div>

                <div class="field">
                    <label class="field__label" for="password">Password</label>
                    <div class="field__control">
                        <svg class="field__icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="11" width="18" height="11" rx="2" /><path d="M7 11V7a5 5 0 0 1 10 0v4" />
                        </svg>
                        <input
                            class="field__input"
                            id="password"
                            name="password"
                            type="password"
                            placeholder="At least 6 characters"
                            autocomplete="new-password"
                            required
                        />
                        <button class="field__toggle" id="togglePassword" type="button" aria-label="Show password" aria-pressed="false" title="Show password">
                            <svg class="eye eye--open" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" /><circle cx="12" cy="12" r="3" />
                            </svg>
                            <svg class="eye eye--slash" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94" /><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19" /><line x1="1" y1="1" x2="23" y2="23" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="field">
                    <label class="field__label" for="confirm">Confirm Password</label>
                    <div class="field__control">
                        <svg class="field__icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="11" width="18" height="11" rx="2" /><path d="M7 11V7a5 5 0 0 1 10 0v4" />
                        </svg>
                        <input
                            class="field__input"
                            id="confirm"
                            name="confirm"
                            type="password"
                            placeholder="Repeat your password"
                            autocomplete="new-password"
                            required
                        />
                        <button class="field__toggle" id="toggleConfirm" type="button" aria-label="Show password" aria-pressed="false" title="Show password">
                            <svg class="eye eye--open" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" /><circle cx="12" cy="12" r="3" />
                            </svg>
                            <svg class="eye eye--slash" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94" /><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19" /><line x1="1" y1="1" x2="23" y2="23" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="form__meta">
                    <label class="checkbox terms">
                        <input type="checkbox" name="terms" value="1" />
                        <span class="checkbox__box">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12" />
                            </svg>
                        </span>
                        <span class="checkbox__label">I agree to the <a class="link" href="#" onclick="return false;">Terms &amp; Conditions</a></span>
                    </label>
                </div>

                <button class="btn" type="submit">
                    <span>Create Account</span>
                    <svg class="btn__arrow" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="5" y1="12" x2="19" y2="12" /><polyline points="12 5 19 12 12 19" />
                    </svg>
                </button>

            </form>

            <div class="divider"><span>or</span></div>

            <p class="card__foot">
                Already have an account?
                <a class="link" href="login.php">Sign in</a>
            </p>

        </section>
    </main>

    <script src="js/main.js" defer></script>
</body>
</html>
