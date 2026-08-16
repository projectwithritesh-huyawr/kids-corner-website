<?php
// login.php — Premium dark glassmorphism login page
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Demo accounts — replace this block with your database check
$demoUsers = [
    'admin' => ['password' => 'admin123', 'name' => 'Captain Star'],
    'user'  => ['password' => 'user123',  'name' => 'Sunny'],
];

$error   = '';
$success = '';

// Registered users from register.php
if (isset($_SESSION['registered_users'])) {
    foreach ($_SESSION['registered_users'] as $id => $u) {
        $demoUsers[$id] = ['password' => $u['password'], 'name' => $u['name']];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = trim($_POST['identifier'] ?? '');
    $password   = $_POST['password'] ?? '';
    $remember   = isset($_POST['remember']);

    if ($identifier === '' || $password === '') {
        $error = 'Please enter your email/username and password.';
    } elseif (isset($demoUsers[$identifier])) {
        $stored = $demoUsers[$identifier]['password'];
        $valid  = strpos($stored, '$2y$') === 0
            ? password_verify($password, $stored)
            : hash_equals($stored, $password);

        if ($valid) {
            $_SESSION['logged_in'] = true;
            $_SESSION['name']      = $demoUsers[$identifier]['name'];
            $_SESSION['identifier'] = $identifier;

            if ($remember) {
                setcookie('remember_user', $identifier, time() + 30 * 86400, '/', '', false, true);
            } else {
                setcookie('remember_user', '', time() - 3600, '/', '', false, true);
            }

            header('Location: kids.php');
            exit;
        }

        $error = 'Invalid email/username or password.';
    } else {
        $error = 'Invalid email/username or password.';
    }
}

if (isset($_GET['registered'])) {
    $success = 'Account created successfully — please sign in.';
}

if (isset($_GET['login']) && $_GET['login'] === 'success') {
    $success = 'Login successful — welcome back!';
}

$prefill = trim($_POST['identifier'] ?? ($_COOKIE['remember_user'] ?? ''));
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="Sign in to your account" />
    <title>Sign In</title>
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

    <main class="page">
        <section class="card" aria-label="Login form">

            <div class="card__head">
                <div class="logo">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 2l2.4 4.8L20 8l-4 4 1 6-5-2.6L7 18l1-6-4-4 5.6-1.2z" />
                    </svg>
                </div>
                <h1 class="card__title">Welcome Back</h1>
                <p class="card__subtitle">Sign in to continue to your account</p>
            </div>

            <?php if ($error !== ''): ?>
                <div class="alert alert--error" role="alert">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                        <circle cx="12" cy="12" r="10" /><line x1="12" y1="8" x2="12" y2="12" /><line x1="12" y1="16" x2="12.01" y2="16" />
                    </svg>
                    <span><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
            <?php endif; ?>

            <?php if ($success !== ''): ?>
                <div class="alert alert--success" role="status">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" /><polyline points="22 4 12 14.01 9 11.01" />
                    </svg>
                    <span><?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
            <?php endif; ?>

            <form class="form" method="post" action="login.php" novalidate>

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
                            value="<?php echo htmlspecialchars($prefill, ENT_QUOTES, 'UTF-8'); ?>"
                        />
                    </div>
                </div>

                <div class="field">
                    <div class="field__row">
                        <label class="field__label" for="password">Password</label>
                    </div>
                    <div class="field__control">
                        <svg class="field__icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="11" width="18" height="11" rx="2" /><path d="M7 11V7a5 5 0 0 1 10 0v4" />
                        </svg>
                        <input
                            class="field__input"
                            id="password"
                            name="password"
                            type="password"
                            placeholder="••••••••"
                            autocomplete="current-password"
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

                <div class="form__meta">
                    <label class="checkbox">
                        <input type="checkbox" name="remember" value="1" />
                        <span class="checkbox__box">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12" />
                            </svg>
                        </span>
                        <span class="checkbox__label">Remember me</span>
                    </label>
                    <a class="link link--forgot" href="forgot-password.php">Forgot password?</a>
                </div>

                <button class="btn" type="submit">
                    <span>Sign In</span>
                    <svg class="btn__arrow" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="5" y1="12" x2="19" y2="12" /><polyline points="12 5 19 12 12 19" />
                    </svg>
                </button>

            </form>

            <div class="divider"><span>or</span></div>

            <p class="card__foot">
                Don't have an account?
                <a class="link" href="register.php">Register</a>
            </p>

            <p class="card__hint">Demo accounts: <strong>admin / admin123</strong> &middot; <strong>user / user123</strong></p>

        </section>
    </main>

    <script src="js/main.js" defer></script>
</body>
</html>
