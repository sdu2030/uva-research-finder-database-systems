<?php
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Normalize empty path to "/"
if ($path === '' || $path === null) {
    $path = '/';
}

switch ($path) {

    // ---------- AUTH / LANDING ----------
    case '/':
    case '/login.php':
        require 'login.php';
        break;

    // ---------- STUDENT ----------
    case '/student.php':
        require 'student.php';
        break;

    case '/projects.php':
        require 'projects.php';
        break;

    case '/project_details.php':
        require 'project_details.php';
        break;

    // ---------- PROFESSOR ----------
    case '/professor.php':
        require 'professor.php';
        break;

    case '/prof_details.php':
        require 'prof_details.php';
        break;

    // ---------- CREATE / UPDATE ----------
    case '/create_project.php':
        require 'create_project.php';
        break;

    case '/update_project.php':
        require 'update_project.php';
        break;  
    
    case '/create_person.php':
        require 'create_person.php';
        break;
    // ---------- FALLBACK ----------
    default:
        http_response_code(404);
        echo "<h2>404 - Page Not Found</h2>";
        echo "<p>The page <code>$path</code> does not exist.</p>";
        break;
}
?>
