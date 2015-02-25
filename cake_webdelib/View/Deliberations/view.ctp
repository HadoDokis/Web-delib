<?php
echo $this->Html->script('ckeditor/ckeditor');
echo $this->Html->script('ckeditor/adapters/jquery');

$this->Html->addCrumb('Validation d\'un projet');

echo $this->Bs->tag('h3', 'Validation d\'un projet');

// Initialisation des boutons action de la vue
$linkBarre = $this->Bs->div('btn-toolbar', null, array('role'=>'toolbar'));
$linkBarre .= $this->Bs->div('btn-group');//
$linkBarre .= $this->Html2->btnCancel($previous);
$linkBarre .= $this->Bs->btn('Générer', 
        array('controller' => 'deliberations', 'action' => 'genereFusionToClient', $projet['Deliberation']['id']), 
        array('type' => 'default', 
            'icon' => 'glyphicon glyphicon-cog', 
            'title' => 'Générer le document du projet ' . $projet['Deliberation']['objet']
            ));
if ($Droits->check($this->Session->read('user.User.id'), 'Deliberations:edit'))
    $linkBarre .= $this->Bs->btn('Modifier', 
            array('controller' => 'deliberations', 'action' => 'genereFusionToClient', $projet['Deliberation']['id']), 
            array('type' => 'primary', 
                'icon' => 'glyphicon glyphicon-edit', 
                'title' => 'Modifier le projet ' . $projet['Deliberation']['objet']
            ));
$linkBarre .= $this->Bs->close();
$linkBarre .= $this->Bs->div('btn-group');
$linkBarre .= $this->Bs->confirm('Commenter', 
        array('controller' => 'commentaires', 'action' => 'add', $projet['Deliberation']['id']), 
        array('type' => 'info', 
            'icon' => 'glyphicon glyphicon-comment', 
            'title' => 'Modifier le projet ' . $projet['Deliberation']['objet'],
            'texte' => $this->BsForm->create('Commentaire',array('url' => array('controller' => 'commentaires', 'action' => 'add', $projet['Deliberation']['id'])))
                . $this->Form->hidden('Commentaire.delib_id', array('value' => $projet['Deliberation']['id']))
                . $this->BsForm->input('Commentaire.texte', array('type' => 'textarea', 'label' => 'Commentaire', 'cols' => '3', 'cols' => '3'))
                . $this->BsForm->end()
                ,
                'header' => 'Nouveau commentaire (projet '.$projet['Deliberation']['id'].')',
                'style'=>array(
                    'border-bottom-right-radius'=> '4px',
                    'border-top-right-radius'=> '4px',
                )
            ), array('form'=>true));
$linkBarre .= $this->Bs->close();

$linkBarre .= $this->Bs->div('btn-group');
$linkBarre .= $this->Bs->btn('Retourner à', 
            array('controller' => 'deliberations', 'action' => 'retour', $projet['Deliberation']['id']), 
            array('type' => 'primary', 
                'icon' => 'glyphicon glyphicon-retweet', 
                'title' => 'Modifier le projet ' . $projet['Deliberation']['objet']
            ));
if ($Droits->check($this->Session->read('user.User.id'), 'Deliberations:rebond'))
$linkBarre .= $this->Bs->btn('Envoyer à', 
            array('controller' => 'deliberations', 'action' => 'rebond', $projet['Deliberation']['id']), 
            array('type' => 'primary', 
                'icon' => 'glyphicon glyphicon-share-alt', 
                'title' => 'Modifier le projet ' . $projet['Deliberation']['objet']
            ));        
