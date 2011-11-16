<?php
    $urlPage =  FULL_BASE_URL . $this->webroot;
    echo ("<br />Choix du format de sortie des &eacute;ditions : ");
    $format = $session->read('user.format.sortie');
    echo $form->select('User.Sortie', array (0=>'pdf', 1=>'odt') , $format, array('id' => "$urlPage", 'onChange'=>'changeFormat(this)'),null,false);
?>
