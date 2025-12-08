<?php
if (isset($_POST['logoutBtn'])) {
    session_destroy();
    header("Location: login.php");
    exit;
}
?>

<?php
// Hide profile + logout when not logged in
if (!isset($_SESSION["loggedIn"])) {
    echo '<style> #logout, #profileBtn { display: none; }</style>';
}
?>

<div class="row text-bg-dark m-3 p-3 align-items-center">
    <div class="text-start col-sm-6">
        <h1>HooResearches</h1>
        <p class="mb-0">A UVa CS research finder</p>
    </div>

    <div class="text-end col-sm-6">
        <?php
            if (isset($_SESSION["currentUser"])) {
                echo "<h5 class='mb-2'>" . htmlspecialchars($_SESSION["currentUser"]["name"]) . "</h5>";
            }
        ?>

        <div class="d-inline-flex gap-2">
            <!-- ✅ All Projects (visible to everyone) -->
            <a href="projects.php" class="btn btn-outline-light btn-sm">
                All Projects
            </a>

            <!-- ✅ My Profile (changes based on user type) -->
            <?php if (isset($_SESSION["isResearcher"]) && $_SESSION["isResearcher"] === true): ?>
                <a href="professor.php" id="profileBtn" class="btn btn-outline-light btn-sm">
                    My Profile
                </a>
            <?php else: ?>
                <a href="student.php" id="profileBtn" class="btn btn-outline-light btn-sm">
                    My Profile
                </a>
            <?php endif; ?>

            <!-- ✅ Logout -->
            <form method="post" id="logout" name="logout" class="d-inline">
                <button type="submit" class="btn btn-primary btn-sm" id="logoutBtn" name="logoutBtn">
                    Logout
                </button>
            </form>
        </div>
    </div>
</div>
