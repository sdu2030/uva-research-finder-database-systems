<?php
session_start();
require('connect-db.php');
require('project-db.php');

// ----------------------
// AUTH GUARD
// ----------------------
if (!isset($_SESSION["loggedIn"]) || $_SESSION["loggedIn"] !== true) {
    header("Location: login.php");
    exit;
}

$currentUser = $_SESSION["currentUser"] ?? null;
if (!$currentUser || !isset($currentUser["UID"])) {
    // Session is weird or incomplete -> force re-login
    header("Location: login.php");
    exit;
}

$uid          = $currentUser["UID"];              // e.g., akp5ve
$name         = $currentUser["name"] ?? $uid;
$isResearcher = $_SESSION["isResearcher"] ?? false;

// If a researcher somehow hits student.php, reroute them
if ($isResearcher) {
    header("Location: prof_details.php");
    exit;
}

// Keep a simple UID alias for legacy code (e.g., projects.php)
$_SESSION["uid"] = $uid;

// ----------------------
// 1) Projects the student has flagged (marks_interest)
// ----------------------
$flagged_projects = [];
try {
    $flagged_sql = "
        SELECT p.*
        FROM Projects p
        INNER JOIN marks_interest m ON p.PID = m.PID
        WHERE m.UID = :uid
    ";
    $flagged_stmt = $db->prepare($flagged_sql);
    $flagged_stmt->execute([':uid' => $uid]);
    $flagged_projects = $flagged_stmt->fetchAll();
} catch (PDOException $e) {
    // Optional: log error
    // error_log('Flagged projects error: ' . $e->getMessage());
}

// ----------------------
// 2) Projects that match this student's qualifications
//    Student_qualifications(UID, qualification)
//    Project_qualifications(PID, qualification)
// ----------------------
$matching_projects = [];
try {
    $match_sql = "
        SELECT DISTINCT p.*
        FROM Projects p
        INNER JOIN Project_qualifications pq ON p.PID = pq.PID
        INNER JOIN Student_qualifications sq 
            ON sq.qualification = pq.qualification
        WHERE sq.UID = :uid
    ";
    $match_stmt = $db->prepare($match_sql);
    $match_stmt->execute([':uid' => $uid]);
    $matching_projects = $match_stmt->fetchAll();
} catch (PDOException $e) {
    // Optional: log error
    // error_log('Matching projects error: ' . $e->getMessage());
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>HooResearches — Student Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php require('header.php'); ?>

<div class="container mt-4">
    <!-- Header / identity + navigation -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3>Student Dashboard</h3>
            <p class="mb-0">
                Signed in as
                <strong><?php echo htmlspecialchars($name); ?></strong>
                (<?php echo htmlspecialchars($uid); ?>)
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="projects.php" class="btn btn-outline-primary btn-sm">
                Browse All Projects
            </a>
        </div>
    </div>

    <!-- Flagged Projects -->
    <div class="mb-4">
        <h4>Your Flagged Projects</h4>
        <?php if (empty($flagged_projects)): ?>
            <p class="text-muted">You haven’t flagged any projects yet.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-secondary">
                        <tr>
                            <th>Title</th>
                            <th>Researcher</th>
                            <th>Paid/Credit</th>
                            <th>Keywords</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($flagged_projects as $proj): ?>
                        <tr>
                            <td>
                                <a href="project_details.php?pid=<?php echo $proj['PID']; ?>">
                                    <?php echo htmlspecialchars($proj['title']); ?>
                                </a>
                            </td>
                            <td>
                                <a href="prof_details.php?prof=<?php echo $proj['UID']; ?>">
                                    <?php echo getResearcher($proj['PID']); ?>
                                </a>
                            </td>
                            <td><?php echo htmlspecialchars($proj['paid_credit']); ?></td>
                            <td><?php echo getKeywords($proj['PID']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <hr>

    <!-- Projects Matching Qualifications -->
    <div class="mb-4">
        <h4>Projects Matching Your Qualifications</h4>
        <?php if (empty($matching_projects)): ?>
            <p class="text-muted">No projects currently match your qualifications.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-secondary">
                        <tr>
                            <th>Title</th>
                            <th>Researcher</th>
                            <th>Paid/Credit</th>
                            <th>Keywords</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($matching_projects as $proj): ?>
                        <tr>
                            <td>
                                <a href="project_details.php?pid=<?php echo $proj['PID']; ?>">
                                    <?php echo htmlspecialchars($proj['title']); ?>
                                </a>
                            </td>
                            <td>
                                <a href="prof_details.php?prof=<?php echo $proj['UID']; ?>">
                                    <?php echo getResearcher($proj['PID']); ?>
                                </a>
                            </td>
                            <td><?php echo htmlspecialchars($proj['paid_credit']); ?></td>
                            <td><?php echo getKeywords($proj['PID']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require('footer.php'); ?>
</body>
</html>
