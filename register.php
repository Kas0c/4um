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
<header>
    <a href="index.php">
        <h1><span class="letter">4</span><span class="um">um</span><sub>beta</sub></h1>
    </a>
</header>
<div class="forms">
    <form method="POST" action="register.php">
        <h2>Registration</h2>
        <label>Email:</label><br>
        <input type="email" name="email" required><br><br>
        <label>Username:</label><br>
        <input type="text" name="username" required><br><br>
        <label>Password:</label><br>
        <input type="password" name="password" required><br><br>
        <input type="submit" value="Register now">
    </form>
    <?php if (!empty($message)) echo "<p>$message</p>"; ?>
    <p>Already have an account?<a href="login.php">Log in</a></p>
</div>
<?php include 'footer.php'; ?>

