<?php
session_start();
require_once 'connect-db.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$uid   = $_SESSION['uid'] ?? null;
$name  = $_SESSION['name'] ?? null;

// --- Keyword filter from dropdown ---
$selectedKeyword = $_GET['keyword'] ?? 'none';

// Data containers
$keywordProjects  = [];
$flaggedProjects  = [];
$recommended      = [];

$keywordError     = '';
$flaggedError     = '';
$recommendedError = '';

// --- 1) Projects by selected keyword ---
if ($selectedKeyword !== 'none') {
    try {
        $sql = "
            SELECT DISTINCT
              p.PID,
              p.title,
              GROUP_CONCAT(pk.keyword ORDER BY pk.keyword SEPARATOR ', ') AS keywords
            FROM Project p
            JOIN Project_keywords pk ON pk.PID = p.PID
            WHERE pk.keyword = :kw
            GROUP BY p.PID, p.title
            ORDER BY p.PID DESC
            LIMIT 20
        ";
        $stmt = $db->prepare($sql);
        $stmt->execute([':kw' => $selectedKeyword]);
        $keywordProjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $keywordError = $e->getMessage();
    }
}

// --- 2) Flagged projects for this student (marks_interest) ---
if ($uid) {
    try {
        $sql = "
            SELECT
              p.PID,
              p.title,
              GROUP_CONCAT(pk.keyword ORDER BY pk.keyword SEPARATOR ', ') AS keywords
            FROM marks_interest mi
            JOIN Project p ON mi.PID = p.PID
            LEFT JOIN Project_keywords pk ON pk.PID = p.PID
            WHERE mi.UID = :uid
            GROUP BY p.PID, p.title
            ORDER BY p.PID DESC
            LIMIT 20
        ";
        $stmt = $db->prepare($sql);
        $stmt->execute([':uid' => $uid]);
        $flaggedProjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $flaggedError = $e->getMessage();
    }

    // --- 3) Projects matching student qualifications ---
    // Student_qualifications(UID, qualification)
    // Project_qualifications(PID, qualification)
    try {
        $sql = "
            SELECT DISTINCT
              p.PID,
              p.title,
              GROUP_CONCAT(DISTINCT pk.keyword ORDER BY pk.keyword SEPARATOR ', ') AS keywords
            FROM Student_qualifications sq
            JOIN Project_qualifications pq
              ON sq.qualification = pq.qualification
            JOIN Project p
              ON pq.PID = p.PID
            LEFT JOIN Project_keywords pk
              ON pk.PID = p.PID
            WHERE sq.UID = :uid
            GROUP BY p.PID, p.title
            ORDER BY p.PID DESC
            LIMIT 20
        ";
        $stmt = $db->prepare($sql);
        $stmt->execute([':uid' => $uid]);
        $recommended = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $recommendedError = $e->getMessage();
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>HooResearches – Student Portal</title>
    <meta name="description" content="Student page for HooResearches, a UVa CS research finder.">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet" crossorigin="anonymous">
</head>
<body>
<div class="text-left text-bg-dark m-3 p-3">
    <h1>HooResearches</h1>
    <p>A UVa CS research finder</p>
</div>

<div class="container mb-5">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 mb-1">Student Portal</h2>
            <?php if ($uid): ?>
                <small class="text-muted">
                    Logged in as <strong><?= htmlspecialchars($uid) ?></strong>
                    <?= $name ? " (" . htmlspecialchars($name) . ")" : "" ?>
                </small>
            <?php else: ?>
                <small class="text-muted">
                    Browsing as guest. Sign in to flag projects and get personalized matches.
                </small>
            <?php endif; ?>
        </div>
        <div class="btn-group">
            <a href="login.php" class="btn btn-outline-secondary btn-sm">Back to Login</a>
        </div>
    </div>

    <!-- Primary actions -->
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Browse All Projects</h5>
                    <p class="card-text">
                        See all available research projects. You can filter further on the projects page.
                    </p>
                    <a href="projects.php" class="btn btn-primary">Browse Projects</a>
                </div>
            </div>
        </div>

        <?php if ($uid): ?>
        <div class="col-md-6">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Matched to Your Qualifications</h5>
                    <p class="card-text">
                        We match your qualifications to project requirements using your stored profile.
                    </p>
                    <p class="text-muted small mb-0">
                        (Using <code>Student_qualifications</code> and <code>Project_qualifications</code>.)
                    </p>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Keyword -> projects section -->
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h3 class="h5 mb-0">Find Projects by Keyword</h3>
            <small class="text-muted">Select a keyword to see matching projects.</small>
        </div>

        <form class="row g-2 align-items-end mb-3" method="get" action="student.php">
            <div class="col-md-6">
                <label for="keyword" class="form-label fw-bold">Keyword</label>
                <select name="keyword" id="keyword" class="form-select">
                    <option value="none">– Select a keyword –</option>
                    <?php
                    $keywordsList = [
                        "machine learning", "GPU", "CS and medicine", "smart devices", "x86",
                        "web development", "CS education", "LLMs", "parallel computing",
                        "game development", "VR", "cybersecurity", "databases",
                        "artificial intelligence", "cryptocurrency", "software testing",
                        "cloud computing", "networks", "memory"
                    ];
                    foreach ($keywordsList as $kw):
                    ?>
                        <option value="<?= htmlspecialchars($kw) ?>"
                            <?= ($selectedKeyword === $kw) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($kw) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-outline-primary w-100 mt-2">Show Projects</button>
            </div>
        </form>

        <?php if (!empty($keywordError)): ?>
            <div class="alert alert-danger">
                Error loading keyword projects: <?= htmlspecialchars($keywordError) ?>
            </div>
        <?php elseif ($selectedKeyword !== 'none'): ?>
            <?php if (empty($keywordProjects)): ?>
                <p class="text-muted">
                    No projects found with keyword <strong><?= htmlspecialchars($selectedKeyword) ?></strong>.
                </p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-sm align-middle">
                        <thead class="table-secondary">
                        <tr>
                            <th>Title</th>
                            <th>Keywords</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($keywordProjects as $p): ?>
                            <tr>
                                <td>
                                    <a href="project_details.php?pid=<?= urlencode($p['PID']) ?>"
                                       class="text-decoration-none">
                                        <?= htmlspecialchars($p['title']) ?>
                                    </a>
                                </td>
                                <td><?= htmlspecialchars($p['keywords'] ?? '') ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <p class="text-muted small">Showing up to 20 matching projects.</p>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <?php if ($uid): ?>
        <!-- Flagged projects -->
        <div class="mb-4">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h3 class="h5 mb-0">Your Flagged Projects</h3>
                <small class="text-muted">Projects you’ve marked as interested.</small>
            </div>

            <?php if (!empty($flaggedError)): ?>
                <div class="alert alert-danger">
                    Error loading flagged projects: <?= htmlspecialchars($flaggedError) ?>
                </div>
            <?php elseif (empty($flaggedProjects)): ?>
                <p class="text-muted">
                    You haven’t flagged any projects yet. Open a project and use “Mark as interested”.
                </p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-sm align-middle">
                        <thead class="table-secondary">
                        <tr>
                            <th>Title</th>
                            <th>Keywords</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($flaggedProjects as $p): ?>
                            <tr>
                                <td>
                                    <a href="project_details.php?pid=<?= urlencode($p['PID']) ?>"
                                       class="text-decoration-none">
                                        <?= htmlspecialchars($p['title']) ?>
                                    </a>
                                </td>
                                <td><?= htmlspecialchars($p['keywords'] ?? '') ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <!-- Recommended projects by qualifications -->
        <div class="mb-4">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h3 class="h5 mb-0">Projects Matching Your Qualifications</h3>
                <small class="text-muted">Based on your stored qualifications.</small>
            </div>

            <?php if (!empty($recommendedError)): ?>
                <div class="alert alert-danger">
                    Error loading recommended projects: <?= htmlspecialchars($recommendedError) ?>
                </div>
            <?php elseif (empty($recommended)): ?>
                <p class="text-muted">
                    No recommended projects found based on your current qualifications.
                    You can still browse all projects above.
                </p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-sm align-middle">
                        <thead class="table-secondary">
                        <tr>
                            <th>Title</th>
                            <th>Keywords</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($recommended as $p): ?>
                            <tr>
                                <td>
                                    <a href="project_details.php?pid=<?= urlencode($p['PID']) ?>"
                                       class="text-decoration-none">
                                        <?= htmlspecialchars($p['title']) ?>
                                    </a>
                                </td>
                                <td><?= htmlspecialchars($p['keywords'] ?? '') ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <p class="text-muted small">Showing up to 20 recommended projects.</p>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        crossorigin="anonymous"></script>
</body>
</html>
