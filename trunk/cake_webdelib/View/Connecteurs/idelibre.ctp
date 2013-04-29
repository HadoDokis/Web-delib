<div class='spacer'> </div>
<?php  
    echo $this->Form->create('Connecteur',array('url'=>'/connecteurs/makeconf/idelibre')); 
 ?>
    <div class='spacer'> </div>
    <div id='affiche'>
    <fieldset>
        <legend>Paramètrage de Idélibre</legend>
<?php  
    echo $this->Form->input('idelibre_host', 
                             array('type' => 'text', 
                                   "placeholder"=>"Exemple : http://idelibre-server.ma-ville.fr", 
                                   'label' => 'Serveur de I-Délibre : ' , 
                                   'value' => Configure::read('IDELIBRE_HOST'))).'<br>';
?>
    </fieldset>
</div>
    <div class='spacer'> </div>
<?php
    echo $this->Html2->boutonsSaveCancel('','/connecteurs/index');
    echo $this->Form->end();
?>
