<?php
    require 'config.php';
    require_once 'user.php';

    global $db;
    $result = $db->query("SELECT id, name, pic FROM sub4um");
    $subforums = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];

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
    <main>
        <?php if (isLoggedIn()): ?>
            <a href="newdep.php"><button>Create new group</button></a>
        <?php endif; ?>
        <h2>Groups:</h2>
        <?php if (count($subforums) > 0): ?>
            <ul>
                <?php foreach ($subforums as $sub4um): ?>
                    <li>
                        <a href="group.php?id=<?php echo $sub4um['id']; ?>" style="text-decoration:none; color:black;">
                            <img src="img/<?php echo htmlspecialchars($sub4um['pic']); ?>" alt="group icon" width="60" height="60" style="vertical-align:middle; border-radius:6px;">
                            <strong><?php echo htmlspecialchars($sub4um['name']); ?></strong>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No groups existing</p>
        <?php endif; ?>
    </main>
<?php include 'footer.php'; ?>
