<?php
echo $this->Bs->tag('h3', __('Choisir le format de sortie des éditions'));
echo $this->BsForm->create(null, array('url'=>array(
    'admin'=> false,
    'prefix'=> null,
    'controller'=>'users',
    'action'=>'changeFormatSortie'
)));
echo $this->BsForm->select('User.formatSortie', 
        array(
            0 => __('Pdf'), 
            1 => __('Odt')), 
        array(  
            'selected'=> $this->Html->value('User.formatSortie'),
            'autocomplete' => 'off',
            'empty'=> false,  
            'label'=> __('Format'),  
            'help'=>__('Choix du format de sortie des &eacute;ditions'),
            'onChange'=>'javascript: this.form.submit()'));
echo $this->BsForm->end();

