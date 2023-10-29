<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    $user = isset($_POST['user']) ? $_POST['user'] : null;
    $password = isset($_POST['password']) ? $_POST['password'] : null;

    if ($user && $password) {
        $stmt = $mysqli->prepare('SELECT * FROM login WHERE user=?');
        $stmt->bind_param('s', $user);

        $stmt->execute();

        $result = $stmt->get_result();

        $row = mysqli_fetch_assoc($result);

        // Variable $hash hold the password hash on database
        $hash = isset($row['password']) ? $row['password'] : '';
        /*
        password_Verify() function verify if the password entered by the user
        match the password hash on the database.
        */
        if (password_verify($_POST['password'], $hash)) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['user_name'] = $row['name'];
            $_SESSION['user_admin'] = $row['admin'];
            $_SESSION['allow_loop'] = $row['allow_loop'];
            $_SESSION['department_id'] = $row['department_id'];
            $_SESSION['session_start'] = time();
        }
    }
}
