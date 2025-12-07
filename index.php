<?php
// index.php - front controller / router for App Engine.

// Figure out what path was requested, e.g. "/student.php", "/professor.php"
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';

// Normalize (optional): remove trailing slashes
$uri = rtrim($uri, '/');
if ($uri === '') {
    $uri = '/';
}

switch ($uri) {
    case '/student.php':
    case '/student':
        require __DIR__ . '/student.php';
        break;

    case '/professor.php':
    case '/professor':
        require __DIR__ . '/professor.php';
        break;

    // Default: show login/landing page
    default:
        // If you have a real login.php in this branch:
        if (file_exists(__DIR__ . '/login.php')) {
            require __DIR__ . '/login.php';
        } else {
            // Simple placeholder so the app works even without login.php
            ?>
            <!doctype html>
            <html>
            <head>
                <meta charset="utf-8">
                <title>HooResearches</title>
            </head>
            <body>
                <h1>HooResearches</h1>
                <p>Select a portal:</p>
                <ul>
                    <li><a href="/student.php">Student Portal</a></li>
                    <li><a href="/professor.php">Professor Portal</a></li>
                </ul>
            </body>
            </html>
            <?php
        }
        break;
}
