<?php

// ======================================================
// DATABASE CONNECTION FILE
// ======================================================
// Is file ka kaam PHP ko MySQL database ke saath connect karna hai.
// Hum is connection ko baad mein signup.php aur login.php
// jaisi files mein use karenge.
// ======================================================


// Database server ka naam
// XAMPP mein normally localhost hota hai
$host = "localhost";


// Database ka username
// XAMPP ke default MySQL mein usually "root" hota hai
$username = "root";


// Database ka password
// Agar tumhare MySQL root user ka password nahi hai
// to isko empty string ("") rakho
$password = "";


// Jis database ko hum use karna chahte hain
// Ye wahi database name hai jo tumne phpMyAdmin mein banaya hai
$database = "login_system";


// ======================================================
// MYSQL DATABASE CONNECTION
// ======================================================
// mysqli ek PHP extension hai jo MySQL database
// ke saath connection banane ke liye use hoti hai.
//
// Agar tumne MySQL ka port 3307 kiya hai,
// to connection mein port bhi specify karna hoga.
// ======================================================

$connection = new mysqli(
    $host,
    $username,
    $password,
    $database,
    3306
);


// ======================================================
// CONNECTION ERROR CHECK
// ======================================================
// Agar database connection fail ho jaye,
// to $connection->connect_error mein error message hota hai.
//
// Hum check kar rahe hain ke connection mein koi error hai ya nahi.
// ======================================================

if ($connection->connect_error) {

    // Agar error hai to program yahin stop ho jayega
    // aur error message screen par show hoga.
    die("Database connection failed: " . $connection->connect_error);
}


// ======================================================
// SUCCESS
// ======================================================
// Agar code yahan tak aa gaya hai,
// iska matlab database connection successfully establish ho gaya.
// ======================================================

?>