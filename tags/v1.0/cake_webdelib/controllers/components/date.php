<?php
/*
 * Création : 20 janv. 2006
 * Christophe Espiau
 * 
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
class DateComponent extends Object {
	
	var $days = array ('Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi');
	
	var $months = array ('','Janvier','F�vrier','Mars','Avril','Mai','Juin',
						'Juillet','Ao�t','Septembre','Octobre','Novembre','D�cembre');
						
	function frenchDateConvocation($timestamp)
	{
		return $this->days[date('w',$timestamp)].' '.date('d',$timestamp)
				.' '.$this->months[date('n',$timestamp)].' '.date('Y',$timestamp).' � '.date('H',$timestamp).':'.date('i',$timestamp);
	}					

	function frenchDate($timestamp)
	{
		return $this->days[date('w',$timestamp)].' '.date('d',$timestamp)
				.' '.$this->months[date('n',$timestamp)].' '.date('Y',$timestamp);
	}	
 
       function frDate ($mysqlDate) {
                if (empty($mysqlDate))
	            return null;
		else {
		    $tmp =  explode(' ', $mysqlDate);
		    $temp = explode('-', $tmp[0]);
		    return($temp[2].'/'.$temp[1].'/'.$temp[0]);;
		}
 
	}
}

?>
