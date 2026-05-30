<?php
    if(!function_exists('get_promo_by_account_id')) require_once(__DIR__ . '/../api/basket.php');

    function format_promotion_code($code) {
        return strtoupper($code);
    }
    function format_promotion__value($promotion) {
        return '-' . number_format($promotion, 2, '.', '') . '€';
    }

    function render_promotion_card($code, $promotion_value) {
        return '
            <div class="promotion__card" data-promo-code="' . $code . '">
                <div class="promotion__info">
                    <h4 class="promotion__code">' . format_promotion_code($code) . '</h4>
                    <p class="promotion__value">' . format_promotion__value($promotion_value) . '</p>
                </div>

                <div class="promotion__actions">
                    <button class="copy__button btn btn-svg btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" fill="currentColor">
                            <path d="M384 336H192c-8.8 0-16-7.2-16-16V64c0-8.8 7.2-16 16-16l140.1 0L400 115.9V320c0 8.8-7.2 16-16 16zM192 384H384c35.3 0 64-28.7 64-64V115.9c0-12.7-5.1-24.9-14.1-33.9L366.1 14.1c-9-9-21.2-14.1-33.9-14.1H192c-35.3 0-64 28.7-64 64V320c0 35.3 28.7 64 64 64zM64 128c-35.3 0-64 28.7-64 64V448c0 35.3 28.7 64 64 64H256c35.3 0 64-28.7 64-64V416H272v32c0 8.8-7.2 16-16 16H64c-8.8 0-16-7.2-16-16V192c0-8.8 7.2-16 16-16H96V128H64z"/>
                        </svg>
                        <span>Copier</span>
                    </button>
                </div>
            </div>
        ';
    }

    if($account_data) {
        $promotions = get_promo_by_account_id($account_data["id"]);

        if(empty($promotions)) echo '<p>Vous n\'avez pas de code promotionnel pour le moment.</p>';
        else {
            foreach($promotions as $code => $promotion) {
                echo render_promotion_card($code, $promotion);
                if($code !== array_key_last($promotions)) echo "<hr />";
            }
        }
    }
?>