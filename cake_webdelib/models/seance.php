<?php
class Seance extends AppModel {

    var $name = 'Seance';
    var $days = array ('Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi');
    var $months = array ('','Janvier','F�vrier','Mars','Avril','Mai','Juin',
			 'Juillet','Ao�t','Septembre','Octobre','Novembre','D�cembre');

    var	$cacheQueries = false;
	
    var $validate = array(
		'type_id' => array(
			array(
				'rule' => 'notEmpty',
				'message' => 'Entrer le type de seance associ�.'
			)
		),
		'avis' => array(
			array(
				'rule' => 'notEmpty',
				'message' => 'S�lectionner un avis'
			)
		),
		'date' => array(
			array(
				'rule' => 'notEmpty',
				'message' => 'Entrer une date valide.'
			)
		),
		'commission' => array(
			array(
				'rule' => 'notEmpty',
				'message' => 'Entrer le texte de d�bat.'
			)
		),
		'debat_global' => array(
			array(
				'rule' => 'notEmpty',
				'message' => 'Entrer le texte de d�bat.'
			)
		)
	);

    //var $displayField="libelle";

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
        'foreignKey' => 'secretaire_id'),
      'President' => array('className' => 'Acteur',
        'conditions' => '',
        'order' => '',
        'dependent' => false,
        'foreignKey' => 'president_id')
    );
 
    var $hasMany = array(
        'Infosup'=>array('dependent' => true,
                         'foreignKey' => 'foreign_key',
			 'conditions' => array('Infosup.model' => 'Seance')
                 )
        );



	/* retourne la liste des s�ances futures avec le nom du type de s�ance  */
	function generateList($conditionSup=null, $afficherTtesLesSeances = false, $natures = array()) {
		$generateList = array();
                $typeseances = $this->Typeseance->TypeseancesNature->getTypeseanceParNature($natures);
                $conditions  = array();
                $conditions['Seance.type_id'] = $typeseances;
		$conditions['Seance.traitee'] = '0';
                
		if (!empty($conditionSup))
			$conditions = Set::pushDiff($conditions,$conditionSup);

		$seances = $this->find('all',array(
                                       'conditions'=>$conditions, 
                                       'order'=>'date ASC'));

		foreach ($seances as $seance) {
			if ($afficherTtesLesSeances) {
				$dateTimeStamp = strtotime($seance['Seance']['date']);
				$dateFr =  $this->days[date('w', $dateTimeStamp)].' '.date('d', $dateTimeStamp).' '.$this->months[date('n', $dateTimeStamp)].' '.date('Y',$dateTimeStamp).' - '.date('H', $dateTimeStamp).':'.date('i', $dateTimeStamp );
				$generateList[$seance['Seance']['id']]= $seance['Typeseance']['libelle']. " du ".$dateFr;
			}
			else {
				$retard=$seance['Typeseance']['retard'];
				if($seance['Seance']['date'] >=date("Y-m-d", mktime(date("H"), date("i"), date("s"), date("m"), date("d")+$retard,  date("Y")))){
					$dateTimeStamp = strtotime($seance['Seance']['date']);
					$dateFr =  $this->days[date('w', $dateTimeStamp)].' '.date('d', $dateTimeStamp).' '.$this->months[date('n', $dateTimeStamp)].' '.date('Y',$dateTimeStamp).' - '.date('H', $dateTimeStamp).':'.date('i', $dateTimeStamp );
					$generateList[$seance['Seance']['id']]= $seance['Typeseance']['libelle']. " du ".$dateFr;
				}
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
  
    function NaturecanSave($seance_id, $nature_id) {
        if (empty($seance_id))
            return true;
        $seance = $this->read('type_id', $seance_id);
        $natures = $this->Typeseance->TypeseancesNature->getNaturesParTypeseance($seance['Seance']['type_id']);
        return in_array($nature_id, $natures); 
    }

}
?>
