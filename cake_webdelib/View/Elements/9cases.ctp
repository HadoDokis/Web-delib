<?php
$etat_icon=array(
                'refuse'=>array(
                    'type'=>'danger',
                    'icon'=>'glyphicon  glyphicon glyphicon-eject'),
                'versionne'=>array(
                    'type'=>'info',
                    'icon'=>'glyphicon glyphicon-step-backward'),
                'encours'=>array(
                    'type'=>'default',
                    'icon'=>'glyphicon glyphicon-pause'),//glyphicon glyphicon-time
                'fini'=>array(
                    'type'=>'default',
                    'icon'=>'glyphicon glyphicon-lock'),
                'atraiter'=>array(
                    'type'=>'success',
                    'icon'=>'glyphicon glyphicon-play'),
                'attente'=>array(
                    'type'=>'warning',
                    'icon'=>'glyphicon glyphicon-pause'),
                'valide_editable'=>array(
                    'type'=>'success',
                    'icon'=>'glyphicon glyphicon-ok'),
            );

echo $this->Bs->table(array(array(
    'title' => (!empty($traitement_lot) ? $this->Form->checkbox(null, array('id'=> 'masterCheckbox','autocomplete'=>'off')):'' ).'État'),
    array('title' => 'Vue synthétique', array('width'=>'100%')),
    array('title' => 'Actions')
        ), array('hover', 'striped','bordered'));

