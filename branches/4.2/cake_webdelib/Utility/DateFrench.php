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
    abstract class DateFrench
    {
        /**
         * La liste des jours de la semaine en français
         *
         * @var array
         */
        public static $days = array( 'Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi' );

        /**
         * La liste des mois en français
         *
         * @var array
         */
        public static $months = array( '', 'janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre' );

        /**
         * Retourne une chaîne de caractère en français contenant la date et
         * l'heure du timestamp.
         *
         * Exemple: Jeudi 22 août 2013 à 15 h 30
         *
         * @param integer $timestamp
         * @return string
         */
        public static function frenchDateConvocation($timestamp) {
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
         * @param integer $timestamp
         * @return string
         */
        public static function frenchDate($timestamp) {
            return self::$days[date('w', $timestamp)]
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
                return($temp[2] . '/' . $temp[1] . '/' . $temp[0]);
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
                    return(substr($tmp[1], 0, 5));
                } elseif ($part == "hh") {
                    return(substr($tmp[1], 0, 2));
                } elseif ($part == "mm") {
                    return(substr($tmp[1], 3, 2));
                }
            }
        }

        /**
         * Retourne une date complètement en lettres à partir d'un timestamp.
         *
         * Exemple: L'an deux mille treize le vingt deux août
         *
         * @param integer $timestamp
         * @return string
         */
        public static function dateLettres($timestamp) {
            $jour = self::$days[date('w', $timestamp)];
            $nbJour = date('d', $timestamp);
            switch ($nbJour) {
                case 1:
                    $nbJour = "un";
                    break;
                case 2:
                    $nbJour = "deux";
                    break;
                case 3:
                    $nbJour = "trois";
                    break;
                case 4:
                    $nbJour = "quatre";
                    break;
                case 5:
                    $nbJour = "cinq";
                    break;
                case 6:
                    $nbJour = "six";
                    break;
                case 7:
                    $nbJour = "sept";
                    break;
                case 8:
                    $nbJour = "huit";
                    break;
                case 9:
                    $nbJour = "neuf";
                    break;
                case 10:
                    $nbJour = "dix";
                    break;
                case 11:
                    $nbJour = "onze";
                    break;
                case 12:
                    $nbJour = "douze";
                    break;
                case 13:
                    $nbJour = "treize";
                    break;
                case 14:
                    $nbJour = "quatorze";
                    break;
                case 15:
                    $nbJour = "quinze";
                    break;
                case 16:
                    $nbJour = "seize";
                    break;
                case 17:
                    $nbJour = "dix sept";
                    break;
                case 18:
                    $nbJour = "dix huit";
                    break;
                case 19:
                    $nbJour = "dix neuf";
                    break;
                case 20:
                    $nbJour = "vingt";
                    break;
                case 21:
                    $nbJour = "vingt et un";
                    break;
                case 22:
                    $nbJour = "vingt deux";
                    break;
                case 23:
                    $nbJour = "vingt trois";
                    break;
                case 24:
                    $nbJour = "vingt quatre";
                    break;
                case 25:
                    $nbJour = "vingt cinq";
                    break;
                case 26:
                    $nbJour = "vingt six";
                    break;
                case 27:
                    $nbJour = "vingt sept";
                    break;
                case 28:
                    $nbJour = "vingt huit";
                    break;
                case 29:
                    $nbJour = "vingt neuf";
                    break;
                case 30:
                    $nbJour = "trente";
                    break;
                case 31:
                    $nbJour = "trente et un";
                    break;
            }
            $Mois = self::$months[date('n', $timestamp)];
            $nbAnnee = date('Y', $timestamp);
            switch ($nbAnnee) {
                case 2004:
                    $nbAnnee = "deux mille quatre";
                    break;
                case 2005:
                    $nbAnnee = "deux mille cinq";
                    break;
                case 2006:
                    $nbAnnee = "deux mille six";
                    break;
                case 2007:
                    $nbAnnee = "deux mille sept";
                    break;
                case 2008:
                    $nbAnnee = "deux mille huit";
                    break;
                case 2009:
                    $nbAnnee = "deux mille neuf";
                    break;
                case 2010:
                    $nbAnnee = "deux mille dix";
                    break;
                case 2011:
                    $nbAnnee = "deux mille onze";
                    break;
                case 2012:
                    $nbAnnee = "deux mille douze";
                    break;
                case 2013:
                    $nbAnnee = "deux mille treize";
                    break;
                case 2014:
                    $nbAnnee = "deux mille quatorze";
                    break;
                case 2015:
                    $nbAnnee = "deux mille quinze";
                    break;
            }
            return("L'an $nbAnnee le $nbJour $Mois ");
        }
    }
?>