<?php
    if(!function_exists("is_logged") || !function_exists("is_blocked") || !function_exists("get_account_by_id")) require_once('./api/account.php');

    if (is_logged() && isset($_SESSION['uuid'])) {
        is_blocked($_SESSION['uuid'], $redirect = true);

        if(get_access(["admin"], false) && isset($_GET['view_id'])) $account_data = get_account_by_id($_GET['view_id']);
        else $account_data = get_account_by_id($_SESSION['uuid']);

        if(!$account_data || $account_data['id'] !== $_SESSION['uuid'] && !get_access(["admin"], false)) {
            header("Location: ./sign_in.php");
            exit();
        }
    } else {
        header("Location: ./sign_in.php");
        exit();
    }
?>
<!DOCTYPE html>
<html lang="fr-FR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>Mon profil - O'ZYA Restaurant</title>

        <link rel="icon" type="image/x-icon" href="./assets/icons/favicon.ico">
        <link rel="stylesheet" href="./styles/global.css">
        <link rel="stylesheet" href="./styles/forms.css">
        <link rel="stylesheet" href="./styles/profile.css">

        <script src="./scripts/shared.js" defer></script>
        <script src="./scripts/theme.js" defer></script>
        <script src="./scripts/nav.js" defer></script>
        <script src="./scripts/api.js" defer></script>
        <script src="./scripts/form_validation.js" defer></script>
        <script src="./scripts/profile.js" defer></script>
    </head>
    <body>
        <?php include_once('./components/header.php'); ?>

        <main>
            <?php 
                if(get_access(["admin"], false) && $_SESSION['uuid'] !== $account_data['id']) {
                    echo '<a id="back__button" class="btn btn-svg btn-primary" href="./admin.php">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" fill="currentColor">
                            <path d="M7.4 273.4C2.7 268.8 0 262.6 0 256s2.7-12.8 7.4-17.4l176-168c9.6-9.2 24.8-8.8 33.9 .8s8.8 24.8-.8 33.9L83.9 232 424 232c13.3 0 24 10.7 24 24s-10.7 24-24 24L83.9 280 216.6 406.6c9.6 9.2 9.9 24.3 .8 33.9s-24.3 9.9-33.9 .8l-176-168z"/>
                        </svg>
                        Retour
                    </a>';
                } else {
                    echo '<h1>Mon Profil</h1>';
                }
            ?>

            <div id="profile__container">
                <section id="profile__info">
                    <!-- MARK: - Personal informations -->
                    <div class="form__card">
                        <div id="profile__info__header">
                            <h2>Informations personnelles</h2>
                            <?php if($_SESSION['uuid'] === $account_data['id'] || get_access(["admin"], false)) { ?>
                                <button id="edit__button" class="btn btn-svg btn-primary">
                                    <svg id="edit__icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="currentColor">
                                        <path xmlns="http://www.w3.org/2000/svg" d="M36.4 360.9L13.4 439 1 481.2C-1.5 489.7 .8 498.8 7 505s15.3 8.5 23.7 6.1L73 498.6l78.1-23c12.4-3.6 23.7-9.9 33.4-18.4c1.4-1.2 2.7-2.5 4-3.8L492.7 149.3c21.9-21.9 24.6-55.6 8.2-80.5c-2.3-3.5-5.1-6.9-8.2-10L453.3 19.3c-25-25-65.5-25-90.5 0L58.6 323.5c-10.4 10.4-18 23.3-22.2 37.4zm46 13.5c1.7-5.6 4.5-10.8 8.4-15.2c.6-.6 1.1-1.2 1.7-1.8L321 129 383 191 154.6 419.5c-4.7 4.7-10.6 8.2-17 10.1l-23.4 6.9L59.4 452.6l16.1-54.8 6.9-23.4z"/>
                                    </svg>
                                    <svg id="check__icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" fill="currentColor">
                                        <path class="fa-primary" d="M438.6 105.4c12.5 12.5 12.5 32.8 0 45.3l-256 256c-12.5 12.5-32.8 12.5-45.3 0l-128-128c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0L160 338.7 393.4 105.4c12.5-12.5 32.8-12.5 45.3 0z"/>
                                    </svg>
                                </button>
                            <?php } ?>
                        </div>

                        <div id="profile__info__content">
                            <div class="form__group col__group">
                                <label for="lastname">Nom</label>
                                <input type="text" id="lastname" name="lastname" value="<?= $account_data['lastname'] ?>" required disabled/>
                            </div>
                                
                            <div class="form__group col__group">
                                <label for="firstName">Prénom</label>
                                <input type="text" id="firstName" name="firstName" value="<?= $account_data['firstname'] ?>" required disabled/>
                            </div>
                                
                            <div class="form__group col__group">
                                <label for="email">E-mail</label>
                                <input type="email" id="email" name="email" value="<?= $account_data['email'] ?>" required disabled/>
                            </div>
                            
                            <div class="form__group col__group">
                                <label for="phone">N° de téléphone</label>
                                <input type="tel" id="phone" name="phone" value="<?= $account_data['phone'] ?>" required disabled/>
                            </div>
                                
                            <div class="form__group col__group">
                                <label for="address">Adresse</label>
                                <input type="text" id="address" name="address" value="<?= $account_data['address'] ?>" required disabled/>
                            </div>
                        </div>
                        <button id="btn-save-profile" class="btn btn-primary" style="display:none;">
                        Sauvegarder
                        </button>
                        <p id="msg-profile" style="display:none; text-align:center;"></p>
                    </div>

                    <!-- MARK: - Loyalty Account -->
                    <div id="profile__reductions" class="form__card">
                        <h2>Vos réductions fidélité</h2>
                        <div class="scrollable__wrapper">
                            <div class="scrollable__container">
                                <!-- TODO: Display reductions code -->
                            </div>
                        </div>
                    </div>
                </section>

                <section id="profile__orders">
                    <!-- MARK: - Orders History -->
                    <div class="form__card">
                        <h2>Historique des commandes</h2>

                        <div class="scrollable__wrapper">
                            <div class="scrollable__container">
                                <?php include_once('./components/orders_table.php'); ?>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </main>
            
        <?php include_once('./components/footer.php'); ?>
    </body>
</html>