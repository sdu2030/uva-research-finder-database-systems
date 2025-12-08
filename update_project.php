<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require('connect-db.php');
require('project-db.php');
?>

<?php
$current_project = getProjectById($_SESSION['pid']);
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!empty($_POST['updateBtn'])) {
        updateProject($_POST['title'], $_POST['paid_credit'], $_POST['num_students'], $_POST['project_desc'], $_SESSION['currentUser']['UID']);
        if (isset($_POST['keywords'])) {
            $selectedKeywords = $_POST['keywords'];
            foreach ($selectedKeywords as $keyword) {
                addProjectKeyword($_POST['PID'], $keyword);
            }
        }
        if (isset($_POST['project_quals'])) {
            $selectedQuals = $_POST['project_quals'];
            foreach ($selectedQuals as $project_qual) {
                addProjectQual($_POST['PID'], $project_qual);
            }
        }
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="Tommy Le">
    <meta name="description" content="The update project page for HooResearches, a CS 3750 (Database Systems) project.">
    <meta name="keywords" content="CS 3750, UVa research, create project, Database Systems">
    <title>HooResearches Update Project</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
    <?php require("header.php"); ?>
    <form method="post" action="<?php $_SERVER['PHP_SELF'] ?>" onsubmit="return validateInput()">
        <h3 class="text-center">Update Project</h3>
        <div class="row mb-3 justify-content-center">
            <div class="col-sm-10">
                <label for="title" class="form-label fw-bold">Title*</label>
                <input type="text" class="form-control" id="title" name="title">
            </div>
        </div>
        <div class="row mb-3 justify-content-center">
            <div class="form-check col-sm-3">
                <label for="paid_credit">Paid or Credit*</label>
                <select name="paid_credit" id="paid_credit">
                    <option value="paid">Paid</option>
                    <option value="credit">Credit</option>
                </select>
            </div>
            <div class="col-sm-7">
                <label for="num_students" class="form-label fw-bold">Number of Students*</label>
                <input type="text" class="form-control" id="num_students" name="num_students">
            </div>
        </div>
        <div class="row mb-3 justify-content-center">
            <div class="col-sm-10">
                <label for="project_desc" class="form-label fw-bold">Description*</label>
                <textarea class="form-control" id="project_desc" name="project_desc"></textarea>
            </div>
        </div>
        <div class="row mb-3 justify-content-center">
            <div class="col-sm-10">
                <label for="keywords">Keywords</label>
                <select name="keywords[]" id="keywords" multiple="multiple">
                    <option value="machine learning">Machine Learning</option>
                    <option value="os">OS</option>
                </select>
            </div>
        </div>
        <div class="row mb-3 justify-content-center">
            <div class="col-sm-10">
                <label for="project_quals">Qualifications</label>
                <select name="project_quals[]" id="project_quals" multiple="multiple">
                    <option value="python">Python</option>
                </select>
            </div>
        </div>
        <div class="row justify-content-center">
            <button type="submit" class="btn btn-primary col-sm-10" id="updateBtn" name="updateBtn">Update</button>
        </div>
        <input type="hidden" id="PID" name="PID">
    </form>
    <?php require("footer.php"); ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
