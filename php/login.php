<?php
    function create_session($uuid, $role, $redirection = "/") {
        $_SESSION['uuid'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['logged_in'] = true;

        header("Location: " . $redirection);
        exit();
    }

    $data = file_get_contents("../data/users.json");
    $users = json_decode($data, true);

    $trouve = false;

    foreach ($users as $user) {
        if ($user["email"] == $_POST["email"] && password_verify($_POST["password"], $user["password"])) {
            $_SESSION["user"] = $user;
            $trouve = true;

            create_session($user["id"], $user["role"]);
        }
    }

    if (!$trouve) {
        echo "Email ou mot de passe incorrect";
    }
?>