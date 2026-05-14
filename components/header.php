<?php
    if(!function_exists("is_logged")) require_once(__DIR__ . '/../api/account.php');

    $current_page = strtolower(basename($_SERVER['PHP_SELF'], ".php"));

    if(is_logged()) {
        if($current_page === "profile") {
            $link_label = "Se déconnecter";
            $link = "/api/account.php?auth_method=log_out";
        } else {
            $link_label = "Mon profil";
            $link = "./profile.php";
        }
    } else {
        $link_label = "Se connecter";
        $link = "./sign_in.php";
    }

    if($current_page === "sign_in" || $current_page === "sign_up") $auth_page = true;
    else $auth_page = false;

    echo '
        <header data-menu-state="closed">
            <nav>
                <div class="brand">
                    <a href="/">
                        <img src="./assets/images/logo.png" alt="O\'ZYA Restaurant Logo">
                    </a>
                </div>

                <ul class="nav-links">
                    <a href="/" ' . ($current_page === "index" ? 'id="active__menu"' : '') . '>Accueil</a>
                    <a href="./products.php" ' . ($current_page === "products" ? 'id="active__menu"' : '') . '>La carte</a>
                </ul>

                <div class="user-links">
                    <a class="btn btn-svg" href="./basket.php">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" fill="currentColor">
                            <path d="M243.1 2.7c11.8 6.1 16.3 20.6 10.2 32.4L171.7 192H404.3L322.7 35.1c-6.1-11.8-1.5-26.3 10.2-32.4s26.2-1.5 32.4 10.2L458.4 192h36.1H544h8c13.3 0 24 10.7 24 24s-10.7 24-24 24H532L476.1 463.5C469 492 443.4 512 414 512H162c-29.4 0-55-20-62.1-48.5L44 240H24c-13.3 0-24-10.7-24-24s10.7-24 24-24h8H81.5h36.1L210.7 12.9c6.1-11.8 20.6-16.3 32.4-10.2zM93.5 240l53 211.9c1.8 7.1 8.2 12.1 15.5 12.1H414c7.3 0 13.7-5 15.5-12.1l53-211.9H93.5zM224 312v80c0 13.3-10.7 24-24 24s-24-10.7-24-24V312c0-13.3 10.7-24 24-24s24 10.7 24 24zm64-24c13.3 0 24 10.7 24 24v80c0 13.3-10.7 24-24 24s-24-10.7-24-24V312c0-13.3 10.7-24 24-24zm112 24v80c0 13.3-10.7 24-24 24s-24-10.7-24-24V312c0-13.3 10.7-24 24-24s24 10.7 24 24z"/>
                        </svg>
                    </a>

                    <a class="btn btn-primary ' . ($auth_page ? 'hidden' : '') . '" href="' . $link . '">' . $link_label . '</a>

                    <button id="color-scheme-toggle" class="btn btn-svg">
                        <svg class="light-theme" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="currentColor">
                            <path d="M280 24V88c0 13.3-10.7 24-24 24s-24-10.7-24-24V24c0-13.3 10.7-24 24-24s24 10.7 24 24zm157 84.9l-45.3 45.3c-9.4 9.4-24.6 9.4-33.9 0s-9.4-24.6 0-33.9L403.1 75c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9zM108.9 75l45.3 45.3c9.4 9.4 9.4 24.6 0 33.9s-24.6 9.4-33.9 0L75 108.9c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0zM24 232H88c13.3 0 24 10.7 24 24s-10.7 24-24 24H24c-13.3 0-24-10.7-24-24s10.7-24 24-24zm400 0h64c13.3 0 24 10.7 24 24s-10.7 24-24 24H424c-13.3 0-24-10.7-24-24s10.7-24 24-24zM154.2 391.8L108.9 437c-9.4 9.4-24.6 9.4-33.9 0s-9.4-24.6 0-33.9l45.3-45.3c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9zm237.6-33.9L437 403.1c9.4 9.4 9.4 24.6 0 33.9s-24.6 9.4-33.9 0l-45.3-45.3c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0zM280 424v64c0 13.3-10.7 24-24 24s-24-10.7-24-24V424c0-13.3 10.7-24 24-24s24 10.7 24 24zm40-168a64 64 0 1 0 -128 0 64 64 0 1 0 128 0zm-176 0a112 112 0 1 1 224 0 112 112 0 1 1 -224 0z"/>
                        </svg>
                        <svg class="dark-theme" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="currentColor">
                            <path d="M295.8 137.8c1 3.6 4.4 6.2 8.2 6.2s7.1-2.5 8.2-6.2l11-38.6 38.6-11c3.6-1 6.2-4.4 6.2-8.2s-2.5-7.1-6.2-8.2l-38.6-11-11-38.6c-1-3.6-4.4-6.2-8.2-6.2s-7.1 2.5-8.2 6.2l-11 38.6-38.6 11c-3.6 1-6.2 4.4-6.2 8.2s2.5 7.1 6.2 8.2l38.6 11 11 38.6zM403.8 310.8c1.6 5.5 6.6 9.2 12.2 9.2s10.7-3.8 12.2-9.2l16.6-58 58-16.6c5.5-1.6 9.2-6.6 9.2-12.2s-3.8-10.7-9.2-12.2l-58-16.6-16.6-58c-1.6-5.5-6.6-9.2-12.2-9.2s-10.7 3.8-12.2 9.2l-16.6 58-58 16.6c-5.5 1.6-9.2 6.6-9.2 12.2s3.8 10.7 9.2 12.2l58 16.6 16.6 58zM48 320c0-70 50-128.3 116.2-141.3C141.6 206.3 128 241.5 128 280c0 83.2 63.5 151.6 144.7 159.3c-23 15.6-50.8 24.7-80.7 24.7c-79.5 0-144-64.5-144-144zM192 128C86 128 0 214 0 320S86 512 192 512c69.4 0 130.2-36.9 163.9-92c5.3-8.7 4.6-19.9-2-27.8s-17.3-10.8-26.9-7.2c-12.1 4.5-25.3 7-39.1 7c-61.9 0-112-50.1-112-112c0-45 26.6-83.9 65-101.7c9.3-4.3 14.8-14 13.8-24.2s-8.4-18.6-18.3-20.9c-14.3-3.4-29.2-5.2-44.4-5.2z"/>
                        </svg>
                    </button>
                </div>

                <button id="menu-toggle">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </nav>
        </header>
    ';
?>