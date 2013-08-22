<?php
    /**
     * Code source de la classe DateFrench.
     *
     * PHP 5.3
     *
     * @package app.Utility
     * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
     */

    /**
     * La classe DateFrench fournit des méthodes permettant d'obtenir des dates
     * en français.
     *
     * @see DateComponent
     *
     * @package app.Utility
     */
    abstract class DateFrench {

        /**
         * La liste des jours de la semaine en français
         *
         * @var array
         */
        public static $daysOfWeek = array('Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi');

        /**
         * Une table de correspondance des jours en français.
         *
         * @var array
         */
        public static $days = array(
            1 => 'un',
            2 => 'deux',
            3 => 'trois',
            4 => 'quatre',
            5 => 'cinq',
            6 => 'six',
            7 => 'sept',
            8 => 'huit',
            9 => 'neuf',
            10 => 'dix',
            11 => 'onze',
            12 => 'douze',
            13 => 'treize',
            14 => 'quatorze',
            15 => 'quinze',
            16 => 'seize',
            17 => 'dix sept',
            18 => 'dix huit',
            19 => 'dix neuf',
            20 => 'vingt',
            21 => 'vingt et un',
            22 => 'vingt deux',
            23 => 'vingt trois',
            24 => 'vingt quatre',
            25 => 'vingt cinq',
            26 => 'vingt six',
            27 => 'vingt sept',
            28 => 'vingt huit',
            29 => 'vingt neuf',
            30 => 'trente',
            31 => 'trente et un',
        );

        /**
         * La liste des mois en français
         *
         * @var array
         */
        public static $months = array('', 'janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre');

        /**
         * Une table de correspondance des années en français (entre 2004 et 2015).
         *
         * @var array
         */
        public static $years = array(
            2004 => 'deux mille quatre',
            2005 => 'deux mille cinq',
            2006 => 'deux mille six',
            2007 => 'deux mille sept',
            2008 => 'deux mille huit',
            2009 => 'deux mille neuf',
            2010 => 'deux mille dix',
            2011 => 'deux mille onze',
            2012 => 'deux mille douze',
            2013 => 'deux mille treize',
            2014 => 'deux mille quatorze',
            2015 => 'deux mille quinze',
        );

        /**
         * Retourne un timestamp à partir d'un timestamp ou d'une conversion par
         * strtotime().
         *
         * @param integer|string $timestamp
         * @return type
         */
        public static function timestamp($timestamp) {
            if (is_int($timestamp)) {
                return $timestamp;
            } else {
                return strtotime($timestamp);
            }
        }

        /**
         * Retourne une chaîne de caractère en français contenant la date et
         * l'heure du timestamp.
         *
         * Exemple: Jeudi 22 août 2013 à 15 h 30
         *
         * @param integer|string $timestamp
         * @return string
         */
        public static function frenchDateConvocation($timestamp) {
            $timestamp = self::timestamp($timestamp);

            return self::frenchDate($timestamp)
                . ' à ' . date('H', $timestamp)
                . ' h ' . date('i', $timestamp);
        }

        /**
         * Retourne une chaîne de caractère en français contenant la date et
         * l'heure du timestamp.
         *
         * Exemple: Jeudi 22 août 2013
         *
         * @param integer|string $timestamp
         * @return string
         */
        public static function frenchDate($timestamp) {
            $timestamp = self::timestamp($timestamp);

            return self::$daysOfWeek[date('w', $timestamp)]
                . ' ' . date('d', $timestamp)
                . ' ' . self::$months[date('n', $timestamp)]
                . ' ' . date('Y', $timestamp);
        }

        /**
         * Retourne une chaîne de caractères en français contenant une date au
         * format court à partir d'une date au format SQL.
         *
         * Exemple: 22/08/2013
         *
         * @param string $dateSql Une date au format SQL
         * @return string
         */
        public static function frDate($dateSql) {
            if (empty($dateSql))
                return null;
            else {
                $tmp = explode(' ', $dateSql);
                $temp = explode('-', $tmp[0]);
                return $temp[2] . '/' . $temp[1] . '/' . $temp[0];
            }
        }

        /**
         * Retourne une heure à partir d'une valeur SQL.
         *
         * Si part vaut null, le résultat sera par exemple 15:30
         * Si part vaut 'hh', le résultat sera par exemple 15
         * Si part vaut 'mm', le résultat sera par exemple 30
         *
         * @param string $dateSql Une date au format SQL
         * @param string $part null, hh ou mm
         * @return string
         */
        public static function Hour($dateSql, $part = null) {
            if (empty($dateSql))
                return null;
            else {
                $tmp = explode(' ', $dateSql);
                if ($part == null) {
                    return substr($tmp[1], 0, 5);
                } elseif ($part == "hh") {
                    return substr($tmp[1], 0, 2);
                } elseif ($part == "mm") {
                    return substr($tmp[1], 3, 2);
                }
            }
        }

        /**
         * Retourne une date complètement en lettres à partir d'un timestamp.
         *
         * Exemple: L'an deux mille treize le vingt deux août
         *
         * @param integer|string $timestamp
         * @return string
         */
        public static function dateLettres($timestamp) {
            $timestamp = self::timestamp($timestamp);

            $jour = self::$daysOfWeek[date('w', $timestamp)];
            $nbJour = self::$days[(int) date('d', $timestamp)];
            $Mois = self::$months[date('n', $timestamp)];
            $nbAnnee = self::$years[date('Y', $timestamp)];

            return "L'an $nbAnnee le $nbJour $Mois ";
        }

    }

?>