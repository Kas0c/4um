<?php
    require_once 'config.php';

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    function register($email, $username, $password) {
        global $db;

        // Sprawdza czy użytkownik istnieje
        $query = $db->prepare("SELECT id FROM user WHERE email = ? OR username = ?");
        $query->bind_param("ss", $email, $username);
        $query->execute();
        $query->store_result();
        if ($query->num_rows > 0) {
            return "User with this email or username already exists.";
        }
        $query->close();

        // Haszuje hasło
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $defaultIcon = "default.png";

        // Tworzy usera
        $query = $db->prepare("INSERT INTO user (id, email, username, password, icon) VALUES (NULL, ?, ?, ?, ?)");
        $query->bind_param("ssss", $email, $username, $passwordHash, $defaultIcon);

        if ($query->execute()) {
            return true;
        } else {
            return "Register error: " . $db->error;
        }
    }

    function login($username, $password) {
        global $db;

        $query = $db->prepare("SELECT id, username, password, icon FROM user WHERE username = ?");
        $query->bind_param("s", $username);
        $query->execute();
        $result = $query->get_result();

        if ($row = $result->fetch_assoc()) {
            if (password_verify($password, $row['password'])) {
                // loguje usera
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['username'] = $row['username'];
                $_SESSION['icon'] = $row['icon'];
                return true;
            } else {
                return "Wrong password.";
            }
        } else {
            return "User does not exist.";
        }
    }

    function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
?>

