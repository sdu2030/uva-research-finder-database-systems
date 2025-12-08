<?php
session_start();
require_once 'connect-db.php';   // must define $db (PDO)

// Logged-in user info (set in login.php)
$uid  = $_SESSION['uid']  ?? null;
$name = $_SESSION['name'] ?? null;

// Pagination for lists on this page
$perPage = 20;
$page    = max(1, (int)($_GET['page'] ?? 1));
$offset  = ($page - 1) * $perPage;

// Fixed keyword list (20 keywords)
$allKeywords = [
    "machine learning", "GPU", "CS and medicine", "smart devices", "x86",
    "web development", "CS education", "LLMs", "parallel computing",
    "game development", "VR", "cybersecurity", "databases",
    "artificial intelligence", "cryptocurrency", "software testing",
    "cloud computing", "networks", "memory"
];

// Which keyword (if any) is selected
$selectedKeyword = $_GET['keyword'] ?? '';

$projectsForKeyword = [];
$totalForKeyword    = 0;

// Helper to safely run a query; avoids fatal errors
function safeQuery(PDO $db, string $sql, array $params = []) {
    try {
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    } catch (PDOException $e) {
        return false;
    }
}

/**
 * 1) Projects by selected keyword (title + keywords, clickable)
 */
if ($selectedKeyword && in_array($selectedKeyword, $allKeywords, true)) {
    // Count how many projects match this keyword
    $countSql = "
        SELECT COUNT(DISTINCT p.PID) AS cnt
        FROM Project p
        JOIN Project_keywords pk ON pk.PID = p.PID
        WHERE pk.keyword = :kw
    ";
    $countStmt = safeQuery($db, $countSql, [':kw' => $selectedKeyword]);
    if ($countStmt !== false) {
        $totalForKeyword = (int)$countStmt->fetchColumn();
    }

    // Fetch matching projects (limited/paged)
    $projSql = "
        SELECT 
          p.PID,
          p.title,
          GROUP_CONCAT(DISTINCT pk2.keyword ORDER BY pk2.keyword SEPARATOR ', ') AS keywords
        FROM Project p
        JOIN Project_keywords pk ON pk.PID = p.PID AND pk.keyword = :kw
        LEFT JOIN Project_keywords pk2 ON pk2.PID = p.PID
        GROUP BY p.PID, p.title
        ORDER BY p.PID DESC
        LIMIT :offset, :perPage
    ";
    try {
        $projStmt = $db->prepare($projSql);
        $projStmt->bindValue(':kw', $selectedKeyword, PDO::PARAM_STR);
        $projStmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $projStmt->bindValue(':perPage', $perPage, PDO::PARAM_INT);
        $projStmt->execute();
        $projectsForKeyword = $projStmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $projectsForKeyword = [];
    }
}

/**
 * 2) Projects matching student's qualifications
 *    (student must be logged in; based on Student_qualifications + Project_qualifications)
 */
$matchedProjects      = [];
$totalMatchedProjects = 0;

if ($uid) {
    $countMatchSql = "
        SELECT COUNT(*) FROM (
            SELECT DISTINCT p.PID
            FROM Project p
            JOIN Project_qualifications pq ON pq.PID = p.PID
            JOIN Student_qualifications sq
              ON sq.UID = :uid
             AND sq.qualification = pq.qualification
        ) AS matched
    ";
    $countMatchStmt = safeQuery($db, $countMatchSql, [':uid' => $uid]);
    if ($countMatchStmt !== false) {
        $totalMatchedProjects = (int)$countMatchStmt->fetchColumn();
    }

    $matchSql = "
        SELECT
          p.PID,
          p.title,
          GROUP_CONCAT(DISTINCT pk.keyword ORDER BY pk.keyword SEPARATOR ', ') AS keywords
        FROM Project p
        JOIN Project_qualifications pq ON pq.PID = p.PID
        JOIN Student_qualifications sq
          ON sq.UID = :uid
         AND sq.qualification = pq.qualification
        LEFT JOIN Project_keywords pk ON pk.PID = p.PID
        GROUP BY p.PID, p.title
        ORDER BY p.PID DESC
        LIMIT :offset, :perPage
    ";
    try {
        $matchStmt = $db->prepare($matchSql);
        $matchStmt->bindValue(':uid', $uid, PDO::PARAM_STR);
        $matchStmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $matchStmt->bindValue(':perPage', $perPage, PDO::PARAM_INT);
        $matchStmt->execute();
        $matchedProjects = $matchStmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $matchedProjects = [];
    }
}