if(!empty($projets))
foreach ($projets as $projet) {
    
    
    $Service_libelle='<b>Service émetteur :</b><br/>';
    if (isset($projet['Deliberation']['Service']['libelle']))
       $Service_libelle.=$projet['Deliberation']['Service']['libelle'];
    elseif (isset($projet['Service']['libelle']))
        $Service_libelle.=$projet['Service']['libelle'];
    
    $Deliberation_objet=$projet['Deliberation']['objet'];
    $Circuit_last_viseur = '<b>Dernière action de : </b><br/>'.$projet['last_viseur'];
    $Circuit_nom='<b>Circuit : </b><br/>'.$projet['Circuit']['nom'];
    if (isset($projet['last_viseur']) && !empty($projet['last_viseur']))
        $Circuit_nom.='<br/>Dernière action de : ' . $projet['last_viseur'];
        
    $Deliberation_titre=$projet['Deliberation']['titre'].'<br>';
    $Deliberation_date_limite='<b>A traiter avant le : </b>'.$projet['Deliberation']['date_limite'].'<br>';
    $this->BsForm->setLeft(0);
    $this->BsForm->setRight(10);
    $Seance_libelle='<b>Séance(s) :</b><br/>';
    if (in_array('attribuerSeance', $projet['Actions'])) {
        $Seance_libelle.=$this->BsForm->create('Deliberation', array('url' => array('controller' => 'deliberations', 'action' => 'attribuerSeance'), 'type' => 'post'));
        /*$case_seance.=$this->BsForm->input(
            , '<span class="fa fa-save"></span>'
                array('content'=>,
                'type' => 'button',
                'state' => 'success',
                //'id' => 'generer_multi_seance',
                'side'=>'right',
                
            )*/
        $Seance_libelle.=$this->BsForm->select('Deliberation.seance_id',$projet['Seances'],
            array(  'class'=>'select2multiple',
                   // 'before'=>false,
                   // 'after'=>false,
                   // 'type' => 'select',
                    'label'=>false,
                    'empty' => true,
                    'multiple' => true)
        );
        /*$Seance_libelle.=$this->BsForm->inputGroup(
            'Deliberation.seance_id', 
                array('content'=>'<span class="fa fa-save"></span>',
                'type' => 'button',
                'state' => 'success',
                //'id' => 'generer_multi_seance',
                'side'=>'right',
                
            ),
            array(  'class'=>'select2multiple pull-left',
                    'type' => 'select',
                    'label' => false,
                    'empty' => true,
                    'options' => $projet['Seances'],
                    'multiple' => true)
        );*/
        
        //<span class="fa fa-save"></span> 
        $Seance_libelle.= $this->Bs->btn('<span class="fa fa-save"></span> Sauvegarder', 
                array(), 
                array('type' => 'success', 'escapeTitle'=> false, 'escape' => false));
        $Seance_libelle.=$this->Form->hidden('Deliberation.id', array('value' => $projet['Deliberation']['id']));
       $Seance_libelle.= $this->BsForm->end();
    } else {
        foreach ($projet['listeSeances'] as $seance)
            $Seance_libelle.= $seance['libelle'] . (isset($seance['date']) && !empty($seance['date']) ? ' : ' . $this->Html2->ukToFrenchDateWithHour($seance['date']) : '') . '<br/>';
    }
    $Typeacte_name='';
    if (isset($projet['Typeacte']['name'])){
        $Typeacte_name = strtolower($projet['Typeacte']['name']);
    }
        $Theme_libelle = '<b>Thème : </b>';
        if (isset($projet['Theme']['libelle']))
                    $Theme_libelle.= $projet['Theme']['libelle'];
            
        $Deliberation_num_pref = '<b>Classification : </b>'.$projet['Deliberation']['num_pref'];
             
        $etat=$this->Bs->btn(null, null,
                        array(
                            'tag'=>'button',
                            'type'=>empty($projet['iconeEtat']['status'])?$etat_icon[$projet['iconeEtat']['image']]['type']:$projet['iconeEtat']['status'],
                            'size' => 'lg',
                            //'disabled'=>'disabled',
                            'data-toggle'=>'popover',
                            'data-content'=>$projet['iconeEtat']['image'],
                            'data-placement'=>'right',
                            'icon'=>$etat_icon[$projet['iconeEtat']['image']]['icon'],
                            'title' => $projet['iconeEtat']['titre']
                    )
                );

                $etat.= '<h4 class="text-center"><span class="label label-default" '.(!empty($projet['listeSeances'][0]['color'])?'style="background-color: '.$projet['listeSeances'][0]['color'].'"':'').'>'.$projet['Deliberation']['id'].'</span></h4>';
        
                if (!empty($traitement_lot)){
                    
                    $etat.='<br/>'.$this->Form->checkbox( 'Deliberation.check.id_' . $projet['Deliberation']['id'],
                                array('checked' => false, 'class'=>'checkbox_deliberation_generer', 'autocomplete'=>'off'));
                }
                
    /////////// BOUTTONS
    //$actions=$this->Bs->div('btn-toolbar',array('role'=>'toolbar'));
    $actions=$this->Bs->div('btn-group-vertical');
    if (in_array('view', $projet['Actions']))
            $actions.= $this->Bs->btn(null,array('controller' => 'deliberations', 'action' => 'view', $projet['Deliberation']['id']), array(
            'type'=>'default',
            'icon'=>'glyphicon glyphicon-eye-open large',
            'title' => 'Voir le projet ' . $projet['Deliberation']['objet'],
        ));

    if (in_array('edit', $projet['Actions']) && empty($projet['Deliberation']['signee']))
        $actions.= $this->Bs->btn(null,array('controller' => 'deliberations', 'action' => 'edit', $projet['Deliberation']['id']), array(
            'type'=>'primary',
            'icon'=>'glyphicon glyphicon-edit',
            'title' => 'Modifier le projet ' . $projet['Deliberation']['objet'],
        ));

     if (in_array('traiter', $projet['Actions'])){
        if(!empty($projet['iconeEtat']['status']))
        {
           if ($projet['iconeEtat']['status'] == 'warning') {
               echo $this->Bs->lineColor('warning');
           }
           if ($projet['iconeEtat']['status'] == 'danger') {
               echo $this->Bs->lineColor('danger');
           } 
        }

        $actions.=  $this->Bs->btn('',
        array('controller' => 'deliberations', 'action' => 'traiter', $projet['Deliberation']['id']),
        array(
            'type' => 'success',
            'icon'=>'glyphicon glyphicon-play',
            'title' => 'Traiter le projet ' . $projet['Deliberation']['objet']));
     }

    if (in_array('validerEnUrgence', $projet['Actions']))
        $actions.=  $this->Bs->btn('',
            array('controller' => 'deliberations', 'action' => 'validerEnUrgence', $projet['Deliberation']['id']),
            array(
                'type' => 'warning',
                'icon'=>'eject',
                'title' => 'Valider en urgence le projet ' . $projet['Deliberation']['objet']),
            'Confirmez-vous la validation en urgence du projet \'' . $projet['Deliberation']['id'] . '\'');

    if (in_array('goNext', $projet['Actions']))
        $actions.=  $this->Bs->btn('',
            array('controller' => 'deliberations', 'action' => 'goNext', $projet['Deliberation']['id']),
            array(
                'type' => 'warning',
                'icon'=>'fast-forward',
                'title' => 'Sauter une ou des étapes pour le projet ' . $projet['Deliberation']['id'],
                'escape' => false));
    
    if (in_array('attribuerCircuit', $projet['Actions']) && empty($projet['Deliberation']['signee'])) {
        $actionAttribuer = array('controller' => 'deliberations', 'action' => 'attribuercircuit', $projet['Deliberation']['id']);
        $actions.=  $this->Bs->btn('',
            $actionAttribuer,
            array('type' => 'primary',
                'icon'=>'glyphicon glyphicon-road',
                'escape' => false,
                'title' => 'Attribuer un circuit pour le projet ' . $projet['Deliberation']['id']),
            false);

    }
    if (in_array('delete', $projet['Actions']))
            $actions.= $this->Bs->btn(null,array('controller' => 'deliberations', 'action' => 'delete', $projet['Deliberation']['id']), array(
            'type'=>'danger',
            'icon'=>'glyphicon glyphicon-trash',
            'confirm'=>'Confirmez-vous la suppression du projet \'' . $projet['Deliberation']['objet'] . '\' ?',
            'title' => 'Supprimer le projet ' . $projet['Deliberation']['objet'],
        ));
    if (in_array('generer', $projet['Actions'])) {
                $actions.=$this->Bs->btn('Générer', 
                        array('controller' => 'deliberations', 'action' => 'genereFusionToClient', $projet['Deliberation']['id']), 
                        array('type' => 'default', 
                            'icon' => 'glyphicon glyphicon-cog', 
                            'class' => 'waiter',
                            'title' => 'Générer le document du projet ' . $projet['Deliberation']['objet']
                            ));
                /*$actions.= $this->Bs->btn('Générer <rerspan class="caret"></span>', 
                        array(), 
                        array('type' => 'default', 
                            'icon' => 'glyphicon glyphicon-cog', 
                            'escape'=>false,'class'=>'dropdown-toggle', 
                            'data-toggle'=>'dropdown')).
                $this->Bs->nestedList(array(
                $this->Bs->link('Pdf', array('controller' => 'deliberations', 'action' => 'genereFusionToClient', $projet['Deliberation']['id'] , 'projet'), 
                    array(
                            'title' => 'Générer le document PDF du projet ' . $projet['Deliberation']['objet'],
                            'class' => 'waiter',
                            'data-modal' => 'Génération du PV sommaire en cours')),
                $this->Bs->link('Odt', array('controller' => 'deliberations', 'action' => 'genereFusionToClient', $projet['Deliberation']['id'] , 'deliberation'), 
                    array(
                    'title' => 'Générer le document PDF du projet ' . $projet['Deliberation']['objet'],
                    'class' => 'waiter',
                    'data-modal' => 'Génération du PV complet en cours'))
                )
                , array('class'=>'dropdown-menu','role'=>'menu'));*/
        }
    if (in_array('telecharger', $projet['Actions'])) {
    {
        $this->Bs->btn('Télécharger', 
                array('controller' => 'deliberations', 'action' => 'downloadDelib', $projet['Deliberation']['id']), 
                array('type' => 'default', 
                    'icon' => 'glyphicon glyphicon-download', 
                    'title' => 'Visionner le document PDF du projet ' . $projet['Deliberation']['objet']
                    ));
    }
    
    $actions.= $this->Bs->close(1);
    }
    $actions.= $this->Bs->close(2);

    echo $this->Bs->cell($etat);
    
    //Creation du tableau a partir du JSON 9cases
    $i = 1;
    $contentFields = $this->Bs->row().$this->Bs->col('xs4');

    foreach($projet['fields'] as $key=>$field)
    {
        if(isset($field['model']) && $field['model']=='Infosupdef'){
            $content = $key.'h' .(!empty($field['nom'])?'<b>'.$field['nom'].'</b> : ':'');
            if (isset($field['code']) && isset($field['compacte'][$field['code']])) {
                if ($field['type'] == 'richText') {
                    $content .= $this->Html->link('[Afficher le texte]', 'javascript:afficheMasqueTexteEnrichi(\'afficheMasque' . $field['code'] . '\', \'' . $field['code'] . '\')', array(
                        'id' => 'afficheMasque' . $field['code'], 'affiche' => 'masque')) .
                            $this->Bs->div('annexesGauche') . $this->Bs->close() .
                            $this->Bs->div('spacer') . $this->Bs->close() .
                            $this->Bs->div('fckEditorProjet') .
                            $this->Form->input($field['compacte'][$field['code']], array('label' => '', 'type' => 'textarea', 'style' => 'display:none;', 'value' => $field['code']));
                            $this->Bs->close() . 
                            $this->Bs->div('spacer') . $this->Bs->close();
                } elseif ($field['type'] == 'listmulti') {
                    $content .= implode(', ', $field['compacte'][$field['code']]);
                } else {
                    $content .=$field['compacte'][$field['code']];
                }
            }
            $contentFields .= $this->Bs->col('xs4').$content.'<br>'.$this->Bs->close();
        } else {
            if(isset($field['model'])){
                $contentFields .= ${$field['model'].'_'.$field['fields']};
            }
        }
        if($i==3 || $i==6 || $i==9){
            $contentFields .= $this->Bs->close();
            if($i!=9) $contentFields .= $this->Bs->col('xs4');
        }
        $i++;
    }
    $contentFields .= $this->Bs->close(1);
    
    echo $this->Bs->cell($contentFields , 'max');   
    echo $this->Bs->cell($actions, 'text-right');
}
else {
   echo $this->Bs->cell(
           $this->Bs->tag('p',
                   $this->Bs->tag('span', '',array('class'=>'glyphicon glyphicon-remove'))
                   .' '.__('Aucun projet à afficher')), 'text-center', array('colspan'=> 3));
   
}
echo $this->Bs->endTable();


