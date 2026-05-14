<?php
    require_once('./php/function_account.php');

    require_once('./php/header.php');

    get_access("admin", true);
    $accounts_data = get_accounts_data();
?>

<!DOCTYPE html>
<html lang="fr-FR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        
        <title>Administrateur - O'ZYA Restaurant</title>

        <link rel="icon" type="image/x-icon" href="./assets/icons/favicon.ico">
        <link rel="stylesheet" href="./styles/main.css">
    </head>
    <body>
        <?php echo get_header(true, false); ?>

        <main class="flex-col gap-40 ph-40">
            <h1 class="w-full">Administration</h1>

            <div class="form-card flex-1 gap-24 max-w-full m-0">
                <h2 class="text-primary font-600">Comptes utilisateurs</h2>

                <div class="scrollable-wrapper flex-1">
                    <div class="scrollable-container flex-col gap-14">
                        <hr />
                        <div class="scrollable-container flex-col gap-14">
                        <?php foreach ($accounts_data as $account) { ?>
                            <div class="flex-row justify-between items-center">
                                <div>
                                    <p><?= $account["lastname"] . " " . $account["firstname"] ?></p>
                                    <p>Email: <?= $account["email"] ?> | Role: <?= $account["role"] ?></p>
                                </div>
                                <div class="flex-row gap-8">
                                    <button class="btn btn-primary" >Bloquer / Désactiver</button>
                                    <button class="btn btn-primary" >Modifier Statut (VIP, Premium)</button>
                                    <button class="btn btn-primary" >Accorder remise</button>
                                </div>
                            </div>
                            <hr />
                        <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </body>
</html>