<?php

// ======================================================
// SIGNUP / REGISTRATION SYSTEM
// ======================================================
// Is file ka kaam new user ko register karna hai.
//
// User form mein:
// 1. Name
// 2. Email
// 3. Password
// 4. Confirm Password
//
// enter karega.
//
// PHP:
// - Form data receive karega
// - Validation karega
// - Check karega email already exist karti hai ya nahi
// - Password ko securely hash karega
// - User ko database mein save karega
// ======================================================


// ======================================================
// DATABASE CONNECTION FILE INCLUDE KARNA
// ======================================================
// Humne database connection ka code alag file mein rakha hai.
//
// __DIR__ ka matlab current file ka folder hai.
// ".." ka matlab ek folder peeche jana.
//
// signup.php
//    ↓
// config/database.php
//
// Isliye hum database.php ko include kar rahe hain.
// ======================================================

require_once __DIR__ . "/config/database.php";


// ======================================================
// VARIABLES
// ======================================================
// Ye variables error messages aur form data ko store karne
// ke liye use honge.
// ======================================================

$name = "";
$email = "";

$error = "";
$success = "";


// ======================================================
// CHECK KARNA KE FORM SUBMIT HUA HAI YA NAHI
// ======================================================
// $_SERVER["REQUEST_METHOD"] check karta hai ke page par
// form POST method se submit hua hai ya nahi.
//
// Agar user ne form submit kiya hai to condition TRUE hogi.
// ======================================================

if ($_SERVER["REQUEST_METHOD"] === "POST") {


    // ==================================================
    // FORM DATA RECEIVE KARNA
    // ==================================================
    // $_POST se hum form ke input fields ki values
    // receive karte hain.
    //
    // trim() extra spaces remove karta hai.
    // ==================================================
    
    
    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirm_password = $_POST["confirm_password"] ?? "";


    // ==================================================
    // BASIC VALIDATION
    // ==================================================
    // Sabhi required fields check kar rahe hain.
    // ==================================================

    if (
        empty($name) ||
        empty($email) ||
        empty($password) ||
        empty($confirm_password)
    ) {

        $error = "Please fill in all fields.";

    }


    // ==================================================
    // EMAIL VALIDATION
    // ==================================================
    // filter_var() email format check karta hai.
    //
    // Example:
    // user@gmail.com       → Valid
    // usergmail.com        → Invalid
    // ==================================================

    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = "Please enter a valid email address.";

    }


    // ==================================================
    // PASSWORD LENGTH VALIDATION
    // ==================================================
    // Password kam az kam 8 characters ka hona chahiye.
    // ==================================================

    elseif (strlen($password) < 8) {

        $error = "Password must be at least 8 characters long.";

    }


    // ==================================================
    // PASSWORD CONFIRMATION
    // ==================================================
    // Check kar rahe hain ke dono passwords same hain ya nahi.
    // ==================================================

    elseif ($password !== $confirm_password) {

        $error = "Passwords do not match.";

    }


    // ==================================================
    // AGAR AB TAK KOI ERROR NAHI HAI
    // ==================================================
    // Ab database mein user create karne ka process start hoga.
    // ==================================================

    else {


        // ==================================================
        // CHECK KARNA KE EMAIL ALREADY EXIST KARTI HAI YA NAHI
        // ==================================================
        // Hum prepared statement use kar rahe hain.
        //
        // Prepared statements SQL Injection se protection
        // provide karne mein help karte hain.
        // ==================================================

        $check_query = "SELECT id FROM users WHERE email = ?";

        $check_statement = $connection->prepare($check_query);


        // ==================================================
        // EMAIL KO QUERY KE ? KE SAATH BIND KARNA
        // ==================================================
        // "s" ka matlab string hai.
        // ==================================================

        $check_statement->bind_param("s", $email);


        // Query execute karna
        $check_statement->execute();


        // ==================================================
        // RESULT CHECK KARNA
        // ==================================================
        // Agar email database mein already available hai,
        // to result mein ek row milegi.
        // ==================================================

        $check_result = $check_statement->get_result();


        if ($check_result->num_rows > 0) {

            // Email already registered hai.
            $error = "This email is already registered.";

        } else {


            // ==================================================
            // PASSWORD HASH KARNA
            // ==================================================
            // IMPORTANT:
            // Password ko database mein plain text mein
            // kabhi save nahi karna chahiye.
            //
            // password_hash() password ko secure hash mein
            // convert karta hai.
            //
            // PASSWORD_DEFAULT PHP ka recommended default
            // hashing algorithm use karta hai.
            // ==================================================

            $hashed_password = password_hash(
                $password,
                PASSWORD_DEFAULT
            );


            // ==================================================
            // USER KO DATABASE MEIN INSERT KARNA
            // ==================================================
            // Ab hum name, email aur hashed password ko
            // users table mein save karenge.
            // ==================================================

            $insert_query = "
                INSERT INTO users (name, email, password)
                VALUES (?, ?, ?)
            ";


            // Prepared statement create karna
            $insert_statement = $connection->prepare($insert_query);


            // ==================================================
            // VALUES BIND KARNA
            // ==================================================
            // Teen "s" ka matlab:
            //
            // 1st s = name
            // 2nd s = email
            // 3rd s = hashed password
            // ==================================================

            $insert_statement->bind_param(
                "sss",
                $name,
                $email,
                $hashed_password
            );


            // ==================================================
            // INSERT QUERY EXECUTE KARNA
            // ==================================================

            if ($insert_statement->execute()) {

                // User successfully database mein create ho gaya.
                $success = "Account created successfully!";

                // Form fields clear karna
                $name = "";
                $email = "";

            } else {

                // Agar database mein insert fail ho jaye.
                $error = "Something went wrong. Please try again.";

            }


            // Insert statement close karna
            $insert_statement->close();

        }


        // Email checking statement close karna
        $check_statement->close();

    }

}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <!-- Character encoding -->
    <meta charset="UTF-8">

    <!-- Mobile responsive design -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Browser tab ka title -->
    <title>Sign Up</title>

    <!-- External CSS file -->
    <link rel="stylesheet" href="assets/css/style.css">

