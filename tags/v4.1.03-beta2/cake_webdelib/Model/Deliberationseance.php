<?php
class Deliberationseance extends AppModel {
    var $name = 'Deliberationseance';
    var $useTable = 'deliberations_seances';
    var $belongsTo = array('Deliberation', 'Seance');
    
    
    /**
    * Suppression d'une seance par rapport à une délibération
    * @param int $delib_id id de délibération
    * @param int $seance_id id de séance
    * @version 4.1.03
    */
    function deleteDeliberationseance($delib_id, $seance_id)
    {
        //On récupère l'id de Deliberationseance à supprimer
        $deliberationseance = $this->find('first', array('conditions' => array( 'seance_id' => $seance_id,
                        'deliberation_id'      => $delib_id,
                        'Deliberation.etat !=' => -1),
                        'fields'     => array( 'Deliberationseance.id')));

        $this->delete($deliberationseance['Deliberationseance']['id']);
        
        $multiDelibs=$this->Deliberation->getMultidelibs($delib_id);     
        foreach($multiDelibs as $multiDelib_id) {
            $this->_deleteMultiDelib($multiDelib_id, $seance_id);
        }

        $this->_reOrdonne($seance_id);
    }
    /**
    * Suppression d'une seance par rapport à une délibération
    * @param int $delib_id Description
    * @param int $delib_id Description
    * @version 4.1.03
    */
    function _deleteMultiDelib($delib_id, $seance_id)
    {
        //On récupère l'id de Deliberationseance à supprimer
        $deliberationseance = $this->find('first', array('conditions' => array( 'seance_id' => $seance_id,
                        'deliberation_id'      => $delib_id,
                        'Deliberation.etat !=' => -1),
                        'fields'     => array( 'Deliberationseance.id')));

        $this->delete($deliberationseance['Deliberationseance']['id']);

        $this->_reOrdonne($seance_id);
    }
    
    
    /**
    * Ajout d'une seance par rapport à une délibération
    * @param int $delib_id id le l'acte
    * @param int $seance_id id de la séance
    * @version 4.1.03
    */
    function addDeliberationseance($delib_id, $seance_id)
    {
        //Fix Vérifier qu'il n'existe pas déjà avant de l'ajouter à une séance
        $deliberationseance['position'] = intval($this->_getLastPosition($seance_id));
        $deliberationseance['deliberation_id'] = $delib_id;
        $deliberationseance['seance_id'] = $seance_id;
        $this->create($deliberationseance);
        $this->save();

        $multiDelibs=$this->Deliberation->getMultidelibs($delib_id);     
        foreach($multiDelibs as $multiDelib_id) {
            $this->_addMultiDelib($seance_id,$delib_id,$multiDelib_id);
        }
        
        $this->_reOrdonne($seance_id);
    }
    
    /**
    * Ajout d'une seance par rapport à une délibération
    * @param int $delib_id id le l'acte
    * @param int $seance_id id de la séance
    * @version 4.1.03
    */
    function _addMultiDelib($seance_id,$parent_id, $delib_id)
    {
        $position=intval($this->_getLastPositionMultidelibByParent($seance_id, $parent_id));
        $position++;
        $this->_decaleMultiDelib($seance_id, $position);
        
        $deliberationseance['position'] = $position;
        $deliberationseance['deliberation_id'] = $delib_id;
        $deliberationseance['seance_id'] = $seance_id;
        $this->create($deliberationseance);
        $this->save();
    }
    
    /**
    * Retourne la position la plus haute d'une séance
    * @param int $seance_id id de la séance
    * @version string
    */
    function _getLastPosition($seance_id) {
        $deliberations = $this->find('first', array(
                                    'fields' => array('MAX (position) AS position'),
                                    'conditions' => array('Seance.id' => $seance_id,
                                    'Deliberation.etat !='=> -1) )
                                    );

        return($deliberations[0]['position']+1);
    }
    
    /**
    * Retourne la position la plus haute d'une délibération par raport à une séance et sa délibération parent 
    * @param int $seance_id id de la séance
    * @version 4.1.03
    * @return int Retourne la position du dernier enregistrement
    */
    function _getLastPositionMultidelibByParent($seance_id, $parent_id) {
        
        $deliberations = $this->find('first', array(
                                    'fields' => array('MAX (position) AS position'),
                                    'recursive' => -1,
                                    'joins'=>array(
                                                    array('table' => 'deliberations',
                                                        'alias' => 'Deliberation_parent',
                                                        'type' => 'inner',
                                                        'conditions' => array(
                                                            'Deliberation_parent.parent_id =  Deliberationseance.deliberation_id'
                                                        )
                                                    ),
                                                    array('table' => 'deliberations',
                                                        'alias' => 'Deliberation_fils',
                                                        'type' => 'inner',
                                                        'conditions' => array(
                                                            'Deliberation_fils.id = Deliberationseance.deliberation_id'
                                                        )
                                                    ),
                                                    ),
                                    'conditions' => array(  'seance_id' => $seance_id,
                                                            'Deliberationseance.deliberation_id'=>$parent_id)
                                    ));
        
        return($deliberations[0]['position']);
    }
    
    
    /**
    * Re-ordonne la séance passé en paramètre
    * @param int $seance_id id de la séance
    * @version 4.1.03
    */
    function _reOrdonne($seance_id)
    {
        //La position par default
        $position = 1;
        //Fix l'état est-il indipensable
        //On recherche toute les délibérations de la séance par ordre de classement
        $deliberations = $this->find('all', array('conditions' => array( 'Seance.id' => $seance_id,
                        'Deliberation.etat !=' => -1),
                        'fields'     => array( 'Deliberationseance.id',
                                                'Deliberationseance.deliberation_id',
                                                'Deliberationseance.position' ),
                        'order'      => array( 'Deliberationseance.position ASC' )));
       
        // Reclasser l'odre pour toutes les délibérations de la séance passé en paramètre
        foreach($deliberations as $delib) {
                if ($position != $delib['Deliberationseance']['position'])
                        $this->save(array( 'id'      => $delib['Deliberationseance']['id'],
                                'deliberation_id'      => $delib['Deliberationseance']['deliberation_id'],
                                'seance_id'      => $seance_id,
                                'position' => $position),
                                        array( 'validate' => false,
                                                        'callbacks' => false));
            $position++;
        }
    }
    
    /**
    * Re-ordonne les multidélibérations suite au modification de la mère
    * @param int $seance_id id de la séance
    * @version 4.1.03
    */
    function _decaleMultiDelib($seance_id, $pointeur)
    {
        //La position par default
        $position = 1;
        
        //On recherche toute les délibérations de la séance par ordre de classement
        $seances = $this->find('all', array('conditions' => array( 'Seance.id' => $seance_id,
                        'Deliberation.etat !=' => -1),
                        'fields'     => array( 'Deliberationseance.id',
                                                'Deliberationseance.deliberation_id',
                                                'Deliberationseance.position' ),
                        'order'      => array( 'Deliberationseance.position ASC' )));
       
        // Reclasser l'odre pour toutes les délibérations de la séance passé en paramètre
        foreach($seances as $delib) {
                if ($position != $delib['Deliberationseance']['position'] OR $position!=$pointeur)
                        $this->save(array( 'id'      => $delib['Deliberationseance']['id'],
                                'deliberation_id'      => $delib['Deliberationseance']['deliberation_id'],
                                'seance_id'      => $seance_id,
                                'position' => $position),
                                        array( 'validate' => false,
                                                        'callbacks' => false));
            $position++;
        }
    }                
    
}
?>
