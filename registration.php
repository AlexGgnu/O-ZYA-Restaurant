<?php
    require_once('./php/function_account.php');

    require_once('./php/header.php');
    require_once('./php/footer.php');
    
    if(is_logged()) header("Location: /");

    $error_message = '';
    if (isset($_GET['error']) && $_GET['error'] != '') {
        $error_message = htmlspecialchars(urldecode($_GET['error']));
    }
?>

<!DOCTYPE html>
<html lang="fr-FR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        
        <title>Inscription - O'ZYA Restaurant</title>

        <link rel="icon" type="image/x-icon" href="./assets/icons/favicon.ico">
        <link rel="stylesheet" href="./styles/main.css">
    </head>

    <body>
        <?php echo get_header(false, false); ?>

        <main class="justify-center sm-p-0">
            <form class="form-card sm-flex-1 sm-justify-center sm-rounded-none" method="post" action="./php/function_account.php?auth_method=sign_up">
                <h1 class="text-center text-primary">Inscription</h1>
                
                <div class="form-group flex-row justify-between">
                    <label>Genre</label>
                    <div class="flex-row-rev gap-4">
                        <label for="men-gender">Homme</label>
                        <input type="radio" id="men-gender" name="gender" value="homme" required>
                    </div>
                    <div class="flex-row-rev gap-4">
                        <label for="women-gender">Femme</label>
                        <input type="radio" id="women-gender" name="gender" value="femme" required>
                    </div>
                    <div class="flex-row-rev gap-4">
                        <label for="other-gender">Autre</label>
                        <input type="radio" id="other-gender" name="gender" value="autre" required>
                    </div>
                </div>

                <div class="flex-row gap-20">
                    <div class="form-group">
                        <label for="lastname">Nom</label>
                        <input type="text" id="lastname" name="lastname" placeholder="Nook" required>
                    </div>
                    <div class="form-group">
                        <label for="firstname">Prénom</label>
                        <input type="text" id="firstname" name="firstname" placeholder="Tom" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="email">E-mail</label>
                    <input type="email" id="email" name="email" placeholder="exemple@mail.com" required>
                </div>

                <div class="flex flex-row gap-20">
                    <div class="form-group">
                        <label for="password">Mot de passe</label>
                        <input type="password" id="password" name="password" minlength="8" required>
                    </div>
                    <div class="form-group">
                        <label for="confirme-pwd">Confirmer</label>
                        <input type="password" id="confirme-pwd" name="confirme-pwd" minlength="8" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="phone">Téléphone</label>
                    <input type="tel" id="phone" name="phone" placeholder="06 00 00 00 00" minlength="10" maxlength="14" required>
                </div>
                
                <div class="form-group">
                    <label class="required-field" for="address">Adresse</label>
                    <input type="text" id="address" name="address" placeholder="123 Rue de la Paix, 75001 Paris" required>
                </div>

                <button type="submit" class="btn btn-primary">S'inscrire</button>

                <?php if ($error_message != ''): ?>
                    <p class="text-center text-primary mt-10"><?php echo $error_message; ?></p>
                <?php endif; ?>

                <p class="text-center mt-10">
                    Déjà un compte ? <a href="./connection.php" class="font-bold text-primary">Se connecter</a>
                </p>
            </form>
        </main>
        
        <?php echo get_footer(true); ?>
    </body>
</html>
