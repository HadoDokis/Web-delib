<?php
class Deliberation extends AppModel {

	var $name = 'Deliberation';
	//dependent : pour les suppression en cascades. ici à false pour ne pas modifier le referentiel
	var $belongsTo=array('Service'=>array('className'=>'Service', 
											'conditions'=>'', 
											'order'=>'',
											'dependent'=>false, 
											'foreignKey'=>'service_id'),
						'Theme'=>array('className'=>'Theme', 
											'conditions'=>'', 
											'order'=>'',
											'dependent'=>false, 
											'foreignKey'=>'theme_id'),
						'Circuit'=>array('className'=>'Circuit', 
											'conditions'=>'', 
											'order'=>'',
											'dependent'=>false, 
											'foreignKey'=>'circuit_id'),
						'Redacteur' =>array('className'    => 'Agent',
                              'conditions'   => '',
                              'order'        => '',
                              'dependent'    =>  true,
                              'foreignKey'   => 'redacteur_id'
                        ),
                        'Rapporteur'=> array('className'    => 'Agent',
                              'conditions'   => '',
                              'order'        => '',
                              'dependent'    =>  true,
                              'foreignKey'   => 'rapporteur_id'),
                         'Seance'=> array('className'    => 'Seance',
                              'conditions'   => '',
                              'order'        => '',
                              'dependent'    =>  true,
                              'foreignKey'   => 'seance_id')     
						);
	var $hasMany = array ('Traitement' => array('className'=>'Traitement',
												'foreignKey' => 'delib_id'));
												
												
//												
//	var $hasOne = array('Redacteur' =>
//                        array('className'    => 'Agent',
//                              'conditions'   => '',
//                              'order'        => '',
//                              'dependent'    =>  true,
//                              'foreignKey'   => 'redacteur_id'
//                        ),
//                              'Rapporteur'=>
//                        array('className'    => 'Agent',
//                              'conditions'   => '',
//                              'order'        => '',
//                              'dependent'    =>  true,
//                              'foreignKey'   => 'rapporteur_id')
//                            
//                  );											
//										
											
//	var $hasAndBelongsToMany=array('Agent'=>array('classname'=>'Agent',
//													'joinTable'=>'agents_profils',
//													'foreignKey'=>'agent_id',
//													'associationForeignKey'=>'profil_id',
//													'conditions'=>'',
//													'order'=>'',
//													'limit'=>'',
//													'unique'=>true,
//													'finderQuery'=>'',
//													'deleteQuery'=>''));
	
	

}
?>