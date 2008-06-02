<?php
/*
 * CrÃ©ation : 20 janv. 2006
 * Christophe Espiau
 * 
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
class DateComponent extends Object {
	
	var $days = array ('Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi');
	
	var $months = array ('','Janvier','Février','Mars','Avril','Mai','Juin',
						'Juillet','Août','Septembre','Octobre','Novembre','Décembre');
						
	function frenchDateConvocation($timestamp)
	{
		return $this->days[date('w',$timestamp)].' '.date('d',$timestamp)
				.' '.$this->months[date('n',$timestamp)].' '.date('Y',$timestamp).' à '.date('H',$timestamp).':'.date('i',$timestamp);
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
