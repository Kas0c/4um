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
                <img src="img/<?php echo htmlspecialchars($_SESSION['icon']); ?>" alt="Ikonka" width="40" height="40" style="vertical-align:middle; border-radius:50%;">
                <a href="logout.php">
                    <button>Wyloguj</button>
                </a>
            </div>
        <?php else: ?>
            <a href="login.php">
                <button>Zaloguj się</button>
            </a>
        <?php endif; ?>
    </header>
    <main>


    </main>
<?php include 'footer.php'; ?>
