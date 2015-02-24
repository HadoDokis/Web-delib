<?php
if(!isset($this->request['render']) || $this->request['render']!='banette')
if ($this->params['filtre'] != 'hide') {
    $this->BsForm->setLeft(0);
    $this->Html->addCrumb(__('Séances à traiter'));
    echo $this->Html->tag('h3', __('Séances à traiter'));
    echo $this->Bs->container(array('class'=>'-fluid')).
        $this->Bs->row().
            $this->Bs->col('xs12').
    $this->BsForm->create('Seance', array('url' => array('controller' => 'seances', 'action' => 'genereFusionMultiSeancesToClient'), 'class' => 'waiter form-inliney'));
  
} 

if(isset($this->request['render']) && $this->request['render']=='banette'){
    echo $this->Bs->div('panel panel-default');
echo $this->Bs->div('panel-heading', __('Séances à traiter').
        '<span style="float: right">'.$this->Html->link(__('Voir toutes les seances à traiter'), 
                array('controller'=>$this->request['controller'],'action'=>$this->request['action'])
                ).'</span>'
        );
}
?>
<div class="table-responsive">
<table class='table table-striped table-middle table-bordered table-hover'>
<thead>
<tr>
    <?php if (!isset($this->request['render']) || $this->request['render']!='banette') echo("<th style='width:2px;'><input type='checkbox' id='masterCheckbox' /></th>"); ?>
    <th>Type</th>
    <th style="width: 15em;">Date de la séance</th>
    <th style="width: 15em;">Préparation</th>
    <th style="width: 10em;" >En cours</th>
    <th style="width: 10em;">Finalisation</th>
    <?php if (!$endDiv) : ?>
    <th style="width: 8em;">Actions
    </th> <?php endif; ?>
