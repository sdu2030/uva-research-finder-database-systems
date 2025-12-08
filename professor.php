<?php
session_start();
require_once 'connect-db.php';
require_once 'project-db.php';
require_once 'prof-db.php';

// Prefer UID from session (when login.php sets it in the future)
$uidFromSession = $_SESSION['uid'] ?? null;

// Fallback: allow ?prof=<uid> in URL, similar to prof_details.php
$uidFromParam   = $_GET['prof'] ?? null;

$uid = $uidFromSession ?: $uidFromParam;

// Pagination
$perPage = 20;
$page    = max(1, (int)($_GET['page'] ?? 1));
$offset  = ($page - 1) * $perPage;

function prof_query_params(array $overrides = []): string {
    $params = array_merge($_GET, $overrides);
    return http_build_query($params);
}

$titleFilter = trim($_GET['title'] ?? '');
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="Nicole Savage">
    <meta name="description" content="HooResearches professor dashboard.">
    <meta name="keywords" content="CS 4750, UVa research, HooResearches, professor portal">
    <title>HooResearches Professor Portal</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/styles.css">
</head>

<body id="top">
<div class="text-bg-dark m-3 p-3 d-flex justify-content-between align-items-center">
    <div>
        <h1 class="mb-0">HooResearches</h1>
        <p class="mb-0">Professor Portal</p>
        <?php if ($uid): ?>
            <small>Viewing projects for professor UID: <?= htmlspecialchars($uid) ?></small>
        <?php else: ?>
            <small>No professor selected – open this page via login or prof_details.</small>
        <?php endif; ?>
    </div>
    <div class="d-flex gap-2">
        <a href="login.php" class="btn btn-outline-light btn-sm">← Back to Login</a>
        <a href="create-proj.php" class="btn btn-warning btn-sm">+ Create New Project</a>
    </div>
</div>

<div class="container mb-5">
    <div class="card mb-4">
        <div class="card-body">
            <h3 class="card-title mb-2">My Projects</h3>
            <p class="text-muted mb-3">
                These are projects for the selected professor. If you arrived from login, this will use your UID.
            </p>

            <?php if (!$uid): ?>
                <div class="alert alert-info">
                    No professor UID was found. You can:
                    <ul class="mb-0">
                        <li>Sign in as a professor from <a href="login.php" class="alert-link">login.php</a>, or</li>
                        <li>Open this page with a <code>?prof=UID</code> parameter, e.g. <code>professor.php?prof=tl3uk</code>.</li>
                    </ul>
                </div>
            <?php else: ?>
                <form method="get" class="mb-3">
                    <!-- Preserve prof in query when filtering -->
                    <input type="hidden" name="prof" value="<?= htmlspecialchars($uid) ?>">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-6 col-12">
                            <label for="title" class="form-label fw-bold">Filter by Title</label>
                            <input type="text"
                                   class="form-control"
                                   id="title"
                                   name="title"
                                   value="<?= htmlspecialchars($titleFilter) ?>"
                                   placeholder="e.g., Machine Learning for Health">
                        </div>
                        <div class="col-md-2 col-6">
                            <label class="form-label d-none d-md-block">&nbsp;</label>
                            <button type="submit" class="btn btn-primary w-100">Search</button>
                        </div>
                        <div class="col-md-2 col-6">
                            <label class="form-label d-none d-md-block">&nbsp;</label>
                            <a href="professor.php?prof=<?= urlencode($uid) ?>" class="btn btn-outline-secondary w-100">Clear</a>
                        </div>
                    </div>
                </form>

                <?php
                // Count projects belonging to this professor UID
                $countSql = "
                    SELECT COUNT(*) FROM (
                        SELECT p.PID
                        FROM Project p
                        WHERE p.UID = :uid
                          AND (:title = '' OR p.title LIKE :titleLike)
                    ) AS sub
                ";
                $countStmt = $db->prepare($countSql);
                $countStmt->execute([
                    ':uid'       => $uid,
                    ':title'     => $titleFilter,
                    ':titleLike' => '%' . $titleFilter . '%',
                ]);
                $total = (int)$countStmt->fetchColumn();

                $sql = "
                    SELECT
                      p.PID,
                      p.title,
                      GROUP_CONCAT(DISTINCT pk.keyword ORDER BY pk.keyword SEPARATOR ', ') AS keywords
                    FROM Project p
                    LEFT JOIN Project_keywords pk ON pk.PID = p.PID
                    WHERE p.UID = :uid
                      AND (:title = '' OR p.title LIKE :titleLike)
                    GROUP BY p.PID, p.title
                    ORDER BY p.PID DESC
                    LIMIT :offset, :perPage
                ";
                $stmt = $db->prepare($sql);
                $stmt->bindValue(':uid', $uid, PDO::PARAM_STR);
                $stmt->bindValue(':title', $titleFilter, PDO::PARAM_STR);
                $stmt->bindValue(':titleLike', '%' . $titleFilter . '%', PDO::PARAM_STR);
                $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
                $stmt->bindValue(':perPage', $perPage, PDO::PARAM_INT);
                $stmt->execute();
                $rows = $stmt->fetchAll();

                $start = $total > 0 ? $offset + 1 : 0;
                $end   = min($offset + $perPage, $total);
                ?>

                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="text-muted">
                        <?= $total > 0
                            ? "Showing {$start}–{$end} of {$total} projects"
                            : "No projects found for this professor."
                        ?>
                    </div>
                    <?php if ($total > $perPage): ?>
                        <div>
                            <?php if ($page > 1): ?>
                                <a class="btn btn-sm btn-outline-secondary"
                                   href="?<?= prof_query_params(['page' => $page - 1, 'prof' => $uid]) ?>">← Prev</a>
                            <?php endif; ?>
                            <?php if ($end < $total): ?>
                                <a class="btn btn-sm btn-outline-secondary"
                                   href="?<?= prof_query_params(['page' => $page + 1, 'prof' => $uid]) ?>">Next →</a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <?php foreach ($rows as $p): ?>
                    <div class="card mb-2">
                        <div class="card-body">
                            <h5 class="card-title mb-1">
                                <!-- Uses existing project_details.php -->
                                <a href="project_details.php?pid=<?= urlencode($p['PID']) ?>"
                                   class="text-decoration-none">
                                    <?= htmlspecialchars($p['title']) ?>
                                </a>
                            </h5>
                            <?php if (!empty($p['keywords'])): ?>
                                <p class="mb-0">
                                    <strong>Keywords:</strong>
                                    <?= htmlspecialchars($p['keywords']) ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>

                <?php if ($total > 0): ?>
                    <div class="mt-3">
                        <a href="#top" class="btn btn-link btn-sm">↑ Back to top</a>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="text-bg-dark m-3 p-3">
    <p class="mb-0">&copy; 2025 HooResearches. All Rights Reserved.</p>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
