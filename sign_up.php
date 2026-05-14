<?php
    require_once('./api/account.php');
    if(is_logged()) header("Location: /profile.php");
?>

<!DOCTYPE html>
<html lang="fr-FR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        
        <title>Inscription - O'ZYA Restaurant</title>

        <link rel="icon" type="image/x-icon" href="./assets/icons/favicon.ico">
        <link rel="stylesheet" href="./styles/global.css">
        <link rel="stylesheet" href="./styles/forms.css">

        <script src="./scripts/theme.js" defer></script>
        <script src="./scripts/nav.js" defer></script>
        <script src="./scripts/form_validation.js" defer></script>
    </head>

    <body>
        <?php include_once('./components/header.php'); ?>

        <main>
            <form class="form__card" method="POST" action="/api/account.php?auth_method=sign_up<?php if(isset($_GET['redirection']) && !empty($_GET['redirection'])) echo "&redirection=" . urlencode($_GET['redirection']); ?>">
                <h1>Inscription</h1>
                
                <div>
                    <div class="form__group row__group">
                        <label>Genre</label>

                        <div>
                            <label for="men-gender">Homme</label>
                            <input type="radio" id="men-gender" name="gender" value="homme" required />
                        </div>
                        <div>
                            <label for="women-gender">Femme</label>
                            <input type="radio" id="women-gender" name="gender" value="femme" required />
                        </div>
                        <div>
                            <label for="other-gender">Autre</label>
                            <input type="radio" id="other-gender" name="gender" value="autre" required />
                        </div>
                    </div>

                    <div class="query__group">
                        <div class="form__group row__group">
                            <label for="lastname">Nom</label>
                            <input type="text" id="lastname" name="lastname" placeholder="Nook" autocomplete="family-name" required />
                        </div>
                        <div class="form__group row__group">
                            <label for="firstname">Prénom</label>
                            <input type="text" id="firstname" name="firstname" placeholder="Tom" autocomplete="given-name" required />
                        </div>
                    </div>

                    <div class="form__group row__group">
                        <label for="email">E-mail</label>
                        <input type="email" id="email" name="email" placeholder="exemple@mail.com" autocomplete="email" required />
                    </div>

                    <div class="query__group">
                        <div class="form__group row__group">
                            <label for="password">Mot de passe</label>
                            <div>
                                <input type="password" id="password" name="password" minlength="8" autocomplete="new-password" required />
                                <p id="password__counter" class="form__counter"><span>0</span> / 8 min.</p>
                            </div>

                            <button type="button" class="btn btn-primary btn-svg toggle-password" data-target="password">
                                <svg class="hidden-eye" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512" fill="currentColor">
                                    <path d="M48.4 14.8L29.4 .1 0 38 19 52.7 591.5 497.2l19 14.7L639.9 474l-19-14.7L524 384.1c41.9-44 70.2-93.9 84-128.1C578 181.3 478.4 32 320 32c-66.9 0-123.2 26.6-168.3 63L48.4 14.8zM190.8 125.4C227.6 98 270.8 80 320 80c63 0 116.2 29.5 158.9 70.6c35.6 34.3 61.5 74.5 76.6 105.4c-14.1 28.9-37.6 65.8-69.6 98.5L434 314.2c8.9-17.5 14-37.2 14-58.2c0-70.7-57.3-128-128-128c-8.6 0-17 .8-25.1 2.5c-22.5 4.5-42.9 14.9-59.5 29.5l-44.6-34.6zM395 283.9l-82.2-63.8-8.5-42.6c5.1-1 10.3-1.5 15.7-1.5c44.2 0 80 35.8 80 80c0 9.8-1.8 19.2-5 27.9zm49.9 162.7l-41.6-32.7C377.9 425.3 350.1 432 320 432c-63.1 0-116.2-29.5-158.9-70.6C125.6 327.2 99.7 286.9 84.5 256c9.1-18.7 22.2-40.7 38.9-62.8L85.7 163.5C60.2 197.1 42.1 230.8 32 256c30 74.7 129.6 224 288 224c46.9 0 88.6-13.1 124.9-33.4zm-86.7-68.3L302 334c-23.5-5.4-43.1-21.2-53.7-42.3l-56.1-44.2c-.2 2.8-.3 5.6-.3 8.5c0 70.7 57.3 128 128 128c13.3 0 26.1-2 38.2-5.8z"/>
                                </svg>
                                <svg class="show-eye" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" fill="currentColor">
                                    <path d="M129.1 361.4C93.6 327.2 67.7 286.9 52.5 256c15.1-30.9 41-71.2 76.6-105.4C171.8 109.5 224.9 80 288 80s116.2 29.5 158.9 70.6c35.6 34.3 61.5 74.5 76.6 105.4c-15.1 30.9-41 71.2-76.6 105.4C404.2 402.5 351.1 432 288 432s-116.2-29.5-158.9-70.6zM288 480c158.4 0 258-149.3 288-224C546 181.3 446.4 32 288 32S30 181.3 0 256c30 74.7 129.6 224 288 224zm0-144c-44.2 0-80-35.8-80-80c0-5.4 .5-10.6 1.5-15.7L288 256l-15.7-78.5c5.1-1 10.3-1.5 15.7-1.5c44.2 0 80 35.8 80 80s-35.8 80-80 80zM160 256c0 70.7 57.3 128 128 128s128-57.3 128-128s-57.3-128-128-128c-8.6 0-17 .8-25.1 2.5c-50.3 10-90 49.5-100.3 99.7l-.1 .7c-1.6 8.1-2.5 16.5-2.5 25.1z"/>
                                </svg>
                            </button>
                        </div>
                        <div class="form__group row__group">
                            <label for="confirme-pwd">Confirmer</label>
                            <div>
                                <input type="password" id="confirme-pwd" name="confirme-pwd" minlength="8" autocomplete="new-password" required />
                                <p id="confirme-pwd__counter" class="form__counter"><span>0</span> / 8 min.</p>
                            </div>

                            <button type="button" class="btn btn-primary btn-svg toggle-password" data-target="confirme-pwd">
                                <svg class="hidden-eye" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512" fill="currentColor">
                                    <path d="M48.4 14.8L29.4 .1 0 38 19 52.7 591.5 497.2l19 14.7L639.9 474l-19-14.7L524 384.1c41.9-44 70.2-93.9 84-128.1C578 181.3 478.4 32 320 32c-66.9 0-123.2 26.6-168.3 63L48.4 14.8zM190.8 125.4C227.6 98 270.8 80 320 80c63 0 116.2 29.5 158.9 70.6c35.6 34.3 61.5 74.5 76.6 105.4c-14.1 28.9-37.6 65.8-69.6 98.5L434 314.2c8.9-17.5 14-37.2 14-58.2c0-70.7-57.3-128-128-128c-8.6 0-17 .8-25.1 2.5c-22.5 4.5-42.9 14.9-59.5 29.5l-44.6-34.6zM395 283.9l-82.2-63.8-8.5-42.6c5.1-1 10.3-1.5 15.7-1.5c44.2 0 80 35.8 80 80c0 9.8-1.8 19.2-5 27.9zm49.9 162.7l-41.6-32.7C377.9 425.3 350.1 432 320 432c-63.1 0-116.2-29.5-158.9-70.6C125.6 327.2 99.7 286.9 84.5 256c9.1-18.7 22.2-40.7 38.9-62.8L85.7 163.5C60.2 197.1 42.1 230.8 32 256c30 74.7 129.6 224 288 224c46.9 0 88.6-13.1 124.9-33.4zm-86.7-68.3L302 334c-23.5-5.4-43.1-21.2-53.7-42.3l-56.1-44.2c-.2 2.8-.3 5.6-.3 8.5c0 70.7 57.3 128 128 128c13.3 0 26.1-2 38.2-5.8z"/>
                                </svg>
                                <svg class="show-eye" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" fill="currentColor">
                                    <path d="M129.1 361.4C93.6 327.2 67.7 286.9 52.5 256c15.1-30.9 41-71.2 76.6-105.4C171.8 109.5 224.9 80 288 80s116.2 29.5 158.9 70.6c35.6 34.3 61.5 74.5 76.6 105.4c-15.1 30.9-41 71.2-76.6 105.4C404.2 402.5 351.1 432 288 432s-116.2-29.5-158.9-70.6zM288 480c158.4 0 258-149.3 288-224C546 181.3 446.4 32 288 32S30 181.3 0 256c30 74.7 129.6 224 288 224zm0-144c-44.2 0-80-35.8-80-80c0-5.4 .5-10.6 1.5-15.7L288 256l-15.7-78.5c5.1-1 10.3-1.5 15.7-1.5c44.2 0 80 35.8 80 80s-35.8 80-80 80zM160 256c0 70.7 57.3 128 128 128s128-57.3 128-128s-57.3-128-128-128c-8.6 0-17 .8-25.1 2.5c-50.3 10-90 49.5-100.3 99.7l-.1 .7c-1.6 8.1-2.5 16.5-2.5 25.1z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    <div class="form__group row__group">
                        <label for="phone">Téléphone</label>
                        <input type="tel" id="phone" name="phone" placeholder="06 00 00 00 00" minlength="10" maxlength="14" autocomplete="tel" required />
                    </div>
                    
                    <div class="form__group row__group">
                        <label class="required-field" for="address">Adresse</label>
                        <input type="text" id="address" name="address" placeholder="123 Rue de la Paix, 75001 Paris" autocomplete="street-address" required />
                    </div>
                </div>

                <div>
                    <button class="btn btn-primary" type="submit">S'inscrire</button>

                    <p>
                        Déjà un compte ? <a href="./sign_in.php<?php if(isset($_GET['redirection']) && !empty($_GET['redirection'])) echo "?redirection=" . urlencode($_GET['redirection']); ?>">Se connecter</a>
                    </p>
                </div>
                
                <?php 
                    if (isset($_SESSION['error']) && $_SESSION['error'] != '') {
                        echo '<p class="error">' . htmlspecialchars(urldecode($_SESSION['error'])) . '</p>';
                        unset($_SESSION['error']);
                    }
                ?>
            </form>
        </main>
        
        <?php include_once('./components/footer.php'); ?>
    </body>
</html>
