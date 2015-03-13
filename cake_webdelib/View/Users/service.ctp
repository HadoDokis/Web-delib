<?php
echo $this->Bs->tag('h3', 'Changement du service émetteur');
echo $this->BsForm->create('User', array('url' => array('action' => 'changeTheme'), 'type' => 'post'));
echo $this->BsForm->select('User.Service', $user['Service'], array(
    'label'=> 'Thème',
    'class'=>'selectone',
    'value' => $user['User']['service'],
    'help'=> __('Choix du service émetteur')
        )
    );
echo $this->Html2->btnSaveCancel(null,$previous);
echo $this->BsForm->end();