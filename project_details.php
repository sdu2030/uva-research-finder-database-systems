<?php
require('connect-db.php');
require('project-db.php');
session_start();
$_SESSION['pid'] = 104;
#$list_of_projects = getAllProjects()
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
        <div class="col-sm-10">
            <label for="projectTitle" class="form-label fw-bold">Title*</label>
            <input type="text" class="form-control" id="projectTitle">
        </div>
    </div>

    <div class="row mb-3 justify-content-center">
        <div class="form-check col-sm-3">
            <input class="form-check-input" type="checkbox" id="paid">
            <label class="form-check-label fw-bold" for="paid">Paid?</label>
        </div>
        <div class="col-sm-7">
            <label for="numStudents" class="form-label fw-bold">Number of Students*</label>
            <input type="text" class="form-control" id="numStudents">
        </div>
    </div>

    <div class="row mb-3 justify-content-center">
        <div class="col-sm-10">
            <label for="projectDesc" class="form-label fw-bold">Description*</label>
            <textarea class="form-control" id="projectDesc"></textarea>
        </div>
    </div>

    <div class="row mb-3 justify-content-center">
        <div class="col-sm-10">
            <label for="keywords" class="form-label fw-bold">Keywords</label>
            <input type="text" class="form-control" id="keywords">
        </div>
    </div>
</form>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
