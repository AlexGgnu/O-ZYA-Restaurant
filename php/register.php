<?php

$data = file_get_contents("./data/users.json");
$users = json_decode($data, true);

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
echo "test";
file_put_contents("./data/users.json", json_encode($users));
?>