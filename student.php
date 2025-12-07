<?php
require_once 'connect-db.php';

$perPage = 20;
$action  = $_GET['action'] ?? 'byKeyword';
$page    = max(1, (int)($_GET['page'] ?? 1));
$offset  = ($page - 1) * $perPage;

function current_query_params(array $overrides = []): string {
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
    <meta name="description" content="HooResearches student page for UVA CS research finder.">
    <meta name="keywords" content="CS 4750, UVA research, HooResearches, student portal">
    <title>HooResearches Student Portal</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/styles.css">
</head>

<body id="top">
<div class="hr-header text-bg-dark m-3 p-3 d-flex justify-content-between align-items-center">
    <div>
        <h1>HooResearches</h1>
        <p>A UVa CS research finder — Student Portal</p>
    </div>
    <div>
        <a href="login.php" class="btn btn-outline-light btn-sm btn-back">← Back to Home</a>
    </div>
</div>

<div class="container page-container mb-5">
    <div class="section-card">
        <h3 class="mb-3">Student Dashboard</h3>
        <p class="section-subtitle">
            Search for projects, view your flagged opportunities, or find projects that match your qualifications.
        </p>

        <!-- Tabs -->
        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link <?= $action === 'byKeyword' ? 'active' : '' ?>"
                   href="?action=byKeyword">Search by Keyword</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link <?= $action === 'flagged' ? 'active' : '' ?>"
                   href="?action=flagged">My Flagged Projects</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link <?= $action === 'matchAny' ? 'active' : '' ?>"
                   href="?action=matchAny">Matching Projects</a>
            </li>
        </ul>

        <div class="tab-content mt-3">
            <!-- TAB 1: Search by keyword -->
            <?php if ($action === 'byKeyword'): ?>
                <div class="tab-pane fade show active">
                    <form method="get" class="mb-3">
                        <input type="hidden" name="action" value="byKeyword">
                        <div class="mb-3">
                            <label for="keyword" class="form-label fw-bold">Keyword</label>
                            <input type="text"
                                   class="form-control"
                                   id="keyword"
                                   name="keyword"
                                   value="<?= htmlspecialchars($_GET['keyword'] ?? '') ?>"
                                   placeholder="e.g. machine learning, robotics, HCI">
                        </div>
                        <button type="submit" class="btn btn-primary">Search</button>
                    </form>

                    <?php
                    if (!empty($_GET['keyword'])) {
                        $kw = $_GET['keyword'];

                        // Count total projects for this keyword
                        $countSql = "
                            SELECT COUNT(DISTINCT p.PID)
                            FROM Project p
                            JOIN Project_keywords pk ON pk.PID = p.PID
                            WHERE pk.keyword LIKE :kw
                        ";
                        $countStmt = $db->prepare($countSql);
                        $countStmt->execute([':kw' => "%{$kw}%"]);
                        $total = (int)$countStmt->fetchColumn();

                        $sql = "
                            SELECT 
                              p.PID,
                              p.title,
                              p.paid_credit,
                              p.num_students,
                              p.description,
                              pe.name AS professor_name
                            FROM Project p
                            JOIN Person           pe ON pe.UID = p.UID
                            JOIN Project_keywords pk ON pk.PID = p.PID
                            WHERE pk.keyword LIKE :kw
                            GROUP BY p.PID, p.title, p.paid_credit, p.num_students, p.description, professor_name
                            ORDER BY p.PID DESC
                            LIMIT :offset, :perPage
                        ";
                        $stmt = $db->prepare($sql);
                        $stmt->bindValue(':kw', "%{$kw}%", PDO::PARAM_STR);
                        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
                        $stmt->bindValue(':perPage', $perPage, PDO::PARAM_INT);
                        $stmt->execute();
                        $projects = $stmt->fetchAll();

                        $start = $total > 0 ? $offset + 1 : 0;
                        $end   = min($offset + $perPage, $total);
                        ?>

                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="pagination-summary">
                                <?= $total > 0
                                    ? "Showing {$start}–{$end} of {$total} projects for “" . htmlspecialchars($kw) . "”"
                                    : "No projects found for “" . htmlspecialchars($kw) . "”"
                                ?>
                            </div>
                            <?php if ($total > $perPage): ?>
                                <div>
                                    <?php if ($page > 1): ?>
                                        <a class="btn btn-sm btn-outline-secondary"
                                           href="?<?= current_query_params(['page' => $page - 1]) ?>">← Prev</a>
                                    <?php endif; ?>
                                    <?php if ($end < $total): ?>
                                        <a class="btn btn-sm btn-outline-secondary"
                                           href="?<?= current_query_params(['page' => $page + 1]) ?>">Next →</a>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <?php
                        foreach ($projects as $p): ?>
                            <div class="card result-card">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <?= htmlspecialchars($p['title']) ?>
                                    </h5>
                                    <p class="result-meta mb-2">
                                        PID <?= (int)$p['PID'] ?> ·
                                        Professor: <?= htmlspecialchars($p['professor_name']) ?>
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
            <?php endif; ?>

            <!-- TAB 2: Flagged projects -->
            <?php if ($action === 'flagged'): ?>
                <div class="tab-pane fade show active">
                    <form method="get" class="mb-3">
                        <input type="hidden" name="action" value="flagged">
                        <div class="mb-3">
                            <label for="flagUID" class="form-label fw-bold">Your UID</label>
                            <input type="text"
                                   class="form-control"
                                   id="flagUID"
                                   name="flagUID"
                                   value="<?= htmlspecialchars($_GET['flagUID'] ?? '') ?>"
                                   placeholder="e.g. akp5ve">
                        </div>
                        <button type="submit" class="btn btn-secondary">View Flagged Projects</button>
                    </form>

                    <?php
                    if (!empty($_GET['flagUID'])) {
                        $uid = $_GET['flagUID'];

                        $countSql = "
                            SELECT COUNT(*)
                            FROM Marks_interest mi
                            WHERE mi.UID = :uid
                        ";
                        $countStmt = $db->prepare($countSql);
                        $countStmt->execute([':uid' => $uid]);
                        $total = (int)$countStmt->fetchColumn();

                        $sql = "
                            SELECT
                              p.PID,
                              p.title,
                              pe.name AS professor_name,
                              p.paid_credit,
                              p.num_students
                            FROM Marks_interest mi
                            JOIN Project p ON p.PID = mi.PID
                            JOIN Person  pe ON pe.UID = p.UID
                            WHERE mi.UID = :uid
                            ORDER BY p.PID DESC
                            LIMIT :offset, :perPage
                        ";
                        $stmt = $db->prepare($sql);
                        $stmt->bindValue(':uid', $uid, PDO::PARAM_STR);
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
                                    ? "Showing {$start}–{$end} of {$total} flagged projects for " . htmlspecialchars($uid)
                                    : "No flagged projects found for " . htmlspecialchars($uid)
                                ?>
                            </div>
                            <?php if ($total > $perPage): ?>
                                <div>
                                    <?php if ($page > 1): ?>
                                        <a class="btn btn-sm btn-outline-secondary"
                                           href="?<?= current_query_params(['page' => $page - 1]) ?>">← Prev</a>
                                    <?php endif; ?>
                                    <?php if ($end < $total): ?>
                                        <a class="btn btn-sm btn-outline-secondary"
                                           href="?<?= current_query_params(['page' => $page + 1]) ?>">Next →</a>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <?php foreach ($rows as $r): ?>
                            <div class="card result-card">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <?= htmlspecialchars($r['title']) ?>
                                    </h5>
                                    <p class="result-meta mb-2">
                                        PID <?= (int)$r['PID'] ?> ·
                                        Professor: <?= htmlspecialchars($r['professor_name']) ?>
                                    </p>
                                    <p class="mb-0">
                                        <strong>Paid/Credit:</strong> <?= htmlspecialchars($r['paid_credit']) ?>
                                        &nbsp;·&nbsp;
                                        <strong># Students:</strong> <?= htmlspecialchars($r['num_students']) ?>
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
            <?php endif; ?>

            <!-- TAB 3: Matching projects by qualifications -->
            <?php if ($action === 'matchAny'): ?>
                <div class="tab-pane fade show active">
                    <form method="get" class="mb-3">
                        <input type="hidden" name="action" value="matchAny">
                        <div class="mb-3">
                            <label for="matchUID" class="form-label fw-bold">Your UID</label>
                            <input type="text"
                                   class="form-control"
                                   id="matchUID"
                                   name="matchUID"
                                   value="<?= htmlspecialchars($_GET['matchUID'] ?? '') ?>"
                                   placeholder="e.g. akp5ve">
                        </div>
                        <button type="submit" class="btn btn-success">Find Matching Projects</button>
                    </form>

                    <?php
                    if (!empty($_GET['matchUID'])) {
                        $uid = $_GET['matchUID'];

                        $countSql = "
                            SELECT COUNT(*) FROM (
                                SELECT p.PID
                                FROM Project p
                                JOIN Project_qualifications pq ON pq.PID = p.PID
                                JOIN Student_qualifications sq ON sq.qualification = pq.qualification
                                WHERE sq.UID = :uid
                                GROUP BY p.PID
                                HAVING COUNT(DISTINCT pq.qualification) >= 1
                            ) AS matched
                        ";
                        $countStmt = $db->prepare($countSql);
                        $countStmt->execute([':uid' => $uid]);
                        $total = (int)$countStmt->fetchColumn();

                        $sql = "
                            SELECT
                              p.PID,
                              p.title,
                              pe.name AS professor_name,
                              COUNT(DISTINCT pq.qualification) AS match_count
                            FROM Project p
                            JOIN Person  pe ON pe.UID = p.UID
                            JOIN Project_qualifications pq ON pq.PID = p.PID
                            JOIN Student_qualifications sq ON sq.qualification = pq.qualification
                            WHERE sq.UID = :uid
                            GROUP BY p.PID, p.title, professor_name
                            HAVING match_count >= 1
                            ORDER BY match_count DESC, p.PID DESC
                            LIMIT :offset, :perPage
                        ";
                        $stmt = $db->prepare($sql);
                        $stmt->bindValue(':uid', $uid, PDO::PARAM_STR);
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
                                    ? "Showing {$start}–{$end} of {$total} matching projects for " . htmlspecialchars($uid)
                                    : "No matching projects found for " . htmlspecialchars($uid)
                                ?>
                            </div>
                            <?php if ($total > $perPage): ?>
                                <div>
                                    <?php if ($page > 1): ?>
                                        <a class="btn btn-sm btn-outline-secondary"
                                           href="?<?= current_query_params(['page' => $page - 1]) ?>">← Prev</a>
                                    <?php endif; ?>
                                    <?php if ($end < $total): ?>
                                        <a class="btn btn-sm btn-outline-secondary"
                                           href="?<?= current_query_params(['page' => $page + 1]) ?>">Next →</a>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <?php foreach ($rows as $r): ?>
                            <div class="card result-card">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <?= htmlspecialchars($r['title']) ?>
                                    </h5>
                                    <p class="result-meta mb-2">
                                        PID <?= (int)$r['PID'] ?> ·
                                        Professor: <?= htmlspecialchars($r['professor_name']) ?>
                                    </p>
                                    <p class="mb-0">
                                        <strong>Overlapping qualifications:</strong> <?= (int)$r['match_count'] ?>
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
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="hr-footer text-bg-dark m-3 p-3">
    <p>&copy; 2025 HooResearches. All Rights Reserved.</p>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
