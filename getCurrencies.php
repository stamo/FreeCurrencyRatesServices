<?php
    require_once 'Includes/exchangeRates.php';
    require_once 'Includes/mongoDB.php';
    require_once 'Includes/httpResponceHelper.php';

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
        $currencies = $db_src->getCurrencies();
    } catch (Exception $e) {
        $http_code = 500;
        $error_message = $e->getMessage();
        http_response_code($http_code);
        die($error_message);
    }
    
    if (!$currencies) {
        try {
            $web_src = new OPENEXCHANGE();
            $currencies = $web_src->getCurrencies();
        } catch (Exception $e) {
            $http_code = 500;
            $error_message = $e->getMessage();
            http_response_code($http_code);
            die($error_message);
        }
        try {
            $db_src->saveCurrencies($currencies);
        } catch (Exception $e) {
            $http_code = 500;
            $error_message = $e->getMessage();
            http_response_code($http_code);
            die($error_message);
        }
    }
    
    http_response_code($http_code);
    echo json_encode($currencies);
?>