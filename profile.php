<?php
    require_once 'user.php';
    global $db;

    if (!isLoggedIn()) {
        header("Location: login.php");
        exit;
    }

    $userId = $_SESSION['user_id'];
    $username = $_SESSION['username'];
    $email = '';
    $icon = $_SESSION['icon'];
    $message = "";

    // pobiera dane
    $query = $db->prepare("SELECT email, username, icon FROM user WHERE id = ?");
    $query->bind_param("i", $userId);
    $query->execute();
    $result = $query->get_result();

    if ($row = $result->fetch_assoc()) {
        $email = $row['email'];
        $username = $row['username'];
        $icon = $row['icon'];
    }

    // aktualizuje dane
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $newEmail = trim($_POST["email"]);
        $newUsername = trim($_POST["username"]);
        $newPassword = $_POST["password"];
        $newPasswordHashed = !empty($newPassword) ? password_hash($newPassword, PASSWORD_DEFAULT) : null;

        // Sprawdza czy mail lub username nie istnieje
        $query = $db->prepare("SELECT id FROM user WHERE (email = ? OR username = ?) AND id != ?");
        $query->bind_param("ssi", $newEmail, $newUsername, $userId);
        $query->execute();
        $query->store_result();

        if ($query->num_rows > 0) {
            $message = "Email or username already exists";
        } else {
            if ($newPasswordHashed) {
                $query = $db->prepare("UPDATE user SET email = ?, username = ?, password = ? WHERE id = ?");
                $query->bind_param("sssi", $newEmail, $newUsername, $newPasswordHashed, $userId);
            } else {
                $query = $db->prepare("UPDATE user SET email = ?, username = ? WHERE id = ?");
                $query->bind_param("ssi", $newEmail, $newUsername, $userId);
            }

            if ($query->execute()) {
                $_SESSION['username'] = $newUsername;
                $message = "profile updated";
            } else {
                $message = "Update error: " . $db->error;
            }
        }
    }

    // przesyłanie pliku PNG
    if (isset($_FILES["icon"]) && $_FILES["icon"]["error"] === UPLOAD_ERR_OK) {
        $fileTmp = $_FILES["icon"]["tmp_name"];
        $fileName = basename($_FILES["icon"]["name"]);
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if ($fileExt === "png") {
            $newFileName = "user_" . $userId . ".png";
            $uploadPath = "img/" . $newFileName;
            move_uploaded_file($fileTmp, $uploadPath);

            $query = $db->prepare("UPDATE user SET icon = ? WHERE id = ?");
            $query->bind_param("si", $newFileName, $userId);
            $query->execute();

            $_SESSION['icon'] = $newFileName;
            $icon = $newFileName;
            $message = "profile icon updated";
        } else {
            $message = ".png required";
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
    <h2>Your profile</h2>
    <img src="img/<?php echo htmlspecialchars($icon); ?>" alt="your icon" width="150" height="150" style="border-radius:10px;"><br><br>

    <form method="POST" enctype="multipart/form-data">

        <label>New icon (.png required):</label><br>
        <input type="file" name="icon" accept="image/png"><br><br>
        <label>Email:</label><br>
        <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required><br><br>
        <label>Username:</label><br>
        <input type="text" name="username" value="<?php echo htmlspecialchars($username); ?>" required><br><br>
        <label>New password:</label><br>
        <input type="password" name="password"><br><br>
        <input type="submit" value="Save changes">
    </form>
    <?php if (!empty($message)) echo "<p>$message</p>"; ?>
</div>
<?php include 'footer.php'; ?>

