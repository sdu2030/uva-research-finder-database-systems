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
    header("Location: login.php");
    exit;
}

$uid          = $currentUser["UID"];
$name         = $currentUser["name"] ?? $uid;
$isResearcher = $_SESSION["isResearcher"] ?? false;

// Only researchers should be here; students go to student dashboard
if (!$isResearcher) {
    header("Location: student.php");
    exit;
}

// Also keep simple UID alias
$_SESSION["uid"] = $uid;

// ----------------------
// Fetch this professor's projects
// ----------------------
$my_projects = [];
try {
    $sql = "SELECT * FROM Projects WHERE UID = :uid";
    $stmt = $db->prepare($sql);
    $stmt->execute([':uid' => $uid]);
    $my_projects = $stmt->fetchAll();
} catch (PDOException $e) {
    // Optional: log error
    // error_log('Professor projects error: ' . $e->getMessage());
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>HooResearches — Professor Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php require('header.php'); ?>

<div class="container mt-4">
    <!-- Header / identity + navigation -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3>Professor Dashboard</h3>
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
            <a href="creat-proj.php" class="btn btn-primary btn-sm">
                + Create New Project
            </a>
        </div>
    </div>

    <!-- Professor's own projects -->
    <h4>Your Projects</h4>
    <?php if (empty($my_projects)): ?>
        <p class="text-muted">You don’t have any projects yet. Use “Create New Project” to add one.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-secondary">
                    <tr>
                        <th>Title</th>
                        <th>Paid/Credit</th>
                        <th># Students</th>
                        <th>Keywords</th>
                        <th>Qualifications</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($my_projects as $proj): ?>
                    <tr>
                        <td>
                            <a href="project_details.php?pid=<?php echo $proj['PID']; ?>">
                                <?php echo htmlspecialchars($proj['title']); ?>
                            </a>
                        </td>
                        <td><?php echo htmlspecialchars($proj['paid_credit']); ?></td>
                        <td><?php echo htmlspecialchars($proj['num_students']); ?></td>
                        <td><?php echo getKeywords($proj['PID']); ?></td>
                        <td><?php echo getQualifications($proj['PID']); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php require('footer.php'); ?>
</body>
</html>
