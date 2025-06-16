<?php
    require_once 'user.php';

    $message = "";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email = trim($_POST["email"]);
        $username = trim($_POST["username"]);
        $password = $_POST["password"];

        $result = register($email, $username, $password);

        if ($result === true) {
            $message = "Registered successfully.";
        } else {
            $message = $result;
        }
    }

    include 'header.php';
?>
<div>
    <h2>Registration</h2>
    <form method="POST" action="register.php">
        <label>Email:</label><br>
        <input type="email" name="email" required><br><br>
        <label>Username:</label><br>
        <input type="text" name="username" required><br><br>
        <label>Password:</label><br>
        <input type="password" name="password" required><br><br>
        <input type="submit" value="Register now">
    </form>
    <?php if (!empty($message)) echo "<p>$message</p>"; ?>
</div>
<? include 'footer.php'; ?>

