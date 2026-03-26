<?php
    require_once('./php/function_account.php');
    require_once('./php/footer.php');

    if(is_logged()) header("Location: /");
?>

<!DOCTYPE html>
<html lang="fr-FR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        
        <title>Connexion - O'ZYA Restaurant</title>

        <link rel="icon" type="image/x-icon" href="./assets/icons/favicon.ico">
        <link rel="stylesheet" href="./styles/main.css">
    </head>

    <body>
        <header>
            <section class="header__container flex-row justify-between items-center w-full h-full">
                <a class="h-full" href="/">
                    <img class="h-full" src="./assets/icons/logo.png" alt="Restaurant Logo" />
                </a>
                <div class="header__menu flex-row justify-between items-center gap-32">
                    <a href="/">Accueil</a>
                    <a href="./products.php">La carte</a>
                </div>
            </section>
        </header>

        <main class="justify-center sm-p-0">
            <form class="form-card sm-flex-1 sm-justify-center sm-rounded-none" method="post" action="./php/function_account.php?auth_method=log_in">
                <h1 class="text-center text-primary">Connexion</h1>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required placeholder="exemple@mail.com">
                </div>

                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <button class="btn btn-primary w-full mt-20" type="submit">Se connecter</button>
                
                <p class="text-center mt-10">
                    Pas encore de compte ? <a href="./registration.php" class="font-bold text-primary">S'inscrire</a>
                </p>
            </form>
        </main>

        <?php echo get_footer(true); ?>
    </body>
</html>
