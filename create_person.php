<?php
require("connect-db.php");
require("person-db.php");
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['stuBtn'])) {
        createPerson($_POST['username'], $_POST['password'], $_POST['person_desc'], $_POST['name']);
        if (!empty($_POST['qualifications'])) {
            $selectedQuals = $_POST['student_quals'];
            foreach ($selectedQuals as $student_qual) {
                addStudentQual($_POST['username'], $student_qual);
            }
        }
        header("location: login.php");
    } else if (isset($_POST['profBtn'])) {
        createPerson($_POST['username'], $_POST['password'], $_POST['person_desc'], $_POST['name']);
        setRole($_POST['username'], $_POST['research_role']);
        if (!empty($_POST['research_areas'])) {
            addResearchAreas($_POST['username'], $_POST['research_areas']);
        }
        if (!empty($_POST['publications'])) {
            addPublications($_POST['username'], $_POST['publications']);
        }
        if (!empty($_POST['courses_taught'])) {
            addCoursesTaught($_POST['username'], $_POST['courses_taught']);
        }
        header("location: login.php");
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
        function checkUserType(user) {
            if (user.value === "student") {
                document.getElementById("qualsBlock").style.display = "block";
                document.getElementById("roleBlock").style.display = "none";
                document.getElementById("researchBlock").style.display = "none";
                document.getElementById("pubsBlock").style.display = "none";
                document.getElementById("coursesBlock").style.display = "none";
                document.getElementById("stuBtn").style.display = "block";
                document.getElementById("profBtn").style.display = "none";
            } else if (user.value === "researcher") {
                document.getElementById("qualsBlock").style.display = "none";
                document.getElementById("roleBlock").style.display = "block";
                document.getElementById("researchBlock").style.display = "block";
                document.getElementById("pubsBlock").style.display = "block";
                document.getElementById("coursesBlock").style.display = "block";
                document.getElementById("stuBtn").style.display = "none";
                document.getElementById("profBtn").style.display = "block";
            }
        }
    </script>
</head>
<body>
    <?php require("header.php"); ?>
    <form method="post" action="<?php $_SERVER['PHP_SELF'] ?>" onsubmit="return validateInput()">
        <h3 class="text-center">Create Account</h3>
        <div class="row mb-3 justify-content-center">
            <div class="col-sm-10">
                <label for="username" class="form-label fw-bold">Username</label>
                <input type="text" class="form-control" id="username" name="username">
            </div>
        </div>
        <div class="row mb-3 justify-content-center">
            <div class="col-sm-10">
                <label for="password" class="form-label fw-bold">Password</label>
                <input type="password" class="form-control" id="password" name="password">
            </div>
        </div>
        <div class="row mb-3 justify-content-center">
            <div class="col-sm-10">
                <label for="name" class="form-label fw-bold">Name</label>
                <input type="text" class="form-control" id="name" name="name">
            </div>
        </div>
        <div class="row mb-3 justify-content-center">
            <div class="col-sm-10">
                <label for="user_type" class="form-label fw-bold me-2">User Type</label>
                <select name="user_type" id="user_type" onchange="checkUserType(this);">
                    <option value="student">Student</option>
                    <option value="researcher">Researcher</option>
                </select>
            </div>
        </div>
        <div class="row mb-3 justify-content-center">
            <div class="col-sm-10">
                <label for="person_desc" class="form-label fw-bold">Description</label>
                <textarea class="form-control" id="person_desc" name="person_desc" placeholder="Add an optional description of yourself."></textarea>
            </div>
        </div>
        <div class="row mb-3 justify-content-center">
            <div class="col-sm-10" id="qualsBlock" style="display: block;">
                <label for="student_quals" class="form-label fw-bold">Qualifications</label>
                <select name="student_quals[]" id="student_quals" multiple="multiple">
                    <option value="cs2100">CS 2100 - Data Structures and Algorithms 1</option>
                    <option value="cs2120">CS 2120 - Discrete Mathematics and Theory 1</option>
                    <option value="cs2130">CS 2130 - Computer Systems and Organization 1</option>
                    <option value="cs3100">CS 3100 - Data Structures and Algorithms 2</option>
                    <option value="cs3120">CS 3120 - Discrete Mathematics and Theory 2</option>
                    <option value="cs3130">CS 3130 - Computer Systems and Organization 2</option>
                    <option value="cs3140">CS 3140 - Software Development Essentials</option>
                    <option value="cs3240">CS 3240 - Software Engineering</option>
                </select>
            </div>
        </div>
        <div class="row mb-3 justify-content-center">
            <div class="col-sm-10" id="roleBlock" style="display: none;">
                <label for="research_role" class="form-label fw-bold me-2">Role</label>
                <select name="research_role" id="research_role" onchange="checkUserType(this);">
                    <option value="student">Professor</option>
                    <option value="researcher">Assistant Professor</option>
                    <option value="student">Associate Professor</option>
                    <option value="researcher">Research Associate</option>
                </select>
            </div>
        </div>
        <div class="row mb-3 justify-content-center">
            <div class="col-sm-10" id="researchBlock" style="display: none;">
                <label for="research_areas" class="form-label fw-bold">Research Areas</label>
                <textarea class="form-control" id="research_areas" name="research_areas" placeholder="Add an optional comma-separated list of your research areas."></textarea>
            </div>
        </div>
        <div class="row mb-3 justify-content-center">
            <div class="col-sm-10" id="pubsBlock" style="display: none;">
                <label for="publications" class="form-label fw-bold">Publications</label>
                <textarea class="form-control" id="publications" name="publications" placeholder="Add an optional comma-separated list of your publications."></textarea>
            </div>
        </div>
        <div class="row mb-3 justify-content-center">
            <div class="col-sm-10" id="coursesBlock" style="display: none;">
                <label for="courses_taught" class="form-label fw-bold">Courses Taught</label>
                <textarea class="form-control" id="courses_taught" name="courses_taught" placeholder="Add an optional comma-separated list of your courses."></textarea>
            </div>
        </div>
        <div class="row justify-content-center">
            <button type="submit" class="btn btn-primary col-sm-10" id="stuBtn" name="stuBtn" style="display: block;">Submit</button>
            <button type="submit" class="btn btn-primary col-sm-10" id="profBtn" name="profBtn" style="display: none;">Submit</button>
        </div>
    </form>
    <?php require("footer.php"); ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>