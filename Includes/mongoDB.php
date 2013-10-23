<?php
    class MyMONGODB {

        private $db;
        private $db_name;

        public function __construct() {
           $connection_string = getenv("MONGOLAB_URI");
           $this->db_name = getenv("MONGOLAB_DB");
           $this->db = new MongoClient($connection_string);
           date_default_timezone_set("UTC");
        }
        
        private function getData($collection_name, $query = array()){
            $collection = $this->db->selectCollection($this->db_name, $collection_name);
            $data = $collection->findOne($query);
            return $data;
        }
        
        private function saveData($data, $collection_name) {
            $collection = $this->db->selectCollection($this->db_name, $collection_name);
            $collection->insert($data);
        }
        
        public function getCurrencies() {
            return $this->getData("currencies");
        }
        
        public function saveCurrencies($currencies) {
            $this->saveData($currencies, "currencies");
        }
        
        public function getRates($date) {
            $query = array ("date" => new MongoDate(strtotime("{$date} 00:00:00")));
            return $this->getData("rates", $query);
        }
        
        public function saveRates($rates) {
            $this->saveData($rates, "rates");
        }

        public function saveErrorReport($data) {
            $data = json_decode($data);
            $this->saveData($data, "errorReports");
        }
        
        public function getRateInPeriod($from, $till) {
            $from_mongo = new MongoDate(strtotime("{$from} 00:00:00"));
            $till_mongo = new MongoDate(strtotime("{$till} 00:00:00"));
            $query = array('date' => array( '$gt' => $from_mongo, '$lt' => $till_mongo ));
            $collection = $this->db->selectCollection($this->db_name, "rates");
            $data = $collection->find($query);
            $data->sort(array('date' => 1));
            return $data;
        }
    }
?>