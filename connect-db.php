<?php
// connect-db.php
// Shared DB connection for all pages (login, student, professor, projects, etc.)

// Read settings from environment variables (set in app.yaml)
$db_user = getenv('DB_USER') ?: 'cs4750-research-page';
$db_pass = getenv('DB_PASS') ?: 'researchDB_25';
$db_name = getenv('DB_NAME') ?: 'research_info';
$instance_connection_name = getenv('INSTANCE_CONNECTION_NAME') ?: 'cs4750-db-group:us-east4:cs4750-research-page';

// Default: assume we're running on App Engine and use the unix socket
// /cloudsql/INSTANCE_CONNECTION_NAME  (App Engine automatically provides this)
$dsn = "mysql:unix_socket=/cloudsql/$instance_connection_name;dbname=$db_name;charset=utf8mb4";

// Optional: if you're running locally with php -S or similar and want to use the
// PUBLIC IP instead of the socket, you can uncomment this block and set LOCAL_DEV=1
//
// if (php_sapi_name() === 'cli-server' || getenv('LOCAL_DEV')) {
//     $host = '35.245.184.229';  // Cloud SQL public IP (from instance Overview page)
//     $dsn = "mysql:host=$host;dbname=$db_name;charset=utf8mb4";
// }

try {
    $db = new PDO(
        $dsn,
        $db_user,
        $db_pass,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    // You can customize this, but for now it's helpful to see the error while debugging
    echo "<h3>Database connection failed</h3>";
    echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
    exit;
}
