<?php
class TypeseancesTypeacte extends AppModel {

    var $name = 'TypeseancesTypeacte';

    var $belongsTo = array('Typeacte', 'Typeseance');
    
    function getTypeseanceParNature($natures){
         $liste = array();
         $typeseances = $this->find('all', array('conditions'=>array('typeacte_id'=>$natures),
                                                                     'fields'    => 'typeseance_id',
                                                                     'recursive' => -1));

         return (Set::extract('/TypeseancesTypeacte/typeseance_id', $typeseances));
    }
    
    function getNaturesParTypeseance($typeseance_id){
        $liste = array();
        $natures = $this->find('all', array('conditions'=>array('typeseance_id'=>$typeseance_id),
                                            'fields'    => array('typeacte_id'),
                                            'recursive' => -1));
        foreach($natures as $nature) 
              $liste[] = $nature['TypeseancesTypeacte']['typeacte_id'];
   
        return($liste); 
    }
}
?>
