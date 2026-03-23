<?php
    $data = file_get_contents("../data/users.json");
    $users = json_decode($data, true);

    $trouve = false;

    foreach ($users as $user) {
        if ($user["email"] == $_POST["email"] && password_verify($_POST["password"], $user["password"])) {
            $_SESSION["user"] = $user;
            $trouve = true;

            switch ($user["role"]) {
                case "admin":
                    header("Location: ../administrator.php");
                    exit();
                case "restaurateur":
                    header("Location: ../orders.php");
                    exit();
                case "livreur":
                    header("Location: ../delivery.php");
                    exit();
                default:
                    header("Location: ../profile.php");
                    exit();
            }
        }
    }

    if (!$trouve) {
        echo "Email ou mot de passe incorrect";
    }
?>