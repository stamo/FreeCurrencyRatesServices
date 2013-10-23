<?php
    require_once 'Includes/mailGun.php';
    require_once 'Includes/mongoDB.php';
    require_once 'Includes/httpResponceHelper.php';
    
    $http_code = 201;
    $error_message = "";
    $from = "Currency Rates App <app@currencyratesstamopetkov.mailgun.org>";
    $to = "Stamo Petkov <stamo.petkov@gmail.com>";
    $subject = "Error Report";
    
    if (array_key_exists("data", $_POST)) {
        $data = $_POST["data"];
    } else {
        $http_code = 400;
        http_response_code($http_code);
        die("Missing parameter: data!");
    }
    
    try {
        $db_src = new MyMONGODB();
    } catch (Exception $e) {
        $http_code = 500;
        $error_message = $e->getMessage();
        http_response_code($http_code);
        die($error_message);
    }
    
    $db_src->saveErrorReport($data);
    
    $fields = array('from'    => $from,
                    'to'      => $to,
                    'subject' => $subject,
                    'text'    => $data);
    
    $mail_gun = new MAILGUN();
    
    try {
        $mail_gun->sendMessage($fields);
    } catch (Exception $e) {
        $http_code = 500;
        $error_message = $e->getMessage();
        http_response_code($http_code);
        die($error_message);
    }
    
    http_response_code($http_code);
?>
