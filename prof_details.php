<?php
session_start();
require('connect-db.php');
require('project-db.php');
require('prof-db.php');
$_SESSION['pid'] = 104;
$_SESSION['uid'] = 'akp5ve';

var_dump($_GET);
var_dump($_SESSION);

if (isset($_GET['prof'])) {
    $_SESSION['prof'] = $_GET['prof'];
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="Holly Kiker">
    <meta name="description" content="The researcher details page for HooResearches, a CS 3750 (Database Systems) project.">
    <meta name="keywords" content="CS 3750, UVa research, project details, Database Systems">
    <title>HooResearches Researcher Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
<div class="text-left text-bg-dark m-3 p-3">
    <h1>HooResearches</h1>
    <p>A UVa CS research finder</p>
</div>
<form>
    <h1 class="text-center"><?php echo getName($_SESSION['prof'])?></h3>
    <h4 class="text-center"><?php echo getRole($_SESSION['prof'])?></h4>
    <h5 class="text-center"><?php echo $_SESSION['prof'] . '@virginia.edu'?></h5>
    <div class="row mb-3 justify-content-center">
        
    <div class="row mb-3 justify-content-center">
        <div class="col-sm-10">
            <label for="research_areas" class="form-label fw-bold">Research Areas</label>
            <div>
                <?php echo getResAreas($_SESSION['prof']);?>
            </div>
        </div>
    </div>

    <?php foreach (getProjs($_SESSION['prof']) as $projs): ?>
        <tr>
        <td> 
            <a href="project_details.php?pid=<?php echo $projs['PID']; ?>" class="button">
            <?php echo $projs['title']; ?>
        </a>

        </td>

    </tr>


        <?php endforeach;?>

    <div class="row mb-3 justify-content-center">
        <div class="col-sm-10">
            <label for="publications" class="form-label fw-bold">Publications</label>
            <div>
                <?php echo getPublications($_SESSION['prof']);?>
            </div>
        </div>
    </div>

    <div class="row mb-3 justify-content-center">
        <div class="col-sm-10">
            <label for="courses_taught" class="form-label fw-bold">Courses Taught</label>
            <div>
                <?php echo getCourses($_SESSION['prof']);?>
            </div>
        </div>
    </div>

    
</form>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>