<?php
    require_once 'user.php';

    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        header("Location: index.php");
        exit;
    }

    global $db;

    $groupId = (int)$_GET['id'];

    // Pobierz dane grupy
    $query = $db->prepare("SELECT name, pic FROM sub4um WHERE id = ?");
    $query->bind_param("i", $groupId);
    $query->execute();
    $result = $query->get_result();
    $group = $result->fetch_assoc();

    if (!$group) {
        echo "Grupa nie istnieje.";
        exit;
    }

    // Pobierz tematy
    $query = $db->prepare("
        SELECT topic.id, topic.title, topic.content, topic.created_at, user.username 
        FROM topic 
        JOIN user ON topic.user_id = user.id 
        WHERE topic.group_id = ? 
        ORDER BY topic.created_at DESC
    ");
    $query->bind_param("i", $groupId);
    $query->execute();
    $topics = $query->get_result()->fetch_all(MYSQLI_ASSOC);

    include 'header.php';
?>
    <header>
        <a href="index.php">
            <h1><span class="letter">4</span><span class="um">um</span><sub>beta</sub></h1>
        </a>
        <?php if (isLoggedIn()): ?>
            <div class="opt">
                <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>
                <a href="profile.php">
                    <img src="img/<?php echo htmlspecialchars($_SESSION['icon']); ?>" alt="Ikonka" width="40" height="40" style="vertical-align:middle; border-radius:50%;">
                </a>
                <a href="logout.php">
                    <button>Log out</button>
                </a>
            </div>
        <?php else: ?>
            <div class="opt">
                <a href="login.php">
                    <button>Log in</button>
                </a>
            </div>
        <?php endif; ?>
    </header>
<main>
    <div style="display:flex; justify-content:space-between; align-items:center; padding:10px; border-bottom:1px solid #ccc;">
        <div style="display:flex; align-items:center;">
            <img src="img/<?php echo htmlspecialchars($group['pic']); ?>" width="80" height="80" style="border-radius:8px; margin-right:10px;">
            <h2><?php echo htmlspecialchars($group['name']); ?></h2>
        </div>
        <?php if (isLoggedIn()): ?>
            <a href="topic.php?group_id=<?php echo $groupId; ?>"><button>Create topic</button></a>
        <?php endif; ?>
    </div>

    <h3>Topics:</h3>
    <?php if (count($topics) > 0): ?>
        <ul>
            <?php foreach ($topics as $topic): ?>
                <li>
                    <a href="topicsite.php?id=<?php echo $topic['id']; ?>">
                        <strong><?php echo htmlspecialchars($topic['title']); ?></strong>
                    </a><br>
                    <em>Posted by <?php echo htmlspecialchars($topic['username']); ?> on <?php echo $topic['created_at']; ?></em>
                    <hr>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No topics on this group</p>
    <?php endif; ?>
</main>
<?php include 'footer.php'; ?>
