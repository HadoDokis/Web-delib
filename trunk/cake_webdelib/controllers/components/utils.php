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
	 * d'heures et des minutes que represente une duree .
	 * La valeur retournee est arrondie a la minute la plus proche.
	 * 
	 * @param int $nbOfSeconds Une duree exprimee en secondes.
	 * 
	 * @return string Une duree au format 'xx h yy min'.
	 * 
	 */
	function formattedTime($nbOfSeconds) {
				
		if ($nbOfSeconds == 0)
		{
			return 0;
		}
		
		// Si la duree a formatter est negative,
		// on calcule sa valeur positive et on la retourne
		// precedee du signe - (moins).
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
		
		// Arrondissement a la minute la plus proche.
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
	
	function simplifyArray($complexArray)
    {
    	foreach($complexArray as $array)
    	{
    		$simplifiedArray[$array['id']] = $array['libelle'];

    	}
    	return $simplifiedArray;
    }	
	
    function strtocamel($str){
        $a = "&";
        $b = "Et";
        $str = str_replace ($a, $b, $str);       
        $str = explode(' ', strtolower($str));
        for($i = 1; $i < count($str); $i++)
            $str[$i] = strtoupper(substr($str[$i], 0, 1)) . substr($str[$i], 1);

        return implode('', $str);
    }
	
}
?>
