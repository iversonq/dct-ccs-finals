<?php
session_start();
$pageTitle = "Register Student";
include('../../functions.php');
include('../partials/header.php'); 

// Redirect to login if session email is not set
if (empty($_SESSION['email'])) {
    header("Location: ../../index.php");
    exit;
}

// Disable caching for the page
header("Cache-Control: no-store, no-cache, must-revalidate"); 
header("Cache-Control: post-check=0, pre-check=0", false); 
header("Pragma: no-cache");

// Ensure user session is active and guard the page access
checkUserSessionIsActive();  
guard(); 

// Initialize variables
$errors = [];
$student_data = [];

// Retain student data in session for persistence across form submissions
if (!isset($_SESSION['student_data'])) {
    $_SESSION['student_data'] = [];
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_data = [
        'student_id' => sanitize_input($_POST['student_id']),
        'first_name' => sanitize_input($_POST['first_name']),
        'last_name' => sanitize_input($_POST['last_name']),
    ];

    // Validate student data
    $errors = validateStudentData($student_data);

    if (empty($errors)) {
        // Check for duplicate student data
        $duplicate_errors = checkDuplicateStudentData($student_data['student_id']);
        if (!empty($duplicate_errors)) {
            $errors = array_merge($errors, $duplicate_errors);
        } else {
            // Add student data if no errors
            $added = addStudentData(
                $student_data['student_id'],
                $student_data['first_name'],
                $student_data['last_name']
            );

            if ($added) {
                // Clear session data and redirect on success
                unset($_SESSION['student_data']);
                header("Location: register.php");
                exit;
            } else {
                // Display error if adding student failed
                $errors[] = "Failed to add the student. Please try again.";
            }
        }
    }
}
?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar Section -->
        <?php include('../partials/side-bar.php'); ?>
        
        <!-- Main Content Section -->
        <div class="col-lg-10 col-md-9 mt-5">
            <h2>Register a New Student</h2>
            <br>

            <!-- Breadcrumb Navigation -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Register Student</li>
                </ol>
            </nav>
            <hr>
            <br>

            <!-- Display Form Errors -->
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>System Errors</strong>
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!-- Student Registration Form -->
            <form action="register.php" method="post">
                <div class="form-group">
                    <label for="student_id">Student ID</label>
                    <input type="text" class="form-control" id="student_id" name="student_id" 
                           placeholder="Enter Student ID" 
                           value="<?php echo htmlspecialchars($student_data['student_id'] ?? ''); ?>">
                </div>
                <div class="form-group mt-3">
                    <label for="first_name">First Name</label>
                    <input type="text" class="form-control" id="first_name" name="first_name" 
                           placeholder="Enter First Name" 
                           value="<?php echo htmlspecialchars($student_data['first_name'] ?? ''); ?>">
                </div>
                <div class="form-group mt-3">
                    <label for="last_name">Last Name</label>
                    <input type="text" class="form-control" id="last_name" name="last_name" 
                           placeholder="Enter Last Name" 
                           value="<?php echo htmlspecialchars($student_data['last_name'] ?? ''); ?>">
                </div>
                <button type="submit" class="btn btn-primary mt-3">Add Student</button>
            </form>
            <hr>

            <!-- Student List -->
            <h3 class="mt-5">Student List</h3>
            <table class="table table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>Student ID</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Option</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $students = selectStudents();
                        if(!empty($students)): ?>
                            <?php foreach($students as $student): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($student['student_id']); ?></td>
                                    <td><?php echo htmlspecialchars($student['first_name']); ?></td>
                                    <td><?php echo htmlspecialchars($student['last_name']); ?></td>
                                    <td>
                                        <a href="edit.php?student_id=<?php echo urlencode($student['student_id']); ?>" class="btn btn-info btn-sm">Edit</a>
                                        <a href="delete.php?student_id=<?php echo urlencode($student['student_id']); ?>" class="btn btn-danger btn-sm">Delete</a>
                                        <a href="attach-subject.php?student_id=<?php echo urlencode($student['student_id']); ?>" class="btn btn-warning btn-sm">Attach Subject</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center">No student records found.</td>
                            </tr>
                        <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include('../partials/footer.php'); ?>
