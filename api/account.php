<?php
    if(session_status() == PHP_SESSION_NONE) session_start();

    $account_file_path = __DIR__ . "/../data/accounts.json";
    if (!file_exists($account_file_path)) file_put_contents($account_file_path, json_encode([], JSON_PRETTY_PRINT));
    
    $promotions_file_path = __DIR__ . '/../data/promotions.json';
    if(!file_exists($promotions_file_path)) file_put_contents($promotions_file_path, json_encode(['public' => []], JSON_PRETTY_PRINT));

    // MARK: - Access management
    function is_logged() {
        if (isset($_SESSION["logged_in"]) && $_SESSION["logged_in"]) return true;
        else return false;
    }
    function is_blocked($account_id = null, $redirect = false) {
        if (!is_logged()) return false;

        $account = get_account_by_id($account_id ?? $_SESSION["uuid"]);
        if ($account && isset($account["state"]) && $account["state"] === "blocked") {
            if ($redirect) {
                destroy_session();
                if($redirect) header("Location: /");
            }
            return true;
        } else return false;
    }

    function get_access($autorized_role, $redirect = false) {
        if (!is_logged() || is_blocked($redirect = $redirect) || !in_array($_SESSION["role"], $autorized_role)) {
            if($redirect) header("Location: /");
            else return false;
        } else return true;
    }
    
    // MARK: - Account data management
    function get_accounts_data() {
        global $account_file_path;

        $datas = file_get_contents($account_file_path);
        return json_decode($datas, true);
    }
    function get_account_by_id($id) {
        $accounts_data = get_accounts_data();

        foreach ($accounts_data as $account) {
            if ($account["id"] == $id) return $account;
        }

        return null;
    }
    function get_account_by_role($role) {
        $accounts_data = get_accounts_data();
        $result = [];

        foreach ($accounts_data as $account) {
            if ($account["role"] == $role) $result[] = $account;
        }

        return $result;
    }

    // MARK: - Authentication management
    function create_session($uuid, $role, $redirection = "/") {
        $_SESSION['uuid'] = $uuid;
        $_SESSION['role'] = $role;
        $_SESSION['logged_in'] = true;

        if ($redirection !== "/") $redirection = '/' . $redirection . '.php';
        header("Location: " . $redirection);
    }
    function log_out() {
        session_destroy();
        header("Location: /");
    }

    function sign_up($redirection = "/") {
        global $account_file_path;
        $accounts_data = get_accounts_data();
        $hash_password = password_hash($_POST["password"], PASSWORD_DEFAULT);

        foreach ($accounts_data as $account) {
            if ($account["email"] == $_POST["email"]) {
                $_SESSION['error'] = urlencode("Email deja utilise");
                header("Location: /sign_up.php?redirection=" . urlencode($redirection));
                exit();
            }
        }

        if (!password_verify($_POST["confirme-pwd"], $hash_password)) {
            $_SESSION['error'] = urlencode("Les mots de passe ne correspondent pas");
            header("Location: /sign_up.php?redirection=" . urlencode($redirection));
            exit();
        }

        $new_account_data = [
            "id" => uniqid(),
            "gender" => $_POST["gender"],
            "lastname" => $_POST["lastname"],
            "firstname" => $_POST["firstname"],
            "email" => $_POST["email"],
            "password" => $hash_password,
            "phone" => $_POST["phone"],
            "address" => $_POST["address"],
            "role" => "client",
            "state" => "unblocked"
        ];

        array_push($accounts_data, $new_account_data);
        file_put_contents($account_file_path, json_encode($accounts_data, JSON_PRETTY_PRINT));

        create_session($new_account_data['id'], $new_account_data['role'], $redirection);
    }
    function sign_in($redirection = "/") {
        $accounts_data = get_accounts_data();

        foreach ($accounts_data as $account) {
            if ($account["email"] == $_POST["email"] && password_verify($_POST["password"], $account["password"])) {
                if($account["state"] === "blocked") {
                    $_SESSION['error'] = urlencode("Votre compte est bloqué");
                    header("Location: /");
                    exit();
                }

                create_session($account["id"], $account["role"], $redirection);
                exit();
            }
        }

        $_SESSION['error'] = urlencode("Email ou mot de passe incorrect");
        header("Location: /sign_in.php?redirection=" . urlencode($redirection));
        exit();
    }

    // MARK: - Account management
    function toggle_account_state($account_id) {
        global $account_file_path;
        $accounts_data = get_accounts_data();
        $is_toggled = false;

        foreach ($accounts_data as &$account) { // NOTE: '&' is used to modify the original array element
            if ($account["id"] == $account_id) {
                $account["state"] = $account["state"] === "blocked" ? "unblocked" : "blocked";
                $is_toggled = true;
                break;
            }
        }

        if($is_toggled) file_put_contents($account_file_path, json_encode($accounts_data, JSON_PRETTY_PRINT));
        return $account["state"];
    }
    function update_profile_info($account_id, $new_value) {
        global $account_file_path;
        $accounts_data = get_accounts_data();
        $decoded_values = json_decode($new_value, true);
        $is_updated = false;

        foreach ($accounts_data as &$account) { // NOTE: '&' is used to modify the original array element
            if ($account["id"] == $account_id) {
                if (isset($decoded_values["lastname"]) && $account["lastname"] !== $decoded_values["lastname"]) $account["lastname"] = $decoded_values["lastname"];
                if (isset($decoded_values["firstname"]) && $account["firstname"] !== $decoded_values["firstname"]) $account["firstname"] = $decoded_values["firstname"];
                if (isset($decoded_values["email"]) && $account["email"] !== $decoded_values["email"]) $account["email"] = $decoded_values["email"];
                if (isset($decoded_values["phone"]) && $account["phone"] !== $decoded_values["phone"]) $account["phone"] = $decoded_values["phone"];
                if (isset($decoded_values["address"]) && $account["address"] !== $decoded_values["address"]) $account["address"] = $decoded_values["address"];
                $is_updated = true;
                break;
            }
        }
        
        if($is_updated) file_put_contents($account_file_path, json_encode($accounts_data, JSON_PRETTY_PRINT));
        return $is_updated;
    }
    function update_account_role($account_id, $new_role) {
        global $account_file_path;
        $accounts_data = get_accounts_data();
        $is_updated = false;
        $valid_roles = ["client", "employee", "delivery", "admin"];

        foreach ($accounts_data as &$account) { // NOTE: '&' is used to modify the original array element
            if ($account["id"] == $account_id) {
                if (in_array($new_role, $valid_roles)) {
                    $account["role"] = $new_role;
                    $is_updated = true;
                }
                break;
            }
        }
        
        if($is_updated) file_put_contents($account_file_path, json_encode($accounts_data, JSON_PRETTY_PRINT));
        return $is_updated;
    }

    // MARK: - API management
    if(isset($_GET['redirection']) && !empty($_GET['redirection']) && $_GET['redirection'] !== "/") $redirection = str_replace(['/', '.php', '.html'], "", $_GET['redirection']);
    else $redirection = "/";

    if (isset($_GET['auth_method']) && !empty($_GET['auth_method'])) {
        if($_GET['auth_method'] == "sign_in") sign_in($redirection);
        else if($_GET['auth_method'] == "sign_up") sign_up($redirection);
        else if($_GET['auth_method'] == "log_out") log_out();
    } else if (is_logged() && isset($_POST['action']) && $_POST['action'] == "update_profile_info") {
        $type = "error";
        $title = "Erreur";
        $message = "Impossible de mettre à jour les informations du profil. Veuillez réessayer.";

        if(update_profile_info($_SESSION['uuid'], $_POST['value'])) {
            $type = "success";
            $title = "Modification réussie";
            $message = "Les informations du profil ont été mises à jour avec succès.";
        }

        echo json_encode([
            "type" => $type,
            "title" => $title,
            "message" => $message
        ]);
    } else if (get_access(["admin"]) && isset($_POST['account_id']) && !empty($_POST['account_id']) && get_account_by_id($_POST['account_id']) !== null) {
        if(isset($_POST['action']) && $_POST['action'] == "toggle_state") {
            $type = "error";
            $title = "Erreur";
            $message = "Impossible de mettre à jour l'état du compte. Veuillez réessayer.";
            $new_state = null;

            if($new_state = toggle_account_state($_POST["account_id"])) {
                $type = "success";
                $title = "Modification réussie";
                $message = "L'état du compte a été mis à jour avec succès.";
            }
            error_log("Account ID: " . $_POST["account_id"] . " - New state: " . $new_state);
                    
            echo json_encode([
                "type" => $type,
                "title" => $title,
                "message" => $message,
                "new_state" => $new_state
            ]);
        } else if ($_POST['action'] == "update_role") {
            $type = "error";
            $title = "Erreur";
            $message = "Impossible de mettre à jour le rôle du compte. Veuillez réessayer.";

            if(update_account_role($_POST["account_id"], $_POST["value"])) {
                $type = "success";
                $title = "Modification réussie";
                $message = "Le rôle du compte a été mis à jour avec succès.";
            }

            echo json_encode([
                "type" => $type,
                "title" => $title,
                "message" => $message
            ]);
        }
    }
?>