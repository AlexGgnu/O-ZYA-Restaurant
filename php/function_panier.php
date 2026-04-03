<?php
function appliquer_coupon($code, $montant_actuel) {
    $remise = 0;

    switch ($code) {
        case 'REDUC10': 
            if ($montant_actuel > 20) {
                $remise = 10;
            }
            break;
            
        case 'SOLDES20': 
            $remise = $montant_actuel * 0.20;
            break;
            
        default:
            $remise = 0; 
            break;
    }

    return $remise;
}
?>