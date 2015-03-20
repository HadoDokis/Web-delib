<div class='spacer'> </div>
<?php  

    echo $this->BsForm->create('Connecteur',array('url'=>array('admin'=> true,'prefix'=>'admin', 'controller'=>'connecteurs', 'action'=>'makeconf', 'conversion'), 'type'=>'file' )); 

?>
    <fieldset>
        <legend>Paramètrage de ODFGEDOOo</legend>
<?php  
        echo $this->BsForm->input('gedooo_url', 
                                array('type' => 'text', 
                                      "placeholder"=>"Exemple : http://127.0.0.1:8880/ODFgedooo/OfficeService?wsdl", 
                                      'label' => 'WSDL de ODFGEDOOo : ' , 
                                      'value' => Configure::read('FusionConv.Gedooo.wsdl')));
?>
    </fieldset>
    <fieldset>
        <legend>Paramètrage de CLOUDOOo</legend>
<?php
    echo $this->BsForm->input('cloudooo_url', array('type' => 'text', 
                                              "placeholder"=>"fourni avec votre certificat", 
                                              "placeholder"=>"Exemple : 127.0.0.1",
                                              'label' => 'Adresse de CLOUDOOo :',
                                              'value' => Configure::read('FusionConv.cloudooo_host')));
?>
    <div class='spacer'> </div>
<?php
    echo $this->BsForm->input('cloudooo_port',
                             array('type' => 'text',
                                   "placeholder"=>"Exemple : 8011",
                                   'label'  => 'Port de CLOUDOOo :',
                                   'value' => Configure::read('FusionConv.cloudooo_port'))); 
?>
    </fieldset>
    <div class='spacer'> </div>
<?php
    echo $this->Html2->btnSaveCancel('',array('admin'=>true,'prefix'=>'admin','controller'=>'connecteurs', 'action'=>'index'));
    echo $this->Form->end();
?>
