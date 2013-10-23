<?php
    require_once 'Includes/exchangeRates.php';
    require_once 'Includes/mongoDB.php';
    require_once 'Includes/httpResponceHelper.php';
    require_once 'Includes/currencyManipulator.php';
    
    date_default_timezone_set("UTC");
    $today = date ('Y-m-d');
    $http_code = 200;
    $error_message = "";
    
    try {
        $db_src = new MyMONGODB();
    } catch (Exception $e) {
        $http_code = 500;
        $error_message = $e->getMessage();
        http_response_code($http_code);
        die($error_message);
    }
    
    try {
        $rates = $db_src->getRates($today);
    } catch (Exception $e) {
        $http_code = 500;
        $error_message = $e->getMessage();
        http_response_code($http_code);
        die($error_message);
    }

    if (!$rates) {
        try {
            $web_src = new OPENEXCHANGE();
            $rates = $web_src->getLatest();
        } catch (Exception $e) {
            $http_code = 500;
            $error_message = $e->getMessage();
            http_response_code($http_code);
            die($error_message);
        }
        try {
            $rates["date"] = new MongoDate(strtotime("{$today} 00:00:00"));
            $db_src->saveRates($rates);
        } catch (Exception $e) {
            $http_code = 500;
            $error_message = $e->getMessage();
            http_response_code($http_code);
            die($error_message);
        }
    }
    
    if (array_key_exists("base", $_GET) && $rates["base"] != $_GET["base"]) {
        $base = $_GET["base"];
        $manipulator = new CURRENCYMANIPULATOR($rates, $base);
        $rates = $manipulator->setBaseCurrency();
    } 
    
    http_response_code($http_code);
    echo json_encode(array("base" => $rates["base"], "rates" => $rates["rates"], "timestamp" => $rates["timestamp"]));
?>
