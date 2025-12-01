<?php
// student.php
// Future: connect to Google Cloud SQL using connect-db.php
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="Nicole Savage">
    <meta name="description" content="The student page for HooResearches, a CS 4750 project.">
    <meta name="keywords" content="CS 4750, UVA research, student, project search, Database Systems">
    <title>HooResearches Student Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
<div class="text-left text-bg-dark m-3 p-3">
    <h1>HooResearches</h1>
    <p>A UVa CS research finder — Student Portal</p>
</div>

<div class="container mb-5">
    <h3 class="text-center mb-4">Search Research Projects</h3>

    <!-- Search by Keyword -->
    <form method="get" action="">
        <div class="row mb-3 justify-content-center">
            <div class="col-sm-10">
                <label for="keyword" class="form-label fw-bold">Search by Keyword</label>
                <input type="text" class="form-control" id="keyword" name="keyword" placeholder="e.g. machine learning, robotics">
            </div>
        </div>
        <div class="row justify-content-center">
            <button type="submit" class="btn btn-primary col-sm-10">Search</button>
        </div>
    </form>

    <hr class="my-4">

    <!-- Flagged Projects -->
    <form method="get" action="">
        <h4 class="text-center mb-3">View My Flagged Projects</h4>
        <div class="row mb-3 justify-content-center">
            <div class="col-sm-10">
                <label for="uid" class="form-label fw-bold">Enter Your UID</label>
                <input type="text" class="form-control" id="uid" name="uid" placeholder="e.g. akp5ve">
            </div>
        </div>
        <div class="row justify-content-center">
            <button type="submit" class="btn btn-secondary col-sm-10">View</button>
        </div>
    </form>

    <hr class="my-4">

    <!-- Projects Matching Qualifications -->
    <form method="get" action="">
        <h4 class="text-center mb-3">Find Matching Projects</h4>
        <div class="row mb-3 justify-content-center">
            <div class="col-sm-10">
                <label for="qualUID" class="form-label fw-bold">Enter Your UID</label>
                <input type="text" class="form-control" id="qualUID" name="qualUID" placeholder="e.g. akp5ve">
            </div>
        </div>
        <div class="row justify-content-center">
            <button type="submit" class="btn btn-success col-sm-10">Find Matches</button>
        </div>
    </form>

    <div class="mt-4 text-center">
        <p class="text-muted">Note: * Indicates a required field.</p>
    </div>
</div>

<div class="text-left text-bg-dark m-3 p-3">
    <p>&copy; 2025 HooResearches. All Rights Reserved.</p>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
