<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $role === 'Student') {

    // Εγγραφή σε μάθημα
    if (isset($_POST['enroll_course'])) {
        $course_id = $_POST['course_id'];
        
        // Έλεγχος αν είναι ήδη εγγεγραμμένος
        $check = $conn->query("SELECT id FROM course_enrollments WHERE student_id = $user_id AND course_id = $course_id");
        
        if ($check->num_rows == 0) {
            $stmt = $conn->prepare("INSERT INTO course_enrollments (student_id, course_id) VALUES (?, ?)");
            $stmt->bind_param("ii", $user_id, $course_id);
            $stmt->execute();
        }
    }

    // Υποβολή Εργασίας
    if (isset($_POST['upload_submission'])) {
        $assignment_id = $_POST['assignment_id'];
        
        // Δημιουργία φακέλου uploads αν δεν υπάρχει
        $target_dir = "uploads/";
        if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);
        
        $filename = basename($_FILES["file"]["name"]);
        $target_file = $target_dir . "sub_" . time() . "_" . $filename;
        
        // Έλεγχος τύπου αρχείου (Μόνο PDF)
        $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        if ($fileType != "pdf") {
            // Αν δεν είναι PDF, σφάλμα
            echo "<script>alert('Σφάλμα: Επιτρέπονται μόνο αρχεία PDF!'); window.location.href='index.php';</script>";
        } else {
            // Αν είναι PDF προχώρα στο ανέβασμα
            if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
                $stmt = $conn->prepare("INSERT INTO submissions (assignment_id, student_id, file_path) VALUES (?, ?, ?)");
                $stmt->bind_param("iis", $assignment_id, $user_id, $target_file);
                $stmt->execute();
            }
        }
    }
}
?>