<?php
/*

	Copyright 2018 Richard McQuiston
	Free for personal, non-commercial use.
	
	Donations Gladly Accepted via PayPal to bigrpromotions@gmail.com
*/

class Weather extends Model {
    public $lat, $long, $timezone;
    public $forecastUrl, $forecastHourlyUrl, $countyUrl, $gridDataUrl;

    public function setLatLong( $lat, $long ) {
        $this->lat = $lat;
        $this->long = $long;
    }
    public function getLatLong() {
        return [ $this->lat, $this->long ];
    }
    public function getLocation() {
        $ret = [];
        if ($this->lat != '' && $this->long != '') {
            $data = file_get_contents('https://api.weather.gov/points/' . $this->lat . ',' . $this->long);
            if ($data != '') {
                $ret = json_decode($data);
                if ($ret->properties != '') {
                    $this->forecastUrl = $ret->properties->forecast;
                    $this->forecastHourlyUrl = $ret->properties->forecastHourly;
                    $this->gridDataUrl = $ret->properties->forecastGridData;
                    $this->countyUrl = $ret->properties->county;
                    $this->timezone = $ret->properties->timeZone;
                }
            }
        }
        return $ret;
    }
    public function getForecast() {
        $ret = [];
        if ($this->forecastUrl != '') {
            $data = file_get_contents($this->forecastUrl);
            if ($data != '') {
                $ret = json_decode($data);
            }
        }
        return $ret;
    }
    public function getHourly() {
        $ret = [];
        if ($this->forecastHourlyUrl != '') {
            $data = file_get_contents($this->forecastHourlyUrl);
            if ($data != '') {
                $ret = json_decode($data);
            }
        }
        return $ret;
    }
    public function getGrid() {
        $ret = [];
        if ($this->gridDataUrl != '') {
            $data = file_get_contents($this->gridDataUrl);
            if ($data != '') {
                $ret = json_decode($data);
            }
        }
        return $ret;
    }
    public function getCounty() {
        $ret = [];
        if ($this->countyUrl != '') {
            $data = file_get_contents($this->countyUrl);
            if ($data != '') {
                $ret = json_decode($data);
            }
        }
        return $ret;
    }
}
