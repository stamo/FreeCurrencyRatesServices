<?php
    class MAILGUN {

        private $app_key;
        private $base_url;
        private $domain;

        public function __construct() {
           $this->app_key = getenv("MAIL_GUN_API_KEY");
           $this->base_url = "https://api.mailgun.net/v2/";
           $this->domain = "currencyratesstamopetkov.mailgun.org";
        }
        
        public function sendMessage($fields) {
            $url = $this->base_url . $this->domain . "/messages";
            $authToken = "api:" . $this->app_key;
            
            $fields_string = http_build_query($fields);
            
            $ch = curl_init();

            //set the url, number of POST vars, POST data
            curl_setopt($ch,CURLOPT_URL, $url);
            curl_setopt($ch,CURLOPT_USERPWD, $authToken);
            curl_setopt($ch,CURLOPT_POST, count($fields));
            curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);

            //execute post
            $result = curl_exec($ch);
            
            $error = curl_error($ch);

            //close connection
            curl_close($ch);
            
            if (!$result) {
                throw new Exception($error);
            }
        }
    }
?>