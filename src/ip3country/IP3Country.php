<?php

namespace ip3country;

class IP3Country {

    private $countryCodes = [];
    private $ipRanges = [];
    private $countryTable = [];

    function __construct() {
        $table = file_get_contents(dirname(__FILE__).'/ip_supalite.table');

        $index = 0;
        while ($index < strlen($table)) {
            $c1 = $table[$index++];
            $c2 = $table[$index++];
            $this->countryTable[] = "" . $c1 . $c2;
            if ($c1 === "*") {
                break;
            }
        }

        $lastEndRange = 0;
        while ($index < strlen($table)) {
            $count = 0;
            $n1 = ord($table[$index++]);
            if ($n1 < 240) {
                $count = $n1;
            } else if ($n1 == 242) {
                $n2 = ord($table[$index++]);
                $n3 = ord($table[$index++]);
                
                $count = $n2 | ($n3 << 8);
            } else if ($n1 == 243) {
                $n2 = ord($table[$index++]);
                $n3 = ord($table[$index++]);
                $n4 = ord($table[$index++]);
                $count = $n2 | ($n3 << 8) | ($n4 << 16);
            }

            $lastEndRange += $count * 256;
        
            $cc = ord($table[$index++]);
            $this->ipRanges[] = $lastEndRange;
            $this->countryCodes[] = $this->countryTable[$cc];
        }
    }

    function lookup($ipaddr) {
        $numeric = $ipaddr;
        if (gettype($ipaddr) == "string") {
            $components = explode(".", $ipaddr);
            if (count($components) != 4) {
                return null;
            }
            $numeric = intval($components[0]) * 16777216
                + intval($components[1]) * 65536
                + intval($components[2]) * 256
                + intval($components[3]);
        }
        $index = $this->binarySearch($numeric);
        $cc = $this->countryCodes[$index];
        return $cc === "--" ? null : $cc;
    }

    private function binarySearch($target) {
        $min = 0;
        $max = count($this->ipRanges) - 1;

        while ($min < $max) {
            $mid = ($min + $max) >> 1;
            if ($this->ipRanges[$mid] <= $target) {
                $min = $mid + 1;
            } else {
                $max = $mid;
            }
        }

        return $min;
    }
}