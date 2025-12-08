<?php
    if(isset($_POST['logoutBtn'])) {
        session_destroy();
        header("Location: login.php");
    }
?>

<?php
    if(!isset($_SESSION["loggedIn"])) {
        echo '<style> #logout { display: none; }</style>';
    }
?>

<div class="row text-bg-dark m-3 p-3">
    <div class="text-start col-sm-6">
        <h1>HooResearches</h1>
        <p>A UVa CS research finder</p>
    </div>
    <div class="text-end col-sm-6">
        <?php
            if (isset($_SESSION["currentUser"])) {
                echo "<h1>" . $_SESSION["currentUser"]["name"] . "</h1>";
            }
        ?>
        <form method="post" id="logout" name="logout">
            <button type="submit" class="btn btn-primary" id="logoutBtn" name="logoutBtn">Logout</button>
        </form>
    </div>
</div>
