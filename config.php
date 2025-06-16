<?php

    $db = new mysqli("localhost", "root", "", "4um");

    if ($db->connect_error) {
        die("Can't connect: " . $db->connect_error);
    }
?>