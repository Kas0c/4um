<?php
    require_once 'user.php';

    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        header("Location: index.php");
        exit;
    }

    global $db;

    $topicId = (int)$_GET['id'];

    // Pobiera temat
    $query = $db->prepare("
        SELECT topic.title, topic.content, topic.created_at, user.username 
        FROM topic 
        LEFT JOIN user ON topic.user_id = user.id 
        WHERE topic.id = ?
    ");
    $query->bind_param("i", $topicId);
    $query->execute();
    $result = $query->get_result();
    $topic = $result->fetch_assoc();

    if (!$topic) {
        echo "Temat nie istnieje.";
        exit;
    }

    // dodaje komentarz
    $commentError = "";
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['content'])) {
        $content = trim($_POST['content']);
        $nickname = isLoggedIn() ? null : trim($_POST['nickname']);
        $userId = isLoggedIn() ? $_SESSION['user_id'] : null;

        if (empty($content) || (!$userId && empty($nickname))) {
            $commentError = "Treść komentarza i pseudonim są wymagane.";
        } else {
            $query = $db->prepare("INSERT INTO comments (topic_id, user_id, nickname, content) VALUES (?, ?, ?, ?)");
            $query->bind_param("iiss", $topicId, $userId, $nickname, $content);
            $query->execute();
        }
    }

    // Pobiera komentarze
    $query = $db->prepare("
        SELECT comments.content, comments.created_at, user.username, comments.nickname 
        FROM comments 
        LEFT JOIN user ON comments.user_id = user.id 
        WHERE comments.topic_id = ? 
        ORDER BY comments.created_at DESC
    ");
    $query->bind_param("i", $topicId);
    $query->execute();
    $comments = $query->get_result()->fetch_all(MYSQLI_ASSOC);

    include 'header.php';
?>
    <header>
        <a href="index.php">
            <h1>4um</h1>
        </a>
        <?php if (isLoggedIn()): ?>
            <div>
                <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>
                <a href="profile.php">
                    <img src="img/<?php echo htmlspecialchars($_SESSION['icon']); ?>" alt="Ikonka" width="40" height="40" style="vertical-align:middle; border-radius:50%;">
                </a>
                <a href="logout.php">
                    <button>Log out</button>
                </a>
            </div>
        <?php else: ?>
            <a href="login.php">
                <button>Log in</button>
            </a>
        <?php endif; ?>
    </header>
    <div style="border:1px solid #ccc; padding:20px; margin-bottom:30px;">
        <h2><?php echo htmlspecialchars($topic['title']); ?></h2>
        <p><em>Author: <?php echo htmlspecialchars(isset($topic['username']) ? $topic['username'] : 'Nieznany'); ?> | commented on: <?php echo $topic['created_at']; ?></em></p>

        <p><?php echo nl2br(htmlspecialchars($topic['content'])); ?></p>
    </div>
    <div>
        <h3>Add comment</h3>
        <form method="POST">
            <?php if (!isLoggedIn()): ?>
                <label>Alias:</label><br>
                <input type="text" name="nickname" required><br><br>
            <?php endif; ?>
            <label>Content:</label><br>
            <textarea name="content" rows="4" cols="50" required></textarea><br><br>
            <input type="submit" value="Add comment">
        </form>
        <?php if (!empty($commentError)) echo "<p style='color:red;'>$commentError</p>"; ?>
    </div>
    <div>
        <h3>Comments:</h3>
        <?php if (!empty($comments)): ?>
            <?php foreach ($comments as $comment): ?>
                <div style="border-bottom:1px solid #ddd; margin-bottom:10px;">
                    <?php
                    $commentAuthor = '';

                    if (isset($comment['username'])) {
                        if ($comment['username'] === $topic['username']) {
                            $commentAuthor = '@' . $comment['username'];
                        } else {
                            $commentAuthor = '#' . $comment['username'];
                        }
                    } else {
                        $commentAuthor = $comment['nickname'];
                    }
                    ?>
                    <strong><?php echo htmlspecialchars($commentAuthor); ?></strong>
                    <small> | <?php echo $comment['created_at']; ?></small>
                    <p><?php echo nl2br(htmlspecialchars($comment['content'])); ?></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No comments</p>
        <?php endif; ?>
    </div>

    <hr>


<?php include 'footer.php'; ?>