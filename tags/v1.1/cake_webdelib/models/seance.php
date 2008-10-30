<?php
class Seance extends AppModel {

    var $name = 'Seance';
    var $days = array ('Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi');
    var $months = array ('','Janvier','Février','Mars','Avril','Mai','Juin',
			 'Juillet','Août','Septembre','Octobre','Novembre','Décembre');

    var	$cacheQueries = false;

    var $validate = array(
 	'type_id' => VALID_NOT_EMPTY,
    );

    var $displayField="libelle";

    var $belongsTo=array(
      'Typeseance'=>array(
	'className'=>'Typeseance',
	'conditions'=>'',
	'order'=>'',
	'dependent'=>false,
	'foreignKey'=>'type_id'),
      'Secretaire' => array('className' => 'Acteur',
        'conditions' => '',
        'order' => '',
        'dependent' => false,
        'foreignKey' => 'secretaire_id')
    );

     /* retourne la liste des séances futures avec le nom du type de séance  */
     function generateList() {
         $generateList = array();
         $conditions= 'Seance.traitee = 0';
         $seances = $this->findAll($conditions, null, 'date ASC');
	 foreach ($seances as $seance){
	     $retard=$seance['Typeseance']['retard'];
             if($seance['Seance']['date'] >=date("Y-m-d", mktime(date("H"), date("i"), date("s"), date("m"), date("d")+$retard,  date("Y")))){
	         $dateTimeStamp = strtotime($seance['Seance']['date']); 
	         $dateFr =  $this->days[date('w', $dateTimeStamp)].' '.date('d', $dateTimeStamp).' '.$this->months[date('n', $dateTimeStamp)].' '.date('Y',$dateTimeStamp).' - '.date('H', $dateTimeStamp).':'.date('i', $dateTimeStamp );
                 $generateList[$seance['Seance']['id']]= $seance['Typeseance']['libelle']. " du ".$dateFr;
             }
        } 
        return $generateList;
    }
    


    function generateAllList() {
         $generateList = array();
         $seances = $this->findAll(null, null, 'date ASC');
         foreach ($seances as $seance){
                 $dateTimeStamp = strtotime($seance['Seance']['date']);
                 $dateFr =  $this->days[date('w', $dateTimeStamp)].' '.date('d', $dateTimeStamp).' '.$this->months[date('n', $dateTimeStamp)].' '.date('Y',$dateTimeStamp).' - '.date('H', $dateTimeStamp).':'.date('i', $dateTimeStamp );
                 $generateList[$seance['Seance']['id']]= $seance['Typeseance']['libelle']. " du ".$dateFr;
        }
        return $generateList;
    }

}
?>
