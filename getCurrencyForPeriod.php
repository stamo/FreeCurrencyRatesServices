<?php
    require_once 'Includes/exchangeRates.php';
    require_once 'Includes/mongoDB.php';
    require_once 'Includes/httpResponceHelper.php';
    require_once 'Includes/currencyManipulator.php';
    
    date_default_timezone_set("UTC");
    $http_code = 200;
    $error_message = "";
    
    if (array_key_exists("from", $_GET) && 
            array_key_exists("till", $_GET) && 
            array_key_exists("currency", $_GET) && 
            array_key_exists("base", $_GET)) {
        $from = $_GET["from"];
        $till = $_GET["till"];
        $currency = $_GET["currency"];
        $base = $_GET["base"];
    } else {
        $http_code = 400;
        http_response_code($http_code);
        die("Missing parameter: from, till, base and currency are required!");
    }
    
    try {
        $db_src = new MyMONGODB();
    } catch (Exception $e) {
        $http_code = 500;
        $error_message = $e->getMessage();
        http_response_code($http_code);
        die($error_message);
    }
    
    try {
        $rates = $db_src->getRateInPeriod($from, $till);
    } catch (Exception $e) {
        $http_code = 500;
        $error_message = $e->getMessage();
        http_response_code($http_code);
        die($error_message);
    }
    
    
    $result = array();
    
    foreach ($rates as $rate) {
        $date = date("Y-m-d", $rate["date"]->sec);
        $manipulator = new CURRENCYMANIPULATOR($rate, $base);
        $rate = $manipulator->setBaseCurrency();
        $result[$date] = $rate["rates"][$currency];
    }
    
    http_response_code($http_code);
    echo json_encode($result);
?>