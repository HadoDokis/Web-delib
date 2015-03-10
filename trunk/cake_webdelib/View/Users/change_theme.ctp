<?php
echo $this->Bs->tag('h3', 'Changement de thème');
echo $this->BsForm->create('User', array('url' => array('action' => 'changeTheme'), 'type' => 'post'));
echo $this->BsForm->select('User.theme',$themes, array(
    'label'=> 'Thème',
    'class'=>'selectone',
    'value' => $this->data['User']['theme'],
    'help'=> __('Choix du nouveau thème à appliquer')
        )
    );
echo $this->Html2->btnSaveCancel(null,$previous);
echo $this->BsForm->end();
