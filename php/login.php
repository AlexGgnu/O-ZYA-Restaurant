<?php

session_start();

$data = file_get_contents("./data/users.json");
$users = json_decode($data, true);

foreach($users as $user){

 if($user["email"] == $_POST["email"] &&
    $user["password"] == $_POST["password"]) {

    $_SESSION["user"] = $user;

    echo "Connexion réussie";
    exit();
 }

}

echo "Email ou mot de passe incorrect";

?>