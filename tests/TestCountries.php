<?php

require_once __DIR__ . '/../vendor/autoload.php';
use ip3country\IP3Country;

class TestCountries extends PHPUnit_Framework_TestCase {

    private $cases = [
        "1.1.1.1" => "US",
        "2.2.2.2" => "FR",
        "3.3.3.3" => "US",
        "4.4.4.4" => "US",
        "5.5.5.5" => "DE",
        "6.6.6.6" => "US",
        "7.7.7.7" => "US",
        "8.8.8.8" => "US",
        "9.9.9.9" => "US",
        "11.11.11.11" => "US",
        "12.12.12.12" => "US",
        "13.13.13.13" => "US",
        "14.14.14.14" => "JP",
        "15.15.15.15" => "US",
        "16.16.16.16" => "US",
        "17.17.17.17" => "US",
        "18.18.18.18" => "US",
        "19.19.19.19" => "US",
        "20.20.20.20" => "US",
        "21.21.21.21" => "US",
        "22.22.22.22" => "US",
        "23.23.23.23" => "US",
        "24.24.24.24" => "US",
        "25.25.25.25" => "GB",
        "26.26.26.26" => "US",
        "27.27.27.27" => "CN",
        "28.28.28.28" => "US",
        "29.29.29.29" => "US",
        "30.30.30.30" => "US",
        "31.31.31.31" => "MD",
        "41.41.41.41" => "EG",
        "42.42.42.42" => "KR",
        "45.45.45.45" => "CA",
        "46.46.46.46" => "RU",
        "49.49.49.49" => "TH",
        "101.101.101.101" => "TW",
        "110.110.110.110" => "CN",
        "111.111.111.111" => "JP",
        "112.112.112.112" => "CN",
        "150.150.150.150" => "KR",
        "200.200.200.200" => "BR",
        "202.202.202.202" => "CN",
        "45.85.95.65" => "CH",
        "58.96.74.25" => "AU",
        "88.99.77.66" => "DE",
        "25.67.94.211" => "GB",
        "27.67.94.211" => "VN",
        "27.62.93.211" => "IN",
    ];
    private $ip3c;

    public function setUp() {
        $this->ip3c = new IP3Country();
    }

    public function testHardcoded() {
        foreach ($this->cases as $key => $value) {
            $computed = $this->ip3c->lookup($key);
            $this->assertEquals($value, $computed);
        }
    }

    public function testExhaustive() {
        $ip3c_private = new ReflectionClass(get_class($this->ip3c));
        $ip_ranges = $ip3c_private->getProperty("ipRanges");
        $ip_ranges->setAccessible(true);
        $ranges = $ip_ranges->getValue($this->ip3c);

        $country_codes = $ip3c_private->getProperty("countryCodes");
        $country_codes->setAccessible(true);
        $ccs = $country_codes->getValue($this->ip3c);

        for ($ii = 1; $ii < count($ranges); $ii++) {
            $max = $ranges[$ii];
            $min = $ranges[$ii - 1];
            $expected = $ccs[$ii];

            $result = $this->ip3c->lookup($min);
            if ($result === null) {
                $result = "--";
            }
            $this->assertEquals($expected, $result);

            $result = $this->ip3c->lookup($max - 1);
            if ($result === null) {
                $result = "--";
            }
            $this->assertEquals($expected, $result);
        }
    }
}