</tr>
</thead>
<tbody>
<?php
    if(!empty($seances))
    foreach ($seances as $seance) : ?>
    <tr>
    <?php
    if (!isset($this->request['render']) || $this->request['render']!='banette')
        echo("<td class='text-center table-middle'>"
            . $this->Form->checkbox(
                'Seance.id_' . $seance['Seance']['id'],
                array('checked' => false, 'class' => 'checkbox_seance_generer'))
            . "</td>");
    $attributes=!empty($seance['Typeseance']['color'])?array('style'=>'color: '.$seance['Typeseance']['color']):array();
    ?>
        <td><strong><?php echo $this->Bs->icon('tag', array('lg'), $attributes);?> <?php echo $seance['Typeseance']['libelle']; ?></strong></td>
    <?php echo $this->Html->tag('td', $seance['Seance']['date']); ?>
    <td class="actions" style="text-align:center;vertical-align: middle">

        <!--
        <div class="btn-group">
            <button class="btn dropdown-toggle" data-toggle="dropdown" href="#">
                Préparation de séance
                <span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
                <?php
        echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-list"></i> Afficher les projets',
            array('controller' => 'seances', 'action' => 'afficherProjets', $seance['Seance']['id']),
            array(
                'title' => 'Voir l\'ordre des projets de la séance du ' . $seance['Seance']['date'],
                'escape' => false
            )));
        if (Configure::read('AFFICHE_CONVOCS_ANONYME'))
            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-file-text-o"></i> Générer l\'apercu d\'une convocation',
                array('action' => 'genereFusionToClient', $seance['Seance']['id'], 'convocation'),
                array(
                    'class' => 'waiter',
                    'title' => "Générer l'apercu d'une convocation pour la séance du " . $seance['Seance']['date'],
                    'escape' => false,
                )));

        echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-file-text-o"></i> Générer la liste des convocations',
            array('controller' => 'seances', 'action' => 'sendConvocations', $seance['Seance']['id'], $seance['Typeseance']['modelconvocation_id']),
            array(
                'title' => 'Générer la liste des convocations pour la séance du ' . $seance['Seance']['date'],
                'escape' => false,
            )));
        if (Configure::read('AFFICHE_CONVOCS_ANONYME'))
            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-file-text-o"></i> Générer l\'aperçu de l\'ordre jour',
                array('action' => 'genereFusionToClient', $seance['Seance']['id'], 'ordredujour'),
                array(
                    'class' => 'waiter',
                    'escape' => false,
                    'title' => "Aperçu de l'ordre jour pour la séance du " . $seance['Seance']['date'],
                )));
        echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-file-text-o"></i> Générer l\'ordre jour détaillé',
            array('controller' => 'seances', 'action' => 'sendOrdredujour', $seance['Seance']['id'],
                $seance['Typeseance']['modelordredujour_id']),
            array(
                'title' => 'Générer l\'ordre du jour détaillé pour la séance du ' . $seance['Seance']['date'],
                'escape' => false,
            )));
        if (Configure::read('USE_IDELIBRE'))
            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-tablet"></i> Envoyer à Idélibre',
                array('controller' => 'seances', 'action' => 'sendToIdelibre', $seance['Seance']['id']),
                array(
                    'title' => 'Envoyer à Idelibre la séance du ' . $seance['Seance']['date'],
                    'escape' => false
                )));
        ?>
            </ul>
        </div>-->
        <?php
        echo $this->Html->link(' ',
            array('controller' => 'seances', 'action' => 'afficherProjets', $seance['Seance']['id']),
            array('class' => 'link_classer_odj',
                'title' => 'Voir l\'ordre des projets de la séance du ' . $seance['Seance']['date'],
                'escape' => false
            ));
        if (Configure::read('AFFICHE_CONVOCS_ANONYME'))
            echo $this->Html->link(' ',
                array('action' => 'genereFusionToClient', $seance['Seance']['id'], 'convocation'),
                array(
                    'class' => 'waiter link_convocation_unique',
                    'title' => "Générer l'apercu d'une convocation pour la séance du " . $seance['Seance']['date'],
                    'escape' => false,
                ));
        echo $this->Html->link(' ',
            array('controller' => 'seances', 'action' => 'sendConvocations', $seance['Seance']['id'], $seance['Typeseance']['modelconvocation_id']),
            array(
                'class' => 'link_convocation',
                'title' => 'Générer la liste des convocations pour la séance du ' . $seance['Seance']['date'],
                'escape' => false,
            ));
        if (Configure::read('AFFICHE_CONVOCS_ANONYME'))
            echo $this->Html->link(' ',
                array('action' => 'genereFusionToClient', $seance['Seance']['id'], 'ordredujour'),
                array(
                    'class' => 'waiter link_ordre_jour_unique',
                    'escape' => false,
                    'title' => "Aperçu de l'ordre jour pour la séance du " . $seance['Seance']['date'],
                ));
        echo $this->Html->link(' ',
            array('controller' => 'seances', 'action' => 'sendOrdredujour', $seance['Seance']['id'], $seance['Typeseance']['modelordredujour_id']),
            array(
                'class' => 'link_ordre_jour',
                'title' => 'Générer l\'ordre du jour détaillé pour la séance du ' . $seance['Seance']['date'],
                'escape' => false,
            ));
        if (empty($seance['Seance']['idelibre_id']))
            echo $this->Html->link(' ',
                array('controller' => 'seances', 'action' => 'sendToIdelibre', $seance['Seance']['id']),
                array(
                    'class' => 'link_tablet',
                    'title' => 'Envoyer à Idelibre la séance du ' . $seance['Seance']['date'],
                    'escape' => false,
                ));
        else
            echo $this->Html->link(' ',
            array('controller' => 'seances', 'action' => 'sendToIdelibre', $seance['Seance']['id']),
            array(
                'class' => 'link_tablet',
                'title' => 'Séance déjà envoyée à i-delibRE',
                'onclick' => "return confirm('Cette séance a déjà été envoyé à i-delibRE !\\n\\nSi vous souhaitez la renvoyer de nouveau,\\nAssurez vous au préalable qu\'elle n\'est plus dans i-delibRE.\\n\\nVoulez vous continuer ?')",
                'escape' => false,
            ));
        ?>
    </td>
    <td class="actions">
        <!--        <div class="btn-group">
            <button class="btn dropdown-toggle" data-toggle="dropdown" href="#">
                Séance en cours
                <span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
                <?php
        echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-users"></i> Sélection président/secrétaire',
            array('action' => 'saisirSecretaire', $seance['Seance']['id']),
            array(
                'title' => 'Choix du président et du secrétaire de la séance du ' . $seance['Seance']['date'],
                'escape' => false,
            )));
        echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-pencil"></i> Saisir les débats généraux',
            array('action' => 'saisirDebatGlobal', $seance['Seance']['id']),
            array(
                'title' => 'Saisir les débats généraux de la séance du ' . $seance['Seance']['date'],
                'escape' => false,
            )));

        if ($seance['Typeseance']['action'] == 0)
            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-hand-o-up"></i> Voter',
                array('action' => 'details', $seance['Seance']['id']),
                array(
                    'escape' => false,
                    'title' => 'Afficher les projets et voter pour la séance du ' . $seance['Seance']['date']
                )));
        elseif ($seance['Typeseance']['action'] == 1)
            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-comment"></i> Donner un avis',
                array('action' => 'detailsAvis', $seance['Seance']['id']),
                array(
                    'title' => 'Afficher les projets et donner un avis pour la séance du ' . $seance['Seance']['date'],
                    'escape' => false,
                )));
        elseif ($seance['Typeseance']['action'] == 2)
            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-info"></i> Afficher les projets',
                array('action' => 'details', $seance['Seance']['id']),
                array(
                    'escape' => false,
                    'title' => 'Afficher les projets pour la séance du ' . $seance['Seance']['date']
                )));

        echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-comment"></i> Saisir un commentaire',
            array('action' => 'saisirCommentaire', $seance['Seance']['id']),
            array(
                'title' => 'Saisir un commentaire pour la séance du ' . $seance['Seance']['date'],
                'escape' => false
            )));
        ?>
            </ul>
        </div>-->
        <?php
        echo $this->Html->link(' ',
            array('action' => 'saisirSecretaire', $seance['Seance']['id']),
            array(
                'class' => 'link_secretaire',
                'title' => 'Choix du président et du secrétaire de la séance du ' . $seance['Seance']['date'],
                'escape' => false,
            ));
        echo $this->Html->link(' ',
            array('action' => 'saisirDebatGlobal', $seance['Seance']['id']),
            array(
                'class' => 'link_debat',
                'title' => 'Saisir les débats généraux de la séance du ' . $seance['Seance']['date'],
                'escape' => false,
            ));

        if ($seance['Typeseance']['action'] == 0)
            echo $this->Html->link(' ',
                array('action' => 'details', $seance['Seance']['id']),
                array(
                    'class' => 'link_voter',
                    'escape' => false,
                    'title' => 'Afficher les projets et voter pour la séance du ' . $seance['Seance']['date']
                ), false);

        elseif ($seance['Typeseance']['action'] == 1)
            echo $this->Html->link(' ',
                array('action' => 'detailsAvis', $seance['Seance']['id']),
                array(
                    'class' => 'link_donnerAvis',
                    'title' => 'Afficher les projets et donner un avis pour la séance du ' . $seance['Seance']['date'],
                    'escape' => false,
                ));
        elseif ($seance['Typeseance']['action'] == 2)
            echo $this->Html->link(' ',
                array('action' => 'details', $seance['Seance']['id']),
                array(
                    'class' => 'link_actes',
                    'escape' => false,
                    'title' => 'Afficher les projets pour la séance du ' . $seance['Seance']['date']
                ));

        echo $this->Html->link(' ',
            array('action' => 'saisirCommentaire', $seance['Seance']['id']),
            array(
                'class' => 'link_commentaire_seance',
                'title' => 'Saisir un commentaire pour la séance du ' . $seance['Seance']['date'],
                'escape' => false
            ));
        ?>
    </td>
    <td class="actions">
        <?php
        if ($canSign) {
            echo $this->Html->link(' ',
                array('controller' => 'deliberations', 'action' => 'sendToParapheur', $seance['Seance']['id']),
                array(
                    'class' => 'link_signer',
                    'title' => 'Envoi des actes à la signature pour la séance du ' . $seance['Seance']['date'],
                    'escape' => false
                ));
        }
        echo $this->Html->link(' ',
            array('action' => 'genereFusionToClient', $seance['Seance']['id'], 'pvsommaire'),
            array(
                'class' => 'link_pvsommaire waiter',
                'title' => 'Génération du pv sommaire pour la séance du ' . $seance['Seance']['date'],
                'escape' => false,
            ));
        echo $this->Html->link(' ',
            array('action' => 'genereFusionToClient', $seance['Seance']['id'], 'pvdetaille'),
            array(
                'class' => 'link_pvcomplet waiter',
                'escape' => false,
                'title' => 'Génération du pv complet pour la séance du ' . $seance['Seance']['date'],
            ));
        echo $this->Html->link(' ',
            array('action' => 'clore', $seance['Seance']['id']),
            array(
                'class' => 'link_clore_seance',
                'title' => 'Clôture de la séance du ' . $seance['Seance']['date'],
                'escape' => false,
            ), 'Confirmer la cloture de la séance ?');
        ?>
        <!--
