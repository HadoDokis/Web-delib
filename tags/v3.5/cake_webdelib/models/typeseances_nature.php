<?php
class TypeseancesNature extends AppModel {

    var $belongsTo = array('Nature', 'Typeseance');
    
    function getTypeseanceParNature($natures){
         $liste = array();
         $typeseances = $this->find('all', array('conditions'=>array('nature_id'=>$natures),
                                                                              'fields'    => 'DISTINCT(typeseance_id)',
                                                                              'recursive' => -1));
         foreach ($typeseances as $typeseance){
             $liste[]= $typeseance['TypeseancesNature']['typeseance_id'];
         }
         return $liste;         
    }
    
    function getNaturesParTypeseance($typeseance_id){
        $liste = array();
        $natures = $this->find('all', array('conditions'=>array('typeseance_id'=>$typeseance_id),
                                            'recursive' => -1));
        foreach($natures as $nature) 
              $liste[] = $nature['TypeseancesNature']['nature_id'];
   
        return($liste); 
    }
}
?>
