<div class='spacer'> </div>
<?php  

    echo $this->Form->create('Connecteur',array('url'=>'/connecteurs/makeconf/conversion', 'type'=>'file' )); 

?>
    <fieldset>
        <legend>Paramètrage de ODFGEDOOo</legend>
<?php  
        echo $this->Form->input('urlgedooo', 
                                array('type' => 'text', 
                                      "placeholder"=>"Exemple : http://gedooo.services.adullact.org:8880/ODFgedooo/OfficeService?wsdl", 
                                      'label' => 'WSDL de ODFGEDOOo : ' , 
                                      'value' => Configure::read('GEDOOO_WSDL')));
?>
    </fieldset>
    <fieldset>
        <legend>Paramètrage de CLOUDOOo</legend>
<?php
    echo $this->Form->input('urlcloudooo', array('type' => 'text', 
                                              "placeholder"=>"fourni avec votre certificat", 
                                              "placeholder"=>"Exemple :  http://cloudooo.services.adullact.org/",
                                              'label' => 'Adresse de CLOUDOOo',
                                              'value' => Configure::read('CLOUDOOO_HOST')));
?>
    <div class='spacer'> </div>
<?php
    echo $this->Form->input('portcloudooo',
                             array('type' => 'text',
                                   "placeholder"=>"Exemple : 8011",
                                   'label'  => false,
                                   'before' => '<label>Port de CLOUDOOo</label> ' ,
                                   'value' => Configure::read('CLOUDOOO_PORT'))); 
?>
    </fieldset>
    <div class='spacer'> </div>
<?php
    echo $this->Html2->boutonsSaveCancel('','/connecteurs/index');
    echo $this->Form->end();
?>
