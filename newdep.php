<?php
    require_once 'user.php';

    if (!isLoggedIn()) {
        header("Location: login.php");
        exit;
    }

    global $db;

    $message = "";

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $name = trim($_POST["name"]);

        if (empty($name)) {
            $message = "Group name is required";
        } else {
            // Sprawdza czy grupa istnieje
            $query = $db->prepare("SELECT id FROM sub4um WHERE name = ?");
            $query->bind_param("s", $name);
            $query->execute();
            $query->store_result();

            if ($query->num_rows > 0) {
                $message = "group name already taken";
            } else {
                // obrazek
                $picName = "defaultpic.png";

                if (isset($_FILES["pic"]) && $_FILES["pic"]["error"] === UPLOAD_ERR_OK) {
                    $fileTmp = $_FILES["pic"]["tmp_name"];
                    $fileName = basename($_FILES["pic"]["name"]);
                    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                    if (in_array($fileExt, ["png"])) {
                        $picName = "group_" . time() . "." . $fileExt;
                        move_uploaded_file($fileTmp, "img/" . $picName);
                    } else {
                        $message = ".png required";
                    }
                }

                if (empty($message)) {
                    $query = $db->prepare("INSERT INTO sub4um (name, pic) VALUES (?, ?)");
                    $query->bind_param("ss", $name, $picName);

                    if ($query->execute()) {
                        header("Location: index.php");
                        exit;
                    } else {
                        $message = "Group creation error " . $db->error;
                    }
                }
            }
        }
    }

    include 'header.php';
?>
    <h2>Create new group</h2>
    <form method="POST" enctype="multipart/form-data">
        <label>group name:</label><br>
        <input type="text" name="name" required><br><br>
        <label>Group icon:</label><br>
        <input type="file" name="pic" accept="image/png"><br><br>
        <input type="submit" value="Create group">
    </form>
    <?php if (!empty($message)) echo "<p>$message</p>"; ?>
<?php include 'footer.php'; ?>

