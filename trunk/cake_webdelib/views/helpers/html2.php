<?php
/*
 * Création : 13 févr. 2006
 * Christophe Espiau
 * 
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
class Html2Helper extends HtmlHelper
{
	/*
	 * Retourne deux élements SELECT pour la saisie des heures et des minutes.
	 * 
	 * Cette fonction est une surcharge de la fonction dateTimeOptionTag.
	 * Elle est faite :
	 * -  pour afficher seulement les heures et les minutes
	 * -  pour que les deux champs SELECT n'affichent rien s'il n'y a pas encore eu de pointage enregistré
	 * (alors que la fonction dateTimeOptionTag sélectionne l'heure courante par défaut).
	 * 
	 * @param string $tagName Prefix name for the SELECT element
	 * @param string $selected Option which is selected. Must be a timestamp. See also getTimestamp().
	 * @param array $optionAttr Attribute array for the option elements
	 * 
	 * @return string The HTML formatted SELECT elements
	 * 
	 */
	function timeOptionTag($tagName, $selected=null, $optionAttr=null)
	{
        $output = $this->dateTimeOptionTag($tagName, 'NONE', '24', $selected, $optionAttr);
	    
        if($selected == null)
		{
			return $this->selectBlankOption($output);
		}
		return $output;
	}
	
	/*
	 * 
	 */
	function dateOptionTag($tagName, $selected=null, $optionAttr=null)
	{
		$output = $this->dateTimeOptionTagFr($tagName, 'DMY', 'NONE', $selected, $optionAttr);
		if($selected == null)
		{
			return $this->selectBlankOption($output);
		}
		return $output;
	}
	

	
	/*
	 * 
	 */
	function selectBlankOption($output)
	{
		$output = str_replace(" selected=\"selected\"","",$output);
		$output = str_replace(" value=\"\""," value=\"\" selected=\"selected\"", $output);
		return $output;
	}
	
	/*
	 * Retourne un timestamp Unix à partir d'une date et d'une heure.
	 * 
	 * Un pointage est enregistré en base avec la date dans un champ,
	 * l'heure de début dans un autre et l'heure de fin dans un troisième.
	 * 
	 * Dans l'écran de pointage, pour pouvoir afficher les heure déjà enregistrées,
	 * il faut passer à la fonction timeOptionTag() (voir ci-dessus) le paramètre $selected
	 * sous forme de timestamp, qu'il faut créer à partir du champ date et du champ heureDebut
	 * (ou bien date et heureFin).
	 * 
	 * @param string $date Une date au format aaaa-mm-jj
	 * @param string $time Une heure au format hh:mm:ss
	 * 
	 * @return  int A Unix timestamp, or null if $time is null.
	 */
	function getTimestamp($date, $time)
	{
		// Test pour l'heure de fin.
		// Si celle-ci n'est pas encore enregistrée, elle est à NULL dans la base.
		// Et si $time = null, la fonction doit retourner null.
		if($time == null) return null;
		
		$amj = explode("-",$date);
		$hms = explode(":",$time);
		
		return  mktime($hms[0],$hms[1],$hms[2],$amj[1],$amj[2],$amj[0]);
      }

    function monthOptionTagFr($tagName, $value=null, $selected=null,  $selectAttr=null, $optionAttr=null, $showEmpty = true)
    {
        $value = isset($value)? $value : $this->tagValue($tagName."_month");
        $monthValue = empty($selected) ? null : $selected ;
        $months=array('01'=>'Janvier','02'=>'Fevrier','03'=>'Mars',
        '04'=>'Avril','05'=>'Mai','06'=>'Juin','07'=>'Juillet','08'=>'Aout',
        '09'=>'Septembre','10'=>'Octobre','11'=>'Novembre','12'=>'Decembre');
        $option = $this->selectTag($tagName.'_month', $months, $monthValue, $selectAttr, $optionAttr, $showEmpty);
        return $option;
    }

	
     function yearOptionTagFr($tagName, $value=null, $minYear=null, $maxYear=null, $selected=null, $selectAttr=null, $optionAttr=null, $showEmpty = true)
     {
         $yearValue = empty($selected) ? null : $selected;
         $currentYear = date('Y');
         $maxYear = is_null($maxYear) ? $currentYear + 11 : $maxYear + 1;
         $minYear = is_null($minYear) ? $currentYear - 42 : $minYear;
 
         if ($minYear > $maxYear)
         {
             $tmpYear = $minYear;
             $minYear = $maxYear;
             $maxYear = $tmpYear;
         }
         $minYear = $currentYear < $minYear ? $currentYear : $minYear;
         $maxYear = $currentYear > $maxYear ? $currentYear : $maxYear;
 
         for ($yearCounter = $minYear; $yearCounter < $maxYear; $yearCounter++)
         {
             $years[$yearCounter] = $yearCounter;
         }
         $option = $this->selectTag($tagName."_year", $years, $yearValue, $selectAttr, $optionAttr, $showEmpty);
         return $option;
     }
 	
 	function dateTimeOptionTagFr( $tagName, $dateFormat = 'DMY', $timeFormat = '12',$selected=null,  $selectAttr=null, $optionAttr=null, $showEmpty = false)
    {
        $day    = null;
        $month = null;
        $year  = null;
        $hour = null;
        $min    = null;
        $meridian = null;

	    if(!empty($selected))
        {
		    if(is_int($selected))
            {
                $selected = strftime('%Y-%m-%d  %H:%M',$selected);
            }
            $date = explode('-',$selected);
			$days  = explode(' ',$date[2]);

			$day    = $days[0];
            $month = $date[1];
            $year  = $date[0];

            if($timeFormat != 'NONE' && !empty($timeFormat))
            {
			    $time = explode(':',$days[2]);
                if(($time[0] > 12) && $timeFormat == '12')
                {
                    $time[0] = $time[0] - 12;
                    $meridian = 'pm';
                }
                elseif($time[0] > 12)
                {
                    $meridian = 'pm';
                }
                $hour  = $time[0];
                $min    = $time[1];
            }

        }

        switch ( $dateFormat )
        {
            case 'DMY' :
                $opt = $this->dayOptionTag( $tagName ,null ,$day, $selectAttr, $optionAttr, $showEmpty) . '-' . $this->monthOptionTagFr( $tagName, null, $month, $selectAttr, $optionAttr, $showEmpty) . '-' . $this->yearOptionTagFr( $tagName, null, null, null, $year, $selectAttr, $optionAttr, $showEmpty);
            break;
            case 'MDY' :
                $opt = $this->monthOptionTagFr($tagName, null, $month, $selectAttr, $optionAttr, $showEmpty) .'-'.$this->dayOptionTag( $tagName, null, $day, $selectAttr, $optionAttr, $showEmpty) . '-' . $this->yearOptionTagFr($tagName, null, null, null, $year, $optionAttr, $selectAttr, $showEmpty);
            break;
            case 'YMD' :
                $opt = $this->yearOptionTagFr($tagName, null, null, null, $year, $selectAttr, $optionAttr, $showEmpty) . '-' . $this->monthOptionTagFr( $tagName, null, $month, $selectAttr, $optionAttr, $showEmpty) . '-' . $this->dayOptionTag( $tagName, null, $day, $optionAttr, $selectAttr, $showEmpty);
            break;
            case 'NONE':
                $opt ='';
            break;
            default:
                $opt = '';
            break;
        }
        switch ($timeFormat)
        {
            case '24':
                $opt .= $this->hourOptionTag( $tagName, null , true,  $hour, $selectAttr, $optionAttr, $showEmpty) . ':' . $this->minuteOptionTag( $tagName, null, $min, $selectAttr, $optionAttr, $showEmpty);
            break;
            case '12':
                $opt .= $this->hourOptionTag( $tagName, null, false, $hour, $selectAttr, $optionAttr, $showEmpty) . ':' . $this->minuteOptionTag( $tagName, null, $min, $selectAttr, $optionAttr, $showEmpty) . ' ' . $this->meridianOptionTag($tagName, null, $meridian, $selectAttr, $optionAttr, $showEmpty);
            break;
            case 'NONE':
                $opt .='';
            break;
            default :
                $opt .='';
            break;
        }
        return $opt;
    }

	
	function ukToFrenchDate($date)
	{
		return date("d-m-Y",strtotime($date));	
	}
	

}
?>
