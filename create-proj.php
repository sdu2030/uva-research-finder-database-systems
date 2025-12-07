<?php 

require_once("connect-db.php");

$errors = [];
$success = "";

if (isset($_POST["create_project"])) {
    $project_name = trim($_POST["project_name"] ?? null);
    $description  = trim($_POST["description"] ?? null);
    $num_spots    = $_POST["num_spots"] ?? null;
    $paid         = isset($_POST["paid"]) ? 1 : 0;
    $keywords     = trim($_POST["keywords"] ?? "");

    if ($project_name === "") $errors[] = "Project Name is required.";
    if ($description === "") $errors[] = "Project Description is required.";
    if ($num_spots === "" || !ctype_digit($num_spots) || (int)$num_spots <= 0) {
        $errors[] = "Number of spots must be a positive number.";
    }

    if (empty($errors)) {
        $success = "Project successfully created!";
    }
}




?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="Samuel Du">
    <meta name="description" content="The create project page for HooResearches.">
    <meta name="keywords" content="CS 4750, UVA research, student, project search, Database Systems">
    <title>HooResearches Create Project</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<div class="container mt-5" style="max-width: 800px;">
    <h2 class="mb-4">Create a Project</h2>


    <?php if (!empty($success)): ?>
        <div class="success"><?php echo $success ?></div>
    <?php endif; ?>

    
    <?php if (!empty($errors)): ?>
        <div class="error">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                    <li><?php echo $error; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>




    <form method="POST" action="">
        <!--proj name -->
        <div class="mb-3">
            <label class="form-label fw-bold">Project Name <span class="text-danger">*</span></label>
            <input type="text" name="project_name" class="form-control"
                   value="<?php echo htmlspecialchars($_POST['project_name'] ?? '') ?>" required>
        </div>

        <!--paid?-->
        <div class="mb-3 form-check">
            <input type="checkbox" name="paid" class="form-check-input" id="paidCheck"
                   <?php echo isset($_POST['paid']) ? 'checked' : '' ?>>
            <label class="form-check-label" for="paidCheck">Paid</label>
        </div>

        <!--num spots-->
        <div class="mb-3">
            <label class="form-label fw-bold">Number of Spots <span class="text-danger">*</span></label>
            <input type="number" name="num_spots" class="form-control" min="1"
                   value="<?php echo htmlspecialchars($_POST['num_spots'] ?? '') ?>" required>
        </div>

        <!--proj desc-->
        <div class="mb-3">
            <label class="form-label fw-bold">Project Description <span class="text-danger">*</span></label>
            <textarea name="description" class="form-control" rows="5" required><?php echo htmlspecialchars($_POST['description'] ?? '') ?></textarea>
        </div>

        <!--keywords-->
        <div class="mb-4">
            <label class="form-label fw-bold">Keywords</label>
            <input type="text" name="keywords" class="form-control"
                   placeholder="examples: gpu, engineering, medical, stem cell"
                   value="<?php echo htmlspecialchars($_POST['keywords'] ?? '') ?>">
        </div>

        <!--submit-->
        <button type="submit" class="btn btn-primary">Create Project</button>
        <p class="text">* indicates required fields</p>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>