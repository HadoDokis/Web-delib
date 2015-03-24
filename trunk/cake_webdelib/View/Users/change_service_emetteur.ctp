<?php
echo $this->Bs->tag('h3', __('Changement du service émetteur'));
echo $this->BsForm->create(null, array('url'=>array(
    'admin'=> false,
    'prefix'=> null,
    'controller'=>'users',
    'action'=>'changeServiceEmetteur'
)));
echo $this->BsForm->select('User.ServiceEmetteur.id', $services,
        array(  
            'selected'=> $this->Html->value('User.ServiceEmetteur.id'),
            'autocomplete' => 'off',
            'empty'=> false,  
            'label'=> __('Format'),  
            'help'=>__('Choix du service d\'émissions des projets'),
            'onChange'=>'javascript: this.form.submit()'));
echo $this->BsForm->end();