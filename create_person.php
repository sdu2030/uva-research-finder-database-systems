<?php
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
</head>
<body>
    <?php require("header.php"); ?>
    <form>
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
        <fieldset class="row mb-3 justify-content-center">
            <div class="col-sm-10">
                <legend class="col-form-label col-sm-2 pt-0 fw-bold">User Type</legend>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="student" id="student" value="option1" checked>
                    <label class="form-check-label" for="student">Student</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="researcher" id="researcher" value="option2">
                    <label class="form-check-label" for="researcher">Researcher</label>
                </div>
            </div>
        </fieldset>
        <div class="row mb-3 justify-content-center">
            <div class="col-sm-10">
                <label for="person_desc" class="form-label fw-bold">Description</label>
                <textarea class="form-control" id="person_desc" name="person_desc" placeholder="Add an optional description of yourself."></textarea>
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
            <button type="submit" class="btn btn-primary col-sm-10">Submit</button>
        </div>
    </form>
    <?php require("footer.php"); ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>