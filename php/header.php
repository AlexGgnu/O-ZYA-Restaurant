<?php
    function logo_button() {
        echo '
            <a class="h-full" href="/">
                <img class="h-full" src="./assets/icons/logo.png" alt="Restaurant Logo" />
            </a>
        ';
    }

    function menu_container() {
        $active = strtolower(basename($_SERVER['PHP_SELF'], ".php"));

        echo '
            <div class="header__menu flex-row justify-between items-center gap-32 sm-gap-12">
                <a href="/" ' . ($active === "index" ? 'id="active__menu"' : '') . '>Accueil</a>
                <a href="./products.php" ' . ($active === "products" ? 'id="active__menu"' : '') . '>La carte</a>
            </div>
        ';
    }
    
    function searchbar_button() {
        echo '
            <button class="btn btn-svg" id="search__button">
                <svg class="svg-24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="currentColor">
                    <path d="M368 208A160 160 0 1 0 48 208a160 160 0 1 0 320 0zM337.1 371.1C301.7 399.2 256.8 416 208 416C93.1 416 0 322.9 0 208S93.1 0 208 0S416 93.1 416 208c0 48.8-16.8 93.7-44.9 129.1L505 471c9.4 9.4 9.4 24.6 0 33.9s-24.6 9.4-33.9 0L337.1 371.1z"/>
                </svg>
            </button>
        ';
    }
    function searchbar_section() {
        echo '
            <section class="header__search-bar w-full" id="search__container">
                <input class="text-center" type="text" placeholder="Rechercher vos plats favoris ici..." />
            </section>
        ';
    }

    function right_part($searchbar) {
        echo '<div class="header__right flex-row items-center gap-24 sm-gap-12">';

        if ($searchbar) {
            echo searchbar_button();
        }

        if(is_logged()) {
            echo '
                <a class="btn btn-svg" href="./basket.php">
                    <svg class="svg-24 sm-block" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" fill="currentColor">
                        <path d="M243.1 2.7c11.8 6.1 16.3 20.6 10.2 32.4L171.7 192H404.3L322.7 35.1c-6.1-11.8-1.5-26.3 10.2-32.4s26.2-1.5 32.4 10.2L458.4 192h36.1H544h8c13.3 0 24 10.7 24 24s-10.7 24-24 24H532L476.1 463.5C469 492 443.4 512 414 512H162c-29.4 0-55-20-62.1-48.5L44 240H24c-13.3 0-24-10.7-24-24s10.7-24 24-24h8H81.5h36.1L210.7 12.9c6.1-11.8 20.6-16.3 32.4-10.2zM93.5 240l53 211.9c1.8 7.1 8.2 12.1 15.5 12.1H414c7.3 0 13.7-5 15.5-12.1l53-211.9H93.5zM224 312v80c0 13.3-10.7 24-24 24s-24-10.7-24-24V312c0-13.3 10.7-24 24-24s24 10.7 24 24zm64-24c13.3 0 24 10.7 24 24v80c0 13.3-10.7 24-24 24s-24-10.7-24-24V312c0-13.3 10.7-24 24-24zm112 24v80c0 13.3-10.7 24-24 24s-24-10.7-24-24V312c0-13.3 10.7-24 24-24s24 10.7 24 24z"/>
                    </svg>
                </a>
            ';

            $current_page = strtolower(basename($_SERVER['PHP_SELF'], ".php"));
            if ($current_page === "profile" || $current_page === "administrator") {
                echo '
                    <a class="btn btn-svg btn-primary" href="/php/function_account.php?auth_method=log_out">
                        <span class="font-bold sm-hidden">Se déconnecter</span>

                        <svg class="hidden svg-24 sm-block" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="currentColor">
                            <path d="M72.2 436C58.7 404.6 48 348.4 48 256s10.7-148.6 24.2-180C84.6 47.3 94.6 47.9 95.9 48l.1 0 .1 0c1.3-.1 11.3-.7 23.7 28c13.5 31.4 24.2 87.7 24.2 180c0 45.7-2.6 82.6-6.9 112H120c-13.3 0-24 10.7-24 24s10.7 24 24 24h7s0 0 0 0h49.7s0 0 0 0h28.2c16.7 0 31.6-10.3 37.4-25.9l14.1-37.6-4.9-2.8c-14.1-8-25.4-19.3-33-32.6L199.4 368H185.6c4.2-31.3 6.4-68.4 6.4-112c0-21.4-.5-41.2-1.6-59.6l37.7-32.4c4.7-4 10.3-6.9 16.3-8.4l1.8-.5c6.4-1.6 13-1.6 19.4 0l8.6 2.1-32.7 98c-8.5 25.5 2.3 53.4 25.7 66.5l88 49.5L321.1 480.8c-4 12.7 3.1 26.1 15.8 30.1s26.1-3.1 30.1-15.8L403 379.5c5.6-18-2.1-37.5-18.6-46.8l-32.1-18 28.1-84.4 5.6 18.2C393.3 272 415 288 439.6 288H488c13.3 0 24-10.7 24-24s-10.7-24-24-24H439.6c-3.5 0-6.6-2.3-7.6-5.6l-19.7-64.2c-5.8-18.7-20.9-33.1-39.9-37.9l-95-23.7c-14-3.5-28.7-3.5-42.7 0l-1.8 .5c-13.3 3.3-25.6 9.7-35.9 18.6L184.7 138C170.3 38 136 0 96 0C43 0 0 66.6 0 256S43 512 96 512c28 0 53.2-18.6 70.7-64H113.9c-9.5 16.5-16.7 16.1-17.8 16l-.1 0-.1 0c-1.3 .1-11.3 .7-23.7-28zM368 96a48 48 0 1 0 0-96 48 48 0 1 0 0 96zm-19.9 79.8l-38.3 115-19-10.7c-3.3-1.9-4.9-5.9-3.7-9.5L321 169l27.1 6.8z"/>
                        </svg>
                    </a>
                ';
            } else {
                echo '
                    <a class="btn btn-svg btn-primary" href="./profile.php">
                        <span class="font-bold sm-hidden">Mon profil</span>

                        <svg class="hidden svg-24 sm-block" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" fill="currentColor">
                            <path d="M304 128a80 80 0 1 0 -160 0 80 80 0 1 0 160 0zM96 128a128 128 0 1 1 256 0A128 128 0 1 1 96 128zM49.3 464H398.7c-8.9-63.3-63.3-112-129-112H178.3c-65.7 0-120.1 48.7-129 112zM0 482.3C0 383.8 79.8 304 178.3 304h91.4C368.2 304 448 383.8 448 482.3c0 16.4-13.3 29.7-29.7 29.7H29.7C13.3 512 0 498.7 0 482.3z"/>
                        </svg>
                    </a>
                ';
            }
        } else {
            echo '
                <a class="btn btn-svg btn-primary" href="./connection.php">
                    <span class="font-bold sm-hidden">Se connecter</span>

                    <svg class="hidden svg-24 sm-block" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" fill="currentColor">
                        <path xmlns="http://www.w3.org/2000/svg" d="M304 128a80 80 0 1 0 -160 0 80 80 0 1 0 160 0zM96 128a128 128 0 1 1 256 0A128 128 0 1 1 96 128zM49.3 464H398.7c-8.9-63.3-63.3-112-129-112H178.3c-65.7 0-120.1 48.7-129 112zM0 482.3C0 383.8 79.8 304 178.3 304h91.4C368.2 304 448 383.8 448 482.3c0 16.4-13.3 29.7-29.7 29.7H29.7C13.3 512 0 498.7 0 482.3z"/>
                    </svg>
                </a>
            ';
        }

        echo '</div>';
    }

    function get_header($right_part = true, $searchbar = true) {
        echo '<header>';
        echo '<section class="header__container flex-row justify-between items-center w-full h-full">';

        logo_button();
        menu_container();
        if($right_part) right_part($searchbar);
        
        echo '</section>';
        
        if($searchbar) searchbar_section();
        
        echo '</header>';
    }

?>