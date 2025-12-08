<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require('connect-db.php');
require('project-db.php');
?>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!empty($_POST['addBtn'])) {
        createProject($_POST['title'], $_POST['paid_credit'], $_POST['num_students'], $_POST['project_desc'], $UID);
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
    <meta name="description" content="The create project page for HooResearches, a CS 3750 (Database Systems) project.">
    <meta name="keywords" content="CS 3750, UVa research, create project, Database Systems">
    <title>HooResearches Create Project</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
    <?php require("header.php"); ?>
    <form method="post" action="<?php $_SERVER['PHP_SELF'] ?>" onsubmit="return validateInput()">
        <h3 class="text-center">Create Project</h3>
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
                    <option value="os">OS</option>
                    <option value="gpu">GPU</option>
                    <option value="cs-and-medicine">CS and medicine</option>
                    <option value="smart-devices">Smart devices</option>
                    <option value="machine-learning">Machine learning</option>
                    <option value="x86">x86</option>
                    <option value="web-development">Web development</option>
                    <option value="cs-education">CS education</option>
                    <option value="llms">LLMs</option>
                    <option value="ml">ML</option>
                    <option value="parallel-computing">Parallel computing</option>
                    <option value="game-development">Game development</option>
                    <option value="vr">VR</option>
                    <option value="cybersecurity">Cybersecurity</option>
                    <option value="databases">Databases</option>
                    <option value="artificial-intelligence">Artificial intelligence</option>
                    <option value="cryptocurrency">Cryptocurrency</option>
                    <option value="software-testing">Software testing</option>
                    <option value="cloud-computing">Cloud computing</option>
                    <option value="networks">Networks</option>
                    <option value="memory">Memory</option>
                </select>
            </div>
        </div>
        <div class="row mb-3 justify-content-center">
            <div class="col-sm-10">
                <label for="project_quals">Qualifications</label>
                <select name="project_quals[]" id="project_quals" multiple="multiple">
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
        <div class="row justify-content-center">
            <button type="submit" class="btn btn-primary col-sm-10" id="addBtn" name="addBtn">Submit</button>
        </div>
        <input type="hidden" id="PID" name="PID" value="<?php echo $_POST['PID']; ?>">
    </form>
    <?php require("footer.php"); ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
