<?php
session_start();
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

$servername = "localhost";
$uname   = "root";
$pass   = "";
$dbname     = "myDB";

$password_Err = $email_Err = $error = "";
$password = $email = "";

// Λήψη δεδομένων
if ($_SERVER['REQUEST_METHOD']=== 'POST'){
    $email    = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php
        include("functions.php");
        if ($_SERVER['REQUEST_METHOD'] === 'POST'){
            
            // Έλεγχος αν είναι κενά
            if($email === ""){
                $email_Err = "Please fill the Email field";
            }

            if($password === ""){
                $password_Err = "Please fill the Password field";
            }

            // Αν υπάρχουν δεδομένα κάνε σύνδεση
            if($email !== "" && $password !== ""){
                $conn = new mysqli($servername, $uname, $pass, $dbname);
                if ($conn->connect_error) {
                    echo "Internal database error.";
                } else {
                    // Αναζήτηση χρήστη με email
                    $stmt = $conn->prepare("SELECT id, email, password, role, username FROM users WHERE email = ? LIMIT 1");
                    $stmt->bind_param("s", $email);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    
                    if ($result->num_rows === 1) {
                        $row = $result->fetch_assoc();
                        // Έλεγχος κωδικού (Verify Hash)
                        if (password_verify($password, $row['password'])) {
                            // Αποθήκευση στοιχείων στο Session
                            $_SESSION['logged_in'] = true;
                            $_SESSION['user_id'] = $row['id'];
                            $_SESSION['email'] = $row['email'];
                            $_SESSION['role'] = $row['role'];
                            $_SESSION['username'] = $row['username'];
                            
                            header("Location: index.php"); // Επιτυχία σε πάει στο Dashboard
                        } else {
                            $error = "Wrong email or password.";
                        }
                    } else{
                        $error = "Wrong email or password.";
                    }
                    $stmt->close();
                    $conn->close();
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
            <h2>Log-In</h2>
            <div>
                <span style="color: #c00; font-size: 0.9em; margin-left: 8px;"><?php echo $error; ?></span><br>
            </div>
            <div>
                <span style="color: #c00; font-size: 0.9em; margin-left: 8px;"><?php echo $email_Err; ?></span><br>
                <input type="text" value="<?php echo e($email); ?>" name="email" id="email" placeholder="Email" class="input"/>
            </div>
            <div>
                <span style="color: #c00; font-size: 0.9em; margin-left: 8px;"><?php echo $password_Err; ?></span><br>
                <input type="password" name="password" id="password" placeholder="Password" class="input"/>
            </div>
            <input type="submit" value="Sign-In" class="submit">
            <p><br>Dont have an account?<br><a href="register.php">Register here</a></p>
        </form>

        <div class="photo right-photo"></div>
    </main>
    <footer>
        <p>© 2025 Metropolitan College • All Rights Reserved</p>
    </footer>
</body>
</html>