<?php
/*
	Gestion des annexes : affichage, modification, suppression
	Paramètres :
		string	$mode = 'edit' : mode édition ('edit') ou affichage ('display'), dans ce dernier cas, les autres paramètres ne sont pas ovligatoires
		string	$ref : référence d'appartenance des annexes pour les nouvelles annexes (delibPrincipale, delibRattachee1, delibRattachee2, ...)
		array	$annexes = array() : liste des annexes a afficher
*/
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

        echo $this->Bs->cell($sActions);
    }   
}
echo $this->Bs->endTable();