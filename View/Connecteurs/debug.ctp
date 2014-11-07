<div class='spacer'> </div>
<?php  

    echo $this->Form->create('Connecteur',array('url'=>'/connecteurs/makeconf/debug' )); 

?>
    <fieldset>
        <legend>Paramètrage du mode DEBUG</legend>
<?php  
    $notif = array(1 => 'debug', 0=>'production');
    echo $this->Form->input('debug', array('before'  => '<label>Mode du DEBUG</label>',
                                               'legend'  => false,
                                               'type'    => 'radio',
                                               'options' => $notif,
                                               'value' => Configure::read('debug'),
                                               'div'     => false,
                                               'default' => 0,
                                               'label'   => false));

?>
    </fieldset>
    <div class='spacer'> </div>
<?php
    echo $this->Html2->boutonsSaveCancel('','/connecteurs/index');
    echo $this->Form->end();
?>
