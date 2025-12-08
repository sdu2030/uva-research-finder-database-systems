<?php
session_start();
require_once ("connect-db.php");
require ("login-db.php");
$username = $password = $error = "";

// If already logged in, redirect
if(isset($_SESSION["loggedIn"]) && $_SESSION["loggedIn"] === true) {
    if($_SESSION["isResearcher"] === true)
    {
        header("location: professor.php");
    } else {
        header("location: projects.php");
    }
    exit;
}
if($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST["username"])) {
        $error = "Please enter username.";
    } else {
        $username = $_POST["username"];
    }
    if (empty($_POST["password"])) {
        $error = "Please enter your password.";
    } else {
        $password = $_POST["password"];
    }
    if (!empty($username) && !empty($password)) {
        $current_user = verify_user($username, $password);
        if (empty($current_user)) {
            $error = "Invalid username or password.";
        } else {
            $_SESSION["loggedIn"] = true;
            $_SESSION["currentUser"] = $current_user;
            $_SESSION["isResearcher"] = is_researcher($current_user["UID"]);
            if ($_SESSION["isResearcher"] === true) {
                header("location: professor.php");
            } else {
                header("location: projects.php");
            }
            exit;
        }
    } else {
        $error = "Please enter username and password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="Tommy Le">
    <meta name="description" content="The login page for HooResearches, a CS 3750 (Database Systems) project.">
    <meta name="keywords" content="CS 3750, UVa research, login, Database Systems">
	<title>HooResearches Login</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script>
        function create_person() {
            window.location.href = "create_person.php";
        }
    </script>
</head>
<body>
    <?php require ("header.php") ?>
    <div class="row mb-3 p-3 justify-content-center">
        <?php if(!empty($error)){ echo "<div class='alert alert-danger col-sm-10'>" . $error . "</div>";} ?>
    </div>
    <form method="post" action="<?php $_SERVER['PHP_SELF'] ?>" onsubmit="return validateInput()">
        <h3 class="text-center">Sign In</h3>
        <div class="row mb-3 justify-content-center">
            <div class="col-sm-10">
                <label for="username" class="form-label fw-bold">Username</label>
                <input type="text" class="form-control" id="username" name="username">
            </div>
        </div>
        <div class="row mb-5 justify-content-center">
            <div class="col-sm-10">
                <label for="password" class="form-label fw-bold">Password</label>
                <input type="password" class="form-control" id="password" name="password">
            </div>
        </div>
        <div class="row justify-content-center">
            <button type="submit" class="btn btn-primary col-sm-10" id="loginBtn" name="loginBtn">Login</button>
        </div>
    </form>
    <div class="row mt-3 justify-content-center">
        <button onclick="create_person()" class="btn btn-primary col-sm-10">Create Account</button>
    </div>
    <?php require ("footer.php") ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>