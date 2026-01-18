<?php
require_once 'functions.php';

echo "<h1>Unit Testing Report</h1>";
echo "<hr>";

function printResult($testName, $expected, $actual) {
    echo "<strong>$testName:</strong> ";
    if ($expected === $actual) {
        echo "<span style='color: green; font-weight: bold;'>PASS</span><br>";
    } else {
        echo "<span style='color: red; font-weight: bold;'>FAIL</span> (Expected: " . ($expected ? 'True' : 'False') . ", Got: " . ($actual ? 'True' : 'False') . ")<br>";
    }
}

//TEST 1: Έλεγχος Username
echo "<h3>1. Testing Username Validation</h3>";

// 1 : Σωστό Username
$result = check_field("Student2025", "/^[A-Za-z0-9]+$/", 4, 20);
printResult("Valid Username ('Student2025')", true, $result);

// 2 : Μικρό Username
$result = check_field("abc", "/^[A-Za-z0-9]+$/", 4, 20);
printResult("Short Username ('abc')", false, $result);

// 3 : Username με σύμβολα
$result = check_field("User#1", "/^[A-Za-z0-9]+$/", 4, 20);
printResult("Invalid Characters ('User#1')", false, $result);


//TEST 2: Έλεγχος Email
echo "<h3>2. Testing Email Validation</h3>";

// 1 : Σωστό Email
$result = check_field("student@college.gr", "/^[a-z0-9.]+@[a-z0-9.]+$/", 10, 30);
printResult("Valid Email ('student@college.gr')", true, $result);

// 2 : Email χωρίς @
$result = check_field("studentcollege.gr", "/^[a-z0-9.]+@[a-z0-9.]+$/", 10, 30);
printResult("Missing @ symbol", false, $result);

// 3 : Πολύ μικρό Email
$result = check_field("a@b.c", "/^[a-z0-9.]+@[a-z0-9.]+$/", 10, 30);
printResult("Short Email ('a@b.c')", false, $result);

echo "<hr><p>End of Unit Tests.</p>";
?>