<?php
    require_once('./php/function_account.php');
    require_once('./php/header.php');
    require_once('./php/footer.php');

    $notation_error = '';
    if (isset($_GET['error']) && $_GET['error'] != '') {
        $notation_error = htmlspecialchars(urldecode($_GET['error']));
    }

    $notation_success = '';
    if (isset($_GET['success']) && $_GET['success'] != '') {
        $notation_success = htmlspecialchars(urldecode($_GET['success']));
    }
?>

<!DOCTYPE html>
<html lang="fr-FR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>Notation - O'ZYA Restaurant</title>

        <link rel="icon" type="image/x-icon" href="./assets/icons/favicon.ico">
        <link rel="stylesheet" href="./styles/main.css">
    </head>
    <body>
        <?php echo get_header(false, false); ?>
        
        <main class="justify-center sm-p-0">
            <form class="rating-form form-card sm-flex-1 sm-justify-center sm-gap-40 sm-ph-20 sm-rounded-none" method="post" action="./php/function_notation.php">
                <h1 class="text-center text-primary">Donnez votre avis</h1>

                <div class="flex-col gap-12">
                    <div class="flex justify-between items-center">
                        <h2>Qualité des produits</h2>
                        <div class="rating-stars">★★★★★</div>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <h2>Qualité de la livraison</h2>
                        <div class="rating-stars">★★★★★</div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Commentaire</label>
                    <textarea name="commentaire" placeholder="Dites-nous ce que vous en avez pensé..." required></textarea>
                </div>

                <?php if ($notation_error != '') { ?>
                    <p class="text-center text-error"><?php echo $notation_error; ?></p>
                <?php } ?>

                <?php if ($notation_success != '') { ?>
                    <p class="text-center text-primary"><?php echo $notation_success; ?></p>
                <?php } ?>

                <button class="btn btn-primary w-full mt-20" type="submit">Envoyer mon avis</button>
            </form>
        </main>

        <?php echo get_footer(true); ?>
    </body>
</html>
