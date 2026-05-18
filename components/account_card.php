<?php
    if($account) {
        $selected_roles = [
            "client" => "Client",
            "employee" => "Employé",
            "admin" => "Administrateur",
            "delivery" => "Livreur"
        ];

        echo '
            <div class="account__card" data-account-id="' . $account["id"] . '" data-account-state="' . $account["state"] . '">
                <div class="account__info">
                    <h4 class="account__name">' . $account["lastname"] . " " . $account["firstname"] . '</h4>
                    <p class="account__email">' . $account["email"] . '</p>
                </div>

                <div class="account__actions">
                    <button class="toggle__state__button btn btn-svg btn-primary">
                        <svg class="block__icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="currentColor">
                            <path d="M367.2 412.5L99.5 144.8C77.1 176.1 64 214.5 64 256c0 106 86 192 192 192c41.5 0 79.9-13.1 111.2-35.5zm45.3-45.3C434.9 335.9 448 297.5 448 256c0-106-86-192-192-192c-41.5 0-79.9 13.1-111.2 35.5L412.5 367.2zM0 256a256 256 0 1 1 512 0A256 256 0 1 1 0 256z"/>
                        </svg>
                        <svg class="unblock__icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" fill="currentColor">
                            <path d="M432 48c-44.2 0-80 35.8-80 80v64h32c35.3 0 64 28.7 64 64V448c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V256c0-35.3 28.7-64 64-64H304V128C304 57.3 361.3 0 432 0s128 57.3 128 128v72c0 13.3-10.7 24-24 24s-24-10.7-24-24V128c0-44.2-35.8-80-80-80zM384 240H64c-8.8 0-16 7.2-16 16V448c0 8.8 7.2 16 16 16H384c8.8 0 16-7.2 16-16V256c0-8.8-7.2-16-16-16zM256 376H192c-13.3 0-24-10.7-24-24s10.7-24 24-24h64c13.3 0 24 10.7 24 24s-10.7 24-24 24z"/>
                        </svg>
                        <span>Bloquer</span>
                    </button>
                    <select name="role" class="account__role update__role__button">
                        <option value="client" ' . ($account["role"] === "client" ? "selected" : "") . '>Client</option>
                        <option value="employee" ' . ($account["role"] === "employee" ? "selected" : "") . '>Employé</option>
                        <option value="delivery" ' . ($account["role"] === "delivery" ? "selected" : "") . '>Livreur</option>
                        <option value="admin" ' . ($account["role"] === "admin" ? "selected" : "") . '>Administrateur</option>
                    </select>
                    <a href="admin.php?view_id=' . $account["id"] . '" class="view__profile__button btn btn-svg btn-primary" style="text-decoration: none; display: inline-flex; align-items: center; justify-content: center;">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" fill="currentColor">
                        <path d="M129.1 361.4C93.6 327.2 67.7 286.9 52.5 256c15.1-30.9 41-71.2 76.6-105.4C171.8 109.5 224.9 80 288 80s116.2 29.5 158.9 70.6c35.6 34.3 61.5 74.5 76.6 105.4c-15.1 30.9-41 71.2-76.6 105.4C404.2 402.5 351.1 432 288 432s-116.2-29.5-158.9-70.6zM288 480c158.4 0 258-149.3 288-224C546 181.3 446.4 32 288 32S30 181.3 0 256c30 74.7 129.6 224 288 224zm0-144c-44.2 0-80-35.8-80-80c0-5.4 .5-10.6 1.5-15.7L288 256l-15.7-78.5c5.1-1 10.3-1.5 15.7-1.5c44.2 0 80 35.8 80 80s-35.8 80-80 80zM160 256c0 70.7 57.3 128 128 128s128-57.3 128-128s-57.3-128-128-128c-8.6 0-17 .8-25.1 2.5c-50.3 10-90 49.5-100.3 99.7l-.1 .7c-1.6 8.1-2.5 16.5-2.5 25.1z"/>
                    </svg>
                    <span>Voir le profil</span>
                    </a>
                </div>
            </div>
        ';
    }
?>