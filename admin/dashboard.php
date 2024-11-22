<?php
    include '../functions.php';
    include './partials/header.php';
    include './partials/side-bar.php';

    $pageTitle = "Dashboard";

    // Fetch data for the dashboard
    $total_subjects = countAllSubjects();
    $total_students = countAllStudents();
    $passedAndFailedSubject = calculateTotalPassedAndFailedStudents();
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-5">    
    <h1 class="h2">Dashboard</h1>        

    <!-- Dashboard Stats -->
    <div class="row mt-5">
        <!-- Total Subjects Card -->
        <div class="col-12 col-xl-3">
            <div class="card border-primary mb-3">
                <div class="card-header bg-primary text-white border-primary">
                    <strong>Number of Subjects:</strong>
                </div>
                <div class="card-body text-primary">
                    <h5 class="card-title"><?php echo $total_subjects; ?></h5>
                </div>
            </div>
        </div>

        <!-- Total Students Card -->
        <div class="col-12 col-xl-3">
            <div class="card border-primary mb-3">
                <div class="card-header bg-primary text-white border-primary">
                    <strong>Number of Students:</strong>
                </div>
                <div class="card-body text-success">
                    <h5 class="card-title"><?php echo $total_students; ?></h5>
                </div>
            </div>
        </div>

        <!-- Failed Students Card -->
        <div class="col-12 col-xl-3">
            <div class="card border-danger mb-3">
                <div class="card-header bg-danger text-white border-danger">
                    <strong>Number of Failed Students:</strong>
                </div>
                <div class="card-body text-danger">
                    <h5 class="card-title"><?php echo $passedAndFailedSubject['failed']; ?></h5>
                </div>
            </div>
        </div>

        <!-- Passed Students Card -->
        <div class="col-12 col-xl-3">
            <div class="card border-success mb-3">
                <div class="card-header bg-success text-white border-success">
                    <strong>Number of Passed Students:</strong>
                </div>
                <div class="card-body text-success">
                    <h5 class="card-title"><?php echo $passedAndFailedSubject['passed']; ?></h5>
                </div>
            </div>
        </div>
    </div>    
</main>

<?php
    include './partials/footer.php';
 ?>
