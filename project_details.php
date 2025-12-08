<?php
session_start();
require('connect-db.php');
require('project-db.php');
require('prof-db.php');
var_dump($_SESSION);
var_dump($_GET);

if (isset($_GET['pid'])) {
    $_SESSION['pid'] = $_GET['pid'];
}

$_SESSION['uid'] = $_SESSION['currentUser']['UID'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['interested'])) {
    $pid = $_SESSION['pid'];
    $uid = $_SESSION['uid'];

    if ($_POST['interested'] == "1") {
        markInterested($uid, $pid);
    } else {
        unmarkInterested($uid, $pid);
    }

    header("Location: project_details.php?pid=$pid");
    exit();
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['updateBtn'])) {
        header("Location: update_project.php");
    }
    if (isset($_POST['deleteBtn'])) {
        deleteProject($_SESSION['pid']);
        header("Location: prof_details.php");
    }
}

$interested = getInterestedValue($_SESSION['uid'], $_SESSION['pid']);
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="Holly Kiker">
    <meta name="description" content="The project details page for HooResearches, a CS 3750 (Database Systems) project.">
    <meta name="keywords" content="CS 3750, UVa research, project details, Database Systems">
    <title>HooResearches Project Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
<?php require("header.php"); ?>
<form>
    <h3 class="text-center"><?php echo getTitle($_SESSION['pid'])?></h3>
    <div class="row mb-3 justify-content-center">
        
    <div class="text-center">
        <div>
            <label for="researcher" class="form-label fw-bold">Researcher: </label>
            
                <a href="prof_details.php?prof=<?php echo getResearcherId($_SESSION['pid']); ?>">
                <?php echo getResearcher($_SESSION['pid']); ?>
            </a>
            
        </div>
    </div>

    

    
            
</form>
<div class="row mb-3 justify-content-center">
        <div class="form-check col-sm-3">
<form method="POST" action="project_details.php">
    <!-- This field is sent if checkbox is unchecked -->
    <input type="hidden" name="interested" value="0">
    
    <input type="checkbox" name="interested" value="1"
    <?php if ($interested) echo "checked"; ?>
    onchange="this.form.submit()">
    Interested
</form>
        </div>

        <div class="col-sm-3">
            <label for="pcr" class="form-label fw-bold">Paid or Credit?</label>
            <div >
                <p class="border p-1" >
                <?php echo getPaid_Credit($_SESSION['pid']);?>
                </p>
        </div>
        </div>
        <div class="col-sm-3">
            <label for="numStudents" class="form-label fw-bold">Number of Students</label>
            <div >
                <p class="border p-1">
                <?php echo getNumStudents($_SESSION['pid']);?>
                </p>
        </div>
        </div>
    </div>

    <div class="row mb-3 justify-content-center">
        <div class="col-sm-10">
            <label for="projectDesc" class="form-label fw-bold">Description</label>
            <div>
                <p class="border p-2">
                <?php echo getDescription($_SESSION['pid']);?>
                </p>
            </div>
        </div>
    </div>

    <div class="row mb-3 justify-content-center">
        <div class="col-sm-10">
            <label for="keywords" class="form-label fw-bold">Keywords</label>
            <div>
                <p class="border p-2">
                <?php echo getKeywords($_SESSION['pid']);?>
                </p>
            </div>
        </div>
    </div>

    <div class="row mb-3 justify-content-center">
        <div class="col-sm-10">
            <label for="qualifications" class="form-label fw-bold">Qualifications</label>
            <div>
                <p class="border p-2">
                <?php echo getQualifications($_SESSION['pid']);?>
                </p>
            </div>
        </div>
    </div>
</form>
<?php if ($_SESSION['currentUser']['UID'] == getProjectById($_SESSION['pid'])['UID']) {
    echo '<div class="row mt-3 justify-content-center">
    <form method="post" id="update" name="update">

        <button type="submit" class="btn btn-primary col-sm-10" id="updateBtn" name="updateBtn">Update</button>

    </form>

    <form method="post" id="delete" name="delete">

        <button type="submit" class="btn btn-primary col-sm-10" id="deleteBtn" name="deleteBtn">Delete</button>

    </form>
</div>';
}
?>

<?php require("footer.php"); ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
