<?php 
$this->Html->addCrumb(__('Liste des thèmes'), array('action'=>'index'));

$this->Html->addCrumb(__('Envoi d\'une notification'));

echo $this->Bs->tag('h3', __('Envoi d\'une notification aux utilisateurs du profil :').$libelle_profil);

echo $this->Html->script('ckeditor/ckeditor'); 

echo $this->BsForm->create('Profil', array('url' => array('controller' => 'profils', 'action' => 'notifier', $id)));
//echo $this->Bs->div('fckEditorProjet');
echo $this->BsForm->input('Profil.content', array(
    'label' => 'Message', 
    'type' => 'textarea'));
echo $this->Fck->load('Profil.content');
//echo $this->Bs->close(); 
echo $this->Bs->div('btn-group col-md-offset-' . $this->BsForm->getLeft(), null) ;
echo $this->Html2->btnCancel($previous);
echo $this->Bs->btn("Envoyer", array('action'=>'index'), 
        array('type' => "primary", 
            'icon'=>'glyphicon glyphicon-envelope',
            'escape' => false, 
            'title' => __('Envoyer le message aux utilisateurs de ce profil'),
            'confirm'=> __('Voulez-vous vraiment envoyer un mail vers les utilisateurs du profil ?')
        ));
echo $this->Bs->close(); 
echo $this->BsForm->end(); 