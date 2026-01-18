<?php

require_once 'db.php';

echo "<h1>Integration Testing: Database</h1>";
echo "<hr>";

// 1 Έλεγχος Σύνδεσης
echo "<h3>1. Connection Status</h3>";
if ($conn->ping()) {
    echo "Status: <span style='color: green; font-weight: bold;'>CONNECTED ✔</span><br>";
    echo "Host info: " . $conn->host_info . "<br>";
} else {
    die("Status: <span style='color: red;'>DISCONNECTED ✘</span> - Error: " . $conn->connect_error);
}

// 2 Δοκιμή Εγγραφής
echo "<h3>2. Insert Test User</h3>";

// Δεδομένα δοκιμής
$test_user = "TestBot99";
$test_email = "bot99@test.com";
$test_pass = password_hash("12345678", PASSWORD_DEFAULT);
$test_role = "Student";

// Καθαρισμός σε περίπτωση που έμεινε από προηγούμενο test
$conn->query("DELETE FROM users WHERE email = '$test_email'");

// Query Εισαγωγής
$stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $test_user, $test_email, $test_pass, $test_role);

if ($stmt->execute()) {
    echo "Action: Creating User '$test_user'... <span style='color: green; font-weight: bold;'>SUCCESS ✔</span><br>";
    $last_id = $conn->insert_id;
    echo "New User ID: " . $last_id . "<br>";
} else {
    echo "Action: Creating User... <span style='color: red;'>FAILED ✘</span> (" . $stmt->error . ")<br>";
}
$stmt->close();

// 3 Δοκιμή Διαγραφής
echo "<h3>3. Cleanup (Delete Test User)</h3>";

if (isset($last_id)) {
    $del_sql = "DELETE FROM users WHERE id = $last_id";
    if ($conn->query($del_sql) === TRUE) {
        echo "Action: Deleting User ID $last_id... <span style='color: green; font-weight: bold;'>SUCCESS ✔</span><br>";
    } else {
        echo "Action: Deleting User... <span style='color: red;'>FAILED ✘</span> (" . $conn->error . ")<br>";
    }
} else {
    echo "Skipping cleanup (Insert failed).<br>";
}

$conn->close();
echo "<hr><p>End of DB Tests.</p>";
?>