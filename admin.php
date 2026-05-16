<?php
    if(!function_exists("get_access") || !function_exists("get_accounts_data")) require_once('./api/account.php');

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
        <link rel="stylesheet" href="./styles/global.css">
        <link rel="stylesheet" href="./styles/admin.css">
        <link rel="stylesheet" href="./styles/forms.css">

        <script src="./scripts/theme.js" defer></script>
        <script src="./scripts/nav.js" defer></script>
        <script src="./scripts/api.js" defer></script>
        <script src="./scripts/admin.js" defer></script>
    </head>
    <body>
        <?php include_once('./components/header.php') ?>

        <main id="admin__main">
            <h1>Administration</h1>

            <section id="accounts__container" class="form__card">
                <h2>Comptes utilisateurs</h2>

                <div class="scrollable__wrapper">
                    <div class="scrollable__container">
                        <?php
                            foreach ($accounts_data as $account) {
                                if($account["id"] !== $_SESSION["uuid"]) include('./components/account_card.php');
                                if ($account["id"] !== $_SESSION["uuid"] && $account !== end($accounts_data)) echo "<hr />";
                            }
                        ?>
                    </div>
                </div>
            </section>
        </main>

        <?php include_once('./components/footer.php') ?>
    </body>
</html>