<?php

App::uses('WebUtils', 'Utility');

class UtilsComponent extends Component {

    /**
     * Fonction utile pour l'affichage
     */
    function mysql_DateTime($d) {
        return WebUtils::sqlDateTime($d);
    }

    /*
     * Création : 11 mai 2006
     * Christophe Espiau
     */

    /**
     * Transforme une chaine 
     */
    function decToStringTime($decimal) {
        return WebUtils::decToStringTime($decimal);
    }

    function FrDateToUkDate($dateFr) {
        return WebUtils::FrDateToUkDate($dateFr);
    }

    /**
     * Retourne sous la forme " X h Y min" le nombre 
     * d'heures et des minutes que represente une duree .
     * La valeur retournee est arrondie a la minute la plus proche.
     * @param int $nbOfSeconds Une duree exprimee en secondes.
     * @return string Une duree au format 'xx h yy min'.
     */
    function formattedTime($nbOfSeconds) {
        return WebUtils::formattedTime($nbOfSeconds);
    }

    function simplifyArray($complexArray) {
        return WebUtils::simplifyArray($complexArray);
    }

    function strtocamel($str) {
        return WebUtils::strtocamel($str);
    }

    function strSansAccent($str) {
        return WebUtils::strSansAccent($str);
    }

    /**
     * Equivalent du find('list') mais extrait les informations d'un tableau d'éléments et non en base
     * @param array $elements tableau des données à utiliser pour constituer la liste
     * @param string $key nom de la clé de la liste
     * @param array $values nom des champs a utiliser comme valeur de la liste
     * @param string $format format pour la mise en forme des valeurs de la liste
     */
    function listFromArray($elements, $keyPath, $valuePaths, $format, $ordre = 'ASC') {
        return WebUtils::listFromArray($elements, $keyPath, $valuePaths, $format, $ordre);
    }

}

?>
