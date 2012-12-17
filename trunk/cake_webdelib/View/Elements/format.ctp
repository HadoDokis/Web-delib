<?php
    $urlPage =  FULL_BASE_URL . $this->webroot;
    echo ("<br />Choix du format de sortie des &eacute;ditions : ");
    $format = $this->Session->read('user');
    $selected = @$format['format']['sortie'];

    echo $this->Form->input('User.Sortie', array('id' => "$urlPage",
                                                 'options' =>  array (0=>'pdf', 1=>'odt'), 
                                                 'selected' => $selected, 
                                                 'empty'=> false,  
                                                 'label'=> false,  
                                                 'onChange'=>'changeFormat(this)'));
?>
