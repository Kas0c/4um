<?php
    require_once 'user.php';

    if (!isLoggedIn()) {
        header("Location: login.php");
        exit;
    }

    if (!isset($_GET['group_id']) || !is_numeric($_GET['group_id'])) {
        header("Location: index.php");
        exit;
    }

    global $db;

    $groupId = (int)$_GET['group_id'];
    $message = "";

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $title = trim($_POST['title']);
        $content = trim($_POST['content']);

        if (empty($title) || empty($content)) {
            $message = "Tytuł i treść są wymagane.";
        } else {
            $query = $db->prepare("INSERT INTO topic (group_id, user_id, title, content) VALUES (?, ?, ?, ?)");
            $query->bind_param("iiss", $groupId, $_SESSION['user_id'], $title, $content);

            if ($query->execute()) {
                header("Location: group.php?id=" . $groupId);
                exit;
            } else {
                $message = "Błąd dodawania tematu.";
            }
        }
    }

    include 'header.php';
?>

    <h2>Create new topic</h2>
    <form method="POST">
        <label>Title:</label><br>
        <input type="text" name="title" required><br><br>
        <label>Content:</label><br>
        <textarea name="content" rows="6" cols="50" required></textarea><br><br>
        <input type="submit" value="Create topic">
    </form>
    <?php if (!empty($message)) echo "<p style='color:red;'>$message</p>"; ?>
<?php include 'footer.php'; ?>

