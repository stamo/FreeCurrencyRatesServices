<?php
    class CURRENCYMANIPULATOR {
        private $base;
        private $rates;
        private $timespan;
        
        public function __construct($rates, $base) {
            $this->base = $base;
            $this->rates = $rates["rates"];
            $this->timespan = $rates["timestamp"];
            $this->rates[$rates["base"]] = 1;
        }
        
        public function setBaseCurrency () {
            if (!array_key_exists($this->base, $this->rates)) {
                throw new Exception("Unknown currency!");
            }
            
            $factor = $this->rates[$this->base];
            foreach ($this->rates as $curr => $rate) {
                $new_rate = (float)$rate / (float)$factor;
                $this->rates[$curr] = number_format($new_rate, 6, '.', '');
            }
            
            return array("base" => $this->base, "rates" => $this->rates, "timestamp" => $this->timespan);
        }
    }
?>
