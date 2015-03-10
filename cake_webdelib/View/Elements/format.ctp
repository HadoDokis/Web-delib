<?php
echo $this->BsForm->create(null, array('url'=>array(
    'prefix'=> null,
    'controller'=>'users',
    'action'=>'changeformat'
)));
echo $this->BsForm->select('User.Sortie', 
        array(
            0 => __('Pdf'), 
            1 => __('Odt')), 
        array(  'selected' => AuthComponent::user('format.sortie'), 
                'empty'=> false,  
                'label'=> __('Format'),  
                'help'=>__('Choix du format de sortie des &eacute;ditions'),
                'onChange'=>'javascript: this.form.submit()'));
echo $this->BsForm->end();
