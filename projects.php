<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
require('connect-db.php');
require('project-db.php');
#$list_of_projects = getAllProjects();

if (isset($_GET['pid'])) {
    $_SESSION['pid'] = $_GET['pid'];
    header("Location: project_details.php");
    exit();
}

if (isset($_POST['interested']) && isset($_POST['pid'])) {
    $pid = $_POST['pid'];
    $uid = $_SESSION['uid'];

    if ($_POST['interested']) {
        markInterested($uid, $pid);   // your function to mark interest
    } else {
        unmarkInterested($uid, $pid); // function to remove interest
    }
}

if (isset($_POST['applyBtn'])) {
    $keyword = $_POST['keyword'] ?? "";
    $researcher = $_POST['researcher'] ?? "";
    $pcr = $_POST['p_cr'] ?? "none";
    $_SESSION['pcr'] = $pcr;


    $list_of_projects = applyFilters($keyword, $researcher, $pcr);
} 
else if (isset($_POST['searchBtn'])) {
    $search = $_POST['search'] ?? "";
    $list_of_projects = searchProjects($search);
}
else{
    $list_of_projects = getAllProjects();
}

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
<?php require ("header.php"); ?>


<hr/>
<div class="container">
<h3>All Projects</h3>
<h5>Search</h5>
<div style="display: flex; gap: 20px; align-items: center;">
    <div>
      <form method="POST" action="projects.php">
        <label for="search">Search:</label>
        <input type="text" id="search" name="search">
  </div>
  <div>
    <input type="submit" value="Search" id="searchBtn" name="searchBtn" class="btn btn-light"
           title="Search for project by Title" />  
</div>

</div>
</div>
<h5>Filter</h5>
<form method="POST" action="projects.php">
<div style="display: flex; gap: 20px; align-items: center;">
  <div>
    <label for="keyword">Keyword:</label>
    <select name="keyword" id="keyword">
      <option value="none"> -none- </option>
      <option value="machine learning">machine learning</option>
      <option value="GPU">GPU</option>
      <option value="CS and medicine">CS and medicine</option>
      <option value="smart devices">smart devices</option>
      <option value="x86">x86</option>
      <option value="web development">web development</option>
      <option value="CS education">CS education</option>
      <option value="LLMs">LLMs</option>
      <option value="parallel computing">parallel computing</option>
      <option value="game development">game development</option>
      <option value="VR">VR</option>
      <option value="cybersecurity">cybersecurity</option>
      <option value="databases">databases</option>
      <option value="artificial intelligence">artificial intelligence</option>
      <option value="cryptocurrency">cryptocurrency</option>
      <option value="software testing">software testing</option>
      <option value="cloud computing">cloud computing</option>
      <option value="networks">networks</option>
      <option value="memory">memory</option>
    </select>
  </div>
  <div>
    <label for="researcher">Researcher:</label>
    <input type="text" id="researcher" name="researcher">
  </div>
  <div>
    <label for="p_cr">Paid or Credit:</label>
    <select name="p_cr" id="p_cr">
      <option value="none">-none-</option>
      <option value="paid">Paid</option>
      <option value="credit">Credit</option>
       <option value="either">Either</option>
    </select>
  </div>
  <div>
    <input type="submit" value="Apply" id="applyBtn" name="applyBtn" class="btn btn-light"
           title="Apply filter options" />  
</div>


</div>
</form>


<div class="row justify-content-center">  
<table class="table table-bordered" style="width:100%">
  <thead> <!--header columns of table ; tr for row, td for column -->
  <tr style="background-color:#B0B0B0">
    <th width="30%"><b>Interested</b></th>
    <th width="30%"><b>Title</b></th>
    <th width="30%"><b>Researcher</b></th>        
    <th width="30%"><b>Paid or Credit</b></th> 
    <th width="30%"><b>Students</b></th>
    <th width="30%"><b>Keywords</b></th>        
    <th width="30%"><b>Qualifications</b></th> 
  </tr>
  </thead>

  <?php foreach ($list_of_projects as $proj_info): ?>


<!--    create some logic to pull these associated values -->
  <tr>
    <td>
      <form method="POST" action="projects.php">
        <input type="hidden" name="pid" value="<?php echo $proj_info['PID']; ?>">
        <input type="checkbox" name="interested" onchange="this.form.submit()"
        <?php if (getInterestedValue($_SESSION['uid'],$proj_info['PID'])) echo 'checked'; ?>>
      </form>
</td>
<td>

      <a href="project_details.php?pid=<?php echo $proj_info['PID']; ?>" class="button">

        <?php echo $proj_info['title']; ?>

    </a>

       </td> 

    <td><a href="prof_details.php?prof=<?php echo $proj_info['UID']?>">

                <?php echo getResearcher($proj_info['PID']); ?>

            </a> </td> 

    <td><?php echo $proj_info['paid_credit']; ?> </td>

    <td><?php echo (string) $proj_info['num_students']; ?> </td> 
    <td><?php echo getKeywords($proj_info['PID']); ?> </td> 
    <td><?php echo getQualifications($proj_info['PID']); ?> </td> 
    
    <!--
    <td>
        <form action="request.php" method="post">
            <input type="hidden" name="reqId" value="<?php #echo $proj_info['reqId']; ?>" />
            <input type="submit" value="Update" name="updateBtn" class="btn btn-danger" title="Click to update request" />
        </form>
    </td>

    <td>
      <form action="request.php" method="post" >
        <input type="submit" value="Delete"
                name="deleteBtn" class="btn btn-danger"
                title="Click to delete this request"
        />
        <input type="hidden" name="reqId"
                value="<?#php echo $proj_info['reqId']; ?>" /> 

  -->

  </form>
 


    </td>


  </tr>
  <?php endforeach;?>
  
</table>

</div>  



<br/><br/>
<?php require("footer.php"); ?>
</body>
</html>