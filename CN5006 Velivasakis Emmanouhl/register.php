<?php
session_start();

function e($string) {
    return htmlspecialchars($string, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

// Στοιχεία σύνδεσης βάσης
$servername = "localhost";
$uname   = "root";
$pass   = "";
$dbname     = "myDB";

if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    // Παίρνουμε τα στοιχεία από τη φόρμα
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    $role = isset($_POST['role']) ? $_POST['role'] : '';
    $otp  = isset($_POST['otp']) ? $_POST['otp'] : '';

    // Κωδικοί γραμματείας για επιβεβαίωση ρόλου
    $stud_pass = "STUD2025";
    $prof_pass = "PROF2025";
    $ok = false;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php
        include("functions.php");

        $password_Err = $username_Err = $email_Err = $otp_Err = $role_Err = "";

        if ($_SERVER['REQUEST_METHOD'] === 'POST'){
            
            // ΕΛΕΓΧΟΙ

            // Έλεγχος Username
            if(check_field($username, "/^[A-Za-z0-9]+$/", 4, 20)){
                echo "";
            } else{
                $username_Err = "Username must be 4-20 characters long and contain only letters and numbers.";
            }

            // Έλεγχος Password
            if(check_field($password, "/^[A-Za-z0-9.!@]+$/", 8, 30)){
                echo "";
            } else{
                $password_Err = "Password must be 8-30 characters. Allowed symbols are: @ ! .";
            }

            // Έλεγχος Email
            if(check_field($email, "/^[a-z0-9.]+@[a-z0-9.]+$/", 10, 30)){
                echo "";
            } else{
                $email_Err = "Please enter a valid email address containing '@' (10-30 characters).";
            }

            // Έλεγχος OTP
            if($otp === $stud_pass || $otp === $prof_pass){
                echo "";
            } else{
                $otp_Err = "Invalid code. Please use the One Time Password provided by the secretariat.";
            }

            // Έλεγχος αν ταιριάζει ο ρόλος με το OTP
            if(empty($role)){
                $role_Err = "Please select a role (Student or Professor).";
            } elseif($role === "Student" && $otp === $stud_pass){
                echo "";
            } elseif($role === "Professor" && $otp === $prof_pass){
                echo "";
            } else{
                $role_Err = "The selected Role does not match the provided One Time Password.";
            }

            // Αν όλα είναι σωστά
            $ok = ($username_Err === "" && $email_Err === "" && $password_Err === "" && $otp_Err === "" && $role_Err === "");
            
            if($ok){
                $conn = new mysqli($servername, $uname, $pass, $dbname);
                if ($conn->connect_error) {
                    echo "Internal database error.";
                }else{

                    // Έλεγχος αν υπάρχει ήδη το Username
                    $stmt = $conn->prepare("SELECT username FROM users WHERE username = ? LIMIT 1");
                    $stmt->bind_param("s", $username);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    if ($result->num_rows > 0) {
                        $username_Err = "Username already taken.";
                    }
                    $stmt->close();

                    // Έλεγχος αν υπάρχει ήδη το Email
                    if ($username_Err === ""){
                        $stmt = $conn->prepare("SELECT email FROM users WHERE email = ? LIMIT 1");
                        $stmt->bind_param("s", $email);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        if ($result->num_rows > 0) {
                            $email_Err = "Email already in use.";
                        }
                        $stmt->close();
                    }

                    // Εγγραφή νέου χρήστη
                    if ($email_Err === "" && $username_Err === ""){
                        // Κρυπτογράφηση κωδικού (Hash)
                        $password_hash = password_hash($password, PASSWORD_DEFAULT);
                        
                        $stmt = $conn->prepare("
                            INSERT INTO users (username, email, password, role)
                            VALUES (?, ?, ?, ?)
                        ");
                        $stmt->bind_param("ssss", $username, $email, $password_hash, $role);
                        $stmt->execute();
                        $stmt->close();

                        $conn->close();
                        header("Location: login.php"); // Μετάβαση στο Login
                        exit;
                    }
                }
            }
        }
    ?>
    <header class="header">
        <img class="logo" src="https://portal.mitropolitiko.edu.gr/images/oxygen/logo/MK_LOGO_portal.png" alt="Logo">
    </header>

    <main class="container">
        <div class="photo left-photo"></div>

        <form method="post" action="" class="login-box">
            <h2>Register</h2>
            <div>
                <span style="color: #c00; font-size: 0.9em; margin-left: 8px;"><?php echo $username_Err; ?></span><br>
                <input type="text" name="username" placeholder="Username" class="input"/>
            </div>
            <div>
                <span style="color: #c00; font-size: 0.9em; margin-left: 8px;"><?php echo $email_Err; ?></span><br>
                <input type="text" name="email" placeholder="Email" class="input"/>
            </div>
            <div>
                <span style="color: #c00; font-size: 0.9em; margin-left: 8px;"><?php echo $password_Err; ?></span><br>
                <input type="password" name="password" placeholder="Password" class="input"/>
            </div>
            <div>
                <span style="color: #c00; font-size: 0.9em; margin-left: 8px;"><?php echo $otp_Err; ?></span><br>
                <input type="text" name="otp" placeholder="One Time Password" class="input"/>
            </div>
            <div style="text-align: left;">
                <span style="color: #c00; font-size: 0.9em; margin-left: 8px;"><?php echo $role_Err; ?></span><br>
                <div class="input">
                    <label>Select Your Role:</label><br><br>
                    <label><input type="radio" name="role" value="Student"> Student</label><br>
                    <label><input type="radio" name="role" value="Professor"> Professor</label><br>
                </div>
            </div>
            <input type="submit" value="Create Account" class="submit">
            <p><br> Already have an account? <br> <a href="login.php">Log-in here</a></p>
        </form>
        <div class="photo right-photo"></div>
    </main>
    <footer>
        <p>© 2025 Metropolitan College • All Rights Reserved</p>
    </footer>
</body>
</html>