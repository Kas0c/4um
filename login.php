<?php
    require_once 'user.php';

    if (isLoggedIn()) {
        header("Location: index.php");
        exit;
    }

    $message = "";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = trim($_POST["username"]);
        $password = $_POST["password"];

        $result = login($username, $password);

        if ($result === true) {
            header("Location: index.php");
            exit;
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
    <form method="POST" action="login.php">
        <h2>Log in</h2>
        <label>Username:</label><br>
        <input type="text" name="username" required><br><br>
        <label>Password:</label><br>
        <input type="password" name="password" required><br><br>
        <input type="submit" value="Log in">
    </form>
    <?php if (!empty($message)) echo "<p>$message</p>"; ?>
    <p>Don't have an account?<a href="register.php">Register</a></p>
</div>

<?php include 'footer.php'; ?>
