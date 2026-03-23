<?php
$data = file_get_contents("../data/users.json");
$users = json_decode($data, true);


foreach ($users as $user) {
    if ($user["email"] == $_POST["email"]) {
        echo "Email déjà utilisé";
        exit();
    }
}

if ($_POST["password"] != $_POST["confirme-pwd"]) {
    echo "Les mots de passe ne correspondent pas";
    exit();
}

$newUser = [
    "id" => count($users) + 1,
    "gender" => $_POST["gender"],
    "nom" => $_POST["nom"],
    "prenom" => $_POST["prenom"],
    "email" => $_POST["email"],
    "password" => $_POST["password"],
    "phone" => $_POST["phone"],
    "address" => $_POST["address"],
    "role" => "client"
];

$users[] = $newUser;

file_put_contents("../data/users.json", json_encode($users, JSON_PRETTY_PRINT));

header("Location: ../connection.html");
exit();
?>