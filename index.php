<?php
// index.php - front controller / router for App Engine.

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';

// Normalize: remove trailing slash except for root
$uri = rtrim($uri, '/');
if ($uri === '') {
    $uri = '/';
}

switch ($uri) {
    // --- Login / landing ---
    case '/':
    case '/login':
    case '/login.php':
        require __DIR__ . '/login.php';
        break;

    // --- Student portal ---
    case '/student':
    case '/student.php':
        require __DIR__ . '/student.php';
        break;

    // --- Professor portal ---
    case '/professor':
    case '/professor.php':
        require __DIR__ . '/professor.php';
        break;

    // --- All projects list ---
    case '/projects':
    case '/projects.php':
        require __DIR__ . '/projects.php';
        break;

    // --- Create project page ---
    case '/create-proj':
    case '/create-proj.php':
        require __DIR__ . '/create-proj.php';
        break;

    // --- Project details ---
    case '/project_details':
    case '/project_details.php':
        require __DIR__ . '/project_details.php';
        break;

    // --- Professor details ---
    case '/prof_details':
    case '/prof_details.php':
        require __DIR__ . '/prof_details.php';
        break;

    // --- Fallback: if a .php file exists, serve it directly ---
    default:
        $path = __DIR__ . $uri;
        if (str_ends_with($uri, '.php') && file_exists($path)) {
            require $path;
        } else {
            http_response_code(404);
            ?>
            <!doctype html>
            <html>
            <head><meta charset="utf-8"><title>404 Not Found</title></head>
            <body>
                <h1>404 Not Found</h1>
                <p>No route found for <code><?= htmlspecialchars($uri) ?></code>.</p>
                <p><a href="/login">Back to login</a></p>
            </body>
            </html>
            <?php
        }
        break;
}