/**
 * 3) Student's flagged projects (marks_interest)
 */
$flaggedProjects = [];
if ($uid) {
    $flagSql = "
        SELECT
          p.PID,
          p.title,
          GROUP_CONCAT(DISTINCT pk.keyword ORDER BY pk.keyword SEPARATOR ', ') AS keywords
        FROM Project p
        JOIN marks_interest mi ON mi.PID = p.PID AND mi.UID = :uid
        LEFT JOIN Project_keywords pk ON pk.PID = p.PID
        GROUP BY p.PID, p.title
        ORDER BY p.PID DESC
        LIMIT 20
    ";
    $flagStmt = safeQuery($db, $flagSql, [':uid' => $uid]);
    if ($flagStmt !== false) {
        $flaggedProjects = $flagStmt->fetchAll(PDO::FETCH_ASSOC);
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
    <meta name="description" content="Student project browser for HooResearches, a CS 3750 project.">
    <meta name="keywords" content="CS 3750, UVa research, student, projects, database systems">
    <title>HooResearches – Student Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet" crossorigin="anonymous">
</head>
<body>
<div class="text-left text-bg-dark m-3 p-3">
    <h1>HooResearches</h1>
    <p>A UVa CS research finder</p>
</div>

<div class="container mb-5">
    <!-- Header / welcome -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="h4 mb-0">Student Portal</h2>
            <small class="text-muted">
                Browse research projects, filter by keyword, and see projects matched to your qualifications.
            </small>
        </div>
        <div>
            <?php if ($uid): ?>
                <span class="badge bg-primary">Logged in as <?= htmlspecialchars($name ?: $uid) ?></span>
            <?php else: ?>
                <a href="login.php" class="btn btn-outline-primary btn-sm">Sign in</a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Browse all projects -->
    <div class="card mb-4">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
                <h3 class="h5 mb-1">Browse All Projects</h3>
                <p class="mb-0 text-muted">See the full project list with advanced filters.</p>
            </div>
            <a href="projects.php" class="btn btn-primary">Browse All Projects</a>
        </div>
    </div>

    <!-- Keyword selection -->
    <div class="card mb-4">
        <div class="card-body">
            <h3 class="h5 mb-3">Browse by Keyword</h3>
            <p class="text-muted">
                Select one or more areas you’re interested in. Clicking a keyword will show matching projects.
            </p>

            <div class="mb-3">
                <?php foreach ($allKeywords as $kw): ?>
                    <?php
                    $isActive = ($selectedKeyword === $kw);
                    $url = 'student.php?keyword=' . urlencode($kw);
                    ?>
                    <a href="<?= $url ?>"
                       class="badge rounded-pill <?= $isActive ? 'bg-primary' : 'bg-secondary' ?> text-decoration-none me-2 mb-2">
                        <?= htmlspecialchars($kw) ?>
                    </a>
                <?php endforeach; ?>
            </div>

            <?php if ($selectedKeyword && in_array($selectedKeyword, $allKeywords, true)): ?>
                <hr>
                <h4 class="h6 mb-2">
                    Projects tagged with: <span class="badge bg-info text-dark"><?= htmlspecialchars($selectedKeyword) ?></span>
                </h4>

                <?php
                $start = $totalForKeyword > 0 ? $offset + 1 : 0;
                $end   = min($offset + $perPage, $totalForKeyword);
                ?>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted">
                        Showing <?= $start ?>–<?= $end ?> of <?= $totalForKeyword ?> projects
                    </span>
                    <?php if ($totalForKeyword > $perPage): ?>
                        <div>
                            <?php if ($page > 1): ?>
                                <a class="btn btn-sm btn-outline-secondary"
                                   href="student.php?keyword=<?= urlencode($selectedKeyword) ?>&page=<?= $page - 1 ?>">
                                    ← Prev
                                </a>
                            <?php endif; ?>
                            <?php if ($end < $totalForKeyword): ?>
                                <a class="btn btn-sm btn-outline-secondary"
                                   href="student.php?keyword=<?= urlencode($selectedKeyword) ?>&page=<?= $page + 1 ?>">
                                    Next →
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if (empty($projectsForKeyword)): ?>
                    <p class="text-muted">No projects found for this keyword.</p>
                <?php else: ?>
                    <?php foreach ($projectsForKeyword as $proj): ?>
                        <div class="card mb-2">
                            <div class="card-body">
                                <h5 class="card-title mb-1">
                                    <a href="project_details.php?pid=<?= urlencode($proj['PID']) ?>"
                                       class="text-decoration-none">
                                        <?= htmlspecialchars($proj['title']) ?>
                                    </a>
                                </h5>
                                <?php if (!empty($proj['keywords'])): ?>
                                    <p class="mb-0">
                                        <strong>Keywords:</strong>
                                        <?= htmlspecialchars($proj['keywords']) ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <div class="mt-2">
                    <a href="student.php" class="btn btn-link btn-sm">Clear keyword filter</a>
                </div>
            <?php else: ?>
                <p class="text-muted mb-0">
                    Click a keyword above to see matching projects.
                </p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Projects matching student's qualifications -->
    <div class="card mb-4">
        <div class="card-body">
            <h3 class="h5 mb-2">Projects Matching Your Qualifications</h3>

            <?php if ($uid): ?>
                <p class="text-muted mb-3">
                    These projects share at least one qualification with your profile.
                </p>
                <?php if ($totalMatchedProjects === 0): ?>
                    <p class="text-muted mb-0">No projects currently match your qualifications.</p>
                <?php else: ?>
                    <?php if (!empty($matchedProjects)): ?>
                        <?php foreach ($matchedProjects as $proj): ?>
                            <div class="card mb-2">
                                <div class="card-body">
                                    <h5 class="card-title mb-1">
                                        <a href="project_details.php?pid=<?= urlencode($proj['PID']) ?>"
                                           class="text-decoration-none">
                                            <?= htmlspecialchars($proj['title']) ?>
                                        </a>
                                    </h5>
                                    <?php if (!empty($proj['keywords'])): ?>
                                        <p class="mb-0">
                                            <strong>Keywords:</strong>
                                            <?= htmlspecialchars($proj['keywords']) ?>
                                        </p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted">No matching projects on this page of results.</p>
                    <?php endif; ?>
                <?php endif; ?>
            <?php else: ?>
                <p class="text-muted mb-0">
                    Sign in from the <a href="login.php">login page</a> so we can match projects to your qualifications.
                </p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Flagged projects -->
    <div class="card mb-4">
        <div class="card-body">
            <h3 class="h5 mb-2">Your Flagged Projects</h3>
            <?php if ($uid): ?>
                <?php if (empty($flaggedProjects)): ?>
                    <p class="text-muted mb-0">You haven’t flagged any projects yet.</p>
                <?php else: ?>
                    <?php foreach ($flaggedProjects as $proj): ?>
                        <div class="card mb-2">
                            <div class="card-body">
                                <h5 class="card-title mb-1">
                                    <a href="project_details.php?pid=<?= urlencode($proj['PID']) ?>"
                                       class="text-decoration-none">
                                        <?= htmlspecialchars($proj['title']) ?>
                                    </a>
                                </h5>
                                <?php if (!empty($proj['keywords'])): ?>
                                    <p class="mb-0">
                                        <strong>Keywords:</strong>
                                        <?= htmlspecialchars($proj['keywords']) ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            <?php else: ?>
                <p class="text-muted mb-0">
                    Sign in to flag projects and see them here.
                </p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Back to login -->
    <div class="mb-4">
        <a href="login.php" class="btn btn-outline-secondary btn-sm">← Back to Login</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        crossorigin="anonymous"></script>
</body>
</html>