$linkBarre .= $this->Bs->close();
$linkBarre .= $this->Bs->div('btn-group');
$linkBarre .= $this->Bs->confirm('Valider', 
            array('controller' => 'deliberations', 'action' => 'traiter', $projet['Deliberation']['id'], '1'), 
            array('type' => 'success', 
                'icon' => 'glyphicon glyphicon-thumbs-up', 
                'title' => 'Accepter le projet : ' . $projet['Deliberation']['objet'],
                'texte' => $this->BsForm->create('Commentaire')
                .'<p>Si vous ne souhaitez pas Valider le projet, Fermer la confirmation.</p>'
                .'<p>Voulez vous continuer ?</p>'
                .$this->BsForm->input('Commentaire.texte', array('type' => 'textarea', 'label' => 'Commentaire', 'cols' => '3', 'cols' => '3'))
                .$this->BsForm->end()
                ,
                'header' => 'Vous allez Valider ce projet !'
            ), array('form'=>true)); 
$linkBarre .= $this->Bs->confirm('Refuser', 
            array('controller' => 'deliberations', 'action' => 'traiter', $projet['Deliberation']['id'], '0'), 
            array('type' => 'danger', 
                'icon' => 'glyphicon glyphicon-thumbs-down', 
                'title' => 'Refuser le projet : ' . $projet['Deliberation']['objet'],
                'texte' => $this->BsForm->create('Commentaire')
                .$this->Html->tag('p','Si vous ne souhaitez pas refuser le projet, Fermer la confirmation.')
                .$this->Html->tag('p','Voulez vous continuer ?')
                .$this->BsForm->input('Commentaire.texte', array('type' => 'textarea', 'label' => 'Commentaire', 'cols' => '3', 'cols' => '3'))
                .$this->BsForm->end()
                ,
                'header' => 'Vous allez refuser ce projet !',
                'style'=>array(
                    'border-bottom-right-radius'=> '4px',
                    'border-top-right-radius'=> '4px',
                )
            ), array('form'=>true)); 
$linkBarre .= $this->Bs->close(2);

// affichage  du titre
$title = '<span class="label label-default">' . $projet['Typeacte']['libelle'] . '</span> ';
if (!empty($projet['Multidelib'])) {
    $listeIds = $projet['Deliberation']['id'];
    foreach ($projet['Multidelib'] as $delibRattachee) {
        $listeIds .= ', ' . $delibRattachee['id'];
    }
    $title .= 'Traitement du projet multi-délibérations n&deg;' . $projet['Deliberation']['id'] . ' : ' . $projet['Deliberation']['objet'];
} else {
    $title .= 'Traitement du projet n&deg;' . $projet['Deliberation']['id'] . ' : ' . $projet['Deliberation']['objet'];
}

echo $this->Bs->div('panel panel-default');
echo $this->Bs->div('panel-heading', $title);
echo $this->Bs->div('panel-body');     
//echo $this->Bs->tabPane($title);
echo $linkBarre;
echo $this->Bs->tag('br /');

$aTab=array(
    'infos' => 'Informations principales',
    'circuits' => 'Circuit(s)');

if (!empty($commentaires))
    $aTab['commentaires']='Commentaire(s)';
if (!empty($infosupdefs))
    $aTab['Infos_suppl']='Information(s) supplémentaire(s)';
if (!empty($historiques)) 
    $aTab['historiques']='Historique(s)';
if (!empty($projet['Multidelib']))
    $aTab['Multiprojet']='Multiprojet(s)';

echo $this->Bs->tab($aTab, array('active' => 'infos', 'class' => '-justified')) .
$this->Bs->tabContent();

echo $this->Bs->tabPane('infos', array('class' =>'active'));
echo $this->Bs->tag('br /');

echo $this->element('projetInfo', array('projet' => $projet));

if(!empty($tab_anterieure)){
    
    foreach ($tab_anterieure as $anterieure) {
        $sLis.=$this->Bs->tag('li', $this->Bs->tag('span', '4', array('class' => 'badge'))
                . $this->Html->href('Version du ' . $anterieure['date_version'], $anterieure['lien'])
                , array('class' => 'list-group-item list-group-item-danger'));
    }

    echo $this->Bs->row()
    .$this->Bs->col('xs6')
    .$this->Bs->tag('ul',  $sLis, array('class'=>'list-group'))
    .$this->Bs->close(2);
}

