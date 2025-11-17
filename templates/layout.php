<?php
use Service\Auth;
?><!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Student Disciplinary System</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
    <script src="<?= BASE_URL ?>/assets/js/theme.js" defer></script>
</head>
<body>
    <header class="site-header">
        <div class="brand">
            <a class="logo" href="<?= BASE_URL ?>/?controller=student&action=index">Disciplinary System</a>
            <small class="tag">Records & Actions</small>
        </div>
        <nav class="site-nav">
            <a class="nav-link" href="<?= BASE_URL ?>/?controller=student&action=index">Students</a>
            <a class="nav-link" href="<?= BASE_URL ?>/?controller=incident&action=index">Incidents</a>
            <?php if (Auth::check()): ?>
                <span class="user">Logged in as <?= htmlspecialchars(Auth::user()['FullName'] ?? Auth::user()['Username']) ?></span>
                <a class="nav-link" href="<?= BASE_URL ?>/?controller=auth&action=logout">Logout</a>
            <?php else: ?>
                <a class="nav-link" href="<?= BASE_URL ?>/?controller=auth&action=login">Login</a>
            <?php endif; ?>
            <button id="theme-toggle" class="theme-toggle" aria-label="Toggle theme">🌓</button>
        </nav>
    </header>

    <main class="container">
        <?= $content ?? '' ?>
    </main>

    <footer class="site-footer">
        <small>© <?= date('Y') ?> Disciplinary System</small>
    </footer>
</body>
</html>