<div class="btn-group">
            <button class="btn dropdown-toggle" data-toggle="dropdown" href="#">
                Actions
                <span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
                <?php
        if ($canSign) {
            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-certificate"></i> Envoyer au parapheur',
                array('controller' => 'deliberations', 'action' => 'sendToParapheur', $seance['Seance']['id']),
                array(
                    'title' => 'Envoi des actes à la signature pour la séance du ' . $seance['Seance']['date'],
                    'escape' => false
                )));
        }
        echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-file-text-o"></i> Générer PV sommaire',
            array('action' => 'genereFusionToClient', $seance['Seance']['id'], 'pvsommaire'),
            array(
                'class' => 'waiter',
                'title' => 'Génération du pv sommaire pour la séance du ' . $seance['Seance']['date'],
                'escape' => false,
            )));
        echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-file-text-o"></i> Générer PV complet',
            array('action' => 'genereFusionToClient', $seance['Seance']['id'], 'pvdetaille'),
            array(
                'class' => 'waiter',
                'escape' => false,
                'title' => 'Génération du pv complet pour la séance du ' . $seance['Seance']['date'],
            )));
            echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-times"></i> Clore la séance',
            array('action' => 'clore', $seance['Seance']['id']),
            array(
                'title' => 'Clôture de la séance du ' . $seance['Seance']['date'],
                'escape' => false,
            ),'Confirmez-vous la cloture de la s&eacute;ance ?'));
        ?>
            </ul>
        </div>-->
    </td>

    <?php if (!$endDiv) : ?>
        <td>
            <!--        <div class="btn-group">-->
            <!--            <button class="btn dropdown-toggle" data-toggle="dropdown" href="#">-->
            <!--                Actions-->
            <!--                <span class="caret"></span>-->
            <!--            </button>-->
            <!--            <ul class="dropdown-menu">-->
            <!--                --><?php
            //                echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-edit"></i> Modifier',
            //                    array('controller' => 'seances', 'action' => 'edit', $seance['Seance']['id']),
            //                    array(
            //                        'title' => 'Modifier la séance du ' . $seance['Seance']['date'],
            //                        'escape' => false,
            //                    )));
            //                echo $this->Html->tag('li', $this->Html->link('<i class="fa fa-trash-o"></i> Supprimer',
            //                    array('controller' => 'seances', 'action' => 'delete', $seance['Seance']['id']),
            //                    array(
            //                        'title' => 'Supprimer la séance du ' . $seance['Seance']['date'],
            //                        'escape' => false,
            //                    ),
            //                    "Confirmer la suppression de la séance du : " . $seance['Seance']['date'] . ' ?'));
            //
            ?>
            <!--            </ul>-->
            <!--        </div>-->
            <div class="btn-group">
                <?php
                echo $this->Html->link('<span class="fa fa-edit"></span>',
                    array('controller' => 'seances', 'action' => 'edit', $seance['Seance']['id']),
                    array(
                        'class' => 'bouton_modifier btn btn-default',
                        'title' => 'Modifier la séance du ' . $seance['Seance']['date'],
                        'escape' => false,
                    ));
                echo $this->Html->link('<span class="fa fa-trash-o"></span>',
                    array('controller' => 'seances', 'action' => 'delete', $seance['Seance']['id']),
                    array(
                        'class' => 'bouton_supprimer btn btn-danger',
                        'title' => 'Supprimer la séance du ' . $seance['Seance']['date'],
                        'escape' => false,
                    ),
                    "Confirmer la suppression de la séance du : " . $seance['Seance']['date'] . ' ?');
                ?>
            </div>
        </td>
    <?php endif; ?>
    </tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
