<?php
    require 'config.php';
    require_once 'user.php';


    include 'header.php';
?>
    <header>
        <h1>4um</h1>
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


    </main>
<?php include 'footer.php'; ?>