if (empty($projet['Multidelib']) && !empty($projet['Annex'])) {
    echo $this->element('annexe_view', array_merge(array('ref' => 'delibPrincipale'), array('annexes' => $projet['Annex'])));
}

echo $this->Bs->tabClose();

echo $this->Bs->tabPane('circuits');
echo $this->Bs->tag('h4', 'Circuit(s)');
echo $this->Bs->row().
        $this->Bs->col('xs4').'<b>Circuit :</b> '.$projet['Circuit']['libelle']
        .$this->Bs->close().
        $this->Bs->col('xs6').$visu
.$this->Bs->close(2);
echo $this->Bs->tabClose();


if (!empty($commentaires)) {
echo $this->Bs->tabPane('commentaires');
echo $this->Bs->tag('h4', 'Commentaire(s)');
    
    $sLis='';
    foreach ($commentaires as $commentaire) {
        /*echo $commentaire['Commentaire']['texte'] . ' ';
        
        if ($commentaire['Commentaire']['agent_id'] == $this->Session->read('user.User.id'))
            echo $this->Html->link('Supprimer', '/commentaires/delete/' . $commentaire['Commentaire']['id'] . '/' . $projet['Deliberation']['id']);
        else
            echo $this->Html->link('Prendre en compte', '/commentaires/prendreEnCompte/' . $commentaire['Commentaire']['id'] . '/' . $projet['Deliberation']['id']);
    */
$sLis.=$this->Html->link(
    $this->Bs->tag('h5',
        '<span class="label label-info">' . $commentaire['User']['prenom'] .' '. $commentaire['User']['nom'] . '</span> '
        .$this->Time->i18nFormat($commentaire['Commentaire']['created'], '%d/%m/%Y à %k:%M')   
        , array('class'=>'list-group-item-heading'))
        .$this->Bs->tag('p',$commentaire['Commentaire']['texte'], array('class'=>'list-group-item-text'))
    , '#', array('escape' => false, 'class' => 'list-group-item'));
    }
    echo $this->Bs->row()
    .$this->Bs->col('xs12')
    .$this->Bs->tag('div',  $sLis, array('class'=>'list-group'))
    .$this->Bs->close(2);
    
echo $this->Bs->tabClose();
}


if (!empty($infosupdefs)) {
    echo $this->Bs->tabPane('Infos_suppl');
    echo $this->Bs->tag('h4', 'Informations Supplémentaire(s)');
        echo '<dd><br>';
        foreach ($infosupdefs as $infosupdef) {
            echo $infosupdef['Infosupdef']['nom'] . ' : ';
            if (array_key_exists($infosupdef['Infosupdef']['code'], $this->data['Infosup'])) {
                if ($infosupdef['Infosupdef']['type'] == 'richText') {
                    if (!empty($this->data['Infosup'][$infosupdef['Infosupdef']['code']])) {
                        echo $this->Html->link('[Afficher le texte]', 'javascript:afficheMasqueTexteEnrichi(\'afficheMasque' . $infosupdef['Infosupdef']['code'] . '\', \'' . $infosupdef['Infosupdef']['code'] . '\')', array(
                            'id' => 'afficheMasque' . $infosupdef['Infosupdef']['code'], 'affiche' => 'masque'));
                        echo '<div class="annexesGauche"></div>';
                        echo '<div class="spacer"></div>';
                        echo '<div class="fckEditorProjet">';
                        echo $this->Form->input($infosupdef['Infosupdef']['code'], array('label' => '', 'type' => 'textarea', 'style' => 'display:none;', 'value' => $this->data['Infosup'][$infosupdef['Infosupdef']['code']]));
                        echo '</div>';
                        echo '<div class="spacer"></div>';
                    }
                } elseif ($infosupdef['Infosupdef']['type'] == 'listmulti') {
                    echo implode(', ', $this->data['Infosup'][$infosupdef['Infosupdef']['code']]);
                } else
                    echo $this->data['Infosup'][$infosupdef['Infosupdef']['code']];
            }
            echo '<br>';
        }
        echo '</dd>';
        echo $this->Bs->tabClose();
    }



