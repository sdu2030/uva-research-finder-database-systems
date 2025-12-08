<?php
session_start();
require_once 'connect-db.php';   // make sure this defines $db (PDO)

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');   // not used for real auth in this project
    $userType = $_POST['userType'] ?? 'student';  // "student" or "professor"

    if ($username === '') {
        $error = 'Please enter your UVA UID (e.g., akp5ve).';
    } else {
        try {
            // Look up user in Person table by UID
            $sql = "SELECT UID, name FROM Person WHERE UID = :uid";
            $stmt = $db->prepare($sql);
            $stmt->execute([':uid' => $username]);
            $person = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$person) {
                $error = 'No user found with that UID.';
            } else {
                // Save login info in session
                $_SESSION['uid']      = $person['UID'];
                $_SESSION['name']     = $person['name'];
                $_SESSION['userType'] = ($userType === 'professor') ? 'professor' : 'student';

                // Redirect based on chosen type
                if ($_SESSION['userType'] === 'professor') {
                    header('Location: professor.php');
                } else {
                    header('Location: student.php');
                }
                exit;
            }
        } catch (PDOException $e) {
            // If something is wrong with the DB connection/query
            $error = 'Database error while logging in.';
        }
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
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
          crossorigin="anonymous">
</head>
<body>
    <div class="text-left text-bg-dark m-3 p-3">
        <h1>HooResearches</h1>
        <p>A UVa CS research finder</p>
    </div>

    <div class="container mb-4">
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <!-- Let the form post back to the same URL -->
        <form method="post">
            <h3 class="text-center">Sign In</h3>

            <div class="row mb-3 justify-content-center">
                <div class="col-sm-10">
                    <label for="username" class="form-label fw-bold">Username</label>
                    <input type="text"
                           class="form-control"
                           id="username"
                           name="username"
                           value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                           placeholder="e.g., akp5ve">
                </div>
            </div>

            <div class="row mb-3 justify-content-center">
                <div class="col-sm-10">
                    <label for="password" class="form-label fw-bold">Password</label>
                    <input type="password"
                           class="form-control"
                           id="password"
                           name="password"
                          >
                </div>
            </div>

            <fieldset class="row mb-3 justify-content-center">
                <div class="col-sm-10">
                    <legend class="col-form-label pt-0 fw-bold">User Type</legend>

                    <div class="form-check">
                        <input class="form-check-input"
                               type="radio"
                               name="userType"
                               id="student"
                               value="student"
                               <?= (($_POST['userType'] ?? 'student') === 'student') ? 'checked' : '' ?>>
                        <label class="form-check-label" for="student">Student</label>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input"
                               type="radio"
                               name="userType"
                               id="researcher"
                               value="professor"
                               <?= (($_POST['userType'] ?? '') === 'professor') ? 'checked' : '' ?>>
                        <label class="form-check-label" for="researcher">Researcher</label>
                    </div>
                </div>
            </fieldset>

            <div class="row justify-content-center">
                <button type="submit" class="btn btn-primary col-sm-10">Submit</button>
            </div>
        </form>

        <div class="row mt-3 justify-content-center">
            <!-- Still non-functional for now; just not disabled -->
            <button type="button"
                    class="btn btn-primary col-sm-4"
                   >
                Forgot Password?
            </button>
            <div class="col-sm-2"></div>
            <button type="button"
                    class="btn btn-primary col-sm-4"
                  >
                Need An Account?
            </button>
        </div>
    </div>

    <div class="text-left text-bg-dark m-3 p-3">
        <p>Note: Students can browse projects on HooResearches without signing in but must be signed in to flag projects. Researchers must sign in to create, edit, or delete projects.</p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
            crossorigin="anonymous"></script>
</body>
</html>
