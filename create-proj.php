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
    $qualifications     = trim($_POST["qualifications"] ?? "");

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

<div class="hr-header text-bg-dark m-3 p-3 d-flex justify-content-between align-items-center">
    <div>
        <h1>HooResearches</h1>
        <p>A UVa CS research finder — Professor Portal: Create a Project</p>
    </div>
    <div class="d-flex gap-2">
        <a href="login.php" class="btn btn-outline-light btn-sm btn-back">← Back to Home</a>
        <!-- Create project button -->

        <!-- If your teammate used a different filename, update the href above -->
    </div>
</div>

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

        

        <div class="btn-group" data-toggle="buttons">
            <label data-value="paid" class="control-label btn btn-default" for="button_0">
                <input id="button_0" name="opinion" required="required" value="yes" checked="checked" type="radio"/>
                Paid
            </label>
            <label data-value="credit" class="control-label btn btn-default" for="button_1">
                <input id="button_1" name="opinion" required="required" value="no" type="radio"/>
                Credit
            </label>
            <label data-value="either" class="control-label btn btn-default" for="button_2">
                <input id="button_2" name="opinion" required="required" value="no_idea" type="radio" />
                Either
            </label>
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

        <!--qualifications-->
        <div class="mb-4">
            <label class="form-label fw-bold">Qualifications (separate with commas)</label>
            <input type="text" name="qualifications" class="form-control"
                   placeholder="examples: CS 4444, Python, familiarity with neural networks..."
                   value="<?php echo htmlspecialchars($_POST['qualifications'] ?? '') ?>">
        </div>


        <!--keywords-->
        <div class="mb-5">
            <label class="form-label fw-bold">Keywords (separate with commas)</label>
            <input type="text" name="keywords" class="form-control"
                   placeholder="examples: gpu, engineering, medical, stem cell..."
                   value="<?php echo htmlspecialchars($_POST['keywords'] ?? '') ?>">
        </div>

        <!--submit-->
        <button type="submit" class="btn btn-primary">Create Project</button>
        <br>
        <p class="text">* indicates required fields</p>
    </form>
</div>

<div class="hr-footer text-bg-dark m-3 p-3">
    <p>&copy; 2025 HooResearches. All Rights Reserved.</p>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>