if (!empty($historiques)) {
echo $this->Bs->tabPane('historiques');
echo $this->Bs->tag('h4', 'Historique(s)');
    echo $this->Bs->table(array(
            array('title' => 'Date'),
            array('title' => 'Utilisateur'),
            array('title' => 'Annotation / observation'),
        ), array('striped'));
    
    foreach ($historiques as $historique) {
        echo $this->Bs->cell($this->Time->i18nFormat($historique['Historique']['created'], '%d/%m/%Y %k:%M:%S'));
        echo $this->Bs->cell($historique['User']['prenom'] .' '. $historique['User']['nom']);
        echo $this->Bs->cell(nl2br($historique['Historique']['commentaire']));
        
    }
echo $this->Bs->endTable();
}

if (!empty($projet['Multidelib'])) {
echo $this->Bs->tabPane('Multiprojet(s)');
    echo $this->element('viewDelibRattachee', array(
        'delib' => $projet['Deliberation'],
        'annexes' => $projet['Annex'],
        'natureLibelle' => $projet['Typeacte']['libelle']));
    foreach ($projet['Multidelib'] as $delibRattachee) {
        echo $this->element('viewDelibRattachee', array(
            'delib' => $delibRattachee,
            'annexes' => $delibRattachee['Annex'],
            'natureLibelle' => $projet['Typeacte']['libelle']));
    }
echo $this->Bs->tabClose();
}
    
echo $this->Bs->tabPaneClose();
echo $this->Bs->tag('br /');
echo $this->Bs->tag('hr /');
echo $linkBarre.$this->Bs->close(2);
?>
<script type="text/javascript">
    function afficheMasqueTexteEnrichi(lienId, inputId) {
        var lienAfficherMasquer = $('#' + lienId);
        if (lienAfficherMasquer.attr('affiche') == 'masque') {
            var config = {
                readOnly: true,
                toolbar: 'Basic',
                toolbarStartupExpanded: false
            };
            $('#' + inputId).ckeditor(config);
            lienAfficherMasquer.attr('affiche', 'affiche');
            lienAfficherMasquer.html('[Masquer le texte]');
        } else {
            $('#' + inputId).ckeditor(function () {
                this.destroy();
            });
            $('#' + inputId).hide();
            lienAfficherMasquer.attr('affiche', 'masque');
            lienAfficherMasquer.html('[Afficher le texte]');
        }
    }

    $(document).ready(function () {
        <?php if ($majDeleg): ?>

        function afficheMAJ() {
            $("div.nomcourante").parent().append('<?php
    echo $this->Html->tag('div',
     $this->Html->link( $this->Html->tag('i', '', array('class' => 'fa fa-repeat')) . ' Mise à jour', array('controller'=>'deliberations','action'=>'MajEtatParapheur', $projet['Deliberation']['id']), array('escape' => false, 'class' => 'btn btn-inverse')),
     array('class' => 'majDeleg', 'title'=>'Mettre à jour le statut des étapes de délégations'));
    ?>')
        }

        afficheMAJ();
        <?php endif; ?>
        <?php
         if (isset($visas_retard) && !empty($visas_retard)):
                foreach ($visas_retard as $visa):
                ?>
        $('#etape_<?php echo $visa['Visa']['numero_traitement']; ?> .delegation').before('<?php
        echo $this->Html->link(
                $this->Html->tag('i', '', array('class' => 'fa fa-repeat')), array('plugin'=>'cakeflow', 'controller'=>'traitements','action'=>'traiterDelegationsPassees', $visa['Visa']['traitement_id'], $visa['Visa']['numero_traitement'], 'traiter'), array('escape' => false, 'style' => 'text-decoration:none;margin-right:5px;', 'title'=> 'Mettre à jour le statut de cette étape'));
        ?>');
        <?php
        endforeach;
    endif;
    ?>
    });
</script>