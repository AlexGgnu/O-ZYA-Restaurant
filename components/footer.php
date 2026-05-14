<?php
    if(!function_exists("is_logged")) require_once(__DIR__ . '/../api/account.php');

    echo '
        <footer>
            <section class="footer__content">
                <div class="footer__content__brand">
                    <a href="/">
                        <img class="w-full" src="./assets/images/logo.png" alt="Restaurant Logo" />
                    </a>
                    <button class="btn btn-svg btn-inverse" id="top__button">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="currentColor">
                            <path xmlns="http://www.w3.org/2000/svg" d="M239 111c9.4-9.4 24.6-9.4 33.9 0L465 303c9.4 9.4 9.4 24.6 0 33.9s-24.6 9.4-33.9 0l-175-175L81 337c-9.4 9.4-24.6 9.4-33.9 0s-9.4-24.6 0-33.9L239 111z"/>
                        </svg>
                        Retourner en haut
                    </button>
                </div>
                <div class="footer__links">
                    <div class="footer__links__wrapper">
                        <h2>Plan du site</h2>
                        <ul>
                            <li><a href="/">Accueil</a></li>
                            <li><a href="./products.php">La carte</a></li>
    ';
        
    if (!is_logged()) {
        echo '<li><a href="./sign_in.php">Connexion</a></li>';
        echo '<li><a href="./sign_up.php">Inscription</a></li>';
    }
    if (is_logged()) echo '<li><a href="./profile.php">Profil</a></li>';
    if(get_access("admin")) echo '<li><a href="./administrator.php">Administrateur</a></li>';
    if(get_access("restaurateur")) echo '<li><a href="./orders.php">Commandes</a></li>';
    if(get_access("delivery")) echo '<li><a href="./delivery.php">Livraisons</a></li>';

    echo '
                        </ul>
                    </div>
                    <div class="footer__links__wrapper">
                        <h2>Autres</h2>
                        <ul>
                            <li><a href="./notation.php">Je donne mon avis</a></li>
                        </ul>
                    </div>
                </div>
            </section>
            
            <hr />

            <section class="legacy">
                <p>Copyright © 2026 O\'ZYA Restaurant • Tous droits réservés</p>
            </section>
        </footer>
    ';
?>