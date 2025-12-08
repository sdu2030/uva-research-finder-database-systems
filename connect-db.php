<?php
// connect-db.php
// Shared DB connection for HooResearches.

// Read from env vars (set in app.yaml), with safe fallbacks.
$db_user = getenv('DB_USER') ?: 'cs4750-research-page';
$db_pass = getenv('DB_PASS') ?: 'researchDB_25';
$db_name = getenv('DB_NAME') ?: 'research_info';
$instance_connection_name = getenv('CLOUD_SQL_CONNECTION_NAME')
    ?: 'cs4750-db-group:us-east4:cs4750-research-page';

/
if (!empty($instance_connection_name)) {
    $dsn = sprintf(
        'mysql:unix_socket=/cloudsql/%s;dbname=%s;charset=utf8mb4',
        $instance_connection_name,
        $db_name
    );
} else {
    
    $host = '35.245.184.229';
    $port = 3306;
    $dsn = sprintf(
        'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
        $host,
        $port,
        $db_name
    );
}

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $db = new PDO($dsn, $db_user, $db_pass, $options);
} catch (PDOException $e) {
    http_response_code(500);
    echo "<p>Database connection failed.</p>";
    // For debugging, you can temporarily uncomment this line,
    // but comment it again before you submit:
    // echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
    exit;
}
