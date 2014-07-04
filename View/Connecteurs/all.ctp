<div class='spacer'> </div>
<?php  

    echo $this->Form->create('Connecteur',array('url'=>'/connecteurs/makeconf/all' )); 

?>
    <fieldset>
        <legend>Paramètrage du fichier webdelib.inc</legend>
<?php  
    $notif = array(1 => 'debug', 0=>'production');
    echo $this->Form->input('all', array('before'  => '<label>webdelib.inc</label>',
                                               'legend'  => false,
                                               'type'    => 'textarea',
                                               'div'     => false,
                                               'style'   => 'width: 100%',
                                               'rows'    => 50,
                                               'value'   => $content,
                                               'label'   => false));

?>
    </fieldset>
    <div class='spacer'> </div>
<?php
    echo $this->Form->submit('Configurer', array('class' => "btn btn-primary"));
    echo $this->Form->end();
?>
