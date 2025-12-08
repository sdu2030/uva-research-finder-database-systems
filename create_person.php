<?php
$newUser = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!empty($_POST['stuBtn'])) {
        $newUser = createPerson($_POST['password'], $_POST['person_desc'], $_POST['username']);
        if (!empty($_POST['qualifications'])) {
            $selectedQuals = $_POST['student_quals'];
            foreach ($selectedQuals as $student_qual) {
                addStudentQual($newUser['UID'], $student_qual);
            }
        }
    } else if (!empty($_POST['profBtn'])) {
        $newUser = createPerson($_POST['password'], $_POST['person_desc'], $_POST['username']);
        if (!empty($_POST['research_areas'])) {
            addResearchAreas($newUser['UID'], $_POST['research_areas']);
        }
        if (!empty($_POST['publications'])) {
            addPublications($newUser['UID'], $_POST['publications']);
        }
        if (!empty($_POST['courses_taught'])) {
            addCoursesTaught($newUser['UID'], $_POST['courses_taught']);
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
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script>
        function checkUserType(user) {
            if (user.value === "student") {
                document.getElementById("qualsBlock").style.display = "block";
                document.getElementById("researchBlock").style.display = "none";
                document.getElementById("pubsBlock").style.display = "none";
                document.getElementById("coursesBlock").style.display = "none";
                document.getElementById("stuBtn").style.display = "block";
                document.getElementById("profBtn").style.display = "none";
            } else if (user.value === "researcher") {
                document.getElementById("qualsBlock").style.display = "none";
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
                <input type="text" class="form-control" id="username">
            </div>
        </div>
        <div class="row mb-3 justify-content-center">
            <div class="col-sm-10">
                <label for="password" class="form-label fw-bold">Password</label>
                <input type="text" class="form-control" id="password">
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
                    <option value="python">Python</option>
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
            <button type="submit" class="btn btn-primary col-sm-10" id="stuBtn" style="display: block;">Submit</button>
            <button type="submit" class="btn btn-primary col-sm-10" id="profBtn">Submit</button>
        </div>
    </form>
    <?php require("footer.php"); ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>