<?php
session_start();

// Έλεγχος αν είναι συνδεδεμένος και αν όχι πίσω στο main
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: main.php");
    exit();
}

include 'db.php';

// Φόρτωση στοιχείων χρήστη
$role = $_SESSION['role'];
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
$username = $_SESSION['username'];

// Φόρτωση του σωστού αρχείου ανάλογα με τον ρόλο
if ($role === 'Professor') {
    include 'actions_professor.php';
} elseif ($role === 'Student') {
    include 'actions_student.php';
}
?>

<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Metropolitan College Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --mc-red: #c00000; --mc-dark: #212529; }
        body { background-color: #f8f9fa; }
        .navbar { background-color: var(--mc-dark); border-bottom: 4px solid var(--mc-red); }
        .sidebar { background: white; min-height: 100vh; border-right: 1px solid #dee2e6; }
        .nav-link { color: var(--mc-dark); margin-bottom: 5px; cursor: pointer; }
        .nav-link.active { background-color: var(--mc-red) !important; color: white !important; }
        .card-header { background-color: var(--mc-red); color: white; font-weight: bold; }
        .btn-primary { background-color: var(--mc-red); border-color: var(--mc-red); }
        .btn-primary:hover { background-color: #a00000; }
    </style>
</head>
<body>

<nav class="navbar navbar-dark sticky-top p-2 shadow">
    <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center" href="#">
            <img src="https://akmi-international.com/wp-content/uploads/2022/01/metropolitan.png" alt="Logo" height="40" class="me-2">
            <span class="d-none d-sm-inline">Metropolitan College | Portal</span>
        </a>
        <div class="text-white">
            Χρήστης: <strong><?php echo htmlspecialchars($username); ?></strong> (<?php echo $role; ?>)
            <a href="index.php?logout=1" class="btn btn-sm btn-outline-light ms-2">Αποσύνδεση</a>
        </div>
    </div>
</nav>

<?php
// Λογική Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: main.php");
    exit();
}
?>

<div class="container-fluid">
    <div class="row">
        
        <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse show p-3">
            <div class="position-sticky">
                <ul class="nav flex-column nav-pills">
                    <li class="nav-item"><button class="nav-link active w-100 text-start" data-bs-toggle="tab" data-bs-target="#dashboard"><i class="fas fa-home me-2"></i> Πίνακας Ελέγχου</button></li>
                    
                    <?php if ($role === 'Student'): ?>
                        <li class="nav-item"><button class="nav-link w-100 text-start" data-bs-toggle="tab" data-bs-target="#my-courses"><i class="fas fa-book me-2"></i> Τα Μαθήματά μου</button></li>
                        <li class="nav-item"><button class="nav-link w-100 text-start" data-bs-toggle="tab" data-bs-target="#assignments"><i class="fas fa-file-upload me-2"></i> Εργασίες</button></li>
                        <li class="nav-item"><button class="nav-link w-100 text-start" data-bs-toggle="tab" data-bs-target="#grades"><i class="fas fa-graduation-cap me-2"></i> Βαθμολογίες</button></li>
                    <?php elseif ($role === 'Professor'): ?>
                        <li class="nav-item"><button class="nav-link w-100 text-start" data-bs-toggle="tab" data-bs-target="#manage-courses"><i class="fas fa-chalkboard-teacher me-2"></i> Διαχείριση Μαθημάτων</button></li>
                        <li class="nav-item"><button class="nav-link w-100 text-start" data-bs-toggle="tab" data-bs-target="#post-assignment"><i class="fas fa-plus-circle me-2"></i> Ανάρτηση Εργασίας</button></li>
                        <li class="nav-item"><button class="nav-link w-100 text-start" data-bs-toggle="tab" data-bs-target="#grading"><i class="fas fa-check-double me-2"></i> Βαθμολόγηση</button></li>
                    <?php endif; ?>
                </ul>
            </div>
        </nav>

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            <div class="tab-content">
                
                <div class="tab-pane fade show active" id="dashboard">
                    <h2 class="mb-4">Καλώς ήρθατε στο Campus Ηρακλείου</h2>
                    <div class="card shadow-sm">
                        <div class="card-header">Ανακοινώσεις</div>
                        <div class="card-body">
                            <?php
                            $anns = $conn->query("SELECT * FROM announcements ORDER BY created_at DESC LIMIT 5");
                            if ($anns->num_rows > 0) {
                                while($a = $anns->fetch_assoc()) {
                                    echo "<div class='alert alert-light border'><strong>".htmlspecialchars($a['title'])."</strong><br>".htmlspecialchars($a['content']);
                                    if($role === 'Professor') {
                                        echo "<form method='POST' class='mt-1'><input type='hidden' name='ann_id' value='".$a['id']."'><button type='submit' name='delete_announcement' class='btn btn-sm btn-outline-danger py-0'>Διαγραφή</button></form>";
                                    }
                                    echo "</div>";
                                }
                            } else { echo "<p class='text-muted'>Καμία ανακοίνωση.</p>"; }
                            ?>
                            <?php if ($role === 'Professor'): ?>
                                <hr>
                                <h6>+ Νέα Ανακοίνωση</h6>
                                <form method="POST">
                                    <input type="text" name="title" class="form-control mb-2" placeholder="Τίτλος" required>
                                    <textarea name="content" class="form-control mb-2" placeholder="Κείμενο" required></textarea>
                                    <button type="submit" name="add_announcement" class="btn btn-primary btn-sm">Ανάρτηση</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <?php if ($role === 'Student'): ?>
                    <div class="tab-pane fade" id="my-courses">
                        <h3>Τα Μαθήματά μου</h3>
                        <ul class="list-group mb-4">
                            <?php
                            $res = $conn->query("SELECT c.title FROM courses c JOIN course_enrollments ce ON c.id = ce.course_id WHERE ce.student_id = $user_id");
                            while($row = $res->fetch_assoc()) echo "<li class='list-group-item'>".htmlspecialchars($row['title'])."</li>";
                            ?>
                        </ul>
                        <div class="card p-3 shadow-sm">
                            <h5>Εγγραφή σε Μάθημα</h5>
                            <form method="POST" class="d-flex gap-2">
                                <select name="course_id" class="form-select">
                                    <?php
                                    $res = $conn->query("SELECT * FROM courses WHERE id NOT IN (SELECT course_id FROM course_enrollments WHERE student_id = $user_id)");
                                    while($c = $res->fetch_assoc()) echo "<option value='".$c['id']."'>".$c['title']."</option>";
                                    ?>
                                </select>
                                <button type="submit" name="enroll_course" class="btn btn-success">Εγγραφή</button>
                            </form>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="assignments">
                        <h3>Εργασίες</h3>
                        <div class="row g-3">
                            <?php
                            $sql = "SELECT a.id, a.title, a.description, a.file_path, s.file_path as my_sub FROM assignments a JOIN course_enrollments ce ON a.course_id = ce.course_id LEFT JOIN submissions s ON a.id = s.assignment_id AND s.student_id = $user_id WHERE ce.student_id = $user_id";
                            $res = $conn->query($sql);
                            while($as = $res->fetch_assoc()): ?>
                            <div class="col-md-6">
                                <div class="card h-100 shadow-sm">
                                    <div class="card-header bg-dark text-white"><?php echo htmlspecialchars($as['title']); ?></div>
                                    <div class="card-body">
                                        <p><?php echo htmlspecialchars($as['description']); ?></p>
                                        <a href="<?php echo $as['file_path']; ?>" target="_blank" class="btn btn-outline-danger btn-sm mb-2">Εκφώνηση (PDF)</a>
                                        <?php if($as['my_sub']): ?>
                                            <div class="alert alert-success py-1">Έχετε υποβάλει</div>
                                        <?php else: ?>
                                            <form method="POST" enctype="multipart/form-data">
                                                <input type="hidden" name="assignment_id" value="<?php echo $as['id']; ?>">
                                                <input type="file" name="file" class="form-control form-control-sm mb-2" accept=".pdf" required>
                                                <button type="submit" name="upload_submission" class="btn btn-primary btn-sm w-100">Υποβολή</button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="grades">
                        <h3>Βαθμολογίες</h3>
                        <table class="table table-bordered">
                            <thead><tr><th>Εργασία</th><th>Βαθμός</th></tr></thead>
                            <tbody>
                                <?php
                                $res = $conn->query("SELECT a.title, s.grade FROM submissions s JOIN assignments a ON s.assignment_id = a.id WHERE s.student_id = $user_id");
                                while($row = $res->fetch_assoc()) echo "<tr><td>".htmlspecialchars($row['title'])."</td><td class='fw-bold text-danger'>".($row['grade'] ?? "Εκκρεμεί")."</td></tr>";
                                ?>
                            </tbody>
                        </table>
                    </div>

                <?php elseif ($role === 'Professor'): ?>
                    <div class="tab-pane fade" id="manage-courses">
                        <h3>Διαχείριση Μαθημάτων</h3>
                        <div class="card p-3 mb-3 bg-light">
                            <form method="POST" class="d-flex gap-2">
                                <input type="text" name="course_title" class="form-control" placeholder="Τίτλος Νέου Μαθήματος" required>
                                <button type="submit" name="create_course" class="btn btn-success">Δημιουργία</button>
                            </form>
                        </div>
                        <ul class="list-group">
                            <?php
                            $res = $conn->query("SELECT * FROM courses WHERE professor_id = $user_id");
                            while($c = $res->fetch_assoc()): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <?php echo htmlspecialchars($c['title']); ?>
                                    <form method="POST" onsubmit="return confirm('Διαγραφή;');">
                                        <input type="hidden" name="course_id" value="<?php echo $c['id']; ?>">
                                        <button type="submit" name="delete_course" class="btn btn-danger btn-sm">X</button>
                                    </form>
                                </li>
                            <?php endwhile; ?>
                        </ul>
                    </div>

                    <div class="tab-pane fade" id="post-assignment">
                        <h3>Ανάρτηση Εργασίας</h3>
                        <div class="card p-4 shadow-sm">
                            <form method="POST" enctype="multipart/form-data">
                                <select name="course_id" class="form-select mb-3">
                                    <?php
                                    $res = $conn->query("SELECT * FROM courses WHERE professor_id = $user_id");
                                    while($c = $res->fetch_assoc()) echo "<option value='".$c['id']."'>".$c['title']."</option>";
                                    ?>
                                </select>
                                <input type="text" name="title" class="form-control mb-3" placeholder="Τίτλος Εργασίας" required>
                                <textarea name="description" class="form-control mb-3" placeholder="Οδηγίες"></textarea>
                                <input type="file" name="file" class="form-control mb-3" required>
                                <button type="submit" name="post_assignment" class="btn btn-primary">Ανάρτηση</button>
                            </form>
                        </div>
                        <h5 class="mt-4">Οι εργασίες μου (Διαγραφή)</h5>
                        <ul class="list-group">
                        <?php
                            $res = $conn->query("SELECT a.id, a.title FROM assignments a JOIN courses c ON a.course_id = c.id WHERE c.professor_id = $user_id");
                            while($a = $res->fetch_assoc()): ?>
                                <li class="list-group-item d-flex justify-content-between">
                                    <?php echo htmlspecialchars($a['title']); ?>
                                    <form method="POST"><input type="hidden" name="assignment_id" value="<?php echo $a['id']; ?>"><button type="submit" name="delete_assignment" class="btn btn-danger btn-sm">X</button></form>
                                </li>
                            <?php endwhile; ?>
                        </ul>
                    </div>

                    <div class="tab-pane fade" id="grading">
                        <h3>Βαθμολόγηση</h3>
                        <table class="table table-bordered">
                            <thead><tr><th>Φοιτητής</th><th>Εργασία</th><th>Αρχείο</th><th>Βαθμός</th><th>Save</th></tr></thead>
                            <tbody>
                                <?php
                                $sql = "SELECT s.id as sub_id, u.username, a.title, s.file_path, s.grade FROM submissions s JOIN users u ON s.student_id = u.id JOIN assignments a ON s.assignment_id = a.id JOIN courses c ON a.course_id = c.id WHERE c.professor_id = $user_id";
                                $res = $conn->query($sql);
                                while($sub = $res->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($sub['username']); ?></td>
                                    <td><?php echo htmlspecialchars($sub['title']); ?></td>
                                    <td><a href="<?php echo $sub['file_path']; ?>" class="btn btn-sm btn-info" download>Download</a></td>
                                    <form method="POST">
                                        <td><input type="number" step="0.1" name="grade" value="<?php echo $sub['grade']; ?>" class="form-control form-control-sm" style="width:80px;">
                                            <input type="hidden" name="submission_id" value="<?php echo $sub['sub_id']; ?>"></td>
                                        <td><button type="submit" name="submit_grade" class="btn btn-sm btn-success">OK</button></td>
                                    </form>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>

            </div>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>