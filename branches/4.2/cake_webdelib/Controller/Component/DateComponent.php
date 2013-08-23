<?php
/**
 * @deprecated
 * @see Utility/DateFrench
 */
class DateComponent extends Component {
    
	function DateComponent() {
            App::uses('DateFrench', 'Utility');
        }

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
