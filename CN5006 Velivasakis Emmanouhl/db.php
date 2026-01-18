<?php
$servername = "localhost";
$uname = "root";
$pass = "";
$dbname = "myDB";

// Σύνδεση με τη βάση δεδομένων
$conn = new mysqli($servername, $uname, $pass, $dbname);

// Έλεγχος αν απέτυχε η σύνδεση
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ορίζουμε utf8 για να φαίνονται σωστά τα ελληνικά
$conn->set_charset("utf8");
?>