<?php
require('connect-db.php');
require('project-db.php');
session_start();
$_SESSION['pid'] = 104;
$_SESSION['uid'] = 'akp5ve';
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
<div class="text-left text-bg-dark m-3 p-3">
    <h1>HooResearches</h1>
    <p>A UVa CS research finder</p>
</div>
<form>
    <h3 class="text-center"><?php echo getTitle($_SESSION['pid'])?></h3>
    <div class="row mb-3 justify-content-center">
        
    <div class="row mb-3 justify-content-center">
        <div class="col-sm-10">
            <label for="researcher" class="form-label fw-bold">Researcher</label>
            <div>
                <?php echo getResearcher($_SESSION['pid']);?>
            </div>
        </div>
    </div>

    <div class="row mb-3 justify-content-center">
        <div class="form-check col-sm-3">
            <input class="form-check-input" type="checkbox" id="interest" 
                <?php if (getInterestedValue($_SESSION['uid'],$_SESSION['pid']) )echo 'checked'; ?>>
            <label class="form-check-label fw-bold" for="paid">Interested?</label>
        </div>

        <div class="col-sm-3">
            <label for="pcr" class="form-label fw-bold">Paid or Credit?</label>
            <div >
                <?php echo getPaid_Credit($_SESSION['pid']);?>
        </div>
        </div>
        <div class="col-sm-3">
            <label for="numStudents" class="form-label fw-bold">Number of Students</label>
            <div >
                <?php echo getNumStudents($_SESSION['pid']);?>
        </div>
        </div>
    </div>

    <div class="row mb-3 justify-content-center">
        <div class="col-sm-10">
            <label for="projectDesc" class="form-label fw-bold">Description</label>
            <div>
                <?php echo getDescription($_SESSION['pid']);?>
            </div>
        </div>
    </div>

    <div class="row mb-3 justify-content-center">
        <div class="col-sm-10">
            <label for="keywords" class="form-label fw-bold">Keywords</label>
            <div>
                <?php echo getKeywords($_SESSION['pid']);?>
            </div>
        </div>
    </div>

    <div class="row mb-3 justify-content-center">
        <div class="col-sm-10">
            <label for="qualifications" class="form-label fw-bold">Qualifications</label>
            <div>
                <?php echo getQualifications($_SESSION['pid']);?>
            </div>
        </div>
    </div>
</form>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
