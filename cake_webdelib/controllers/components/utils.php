<? 

class UtilsComponent extends Object
{
	/**
	 * Fonction utile pour l'affichage
	 */
	
	function mysql_DateTime($d) { 
		$date = substr($d,8,2)."/";        // jour 
  		$date = $date.substr($d,5,2)."/";  // mois 
  		$date = $date.substr($d,0,4). " "; // année 
  		$date = $date.substr($d,11,5);     // heures et minutes 
  		return $date; 
	} 
 
/*
 * Création : 11 mai 2006
 * Christophe Espiau
 * 
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
	/**
	 * Transforme une chaine 
	 */
	function decToStringTime($decimal)
	{
		
		return $this->formattedTime($decimal*3600);
		
	}
	
	
	function FrDateToUkDate($dateFr)
	{
		if (empty($dateFr))
		{
			return null;
		}
		else
		{
		    $temp = explode('/', $dateFr);
		    return($temp[2].'-'.$temp[1].'-'.$temp[0]);;
		}
	}
	
	/*
	 * Retourne sous la forme " X h Y min" le nombre 
	 * d'heures et des minutes que repr�sente une dur�e .
	 * La valeur retourn�e est arrondie � la minute la plus proche.
	 * 
	 * @param int $nbOfSeconds Une dur�e exprim�e en secondes.
	 * 
	 * @return string Une dur�e au format 'xx h yy min'.
	 * 
	 */
	function formattedTime($nbOfSeconds) {
				
		if ($nbOfSeconds == 0)
		{
			return 0;
		}
		
		// Si la dur�e � formatter est n�gative,
		// on calcule sa valeur positive et on la retourne
		// pr�c�d�e du signe - (moins).
		elseif ($nbOfSeconds < 0)
		{
			return "- ".$this->formattedTime(- $nbOfSeconds);
		}
				
		$hours = floor($nbOfSeconds/3600);
		$minutes = floor(($nbOfSeconds - $hours*3600)/60);
		$seconds = $nbOfSeconds - $hours*3600 - $minutes*60;
		
		if ($hours == 0 && $minutes == 0 && $seconds < 30)
		{
			return 0;
		}
		
		// Arrondissement � la minute la plus proche.
		$minutes += round($seconds/60);
		
		$debut = $hours." h ";
		$fin = $minutes." min";
		if($hours == 0)
		{
			$debut = "";
		}
		if($minutes == 0)
		{
			$fin = "";
		}
		return $debut.$fin;
	}
	
}
?>