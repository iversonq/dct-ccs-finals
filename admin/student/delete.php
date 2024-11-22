<?php
session_start();
$pageTitle = "Delete Student Record";
include('../../functions.php');
include('../partials/header.php');

// Ensure the user is logged in
if (empty($_SESSION['email'])) {
    header("Location: ../../index.php");
    exit;
}

// Disable caching for this page
header("Cache-Control: no-store, no-cache, must-revalidate"); 
header("Cache-Control: post-check=0, pre-check=0", false); 
header("Pragma: no-cache");

checkUserSessionIsActive();  
guard(); 

$studentToDelete = null;
$errors = [];

// Check if student_id is provided and sanitize it
if (isset($_GET['student_id'])) {
    $student_id = filter_var($_GET['student_id'], FILTER_SANITIZE_NUMBER_INT);
    if ($student_id) {
        $studentToDelete = getSelectedStudentById($student_id);
    } else {
        $errors[] = "Invalid Student ID.";
    }

    // Handle case where student is not found
    if (!$studentToDelete) {
        $errors[] = "Student not found.";
    }
}

// Process deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['student_id'])) {
    $student_id = filter_var($_POST['student_id'], FILTER_SANITIZE_NUMBER_INT);

    // Ensure the student ID is valid
    if (!$student_id) {
        $errors[] = "Invalid Student ID.";
    }

    // Proceed with deletion if no errors
    if (empty($errors)) {
        $deleteStudent = deleteStudentById($student_id);
        
        if ($deleteStudent) {
            $_SESSION['success_message'] = "Student record deleted successfully.";
            header("Location: register.php");
            exit;
        } else {
            $errors[] = "Failed to delete the student record. Please try again.";
        }
    }
}
?>

<div class="container">
    <div class="row">
        <?php include('../partials/side-bar.php'); ?>
        <div class="col-lg-10 col-md-9 mt-5">
            <h2>Delete a Student</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="register.php">Register Student</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Delete Student</li>
                </ol>
            </nav>

            <!-- Display success message -->
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success">
                    <?= htmlspecialchars($_SESSION['success_message']); ?>
                </div>
                <?php unset($_SESSION['success_message']); ?>
            <?php endif; ?>

            <!-- Display errors -->
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="card mt-3">
                <div class="card-body">
                    <?php if ($studentToDelete): ?>
                        <h5>Are you sure you want to delete the following student record?</h5>
                        <ul>
                            <li><strong>Student ID:</strong> <?= htmlspecialchars($studentToDelete['student_id']) ?></li>
                            <li><strong>First Name:</strong> <?= htmlspecialchars($studentToDelete['first_name']) ?></li>
                            <li><strong>Last Name:</strong> <?= htmlspecialchars($studentToDelete['last_name']) ?></li>
                        </ul>
                        <form method="POST">
                            <input type="hidden" name="student_id" value="<?= htmlspecialchars($studentToDelete['student_id']) ?>">
                            <button type="button" class="btn btn-secondary" onclick="window.location.href='register.php';">Cancel</button>
                            <button type="submit" class="btn btn-danger">Delete Student Record</button>
                        </form>
                    <?php else: ?>
                        <p class="text-danger">Student not found.</p>
                        <a href="register.php" class="btn btn-primary">Back to Student List</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('../partials/footer.php'); ?>
