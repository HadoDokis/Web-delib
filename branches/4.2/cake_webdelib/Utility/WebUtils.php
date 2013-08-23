<?php

/**
 * Code source de la classe WebUtils.
 *
 * PHP 5.3
 *
 * @package app.Utility
 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
 */

/**
 * La classe WebUtils fournit diverses méthodes utilitaires pour WebDelib.
 *
 * @see UtilsComponent
 *
 * @package app.Utility
 */
abstract class WebUtils {

    /**
     * Fonction utile pour l'affichage
     */
    public static function sqlDateTime($d) {
        $date = substr($d, 8, 2) . "/";        // jour
        $date = $date . substr($d, 5, 2) . "/";  // mois
        $date = $date . substr($d, 0, 4) . " "; // année
        $date = $date . substr($d, 11, 5);     // heures et minutes
        return $date;
    }

    /**
     * Transforme une chaine
     */
    public static function decToStringTime($decimal) {

        return self::formattedTime($decimal * 3600);
    }

    /**
     * Change le format d'une date FR -> UK
     * @param string $dateFr, format attendu : JJ/MM/AAAA
     * @return string format : AAAA/MM/JJ
     */
    public static function FrDateToUkDate($dateFr) {
        if (empty($dateFr)) {
            return null;
        } else {
            $temp = explode('/', $dateFr);
            return($temp[2] . '-' . $temp[1] . '-' . $temp[0]);
        }
    }

    /**
     * Retourne sous la forme " X h Y min" le nombre
     * d'heures et des minutes que represente une duree .
     * La valeur retournee est arrondie a la minute la plus proche.
     *
     * @param int $nbOfSeconds Une duree exprimee en secondes.
     * @return string Une duree au format 'xx h yy min'.
     */
    public static function formattedTime($nbOfSeconds) {

        if ($nbOfSeconds == 0) {
            return 0;
        }

        // Si la duree a formatter est negative,
        // on calcule sa valeur positive et on la retourne
        // precedee du signe - (moins).
        elseif ($nbOfSeconds < 0) {
            return "- " . self::formattedTime(- $nbOfSeconds);
        }

        $hours = floor($nbOfSeconds / 3600);
        $minutes = floor(($nbOfSeconds - $hours * 3600) / 60);
        $seconds = $nbOfSeconds - $hours * 3600 - $minutes * 60;

        if ($hours == 0 && $minutes == 0 && $seconds < 30) {
            return 0;
        }

        // Arrondissement a la minute la plus proche.
        $minutes += round($seconds / 60);

        $debut = $hours . " h ";
        $fin = $minutes . " min";
        if ($hours == 0) {
            $debut = "";
        }
        if ($minutes == 0) {
            $fin = "";
        }
        return $debut . $fin;
    }

    /**
     * exemple d'utilisation : 
     *  - entrée : array(array('id'=>'foo','libelle'=>'bar')))
     *  - sortie : array('foo' => 'bar')
     * @param array $complexArray de type array(array('id'=>'foo','libelle'=>'bar')))
     * @return array tableau simplifié de type array( id => libelle )
     */
    public static function simplifyArray($complexArray) {
        foreach ($complexArray as $array) {
            $simplifiedArray[$array['id']] = $array['libelle'];
        }
        return $simplifiedArray;
    }

    /**
     * camelize une chaine de caractères
     * @param string $str chaine de caractères quelconque
     * @return string de type camelCase
     */
    public static function strtocamel($str, $ucfirst = false) {
        $str = ucwords(strtolower($str));
        $str = $ucfirst ? ucfirst($str) : lcfirst($str);
        $a = array(' ', "&");
        $b = array('', "Et");
        return str_replace($a, $b, $str);
    }

    /**
     * Convertit les lettres accentuées d'une chaine par leur équivalent sans accent
     * @param string $str chaine avec accents
     * @return string chaine sans accent
     */
    public static function strSansAccent($str) {
        $recherche = array(' ', 'á', 'à', 'â', 'ã', 'ª', 'Á', 'À','Â', 'Ã', 
            'é', 'è', 'ê', 'ë', 'É', 'È', 'Ê', 'Ë', 
            'í', 'ì', 'î', 'ï', 'Í', 'Ì', 'Î', 'Ï',
            'ò', 'ó', 'ô', 'õ', 'ö', 'º', 'Ó', 'Ò', 'Ô', 'Õ', 'Ö', 
            'ú', 'ù', 'û', 'ü', 'Ú', 'Ù', 'Û', 'Ü', 
            'ç', 'Ç', 'Ñ', 'ñ');

        $substi = array('-', 'a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A', 
            'e', 'e', 'e', 'e', 'E', 'E', 'E', 'E',
            'i', 'i', 'i', 'i', 'I', 'I', 'I', 'I', 
            'o', 'o', 'o', 'o', 'o', 'o', 'O', 'O', 'O', 'O', 'O', 
            'u', 'u', 'u', 'u', 'U', 'U', 'U', 'U', 
            'c', 'C', 'N', 'n');

        return(str_replace($recherche, $substi, $str));
    }

    /**
     * Equivalent du find('list') mais extrait les informations d'un tableau d'éléments et non en base
     * @param array $elements tableau des données à utiliser pour constituer la liste
     * @param string $key nom de la clé de la liste
     * @param array $values nom des champs a utiliser comme valeur de la liste
     * @param string $format format pour la mise en forme des valeurs de la liste
     */
    public static function listFromArray($elements, $keyPath, $valuePaths, $format, $ordre = 'ASC') {
        // Initialisation
        $ret = array();
        foreach ($elements as $element) {
            // Extraction de la clé
            $key = Set::extract($element, $keyPath);
            // Si la clé existe déjà on passe au suivant
            if (isset($key[0])) {
                if (!array_key_exists($key[0], $ret)) {
                    // Extraction de la ou des valeurs
                    $values = array();
                    foreach ($valuePaths as $valuePath) {
                        $value = set::extract($element, $valuePath);
                        @$values[] = $value[0];
                    }
                    // Mise en forme
                    $ret[$key[0]] = vsprintf($format, $values);
                }
            }
        }
        // trie du tableau
        if ($ordre === 'ASC')
            asort($ret);
        elseif ($ordre === 'DESC')
            arsort($ret);
        elseif ($ordre === 'KEY_ASC')
            ksort($ret);
        elseif ($ordre === 'KEY_DESC')
            krsort($ret);

        return $ret;
    }

}

?>