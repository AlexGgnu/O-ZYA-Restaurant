<?php
    require_once("./login.php");

    $data = file_get_contents("../data/accounts.json");
    $users = json_decode($data, true);

    $hash_password = password_hash($_POST["password"], PASSWORD_DEFAULT);

    foreach ($users as $user) {
        if ($user["email"] == $_POST["email"]) {
            echo "Email déjà utilisé";
            exit();
        }
    }

    if (!password_verify($_POST["confirme-pwd"], $hash_password)) {
        echo "Les mots de passe ne correspondent pas";
        exit();
    }

    $newUser = [
        "id" => uniqid(),
        "gender" => $_POST["gender"],
        "lastname" => $_POST["lastname"],
        "firstname" => $_POST["firstname"],
        "email" => $_POST["email"],
        "password" => $hash_password,
        "phone" => $_POST["phone"],
        "address" => $_POST["address"],
        "role" => "client"
    ];

    $users[] = $newUser;
    file_put_contents("../data/accounts.json", json_encode($users, JSON_PRETTY_PRINT));

    create_session($user['id'], $user['role']);
?>