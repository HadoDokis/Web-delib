<?php
/**
 * @deprecated
 * @see Utility/DateFrench
 */

App::uses('DateFrench', 'Utility');

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
           return DateFrench::Hour($mysqlDate,$part);
       }

       function dateLettres ($timestamp) {
           return DateFrench::dateLettres($timestamp);
       }
}

?>
