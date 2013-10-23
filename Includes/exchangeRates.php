<?php
    class OPENEXCHANGE {

        private $app_id;
        private $base_url;

        public function __construct() {
           $this->app_id = getenv("OPENEXCHANGE_APP_ID");
           $this->base_url = "http://openexchangerates.org/api/";
        }
        
        private function getData($file) {
            $ch = curl_init("{$this->base_url}{$file}?app_id={$this->app_id}");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            $json = curl_exec($ch);
            curl_close($ch);
            
            if ($json) {
                $data = json_decode($json, true);
                return $data;
            } else {
                throw new Exception("No connection to service");
            }
        }
        
        public function getCurrencies() {
            $file = 'currencies.json';

            return $this->getData($file);
        }
        
        public function getLatest() {
            $file = 'latest.json';

            return $this->getData($file);
        }
        
        public function getHistorical($date) {
            $file = "historical/{$date}.json";
            
            return $this->getData($file);
        }
    }
?>