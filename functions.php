<?php    
 
    // All project functions should be placed here
    function con(){
        
        $conn = mysqli_connect("localhost", "root", "", "dct-ccs-finals");
        
        if($conn === false){
            die("Error: Could not connect " .  mysqli_connect_error());
        }

        return $conn;
    }
    // sanitize input
    function sanitize_input($data) {
        return htmlspecialchars(stripslashes(trim($data)));
    }


    function validateLoginCredentials($email, $password) {
        $errors = [];
    
        if (empty($email)) {
            $errors[] = "Email is required.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email.";
        }
    
        if (empty($password)) {
            $errors[] = "Password is required.";
        }
        return $errors;
    }
    
    function checkLoginCredentials($email, $password) {
        $conn = con(); 
        $hashedPassword = md5($password);
    
        $sql = "SELECT * FROM users WHERE email = ? AND password = ?";
        $stmt = mysqli_prepare($conn, $sql);
    
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ss", $email, $hashedPassword);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
    
            $user = mysqli_fetch_assoc($result);
    
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            return $user ? $user: false;


        } 
        mysqli_close($conn);
        return false;
    }
    
    function displayErrors($errors) {
        $output = "<ul>";
        foreach ($errors as $error) {
            $output .= "<li>" . htmlspecialchars($error) . "</li>";
        }
        $output .= "</ul>";
        return $output;
    }

    function logOut($loginForm){
        unset($_SESSION['email']);

        
        session_destroy();
        header("Location: $loginForm");
        exit;
    }


    function guard() {
        if (empty($_SESSION['email']) && basename($_SERVER['PHP_SELF']) != 'index.php') {
            header("Location: index.php"); 
            exit;
        }
    }

    function guardDashboard() {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    
        if (empty($_SESSION['email'])) {
           
            header("Location: ../index.php"); 
            exit;
        }   
        if (basename($_SERVER['PHP_SELF']) !== 'dashboard.php') {
            header("Location: dashboard.php");
            exit;
        }
    }
    

    function checkUserSessionIsActive() {
        $dashboardPage = 'admin/dashboard.php';
        $indexPage = 'index.php';
        if (isset($_SESSION['email']) && basename($_SERVER['PHP_SELF']) == $indexPage) {
            header("Location: $dashboardPage");
            exit;
        }
    }

    function checkUserSessionIsActiveDashboard() {
        $dashboardPage = 'dashboard.php';
        $currentPage = basename($_SERVER['PHP_SELF']);
    
        if (isset($_SESSION['email']) && $currentPage === 'index.php') {
            header("Location: $dashboardPage");
            exit;
        }
    }

    function validateSubjectData($subject_data) {
        $errors = [];
        if (empty($subject_data['subject_code'])) {
            $errors[] = "Subject Code is required.";
        }
        if (empty($subject_data['subject_name'])) {
            $errors[] = "Subject Name is required.";
        }
        return $errors;
    }
    

    function validateStudentData($student_data) {
   
        $errors = [];
        if (empty($student_data['student_id'])) {
            $errors[] = "Student ID is required.";
        }
        if (empty($student_data['first_name'])) {
            $errors[] = "First Name is required.";
        }
    
        if (empty($student_data['last_name'])) {
            $errors[] = "Last Name is required.";
        }
    
        return $errors;
    
    }

    function checkDuplicateStudentData($student_id) {
        $conn = con(); // Establish connection
    
        $sql = "SELECT student_id FROM students WHERE student_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        
        if ($stmt) {
            // Bind the parameter
            mysqli_stmt_bind_param($stmt, "s", $student_id);
            mysqli_stmt_execute($stmt);
    
            $result = mysqli_stmt_get_result($stmt);
            $existing_student = mysqli_fetch_assoc($result);
    
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
    
            // Check if a matching row is found
            if ($existing_student) {
                return ["Duplicate Student ID"];
            }
        } else {
            mysqli_close($conn);
            return ["Error checking duplicate student ID"];
        }
    
        // No duplicates found
        return [];
    }
    
    

    function addStudentData($student_id, $student_firstname, $student_lastname) {
        $checkStudentData = validateStudentData([
            'student_id' => $student_id,
            'first_name' => $student_firstname,
            'last_name' => $student_lastname,
        ]);
        $checkDuplicateData = checkDuplicateStudentData($student_id);
    
        if (count($checkStudentData) > 0) {
            echo displayErrors($checkStudentData);
            return false;
        }
    
        if (count($checkDuplicateData) > 0) {
            echo displayErrors($checkDuplicateData);
            return false;
        }
    
        $conn = con();
    

        $sql_insert = "INSERT INTO students (student_id, first_name, last_name) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql_insert);
    
        if ($stmt) {
            
            mysqli_stmt_bind_param($stmt, "sss", $student_id, $student_firstname, $student_lastname);
    
         
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);
                mysqli_close($conn);
                return true;
            } else {
                echo "Error: " . mysqli_error($conn); 
            }
        } else {
            echo "Error preparing statement: " . mysqli_error($conn);
        }
    
       
        mysqli_close($conn);
        return false;
    }
    
    
    function selectStudents() {
        $conn = con();
    
        $sql_select = "SELECT * FROM students";
        $result = mysqli_query($conn, $sql_select);
    
        $students = [];
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $students[] = $row;
            }
        }
    
        mysqli_close($conn);
    
        return $students;
    }

    function getSelectedStudentById($student_id) {
        $conn = con();
    
        try {
            $sql = "SELECT * FROM students WHERE student_id = ?";
            $stmt = mysqli_prepare($conn, $sql);
    
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "s", $student_id);
                mysqli_stmt_execute($stmt);
    
                $result = mysqli_stmt_get_result($stmt);
    
                if ($result && mysqli_num_rows($result) > 0) {
                    $student = mysqli_fetch_assoc($result);
                    mysqli_stmt_close($stmt);
                    mysqli_close($conn);
                    return $student; 
                } else {
                    mysqli_stmt_close($stmt);
                    mysqli_close($conn);
                    return null; 
                }
            }
        } catch (Exception $e) {
           
            return null;
        }
    }
    
    function fetchStudentById($student_id) {
        $result = executeQuery("SELECT * FROM students WHERE student_id = ?", [$student_id], true);
        return $result[0] ?? null;
    }
    
    function updateStudentData($studentData) {
        $sql = "UPDATE students SET first_name = ?, last_name = ? WHERE student_id = ?";
        return executeQuery($sql, [$studentData['first_name'], $studentData['last_name'], $studentData['student_id']]);
    }
    
    function executeQuery($sql, $params, $isSelect = false) {
        $conn = con();
        $stmt = mysqli_prepare($conn, $sql);
    
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, str_repeat("s", count($params)), ...$params);
            mysqli_stmt_execute($stmt);
    
            if ($isSelect) {
                $result = mysqli_stmt_get_result($stmt);
                $data = [];
                while ($row = mysqli_fetch_assoc($result)) {
                    $data[] = $row;
                }
                mysqli_stmt_close($stmt);
                mysqli_close($conn);
                return $data;
            }
    
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            return true;
        }
    
        handleDatabaseError("Error executing query", $conn);
    }
    
    function deleteStudentById($student_id) {
        $conn = con(); 
    
        $sql = "DELETE FROM students WHERE student_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
    
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $student_id);
            $executionResult = mysqli_stmt_execute($stmt);
    
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
    
            return $executionResult; 
        } else {
            handleDatabaseError("Error preparing delete query", $conn);
        }
        mysqli_close($conn);
        return false; 
    }

    
    function addSubjectData($subject_data) {
        $conn = con(); 
        
        $errors = validateSubjectData($subject_data);
        
        $sql_check = "SELECT * FROM subjects WHERE subject_code = ?";
        $stmt_check = mysqli_prepare($conn, $sql_check);
        mysqli_stmt_bind_param($stmt_check, "s", $subject_data['subject_code']);
        mysqli_stmt_execute($stmt_check);
        $result_check = mysqli_stmt_get_result($stmt_check);
    
        if (mysqli_num_rows($result_check) > 0) {
            $errors[] = "Duplicate Subject";
        }
        mysqli_stmt_close($stmt_check);
    
    
        $sql_check_name = "SELECT * FROM subjects WHERE subject_name = ?";
        $stmt_check_name = mysqli_prepare($conn, $sql_check_name);
        mysqli_stmt_bind_param($stmt_check_name, "s", $subject_data['subject_name']);
        mysqli_stmt_execute($stmt_check_name);
        $result_check_name = mysqli_stmt_get_result($stmt_check_name);
    
        if (mysqli_num_rows($result_check_name) > 0) {
            $errors[] = "Duplicate Subject";
        }
        mysqli_stmt_close($stmt_check_name);
    
       
        if (empty($errors)) {
            $sql = "INSERT INTO subjects (subject_code, subject_name) VALUES (?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "ss", $subject_data['subject_code'], $subject_data['subject_name']);
                $execute = mysqli_stmt_execute($stmt);
    
                mysqli_stmt_close($stmt);
                mysqli_close($conn);
    
                return $execute ? true : ["Error adding subject to the database."];
            } else {
                $errors[] = "Error preparing statement: " . mysqli_error($conn);
            }
        }
    
        mysqli_close($conn);
        return $errors;
    }

   
    function checkDuplicateSubjectData($subject_code) {
        $conn = con();

        $sql = "SELECT subject_code FROM subjects WHERE subject_code = ?";
        $stmt = mysqli_prepare($conn, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $subject_code);
            mysqli_stmt_execute($stmt);

            $result = mysqli_stmt_get_result($stmt);
            $existing_subject = mysqli_fetch_assoc($result);

            mysqli_stmt_close($stmt);
            mysqli_close($conn);

            if ($existing_subject) {
                return ["Duplicate Subject"];
            }
        } else {
            mysqli_close($conn);
            return ["Error checking duplicate subject code"];
        }

        return [];
    }


    function getSubjectByCode($subject_code){
        $conn = con();
        $sql = "SELECT * FROM subjects WHERE subject_code = ?";
        $stmt = mysqli_prepare($conn, $sql);
        $subject = null;

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $subject_code);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $subject = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);
        }

        mysqli_close($conn);
        return $subject;
    }

   
    function updateSubjectData($subject_code, $subject_name) {
   
        $errors = [];
        if (empty($subject_code)) {
            $errors[] = "Subject Code is required.";
        }
        if (empty($subject_name)) {
            $errors[] = "Subject Name is required.";
        }
        if (!empty($errors)) {
            echo displayErrors($errors);
            return false;
        }
    
        $conn = con();
    
        $sql_update = "UPDATE subjects SET subject_name = ? WHERE subject_code = ?";
        $stmt = mysqli_prepare($conn, $sql_update);
    
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ss", $subject_name, $subject_code);
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);
                mysqli_close($conn);
                return true; 

                echo "Error: " . mysqli_error($conn);
            }
        } else {
            echo "Error preparing statement: " . mysqli_error($conn);
        }
    
        mysqli_close($conn);
        return false; 
    }

    function getSelectedSubjectById($subject_id) {
        $conn = con();  // Database connection
        
        // SQL query to fetch subject details by subject_id
        $query = "SELECT * FROM subjects WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $subject_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($row = mysqli_fetch_assoc($result)) {
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            return $row;  // Return subject details
        }
    
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return false;  // Return false if no subject found
    }
    
    

    function deleteSubjectByCode($subject_code) {
        $conn = con(); 
        $sql = "DELETE FROM subjects WHERE subject_code = ?";
        $stmt = mysqli_prepare($conn, $sql);
    
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $subject_code);
            $executionResult = mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            return $executionResult;
        } else {
         
            echo "Error preparing delete query: " . mysqli_error($conn);
            mysqli_close($conn);
            return false;
        }
    }

    function attachSubjectToStudent($student_id, $subject_id) {
        $conn = con();
    
        $errors = [];
        if (empty($student_id)) {
            $errors[] = "Student ID is required.";
        }
        if (empty($subject_id)) {
            $errors[] = "Subject ID is required.";
        }
    
        $student = getSelectedStudentById($student_id);
        $subject = getSelectedSubjectById($subject_id);
    
        if (!$student) {
            $errors[] = "Student ID not found.";
        }
        if (!$subject) {
            $errors[] = "Subject ID not found.";
        }
    
        if (!empty($errors)) {
            echo displayErrors($errors);
            return false;
        }
    
        // Check if the student is already attached to the subject
        $sql_check = "SELECT * FROM students_subjects WHERE student_id = ? AND subject_id = ?";
        $stmt_check = mysqli_prepare($conn, $sql_check);
        mysqli_stmt_bind_param($stmt_check, "ii", $student_id, $subject_id);
        mysqli_stmt_execute($stmt_check);
        $result_check = mysqli_stmt_get_result($stmt_check);
    
        if (mysqli_num_rows($result_check) > 0) {
            mysqli_stmt_close($stmt_check);
            mysqli_close($conn);
            echo displayErrors(["Subject is already attached to the student."]);
            return false;
        }
        mysqli_stmt_close($stmt_check);
    
       
        $sql = "INSERT INTO students_subjects (student_id, subject_id, grade) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
    
        if ($stmt) {
            $grade = 0; 
            mysqli_stmt_bind_param($stmt, "iis", $student_id, $subject_id, $grade);
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);
                mysqli_close($conn);
                return true;
            } else {
                echo "Error attaching subject: " . mysqli_error($conn);
            }
        } else {
            echo "Error preparing statement: " . mysqli_error($conn);
        }
    
        mysqli_close($conn);
        return false;
    }
    


    
    function getSubjectIdByCode($subject_code) {
        $conn = con();  // Database connection
        
        // Correct query to fetch id using subject_code
        $query = "SELECT id FROM subjects WHERE subject_code = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "s", $subject_code);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($row = mysqli_fetch_assoc($result)) {
            return $row['id'];  // Return the correct column (id) from subjects table
        }
    
        return false;
    }
    function getSelectedSubjectByCode($subject_code) {
        $conn = con();  // Database connection
        $sql = "SELECT * FROM subjects WHERE subject_code = ?";
        $stmt = mysqli_prepare($conn, $sql);
        $subject = null;
    
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $subject_code);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $subject = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);
        }
    
        mysqli_close($conn);
        return $subject;
    }
    
    

    function countAllSubjects() {
        try {
            $conn = con(); // Get MySQLi connection
            $sql = "SELECT COUNT(*) AS total_subjects FROM subjects";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
    
            $result = $stmt->get_result();
            $row = $result->fetch_assoc(); // Fetch the result as an associative array
            return $row['total_subjects'];
        } catch (mysqli_sql_exception $e) {
            return "Error: " . $e->getMessage();
        }
    }
    
    
    function countAllStudents() {
        try {
            $conn = con(); // Get MySQLi connection
            $sql = "SELECT COUNT(*) AS total_students FROM students";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
    
            $result = $stmt->get_result();
            $row = $result->fetch_assoc(); // Fetch the result as an associative array
            return $row['total_students'];
        } catch (mysqli_sql_exception $e) {
            return "Error: " . $e->getMessage();
        }
    }
    function getAttachedSubjectsByStudentId($student_id) {
        $conn = con();
        $subjects = [];
        
      
        $sql = "SELECT s.subject_code, s.subject_name, ss.grade
                FROM subjects s
                INNER JOIN students_subjects ss ON s.id = ss.subject_id
                WHERE ss.student_id = ?";
        
        $stmt = mysqli_prepare($conn, $sql);
        
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $student_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            
            while ($row = mysqli_fetch_assoc($result)) {
                $subjects[] = $row;
            }
    
            mysqli_stmt_close($stmt);
        }
    
        mysqli_close($conn);
        return $subjects;
    }
    function getAllSubjects() {
        $conn = con(); // Assuming you have a connection function
    
        // Query to fetch all subjects
        $sql = "SELECT subject_code, subject_name FROM subjects";
        $result = mysqli_query($conn, $sql);
    
        $subjects = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $subjects[] = $row;
        }
    
        mysqli_close($conn);
        return $subjects;
    }
    
    function detachSubjectFromStudent($student_id, $subject_id) {
        $conn = con();
        $sql = "DELETE FROM students_subjects WHERE student_id = ? AND subject_id = ?";
    
        $stmt = mysqli_prepare($conn, $sql);
    
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ii", $student_id, $subject_id);
            mysqli_stmt_execute($stmt);
    
            if (mysqli_stmt_affected_rows($stmt) > 0) {
                mysqli_stmt_close($stmt);
                mysqli_close($conn);
                return true; 
            } else {
                error_log("No rows affected. Query might have failed.");
                mysqli_stmt_close($stmt);
                mysqli_close($conn);
                return false; 
            }
        } else {
            error_log("Failed to prepare statement: " . mysqli_error($conn));
            mysqli_close($conn);
            return false; 
        }
    }

    function assignGradeToStudent($student_id, $subject_id, $grade) {
        $conn = con();
        $sql = "UPDATE students_subjects SET grade = ? WHERE student_id = ? AND subject_id = ?";
    
        $stmt = mysqli_prepare($conn, $sql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "dii", $grade, $student_id, $subject_id);
            mysqli_stmt_execute($stmt);
    
            if (mysqli_stmt_affected_rows($stmt) > 0) {
                mysqli_stmt_close($stmt);
                mysqli_close($conn);
                return true; 
            } else {
                error_log("Failed to update grade: " . mysqli_error($conn));
                mysqli_stmt_close($stmt);
                mysqli_close($conn);
                return false; 
            }
        } else {
            error_log("Failed to prepare statement: " . mysqli_error($conn));
            mysqli_close($conn);
            return false; 
        }
    }
    
    function calculateTotalPassedAndFailedStudents() {
        try {
            $conn = con(); 
    
            $sql = "SELECT student_id, 
                       SUM(grade) AS total_grades, 
                       COUNT(subject_id) AS total_subjects 
                FROM students_subjects 
                GROUP BY student_id";
    
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
    
            $passed = 0;
            $failed = 0;
    
            while ($row = mysqli_fetch_assoc($result)) {
                $average_grade = $row['total_grades'] / $row['total_subjects'];
    
                if ($average_grade >= 75) {
                    $passed++;
                } else {
                    $failed++;
                }
            }
    
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
    
            return [
                'passed' => $passed,
                'failed' => $failed
            ];
        } catch (Exception $e) {
            return "Error: " . $e->getMessage();
        }
    }
    
    
    
    
    
?>