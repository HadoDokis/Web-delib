<?php
class Seance extends AppModel {

    var $name = 'Seance';
    var $days = array ('Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi');
    var $months = array ('','Janvier','Février','Mars','Avril','Mai','Juin',
			 'Juillet','Août','Septembre','Octobre','Novembre','Décembre');
	
    var $validate = array(
        'type_id'      => array(array( 'rule'    => 'notEmpty',
                                       'message' => 'Entrer le type de seance associé.')),
        'avis'         => array(array( 'rule'    => 'notEmpty',
                                       'message' => 'Sélectionner un avis')),
        'date'         => array(array( 'rule'    => 'notEmpty',
                                       'message' => 'Entrer une date valide.')),
        'commission'   => array(array('rule'     => 'notEmpty',
                                       'message' => 'Entrer le texte de débat.')),
        'debat_global' => array(array( 'rule'    => 'notEmpty',
                                       'message' => 'Entrer le texte de débat.')));

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
        'foreignKey' => 'president_id'));
 
    var $hasMany = array(
        'Infosup'=>array('dependent' => true,
                         'foreignKey' => 'foreign_key',
                         'conditions' => array('Infosup.model' => 'Seance')));

    var $hasAndBelongsToMany = array('Deliberation');

    /* retourne la liste des séances futures avec le nom du type de séance  */
    function generateList($conditionSup=null, $afficherTtesLesSeances = false, $natures = array()) {
        $generateList = array();

        App::import('model','TypeseancesNature');
        $TypeseancesNature = new TypeseancesNature(); 
        $typeseances = $TypeseancesNature->getTypeseanceParNature($natures);

        $conditions  = array();
        $conditions['Seance.type_id'] = $typeseances;
        $conditions['Seance.traitee'] = '0';
            
        if (!empty($conditionSup))
            $conditions = Set::pushDiff($conditions,$conditionSup);

        $seances = $this->find('all',array('conditions' => $conditions, 
                                           'order'      => 'date ASC' ));

        foreach ($seances as $seance) {
            $deliberante = "----";
            if ($seance['Typeseance']['action'] == 0)
                $deliberante = "(*)";
            if ($afficherTtesLesSeances) {
                $dateTimeStamp = strtotime($seance['Seance']['date']);
                $dateFr =  $this->days[date('w', $dateTimeStamp)].' '.date('d', $dateTimeStamp).' '.
                           $this->months[date('n', $dateTimeStamp)].' '.date('Y',$dateTimeStamp).' - '.
                           date('H', $dateTimeStamp).':'.date('i', $dateTimeStamp );
            }
            else {
                $retard=$seance['Typeseance']['retard'];

                if($seance['Seance']['date'] >=date("Y-m-d", mktime(date("H"), date("i"), date("s"), date("m"), date("d")+$retard,  date("Y")))){
                    $dateTimeStamp = strtotime($seance['Seance']['date']);
                    $dateFr =  $this->days[date('w', $dateTimeStamp)].' '.date('d', $dateTimeStamp).' '.
                               $this->months[date('n', $dateTimeStamp)].' '.date('Y',$dateTimeStamp).' - '.
                               date('H', $dateTimeStamp).':'.date('i', $dateTimeStamp );
                }
            }
            $generateList[$seance['Seance']['id']]= "$deliberante ".$seance['Typeseance']['libelle']. " du ".$dateFr;
        }
        return $generateList;
    }

    function generateAllList() {
        $generateList = array();
        $seances = $this->findAll(null, null, 'date ASC');
        foreach ($seances as $seance){
            $deliberante = "";
            if ($seance['Typeseance']['action'] == 0)
                $deliberante = "(*)";

            $dateTimeStamp = strtotime($seance['Seance']['date']);
            $dateFr =  $this->days[date('w', $dateTimeStamp)].' '.date('d', $dateTimeStamp).' '.
                       $this->months[date('n', $dateTimeStamp)].' '.date('Y',$dateTimeStamp).' - '.
                       date('H', $dateTimeStamp).':'.date('i', $dateTimeStamp );
            $generateList[$seance['Seance']['id']]="$deliberante ". $seance['Typeseance']['libelle']. " du ".$dateFr;
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

    function getDeliberations($seance_id, $options = array()) {
        // initialisation des valeurs par défaut
        App::import('Model', 'Deliberationseance');
        $this->Deliberationseance = new Deliberationseance();

        $defaut = array(
            'order'     => array(),
            'conditions' => array());
	$options = array_merge($defaut, $options);

        $options['conditions']['Seance.id'] = $seance_id;
        $options['order'] = array('Deliberationseance.Position ASC');
        return ($this->Deliberationseance->find('all', $options));
    }

    function getLastPosition($seance_id) {
        App::import('Model', 'Deliberationseance');
        $this->Deliberationseance = new Deliberationseance();
        $deliberations = $this->Deliberationseance->find('count', array('conditions' => array('Seance.id' => $seance_id, 
                                                                                              'Deliberation.etat !='=> -1) ));
        return($deliberations+1);
    }


}
?>
