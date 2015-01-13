<?php
/*
	Gestion des annexes : affichage, modification, suppression
	Paramètres :
		string	$mode = 'edit' : mode édition ('edit') ou affichage ('display'), dans ce dernier cas, les autres paramètres ne sont pas ovligatoires
		string	$ref : référence d'appartenance des annexes pour les nouvelles annexes (delibPrincipale, delibRattachee1, delibRattachee2, ...)
		array	$annexes = array() : liste des annexes a afficher
*/
// Initialisation des paramètres
if (empty($mode)) 
    $mode = 'edit';
if ($mode == 'edit' && empty($ref)) 
    return;
// affichage des annexes
$aTableOptions=array('caption'=>'Liste des annexes');

$aTitles=array(
    array('title' => 'Ordre'),
    array('title' => 'Nom du fichier'),
    array('title' => 'Titre'),
    array('title' => 'Joindre au contrôle de légalité'),
    array('title' => 'Joindre à la fusion')
);
if ($mode == 'edit') {
    $aTitles[]=array('title'=>'Actions'); 
    $aTableOptions['attributes']['id'] = 'tableAnnexes' . $ref;
}
if (empty($annexes)) {
        $annexes = array();
        $aTableOptions['attributes']['style'] = 'display:none;';
        $aTableOptions['class'] = 'bootstrap-table';
}
echo $this->Bs->table($aTitles, array('hover', 'striped'), $aTableOptions);
//Mettre dans le modèle
$aPosition=array();
for ($index = 0; $index < count($annexes); $index++) {
    $aPosition[$index+1]=$index+1;
}

