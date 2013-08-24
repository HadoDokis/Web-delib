<?php
/**
 * Code source de la classe DateComponent.
 *
 * PHP 5.3
 *
 * @package app.Controller.Component
 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
 */
App::uses('DateFrench', 'Utility');

/**
 * Classe DateComponent.
 *
 * @package app.Controller.Component
 * @deprecated Utiliser Utility/DateFrench
 */
class DateComponent extends Component {

	function DateComponent() {}

	function frenchDateConvocation($timestamp)
	{
            return DateFrench::frenchDateConvocation($timestamp);
	}

	function frenchDate($timestamp)
	{
            return DateFrench::frenchDate($timestamp);
	}

       function frDate ($mysqlDate) {
           return DateFrench::frDate($mysqlDate);
       }

       function Hour ($mysqlDate, $part=null) {
           return DateFrench::hour($mysqlDate,$part);
       }

       function dateLettres ($timestamp) {
           return DateFrench::dateLettres($timestamp);
       }
}

?>
