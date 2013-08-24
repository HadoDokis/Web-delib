<?php
/**
 * Code source de la classe UtilsComponent.
 *
 * PHP 5.3
 *
 * @package app.Controller.Component
 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
 */
App::uses('WebUtils', 'Utility');

/**
 * Classe UtilsComponent.
 *
 * @package app.Controller.Component
 * @deprecated Utiliser Utility/WebUtils
 */
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
        return WebUtils::frDateToUkDate($dateFr);
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