</head>


<body>


    <!-- ==================================================
         SIGNUP CONTAINER
         ================================================== -->

    <div class="signup-container">

        <h1>Create Account</h1>

        <p>Sign up to create your account.</p>


        <!-- ==================================================
             ERROR MESSAGE
             ==================================================
             Agar PHP validation mein error aaya,
             to ye message show hoga.
             ================================================== -->

        <?php if (!empty($error)): ?>

            <div class="error-message">
                <?php echo htmlspecialchars($error); ?>
            </div>

        <?php endif; ?>


        <!-- ==================================================
             SUCCESS MESSAGE
             ==================================================
             Agar account successfully create ho gaya,
             to ye message show hoga.
             ================================================== -->

        <?php if (!empty($success)): ?>

            <div class="success-message">
                <?php echo htmlspecialchars($success); ?>
            </div>

        <?php endif; ?>


        <!-- ==================================================
             SIGNUP FORM
             ================================================== -->

        <form method="POST" action="">


            <!-- NAME FIELD -->

            <div class="form-group">

                <label for="name">Name</label>

                <input
                    type="text"
                    id="name"
                    name="name"
                    value="<?php echo htmlspecialchars($name); ?>"
                    placeholder="Enter your name"
                    required
                >

            </div>


            <!-- EMAIL FIELD -->

            <div class="form-group">

                <label for="email">Email</label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    value="<?php echo htmlspecialchars($email); ?>"
                    placeholder="Enter your email"
                    required
                >

            </div>


            <!-- PASSWORD FIELD -->

            <div class="form-group">

                <label for="password">Password</label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Enter your password"
                    required
                >

            </div>


            <!-- CONFIRM PASSWORD FIELD -->

            <div class="form-group">

                <label for="confirm_password">
                    Confirm Password
                </label>

                <input
                    type="password"
                    id="confirm_password"
                    name="confirm_password"
                    placeholder="Confirm your password"
                    required
                >

            </div>


            <!-- SUBMIT BUTTON -->

            <button type="submit">
                Create Account
            </button>


        </form>


        <!-- LOGIN LINK -->

        <p>
            Already have an account?
            <a href="login.php">Login here</a>
        </p>


    </div>


</body>

</html>