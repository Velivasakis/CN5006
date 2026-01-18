<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $role === 'Professor') {

    // Ανάρτηση Ανακοίνωσης
    if (isset($_POST['add_announcement'])) {
        $title = trim($_POST['title']);
        $content = trim($_POST['content']);
        if($title && $content) {
            $stmt = $conn->prepare("INSERT INTO announcements (title, content) VALUES (?, ?)");
            $stmt->bind_param("ss", $title, $content);
            $stmt->execute();
        }
    }
    
    // Διαγραφή Ανακοίνωσης
    if (isset($_POST['delete_announcement'])) {
        $id = $_POST['ann_id'];
        $conn->query("DELETE FROM announcements WHERE id=$id");
    }

    // Δημιουργία Μαθήματος
    if (isset($_POST['create_course'])) {
        $title = trim($_POST['course_title']);
        if($title) {
            $stmt = $conn->prepare("INSERT INTO courses (title, professor_id) VALUES (?, ?)");
            $stmt->bind_param("si", $title, $user_id);
            $stmt->execute();
        }
    }

    // Διαγραφή Μαθήματος
    if (isset($_POST['delete_course'])) {
        $course_id = $_POST['course_id'];
        $stmt = $conn->prepare("DELETE FROM courses WHERE id = ? AND professor_id = ?");
        $stmt->bind_param("ii", $course_id, $user_id);
        $stmt->execute();
    }

    // Ανάρτηση Εργασίας
    if (isset($_POST['post_assignment'])) {
        // Έλεγχος αν έχει επιλεγεί μάθημα
        if (!isset($_POST['course_id']) || empty($_POST['course_id'])) {
            $message = "Σφάλμα: Επιλέξτε μάθημα!";
        } else {
            $course_id = $_POST['course_id'];
            $title = trim($_POST['title']);
            $desc = trim($_POST['description']);
            
            // Upload αρχείου
            $target_dir = "uploads/";
            if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);
            
            if (isset($_FILES["file"]["name"]) && $_FILES["file"]["name"] != "") {
                $filename = basename($_FILES["file"]["name"]);
                $target_file = $target_dir . time() . "_prof_" . $filename;
                
                if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
                    $stmt = $conn->prepare("INSERT INTO assignments (course_id, title, description, file_path) VALUES (?, ?, ?, ?)");
                    $stmt->bind_param("isss", $course_id, $title, $desc, $target_file);
                    $stmt->execute();
                }
            }
        }
    }

    // Διαγραφή Εργασίας
    if (isset($_POST['delete_assignment'])) {
        $assignment_id = $_POST['assignment_id'];
        $conn->query("DELETE FROM assignments WHERE id=$assignment_id");
    }

    // Βαθμολόγηση
    if (isset($_POST['submit_grade'])) {
        $sub_id = $_POST['submission_id'];
        $grade = $_POST['grade'];
        $stmt = $conn->prepare("UPDATE submissions SET grade = ? WHERE id = ?");
        $stmt->bind_param("di", $grade, $sub_id);
        $stmt->execute();
    }
}
?>