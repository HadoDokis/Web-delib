<?php
    $urlPage =  FULL_BASE_URL . $this->webroot;
    echo ("<br />Choix du format de sortie des &eacute;ditions : ");
    $format = $this->Session->read('user.format.sortie');
    echo $this->Form->select('User.Sortie', array (0=>'pdf', 1=>'odt'), array('id' => "$urlPage",'selected' => $format, 'empty'=> false,  'onChange'=>'changeFormat(this)'));
?>