//if (isset($traitement_lot) && ($traitement_lot == true)) {
//    echo $this->html->tag('div', '', array('class' => 'spacer'));
//    $actions_possibles['generation'] = 'Génération';
//    echo $this->html->tag('fieldset', null, array('id' => 'generation-multiseance'));
//    echo $this->Form->input('Deliberation.action', array(
//        'options' => $actions_possibles,
//        'empty' => true,
//        'div' => array('class' => 'pull-left'),
//        'label' => false,
//        'after' => '<i class="fa fa-arrow-right" style="margin-left: 10px"></i>'
//    ));
//    echo $this->Form->input('Deliberation.modele', array(
//        'options' => $modeles,
//        'empty' => true,
//        'div' => array('id'=>'divmodeles', 'class' => 'pull-left', 'style' => 'display:none;margin-left: 10px'),
//        'label' => false,
//        'after' => '<i class="fa fa-arrow-right" style="margin-left: 10px"></i>'
//    ));
//    echo $this->Form->button('<i class="fa fa-cogs"></i> Executer<span id="nbDeliberationsChecked"></span>', array(
//        'type' => 'submit',
//        'class' => 'btn btn-primary pull-left',
//        'title' => "Executer",
//        'id' => 'generer_multi_delib',
//        'style' => 'margin-left: 10px'
//    ));
//    echo $this->html->tag('div', '', array('class' => 'spacer'));
//    echo $this->html->tag('/fieldset', null);
//}
//
//if (!empty($listeLiens)) {
//    echo '<div role="toolbar" class="btn-toolbar" style="text-align: center;"><div class="btn-group">';
//    if (in_array('add', $listeLiens)) {
//        echo $this->Html->link('<i class=" fa fa-plus"></i> Ajouter un projet',
//            array("action" => "add"),
//            array('class' => 'btn btn-primary',
//                'escape' => false,
//                'title' => 'Créer un nouveau projet',
//                'style' => 'margin-top: 10px;'));
//    }
//    if (in_array('mesProjetsRecherche', $listeLiens)) {
//        echo '<ul class="actions">';
//        echo '<li>' . $this->Html->link('Nouvelle recherche', '/deliberations/mesProjetsRecherche', array('class' => 'btn', 'escape' => false, 'alt' => 'Nouvelle recherche parmi mes projets', 'title' => 'Nouvelle recherche parmi mes projets')) . '</li>';
//        echo '</ul>';
//    }
//    if (in_array('tousLesProjetsRecherche', $listeLiens)) {
//        echo '<ul class="actions">';
//        echo '<li>' . $this->Html->link('Nouvelle recherche', '/deliberations/tousLesProjetsRecherche', array('class' => 'btn', 'escape' => false, 'alt' => 'Nouvelle recherche parmi tous les projets', 'title' => 'Nouvelle recherche parmi tous les projets')) . '</li>';
//        echo '</ul>';
//    }
//    echo "</div></div>";
//}
?>