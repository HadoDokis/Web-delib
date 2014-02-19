<?php
    $urlPage =  FULL_BASE_URL . $this->webroot;
    echo ("<br />Choix du service émetteur : ");
    $user = $this->Session->read('user');

    echo $this->Form->input('User.Service', array('id' => "$urlPage",
                                                 'options' =>  $user['Service'],
                                                 'selected' => $user['User']['service'],
                                                 'empty'=> false,  
                                                 'label'=> false,  
                                                 'onChange'=>'changeService(this)'));
?>
