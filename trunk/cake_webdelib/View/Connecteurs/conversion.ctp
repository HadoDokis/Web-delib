<div class='spacer'> </div>
<?php  

    echo $this->BsForm->create('Connecteur',array('url'=>'/connecteurs/makeconf/conversion', 'type'=>'file' )); 

?>
    <fieldset>
        <legend>Paramètrage de ODFGEDOOo</legend>
<?php  
        echo $this->BsForm->input('gedooo_url', 
                                array('type' => 'text', 
                                      "placeholder"=>"Exemple : http://127.0.0.1:8880/ODFgedooo/OfficeService?wsdl", 
                                      'label' => 'WSDL de ODFGEDOOo : ' , 
                                      'value' => Configure::read('GEDOOO_WSDL')));
?>
    </fieldset>
    <fieldset>
        <legend>Paramètrage de CLOUDOOo</legend>
<?php
    echo $this->BsForm->input('cloudooo_url', array('type' => 'text', 
                                              "placeholder"=>"fourni avec votre certificat", 
                                              "placeholder"=>"Exemple : 127.0.0.1",
                                              'label' => 'Adresse de CLOUDOOo :',
                                              'value' => Configure::read('CLOUDOOO_HOST')));
?>
    <div class='spacer'> </div>
<?php
    echo $this->BsForm->input('cloudooo_port',
                             array('type' => 'text',
                                   "placeholder"=>"Exemple : 8011",
                                   'label'  => 'Port de CLOUDOOo :',
                                   'value' => Configure::read('CLOUDOOO_PORT'))); 
?>
    </fieldset>
    <div class='spacer'> </div>
<?php
    echo $this->Html2->btnSaveCancel('','/connecteurs/index');
    echo $this->Form->end();
?>
