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
                  .' '.$this->months[date('n',$timestamp)].' '.date('Y',$timestamp).' à '.date('H',$timestamp).' h '.date('i',$timestamp);
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
		    return($temp[2].'/'.$temp[1].'/'.$temp[0]);
		}
 
       }

       function Hour ($mysqlDate) {
                if (empty($mysqlDate))
	            return null;
		else {
		    $tmp =  explode(' ', $mysqlDate);
		    return(substr($tmp[1], 0, 5));
		}
 
       }



       function dateLettres ($timestamp)
       {
	    $jour    = $this->days[date('w',$timestamp)];    
	    $nbJour  = date('d',$timestamp);
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
	    $Mois    = $this->months[date('n',$timestamp)];
	    $nbAnnee = date('Y',$timestamp);
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
                    $nbAnnee= "deux mille douze";
                    break;
                case 2013:
                    $nbAnnee= "deux mille treize";
                    break;
                case 2014:
                    $nbAnnee= "deux mille quatorze";
                    break;
                case 2015:
                    $nbAnnee= "deux mille quinze";
                    break;
           }
	   return("L'an $nbAnnee le $nbJour $Mois ");
       }	       
}

?>
