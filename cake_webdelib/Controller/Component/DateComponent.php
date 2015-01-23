<?php

class DateComponent extends Component {

    var $days = array('Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi');
    var $months = array('', 'janvier', 'février', 'mars', 'avril', 'mai', 'juin',
        'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre');

    function DateComponent() {
        
    }

    function frenchDateConvocation($timestamp) {
        return $this->days[date('w', $timestamp)] . ' ' . date('d', $timestamp)
                . ' ' . $this->months[date('n', $timestamp)] . ' ' . date('Y', $timestamp) . ' à ' . date('H', $timestamp) . ' h ' . date('i', $timestamp);
    }

    function frDate($mysqlDate) {
        if (empty($mysqlDate))
            return null;
        else {
            $tmp = explode(' ', $mysqlDate);
            $temp = explode('-', $tmp[0]);
            return($temp[2] . '/' . $temp[1] . '/' . $temp[0]);
        }
    }

    function Hour($mysqlDate, $part = null) {
        if (empty($mysqlDate))
            return null;
        else {
            $tmp = explode(' ', $mysqlDate);
            if ($part == null) {
                return(substr($tmp[1], 0, 5));
            } elseif ($part == "hh") {
                return(substr($tmp[1], 0, 2));
            } elseif ($part == "mm") {
                return(substr($tmp[1], 3, 2));
            }
        }
    }

}

?>
