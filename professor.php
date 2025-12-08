<?php
session_start();
require_once 'connect-db.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$uid   = $_SESSION['uid'] ?? null;
$name  = $_SESSION['name'] ?? null;

$titleFilter = trim($_GET['title'] ?? '');

$projects = [];
$errorMsg = '';

if ($uid) {
    try {
        if ($titleFilter !== '') {
            $sql = "
                SELECT
                  p.PID,
                  p.title,
                  p.paid_credit,
                  p.num_students,
                  GROUP_CONCAT(pk.keyword ORDER BY pk.keyword SEPARATOR ', ') AS keywords
                FROM Project p
                LEFT JOIN Project_keywords pk ON pk.PID = p.PID
                WHERE p.UID = :uid
                  AND p.title LIKE CONCAT('%', :title, '%')
                GROUP BY p.PID, p.title, p.paid_credit, p.num_students
                ORDER BY p.PID DESC
                LIMIT 20
            ";
            $stmt = $db->prepare($sql);
            $stmt->execute([
                ':uid'   => $uid,
                ':title' => $titleFilter,
            ]);
        } else {
            $sql = "
                SELECT
                  p.PID,
                  p.title,
                  p.paid_credit,
                  p.num_students,
                  GROUP_CONCAT(pk.keyword ORDER BY pk.keyword SEPARATOR ', ') AS keywords
                FROM Project p
                LEFT JOIN Project_keywords pk ON pk.PID = p.PID
                WHERE p.UID = :uid
                GROUP BY p.PID, p.title, p.paid_credit, p.num_students
                ORDER BY p.PID DESC
                LIMIT 20
            ";
            $stmt = $db->prepare($sql);
            $stmt->execute([':uid' => $uid]);
        }
        $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $errorMsg = $e->getMessage();
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>HooResearches – Professor Portal</title>
    <meta name="description" content="Professor page for HooResearches, a UVa CS research finder.">
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
            <h2 class="h4 mb-1">Professor Portal</h2>
            <?php if ($uid): ?>
                <small class="text-muted">
                    Logged in as <strong><?= htmlspecialchars($uid) ?></strong>
                    <?= $name ? " (" . htmlspecialchars($name) . ")" : "" ?>
                </small>
            <?php else: ?>
                <small class="text-muted">
                    Please sign in as a researcher on the login page to view your projects.
                </small>
            <?php endif; ?>
        </div>
        <div class="btn-group">
            <a href="login.php" class="btn btn-outline-secondary btn-sm">Back to Login</a>
        </div>
    </div>

    <?php if (!$uid): ?>
        <div class="alert alert-info">
            You are not currently signed in. Go to <a href="login.php">login</a> and sign in
            as a researcher to see your projects.
        </div>
    <?php else: ?>

        <!-- Search + create -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <form class="d-flex gap-2" method="get" action="professor.php">
                <div>
                    <label for="title" class="form-label fw-bold mb-1">Filter by title</label>
                    <input type="text"
                           class="form-control"
                           id="title"
                           name="title"
                           placeholder="e.g., machine learning"
                           value="<?= htmlspecialchars($titleFilter) ?>">
                </div>
                <div class="align-self-end pb-1">
                    <button type="submit" class="btn btn-outline-primary">Search</button>
                    <?php if ($titleFilter !== ''): ?>
                        <a href="professor.php" class="btn btn-outline-secondary ms-1">Clear</a>
                    <?php endif; ?>
                </div>
            </form>

            <a href="create_project.php" class="btn btn-primary mt-4 mt-md-0">
                + Create Project
            </a>
        </div>

        <?php if (!empty($errorMsg)): ?>
            <div class="alert alert-danger">
                Error loading your projects: <?= htmlspecialchars($errorMsg) ?>
            </div>
        <?php elseif (empty($projects)): ?>
            <p class="text-muted">
                You don’t have any projects yet.
                Click <strong>Create Project</strong> to add one.
            </p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-sm align-middle">
                    <thead class="table-secondary">
                    <tr>
                        <th style="width: 10%;">PID</th>
                        <th style="width: 35%;">Title</th>
                        <th style="width: 25%;">Keywords</th>
                        <th style="width: 15%;">Paid/Credit</th>
                        <th style="width: 15%;"># Students</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($projects as $p): ?>
                        <tr>
                            <td><?= htmlspecialchars($p['PID']) ?></td>
                            <td>
                                <a href="project_details.php?pid=<?= urlencode($p['PID']) ?>"
                                   class="text-decoration-none">
                                    <?= htmlspecialchars($p['title']) ?>
                                </a>
                            </td>
                            <td><?= htmlspecialchars($p['keywords'] ?? '') ?></td>
                            <td><?= htmlspecialchars($p['paid_credit'] ?? '') ?></td>
                            <td><?= htmlspecialchars((string)($p['num_students'] ?? '')) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <p class="text-muted small">Showing up to 20 of your most recent projects.</p>
        <?php endif; ?>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        crossorigin="anonymous"></script>
</body>
</html>
