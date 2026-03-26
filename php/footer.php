<?php
    function main_part() {
        echo '
            <section class="flex-row justify-between items-center ph-80 lg-ph-20 sm-flex-col sm-gap-40 sm-pv-16">
                <div class="flex-col justify-center items-center gap-24 pv-16 sm-gap-0 sm-pv-0">
                    <a href="/" style="width: 200px;">
                        <img class="w-full" src="./assets/icons/logo.png" alt="Restaurant Logo" />
                    </a>
                    <button class="btn btn-svg btn-inverse" id="top__button">
                        <svg class="svg-24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="currentColor">
                            <path xmlns="http://www.w3.org/2000/svg" d="M239 111c9.4-9.4 24.6-9.4 33.9 0L465 303c9.4 9.4 9.4 24.6 0 33.9s-24.6 9.4-33.9 0l-175-175L81 337c-9.4 9.4-24.6 9.4-33.9 0s-9.4-24.6 0-33.9L239 111z"/>
                        </svg>
                        Retourner en haut
                    </button>
                </div>
                <div class="flex-row gap-80 sm-flex-col sm-gap-40">
                    <div class="flex-col gap-20 sm-gap-12">
                        <h3>Plan du site</h3>
                        <div>
                            <ul>
                                <li><a href="/">Accueil</a></li>
                                <li><a href="./products.php">La carte</a></li>
        ';

                                if(!is_logged()) {
                                    echo '
                                        <li><a href="./connection.php">Connexion</a></li>
                                        <li><a href="./registration.php">Inscription</a></li>
                                    ';
                                }
                                if(is_logged()) echo '<li><a href="./profile.php">Profil</a></li>';
                                if(get_access("admin")) echo '<li><a href="./administrator.php">Administrateur</a></li>';

        echo '
                                <li><a href="./orders.php">Commandes</a></li>
                                <li><a href="./delivery.html">Livraisons</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="flex-col gap-20 sm-gap-12">
                        <h3>Autres</h3>
                        <ul>
                            <li><a href="./notation.php">Je donne mon avis</a></li>
                        </ul>
                    </div>
                </div>
            </section>
        ';
    }

    function legacy_part() {
        echo '
            <section class="legacy w-full pv-16 text-right sm-text-center">
                <p>Copyright © 2026 O\'ZYA Restaurant • Tous droits réservés</p>
            </section>
        ';
    }

    function get_footer($only_legacy = false) {
        echo '<footer>';

        if(!$only_legacy) {
            main_part();
            echo '<hr />';
        }
        legacy_part();

        echo '</footer>';
    }
?>