<?php
if($this->request['render']=='banette')
    echo('</div>');
else
if (!empty($models) && !empty($seances)) {
    $this->BsForm->setLeft(0);
    $this->BsForm->setRight(0);
    echo $this->Bs->row().
    $this->Bs->col('xs6').
    $this->BsForm->inputGroup('Seance.model_id', array(
                                'content'=>'<i class="fa fa-cogs"></i> Générer le document <span id="nbSeancesChecked"></span>',
                                'id' => 'generer_multi_seance',
                                'type' => 'button',
                                'state' => 'primary',
                                'side'=>'right'), array('class'=>'pull-left')).
            $this->Bs->close().
    $this->html->tag('div', '', array('class' => 'spacer')).
            
            $this->Bs->col('xs12').
    $this->html->tag('em', 'Note : Pour générer un document multi-séances, cochez les séances souhaitées dans la liste, sélectionnez le modèle d\'édition, puis cliquez sur le bouton "Générer le document".', array('class'=>'help-block')).
    $this->Bs->close();
}
echo $this->BsForm->end();

?>
<script type="text/javascript">
    $(document).ready(function () {
        //Lors d'action sur une checkbox :
        $('input[type=checkbox]').change(selectionChange);
        $('#SeanceModelId').select2({
            width: 'resolve',
            placeholder: 'Modèle d\'édition multi-séances'
        }).change(selectionChange).trigger('change');
    });
    function selectionChange() {
        var nbChecked = $('input[type=checkbox].checkbox_seance_generer:checked').length;
        //Apposer ou non la class disabled au bouton selon si des checkbox sont cochées (style)
        if (nbChecked > 0 && $('#SeanceModelId').val() != '') {
            $('#generer_multi_seance').removeClass('disabled');
            $("#generer_multi_seance").prop("disabled", false);
        } else {
            $('#generer_multi_seance').addClass('disabled');
            $("#generer_multi_seance").prop("disabled", true);
        }
        $('#nbSeancesChecked').text('(' + nbChecked + ')');
    }
</script>
