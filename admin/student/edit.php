<?php
    session_start();
    $pageTitle = "Edit Student";
    include('../../functions.php');
    include('../partials/header.php'); 

    if (empty($_SESSION['email'])) {
        header("Location: ../../index.php");
        exit;
    }

    header("Cache-Control: no-store, no-cache, must-revalidate"); 
    header("Cache-Control: post-check=0, pre-check=0", false); 
    header("Pragma: no-cache");

    checkUserSessionIsActive();  
    guard(); 

    $errors = [];
    $studentToEdit = null;

    if (isset($_GET['student_id'])) {
        $student_id = sanitize_input($_GET['student_id']);
        $studentToEdit = getSelectedStudentById($student_id); 

        if (!$studentToEdit) {
            $errors[] = "Student not found.";
        }
    }

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['student_id'])) {
        $updatedData = [
            'student_id' => sanitize_input($_POST['student_id']),
            'first_name' => sanitize_input($_POST['first_name']),
            'last_name' => sanitize_input($_POST['last_name'])
        ];


        $validationErrors = validateStudentData($updatedData);
        $errors = array_merge($errors, $validationErrors);

      
        if (empty($errors)) {
            $updateStatus = updateStudentData($updatedData);

            if ($updateStatus) {
                header("Location: register.php");
                exit;
            } else {
                $errors[] = "Failed to update the student record.";
            }
        }
    }
?>

<div class="container">
    <div class="row">
        <?php include('../partials/side-bar.php'); ?>

        <div class="col-lg-10 col-md-9 mt-5">
            <h2>Edit Student</h2>
            <br>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="register.php">Register Student</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit Student</li>
                </ol>
            </nav>
            <hr>
            <br>

           
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if ($studentToEdit): ?>
                <form action="edit.php?student_id=<?= urlencode($studentToEdit['student_id']) ?>" method="post">
                    <div class="form-group">
                        <label for="student_id">Student ID</label>
                        <input type="text" class="form-control" id="student_id" name="student_id" value="<?= htmlspecialchars($studentToEdit['student_id']) ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label for="first_name">First Name</label>
                        <input type="text" class="form-control" id="first_name" name="first_name" value="<?= htmlspecialchars($studentToEdit['first_name']) ?>">
                    </div>
                    <div class="form-group">
                        <label for="last_name">Last Name</label>
                        <input type="text" class="form-control" id="last_name" name="last_name" value="<?= htmlspecialchars($studentToEdit['last_name']) ?>">
                    </div>
                    <br>
                    <button type="submit" class="btn btn-primary">Update Student</button>
                </form>
            <?php else: ?>
                <p>No student found with the provided ID.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include('../partials/footer.php'); ?>