<?php
    function get_orders_data() {
        $datas = file_get_contents(__DIR__ . "/../data/orders.json");
        return json_decode($datas, true);
    }

    function get_orders_by_user($user_id) {
        $orders = get_orders_data();
        $result = [];

        foreach ($orders as $order) {
            if ($order["id_client"] == $user_id) {
                $result[] = $order;
            }
        }

        return $result;
    }
?>