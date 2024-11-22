<?php
session_start();
$pageTitle = "Add Subject";
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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $subject_data = [
        'subject_code' => sanitize_input($_POST['subject_code']),
        'subject_name' => sanitize_input($_POST['subject_name'])
    ];

    // Add the new subject to the session
    if (!isset($_SESSION['subject_data'])) {
        $_SESSION['subject_data'] = [];
    }

    $_SESSION['subject_data'][] = $subject_data;

    $result = addSubjectData($subject_data);

    if ($result === true) {
        header("Location: add.php");
        exit;
    } else {
        $errors = $result;
    }
}

$conn = con();
$sql = "SELECT * FROM subjects";
$result = mysqli_query($conn, $sql);
$subjects = mysqli_fetch_all($result, MYSQLI_ASSOC);
mysqli_close($conn);
?>

<div class="container">
    <div class="row">
        <?php include('../partials/side-bar.php'); ?>
        <div class="col-lg-10 col-md-9 mt-5">
            <h2>Add a New Subject</h2>
            <br>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Add Subject</li>
                </ol>
            </nav>
            <hr>
            <br>

            <!-- Display errors -->
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

            <form method="post">
                <div class="form-group">
                    <label for="subject_code">Subject Code</label>
                    <input type="text" class="form-control" id="subject_code" name="subject_code" 
                           placeholder="Enter Subject Code" 
                           value="<?php echo isset($subject_data['subject_code']) ? htmlspecialchars($subject_data['subject_code']) : ''; ?>">
                </div>
                <div class="form-group">
                    <label for="subject_name">Subject Name</label>
                    <input type="text" class="form-control" id="subject_name" name="subject_name" 
                           placeholder="Enter Subject Name" 
                           value="<?php echo isset($subject_data['subject_name']) ? htmlspecialchars($subject_data['subject_name']) : ''; ?>">
                </div>
                <br>
                <button type="submit" class="btn btn-primary">Add Subject</button>
            </form>
            <hr>
            <h3 class="mt-5">Subject List</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>Subject Code</th>
                        <th>Subject Name</th>
                        <th>Options</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($subjects)): ?>
                        <?php foreach ($subjects as $subject): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($subject['subject_code']); ?></td>
                                <td><?php echo htmlspecialchars($subject['subject_name']); ?></td>
                                <td>
                                    <a href="edit.php?subject_code=<?php echo urlencode($subject['subject_code']); ?>" class="btn btn-info btn-sm">Edit</a>
                                    <a href="delete.php?subject_code=<?php echo urlencode($subject['subject_code']); ?>" class="btn btn-danger btn-sm">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="text-center">No subjects found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../partials/footer.php'; ?>