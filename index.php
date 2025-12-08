<?php
switch (@parse_url($_SERVER['REQUEST_URI'])['path']) {
    case '/':                   // URL (without file name) to a default screen
        require 'login.php';
        break;
    case '/login.php':     // if you plan to also allow a URL with the file name
        require 'login.php';
        break;
    case '/create_proj.php':
        require 'create_proj.php';
        break;
    default:
        http_response_code(404);
        exit('Not Found');
}
?>