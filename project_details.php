<?php
session_start();
require_once 'connect-db.php';  

error_reporting(E_ALL);
ini_set('display_errors', 1);

$uid = $_SESSION['uid'] ?? null;

// --- 1. Get PID from query string ---
$pid = isset($_GET['pid']) ? (int)$_GET['pid'] : 0;
if ($pid <= 0) {
    http_response_code(400);
    echo "Invalid project ID.";
    exit();
}


function safeQuery(PDO $db, string $sql, array $params = []) {
    try {
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    } catch (PDOException $e) {
        return false;
    }
}

// --- 2. Load main project details ---
$detailsSql = "
    SELECT
      p.PID,
      p.title,
      p.description,
      p.paid_credit,
      p.num_students,
      p.UID AS researcher_uid,
      per.name AS researcher_name
    FROM Project p
    LEFT JOIN Person per ON per.UID = p.UID
    WHERE p.PID = :pid
";
$detailsStmt = safeQuery($db, $detailsSql, [':pid' => $pid]);
$details = ($detailsStmt !== false) ? $detailsStmt->fetch(PDO::FETCH_ASSOC) : null;

if (!$details) {
    http_response_code(404);
    echo "Project not found.";
    exit();
}

// --- 3. Load keywords ---
$kwSql = "
    SELECT keyword
    FROM Project_keywords
    WHERE PID = :pid
    ORDER BY keyword
";
$kwStmt = safeQuery($db, $kwSql, [':pid' => $pid]);
$keywords = ($kwStmt !== false) ? $kwStmt->fetchAll(PDO::FETCH_COLUMN) : [];
$keywordsStr = implode(', ', $keywords);

// --- 4. Load qualifications ---
$qualSql = "
    SELECT qualification
    FROM Project_qualifications
    WHERE PID = :pid
    ORDER BY qualification
";
$qualStmt = safeQuery($db, $qualSql, [':pid' => $pid]);
$qualifications = ($qualStmt !== false) ? $qualStmt->fetchAll(PDO::FETCH_COLUMN) : [];
$qualStr = implode(', ', $qualifications);

// --- 5. Check if user is interested in this project ---
$isInterested = false;
if ($uid) {
    $intSql = "
        SELECT 1
        FROM marks_interest
        WHERE UID = :uid AND PID = :pid
        LIMIT 1
    ";
    $intStmt = safeQuery($db, $intSql, [':uid' => $uid, ':pid' => $pid]);
    $isInterested = ($intStmt !== false && $intStmt->fetchColumn());
}

// --- 6. Handle interest toggle POST ---
$interestError = '';

if ($uid && isset($_POST['toggle_interest'])) {
    try {
        if ($isInterested) {
            // Unmark interest
            $stmt = $db->prepare(
                "DELETE FROM marks_interest WHERE UID = :uid AND PID = :pid"
            );
        } else {
            // Mark interest
            $stmt = $db->prepare(
                "INSERT IGNORE INTO marks_interest (UID, PID) VALUES (:uid, :pid)"
            );
        }
        $stmt->execute([':uid' => $uid, ':pid' => $pid]);

        // Redirect to avoid form resubmission on refresh
        header("Location: project_details.php?pid=" . urlencode($pid));
        exit();
    } catch (PDOException $e) {
        $interestError = "Error updating interest: " . $e->getMessage();
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="HooResearches Team">
    <meta name="description" content="Project details page for HooResearches, a CS 3750 project.">
    <meta name="keywords" content="CS 3750, UVa research, project details, database systems">
    <title>HooResearches – Project Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet" crossorigin="anonymous">
</head>

<body>
<div class="text-left text-bg-dark m-3 p-3">
    <h1>HooResearches</h1>
    <p>A UVa CS research finder</p>
</div>

<div class="container mb-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="h4 mb-0">Project Details</h2>
            <small class="text-muted">View full information about this research opportunity.</small>
        </div>
        <div class="btn-group">
            <a href="student.php" class="btn btn-outline-secondary btn-sm">← Student Portal</a>
            <a href="projects.php" class="btn btn-outline-secondary btn-sm">← All Projects</a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <!-- Title -->
            <h3 class="card-title text-center mb-4">
                <?= htmlspecialchars($details['title']) ?>
            </h3>

            <!-- Top info row -->
            <div class="row mb-3">
                <div class="col-md-6 mb-2">
                    <label class="form-label fw-bold">Researcher</label>
                    <div>
                        <?php if (!empty($details['researcher_uid'])): ?>
                            <a href="prof_details.php?prof=<?= urlencode($details['researcher_uid']) ?>">
                                <?= htmlspecialchars($details['researcher_name'] ?? $details['researcher_uid']) ?>
                            </a>
                        <?php else: ?>
                            <span class="text-muted">Unknown</span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="col-md-3 mb-2">
                    <label class="form-label fw-bold">Paid or Credit</label>
                    <div><?= htmlspecialchars($details['paid_credit'] ?? 'Not specified') ?></div>
                </div>

                <div class="col-md-3 mb-2">
                    <label class="form-label fw-bold">Number of Students</label>
                    <div><?= htmlspecialchars((string)($details['num_students'] ?? '')) ?></div>
                </div>
            </div>

            <!-- Description + side info -->
            <div class="row mb-3">
                <div class="col-md-8 mb-2">
                    <label class="form-label fw-bold">Description</label>
                    <div class="border rounded p-2 bg-light">
                        <?= nl2br(htmlspecialchars($details['description'] ?? 'No description provided.')) ?>
                    </div>
                </div>

                <div class="col-md-4 mb-2">
                    <label class="form-label fw-bold">Keywords</label>
                    <div class="border rounded p-2 bg-light">
                        <?= $keywordsStr ? htmlspecialchars($keywordsStr) : 'None listed.' ?>
                    </div>

                    <label class="form-label fw-bold mt-3">Qualifications</label>
                    <div class="border rounded p-2 bg-light">
                        <?= $qualStr ? htmlspecialchars($qualStr) : 'None listed.' ?>
                    </div>
                </div>
            </div>

            <!-- Interest button -->
            <?php if ($uid): ?>
                <form method="post" class="mt-3">
                    <input type="hidden" name="toggle_interest" value="1">
                    <button type="submit"
                            class="btn <?= $isInterested ? 'btn-success' : 'btn-outline-success' ?>">
                        <?= $isInterested ? '★ You are interested in this project' : '☆ Mark as interested' ?>
                    </button>
                </form>

                <?php if (!empty($interestError)): ?>
                    <div class="alert alert-danger mt-2">
                        <?= htmlspecialchars($interestError) ?>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <p class="text-muted mt-3">
                    <a href="login.php">Sign in</a> to mark this project as interested.
                </p>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        crossorigin="anonymous"></script>
</body>
</html>