foreach ($annexes as $key=>$annexe) {
    if ($mode == 'edit'){
    $aOptions=array(
        'id' => $mode.'Annexe' . $annexe['id'],
        'data-annexeid' => $annexe['id'], 
    );
    if (!empty($ref))
        $aOptions['data-ref'] = $ref;
        
    $this->Bs->lineAttributes($aOptions);
    
    $sPosition=$this->Html->tag('div', !empty($annexe['ordre'])?$annexe['ordre']:$key+1, array('class' => 'annexe-view'));
    $sPosition.=$this->Bs->div('annexe-edit', null, array('style' => 'display:none;'));
    $sPosition.=$this->BsForm->select('Deliberationseance.position', $aPosition, array(
            'value' => !empty($annexe['ordre'])?$annexe['ordre']:$key+1,
            'autocomplete'=>'off',
            'label'=>false,
            'class'=>'input-sm select2 selectone',
            'id'=>'modifieAnnexeOrdre'.$annexe['id'],
    ));
    $sPosition.= $this->Bs->close();
        
    echo $this->Bs->cell($sPosition);
    
    // lien de téléchargement de la version pdf de l'annexe
    $sAnnexeTitre=$this->Html->tag('div', $annexe['filename'], array('class' => 'annexefilename annexe-view'));
    $sAnnexeTitre.=$this->Bs->div('annexe-edit', null, array('style' => 'display:none;'));
    if (!empty($annexe['edit'])) {
        $sAnnexeTitre.=$this->Html->link('<i class="fa fa-pencil"></i> ' . $annexe['filename'], $annexe['link'], array(
            'id' => 'urlWebdavAnnexe' . $annexe['id'],
            'escape' => false,
            'title' => 'Modifier le fichier directement depuis votre poste en utilisant le protocol WebDAV'));
    } else {
        $sAnnexeTitre.=$this->Html->tag('span', $annexe['filename'],
            array(
                'id' => 'urlWebdavAnnexe' . $annexe['id'],
                'escape' => false,
                'title' => 'Edition WebDAV désactivée pour ce type de fichier'
            ));
    }
    $sAnnexeTitre.= $this->Bs->close();
    
    echo $this->Bs->cell($sAnnexeTitre);
    
    $sTitre=$this->Html->tag('div', $annexe['titre'], array(
            'class'=>'annexe-view',
            'id' => 'afficheAnnexeTitre' . $annexe['id'],
            'data-valeurinit' => $annexe['titre']));
    $sTitre.=$this->Bs->div('annexe-edit', null, array('style' => 'display:none;'));
    $this->BsForm->setLeft(0);
    $sTitre.=$this->BsForm->input('AnnexesAModifier.' . $annexe['id'] . '.titre', array(
            
            'id' => 'modifieAnnexeTitre' . $annexe['id'],
            'label' => false,
            'inline'=>true,
            'value' => $annexe['titre'],
            'maxlength' => '200',
            'disabled' => true,
            'class' => 'editAnnexeTitre',
        )).$this->Bs->close();
    $this->BsForm->setLeft(3);    
    echo $this->Bs->cell($sTitre);
    
    $sCtrlLegalite=$this->Html->tag('div', $annexe['joindre_ctrl_legalite'] ? 'Oui' : 'Non', array(
            'id' => 'afficheAnnexeCtrl' . $annexe['id'],
            'class'=>'annexe-view',
            'data-valeurinit' => $annexe['joindre_ctrl_legalite']));
    $sCtrlLegalite.=$this->Bs->div('annexe-edit', null, array('style' => 'display:none;'));
    $sCtrlLegalite.=$this->BsForm->checkbox('AnnexesAModifier.' . $annexe['id'] . '.joindre_ctrl_legalite', array(
            'id' => 'modifieAnnexeCtrl' . $annexe['id'],
            'label' => false,
            'inline'=>true,
            'checked' => ($annexe['joindre_ctrl_legalite'] == 1),
            'disabled' => 'disabled',
            
        )).$this->Bs->close();
        
        
    echo $this->Bs->cell($sCtrlLegalite);
    
    //$this->Html->tag('span', $annexe['joindre_fusion'] ? 'Oui' : 'Non')
    $sJoindreFusion = $this->Html->tag('div', $annexe['joindre_fusion'] ? 'Oui' : 'Non', array(
            'id' => 'afficheAnnexeFusion' . $annexe['id'],
            'class'=>'annexe-view',
            'data-valeurinit' => $annexe['joindre_fusion']));

    $sJoindreFusion.=$this->Bs->div('annexe-edit', null, array('style' => 'display:none;'));
    $sJoindreFusion .= $this->BsForm->checkbox('AnnexesAModifier.' . $annexe['id'] . '.joindre_fusion', array(
            'id' => 'modifieAnnexeFusion' . $annexe['id'],
            'label' => false,
            'inline'=>true,
            'checked' => ($annexe['joindre_fusion'] == 1),
            'disabled' => 'disabled',
        )).$this->Bs->close();
    
    echo $this->Bs->cell($sJoindreFusion);
    
    $sActions=$this->Bs->div('btn-group annexe-edit-btn');
    $sActions.=$this->Bs->btn('Télécharger', array('controller'=>'annexes','action'=>'download', $annexe['id']), array(
        'type'=>'default',
        'size' => 'sm',
        'icon'=>'glyphicon glyphicon-download',    
        'title' => 'Télécharger l\'annexe',
            'escape' => false,
            'id' => 'voirAnnexe' . $annexe['id'] . $ref,
        ));
        
    $sActions.=$this->Bs->btn('Modifier', 'javascript:void(0);', array(
        'type'=>'primary',
        'size' => 'sm',
        'icon'=>'fa fa-edit',    
        'title' => 'Modifier les caractéristiques de l\'annexe',
            'escape' => false,
            'id' => 'modifierAnnexe' . $annexe['id'] . $ref,
            'onclick' => 'modifierAnnexe(' . $annexe['id'] . ')'
        ));

    $sActions.= $this->Bs->btn('Supprimer', 'javascript:void(0);', array(
            'type'=>'danger',
            'size' => 'sm',
            'icon'=>'fa fa-trash-o',
            'title' => 'Supprimer cette annexe',
            'id' => 'supprimerAnnexe' . $annexe['id'] . $ref,
            'escape' => false,
            'onclick' => 'supprimerAnnexe(' . $annexe['id'] . ')'
        )).$this->Bs->close();
    
    $sActions.=$this->Bs->div('btn-group annexe-cancel-btn', null,array('style'=>'display:none;'));
    $sActions.= $this->Bs->btn(' Annuler', 'javascript:void(0);', array(
            'type'=>'warning',
            'size' => 'sm',
            'icon'=>'fa fa-undo',
            'title' => 'Annuler les modifications de l\'annexe',
            'escape' => false,
            'id' => 'annulerModifierAnnexe' . $annexe['id'] . $ref,
            'onclick' => "annulerModifierAnnexe(" . $annexe['id'] . ")"
        )).$this->Bs->close();

    $sActions.=$this->Bs->div('btn-group annexe-delete-btn', null,array('style'=>'display:none;'));
    $sActions.= $this->Bs->btn('Annuler', 'javascript:void(0);', array(
            'type'=>'warning',
            'size' => 'sm',
            'icon'=>'fa fa-undo',
            'title' => 'Annuler la suppression de l\'annexe',
            'escape' => false,
            'id' => 'annulerSupprimerAnnexe' . $annexe['id'] . $ref,
            'onclick' => "annulerSupprimerAnnexe(" . $annexe['id'] . ")"
        )).$this->Bs->close();
        
        echo $this->Bs->cell($sActions);
    }   
}
echo $this->Bs->endTable();

if ($mode != 'edit') return;
// div pour la suppression des annexes
echo $this->Html->tag('div', '', array('id' => 'supprimeAnnexes' . $ref, 'style' => 'display:none'));

// div pour l'ajout des annexes
echo $this->Html->tag('div', '', array('id' => 'ajouteAnnexes' . $ref, 'style' => 'display:none'));
echo $this->Html->tag(null, '<br />');

// lien pour ajouter une nouvelle annexes
echo $this->Bs->btn('Ajouter une annexe', 'javascript:void(0);', array(
    'type'=>'success',
    'icon'=>'glyphicon glyphicon-plus',
    'id' => 'annexeModalAddLink' . $ref,
    'class' => 'annexeModalAddLink',
    'data-ref' => $ref,
    'onclick' => 'afficherAnnexeModal(this);',
    'escape' => false
));