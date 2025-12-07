<?php
require_once 'connect-db.php';


$perPage = 20;
$page    = max(1, (int)($_GET['page'] ?? 1));
$offset  = ($page - 1) * $perPage;


function current_query_params_prof(array $overrides = []): string {
    $params = array_merge($_GET, $overrides);
    return http_build_query($params);
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="Nicole Savage">
    <meta name="description" content="HooResearches professor page for UVA CS research finder.">
    <meta name="keywords" content="CS 4750, UVA research, HooResearches, professor portal">
    <title>HooResearches Professor Portal</title>


    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/styles.css">
</head>


<body id="top">
<div class="hr-header text-bg-dark m-3 p-3 d-flex justify-content-between align-items-center">
    <div>
        <h1>HooResearches</h1>
        <p>A UVa CS research finder — Professor Portal</p>
    </div>
    <div class="d-flex gap-2">
        <a href="login.php" class="btn btn-outline-light btn-sm btn-back">← Back to Home</a>
        <!-- Create project button -->
        <a href="create-project.php" class="btn btn-warning btn-sm">
            + Create New Project
        </a>
        <!-- If your teammate used a different filename, update the href above -->
    </div>
</div>


<div class="container page-container mb-5">
    <!-- Professor dashboard intro -->
    <div class="section-card">
        <h3 class="mb-2">Professor Dashboard</h3>
        <p class="section-subtitle">
            View all registered faculty, manage your projects, and inspect details for a specific project.
        </p>
    </div>


    <!-- All professors -->
    <div class="section-card">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <div>
                <h4 class="section-title mb-0">All Professors</h4>
                <p class="section-subtitle mb-0">
                    These are the researchers currently registered in the system.
                </p>
            </div>
        </div>
        <?php
        $sqlProfs = "
            SELECT r.UID, pe.name, r.role
            FROM Researcher r
            JOIN Person pe ON pe.UID = r.UID
            WHERE LOWER(r.role) IN ('professor', 'faculty', 'pi')
            ORDER BY pe.name
        ";
        $profs = $db->query($sqlProfs)->fetchAll();


        if (!$profs): ?>
            <p class="mt-2 mb-0">No professors found.</p>
        <?php else: ?>
            <ul class="list-group mt-2">
                <?php foreach ($profs as $pr): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><?= htmlspecialchars($pr['name']) ?> (<?= htmlspecialchars($pr['UID']) ?>)</span>
                        <span class="badge bg-secondary"><?= htmlspecialchars($pr['role']) ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>


    <!-- My Projects -->
    <div class="section-card">
        <h4 class="section-title mb-2">My Projects</h4>
        <p class="section-subtitle">
            Enter your UID to see projects that you own. Use the Create New Project button above to add more.
        </p>
        <form method="get" class="mb-3">
            <div class="row g-2 align-items-end">
                <div class="col-md-6 col-12">
                    <label for="profUID" class="form-label fw-bold">Your UID</label>
                    <input type="text"
                           class="form-control"
                           id="profUID"
                           name="profUID"
                           value="<?= htmlspecialchars($_GET['profUID'] ?? '') ?>"
                           placeholder="e.g. tl3uk">
                </div>
                <div class="col-md-3 col-12">
                    <label class="form-label d-none d-md-block">&nbsp;</label>
                    <button type="submit" name="action" value="myProjects" class="btn btn-primary w-100">
                        Show My Projects
                    </button>
                </div>
            </div>
        </form>


        <?php
        if (($_GET['action'] ?? '') === 'myProjects' && !empty($_GET['profUID'])) {
            $prof_uid = $_GET['profUID'];


            // Count total projects for this professor
            $countSql = "
                SELECT COUNT(*)
                FROM Project p
                WHERE p.UID = :uid
            ";
            $countStmt = $db->prepare($countSql);
            $countStmt->execute([':uid' => $prof_uid]);
            $total = (int)$countStmt->fetchColumn();


            $sql = "
                SELECT p.PID, p.title, p.paid_credit, p.num_students, p.description
                FROM Project p
                WHERE p.UID = :uid
                ORDER BY p.PID DESC
                LIMIT :offset, :perPage
            ";
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':uid', $prof_uid, PDO::PARAM_STR);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->bindValue(':perPage', $perPage, PDO::PARAM_INT);
            $stmt->execute();
            $rows = $stmt->fetchAll();


            $start = $total > 0 ? $offset + 1 : 0;
            $end   = min($offset + $perPage, $total);
            ?>


            <div class="d-flex justify-content-between align-items-center mb-2">
                <div class="pagination-summary">
                    <?= $total > 0
                        ? "Showing {$start}–{$end} of {$total} projects for " . htmlspecialchars($prof_uid)
                        : "No projects found for " . htmlspecialchars($prof_uid)
                    ?>
                </div>
                <?php if ($total > $perPage): ?>
                    <div>
                        <?php if ($page > 1): ?>
                            <a class="btn btn-sm btn-outline-secondary"
                               href="?<?= current_query_params_prof(['action' => 'myProjects', 'page' => $page - 1]) ?>">← Prev</a>
                        <?php endif; ?>
                        <?php if ($end < $total): ?>
                            <a class="btn btn-sm btn-outline-secondary"
                               href="?<?= current_query_params_prof(['action' => 'myProjects', 'page' => $page + 1]) ?>">Next →</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>


            <?php foreach ($rows as $p): ?>
                <div class="card result-card">
                    <div class="card-body">
                        <h5 class="card-title">
                            <?= htmlspecialchars($p['title']) ?>
                        </h5>
                        <p class="result-meta mb-2">
                            PID <?= (int)$p['PID'] ?>
                        </p>
                        <p class="mb-1">
                            <strong>Paid/Credit:</strong> <?= htmlspecialchars($p['paid_credit']) ?>
                            &nbsp;·&nbsp;
                            <strong># Students:</strong> <?= htmlspecialchars($p['num_students']) ?>
                        </p>
                        <p class="card-text mt-2 mb-0">
                            <?= nl2br(htmlspecialchars($p['description'])) ?>
                        </p>
                    </div>
                </div>
            <?php endforeach; ?>


            <?php if ($total > 0): ?>
                <div class="mt-3">
                    <a href="#top" class="btn btn-link btn-sm btn-back">↑ Back to top</a>
                </div>
            <?php endif; ?>
        <?php } ?>
    </div>


    <!-- Project details lookup -->
    <div class="section-card">
        <h4 class="section-title mb-2">Project Details Lookup</h4>
        <p class="section-subtitle">
            Look up a specific project by its ID (PID) to view its full description, keywords, and required qualifications.
        </p>
        <form method="get" class="mb-3">
            <div class="row g-2 align-items-end">
                <div class="col-md-4 col-12">
                    <label for="pid" class="form-label fw-bold">Project ID (PID)</label>
                    <input type="number"
                           class="form-control"
                           id="pid"
                           name="pid"
                           value="<?= htmlspecialchars($_GET['pid'] ?? '') ?>"
                           placeholder="e.g. 98">
                </div>
                <div class="col-md-3 col-12">
                    <label class="form-label d-none d-md-block">&nbsp;</label>
                    <button type="submit" name="action" value="projDetails" class="btn btn-secondary w-100">
                        Lookup Project
                    </button>
                </div>
            </div>
        </form>


        <?php
        if (($_GET['action'] ?? '') === 'projDetails' && !empty($_GET['pid'])) {
            $pid = (int)$_GET['pid'];


            $sql = "
                SELECT
                  p.PID,
                  p.title,
                  p.paid_credit,
                  p.num_students,
                  p.description,
                  pe.name AS professor_name,
                  GROUP_CONCAT(DISTINCT pk.keyword       ORDER BY pk.keyword       SEPARATOR ', ') AS keywords,
                  GROUP_CONCAT(DISTINCT pq.qualification ORDER BY pq.qualification SEPARATOR ', ') AS required_qualifications
                FROM Project p
                JOIN Person pe ON pe.UID = p.UID
                LEFT JOIN Project_keywords       pk ON pk.PID = p.PID
                LEFT JOIN Project_qualifications pq ON pq.PID = p.PID
                WHERE p.PID = :pid
                GROUP BY p.PID, p.title, p.paid_credit, p.num_students, p.description, professor_name
            ";
            $stmt = $db->prepare($sql);
            $stmt->execute([':pid' => $pid]);
            $r = $stmt->fetch();


            if (!$r): ?>
                <p class="mt-2 mb-0">
                    No project found for PID <?= htmlspecialchars((string)$pid) ?>.
                </p>
            <?php else: ?>
                <div class="card result-card mt-2">
                    <div class="card-body">
                        <h5 class="card-title">
                            <?= htmlspecialchars($r['title']) ?>
                        </h5>
                        <p class="result-meta mb-2">
                            PID <?= (int)$r['PID'] ?> ·
                            Professor: <?= htmlspecialchars($r['professor_name']) ?>
                        </p>
                        <p class="mb-1">
                            <strong>Paid/Credit:</strong> <?= htmlspecialchars($r['paid_credit']) ?>
                            &nbsp;·&nbsp;
                            <strong># Students:</strong> <?= htmlspecialchars($r['num_students']) ?>
                        </p>
                        <?php if (!empty($r['keywords'])): ?>
                            <p class="mb-1">
                                <strong>Keywords:</strong> <?= htmlspecialchars($r['keywords']) ?>
                            </p>
                        <?php endif; ?>
                        <?php if (!empty($r['required_qualifications'])): ?>
                            <p class="mb-1">
                                <strong>Required Qualifications:</strong> <?= htmlspecialchars($r['required_qualifications']) ?>
                            </p>
                        <?php endif; ?>
                        <p class="card-text mt-2 mb-0">
                            <?= nl2br(htmlspecialchars($r['description'])) ?>
                        </p>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="#top" class="btn btn-link btn-sm btn-back">↑ Back to top</a>
                </div>
            <?php endif;
        }
        ?>
    </div>
</div>


<div class="hr-footer text-bg-dark m-3 p-3">
    <p>&copy; 2025 HooResearches. All Rights Reserved.</p>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>





