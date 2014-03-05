<div class="seances">
<?php
if (@$this->params['filtre'] == 'hide') {
    $endDiv = true;
    echo $this->Html->tag('div', null, array('class' => 'ouvrable', 'id' => 'seanceATraiter'));
    echo $this->Html->tag('h2', "Séances à traiter");
} else {
    $endDiv = false;
    echo $this->Html->tag('h2', "Séances à traiter");
    echo $this->Form->create('Seance', array('url' => array('controller' => 'seances', 'action' => 'genereFusionMultiSeancesToClient')));
}
?>
<table style="width:100%;">
    <tr>
        <?php if (!$endDiv) echo("<th style='width:2px;'><input type='checkbox' id='masterCheckbox' /></th>"); ?>
        <th style="width:22px;"></th>
        <th style="width:150px;">Type</th>
        <th style="width:190px;">Date Séance</th>
        <th style="width:20%;">Préparation</th>
        <th style="width:20%;">En cours</th>
        <th style="width:20%;">Finalisation</th>
    </tr>
    <?php
    $numLigne = 1;
    foreach ($seances as $seance) :
        $rowClass = ($numLigne & 1) ? array('style' => 'height:36px') : array('style' => 'height:36px', 'class' => 'altrow');
        echo $this->Html->tag('tr', null, $rowClass);
        $numLigne++;

        if (!$endDiv)
            echo("<td style='text-align:center; vertical-align: middle;'>"
                . $this->Form->checkbox(
                    'Seance.id_' . $seance['Seance']['id'],
                    array('checked' => false, 'class' => 'checkbox_seance_generer'))
                . "</td>");
        ?>
        <td>
            <?php
            echo $this->Html->link(SHY,
                '/seances/delete/' . $seance['Seance']['id'],
                array('class' => 'link_supprimer',
                    'title' => 'Supprimer la séance du ' . $seance['Seance']['date'],
                    'escape' => false
                ),
                "Voulez-vous supprimer la séance du : " . $seance['Seance']['date']);
            ?>

        </td>
        <td><b><?php echo $seance['Typeseance']['libelle']; ?></b></td>
        <td><?php echo $this->Html->link($seance['Seance']['date'], array('controller' => 'seances', 'action' => 'edit', $seance['Seance']['id'])); ?></td>
        <td class="actions" style="width:110px;"> <!-- largeur en fonction des icones -->
            <?php
            echo $this->Html->link(SHY,
                array('controller' => 'seances', 'action' => 'afficherProjets', $seance['Seance']['id']),
                array('class' => 'link_classer_odj',
                    'title' => 'Voir l\'ordre des projets de la séance du ' . $seance['Seance']['date'],
                    'escape' => false
                ));
            if (Configure::read('AFFICHE_CONVOCS_ANONYME'))
                echo $this->Html->link(SHY,
                    array('action' => 'genereFusionToClient', $seance['Seance']['id'], 'convocation'),
                    array(
                        'class' => 'waiter link_convocation_unique',
                        'title' => "Générer l'apercu d'une convocation pour la séance du " . $seance['Seance']['date'],
                        'escape' => false,
                    ));
            echo $this->Html->link(SHY,
                array('controller' => 'seances', 'action' => 'sendConvocations', $seance['Seance']['id'], $seance['Typeseance']['modelconvocation_id']),
                array(
                    'class' => 'link_convocation',
                    'title' => 'Générer la liste des convocations pour la séance du ' . $seance['Seance']['date'],
                    'escape' => false,
                ));
            if (Configure::read('AFFICHE_CONVOCS_ANONYME'))
                echo $this->Html->link(SHY,
                    array('action' => 'genereFusionToClient', $seance['Seance']['id'], 'ordredujour'),
                    array(
                        'class' => 'waiter link_ordre_jour_unique',
                        'escape' => false,
                        'title' => "Aperçu de l'ordre jour pour la séance du " . $seance['Seance']['date'],
                    ));
            echo $this->Html->link(SHY,
                array('controller' => 'seances', 'action' => 'sendOrdredujour', $seance['Seance']['id'], $seance['Typeseance']['modelordredujour_id']),
                array(
                    'class' => 'link_ordre_jour',
                    'title' => 'Générer l\'ordre du jour détaillé pour la séance du ' . $seance['Seance']['date'],
                    'escape' => false,
                ));
            echo $this->Html->link(SHY,
                array('controller' => 'seances', 'action' => 'sendToIdelibre', $seance['Seance']['id']),
                array(
                    'class' => 'link_tablet',
                    'title' => 'Envoyer à Idelibre la séance du ' . $seance['Seance']['date'],
                    'escape' => false,
                ));
            ?>
        </td>
        <td class="actions">
            <?php
            echo $this->Html->link(SHY,
                array('action' => 'saisirSecretaire', $seance['Seance']['id']),
                array(
                    'class' => 'link_secretaire',
                    'title' => 'Choix du président et du secrétaire de la séance du ' . $seance['Seance']['date'],
                    'escape' => false,
                ));
            echo $this->Html->link(SHY,
                array('action' => 'saisirDebatGlobal', $seance['Seance']['id']),
                array(
                    'class' => 'link_debat',
                    'title' => 'Saisir les débats généraux de la séance du ' . $seance['Seance']['date'],
                    'escape' => false,
                ));

            if ($seance['Typeseance']['action'] == 0)
                echo $this->Html->link(SHY,
                    array('action' => 'details', $seance['Seance']['id']),
                    array(
                        'class' => 'link_voter',
                        'escape' => false,
                        'title' => 'Afficher les projets et voter pour la séance du ' . $seance['Seance']['date']
                    ), false);

            elseif ($seance['Typeseance']['action'] == 1)
                echo $this->Html->link(SHY,
                    array('action' => 'detailsAvis', $seance['Seance']['id']),
                    array(
                        'class' => 'link_donnerAvis',
                        'title' => 'Afficher les projets et donner un avis pour la séance du ' . $seance['Seance']['date'],
                        'escape' => false,
                    ));
            elseif ($seance['Typeseance']['action'] == 2)
                echo $this->Html->link(SHY,
                    array('action' => 'details', $seance['Seance']['id']),
                    array(
                        'class' => 'link_actes',
                        'escape' => false,
                        'title' => 'Afficher les projets pour la séance du ' . $seance['Seance']['date']
                    ));

            echo $this->Html->link(SHY,
                array('action' => 'saisirCommentaire', $seance['Seance']['id']),
                array(
                    'class' => 'link_commentaire_seance',
                    'title' => 'Saisir un commentaire pour la séance du ' . $seance['Seance']['date'],
                    'escape' => false
                ));
            echo('</td>');
            echo('<td class="actions">');
            if ($canSign) {
                echo $this->Html->link(SHY,
                    array('controller' => 'deliberations', 'action' => 'sendToParapheur', $seance['Seance']['id']),
                    array(
                        'class' => 'link_signer',
                        'title' => 'Envoi des actes à la signature pour la séance du ' . $seance['Seance']['date'],
                        'escape' => false
                    ));
            }
            echo $this->Html->link(SHY,
                array('action' => 'genereFusionToClient', $seance['Seance']['id'], 'pvsommaire'),
                array(
                    'class' => 'link_pvsommaire waiter',
                    'title' => 'Génération du pv sommaire pour la séance du ' . $seance['Seance']['date'],
                    'escape' => false,
                ));
            echo $this->Html->link(SHY,
                array('action' => 'genereFusionToClient', $seance['Seance']['id'], 'pvdetaille'),
                array(
                    'class' => 'link_pvcomplet waiter',
                    'escape' => false,
                    'title' => 'Génération du pv complet pour la séance du ' . $seance['Seance']['date'],
                ));
            echo $this->Html->link(SHY,
                array('action' => 'clore', $seance['Seance']['id']),
                array(
                    'class' => 'link_clore_seance',
                    'title' => 'Clôture de la séance du ' . $seance['Seance']['date'],
                    'escape' => false,
                ), 'Confirmer la cloture de la séance ?');
            ?>
        </td>
        </tr>
    <?php endforeach; ?>

</table>
<div class='spacer'></div>
<?php
if (!empty($models) && !$endDiv) {
    echo $this->html->tag('fieldset', null);
    echo $this->html->tag('legend', 'Edition multi-séances');
    echo $this->html->tag('p', 'Cochez des séances dans la liste ci-dessus, sélectionnez le modèle d\'édition, puis cliquez sur le bouton Générer pour lancer la génération du document. Attention, le traitement peut être long.');
    echo $this->Form->input('Seance.model_id', array('options' => $models, 'label' => array('text' => "Modèle d'édition", 'style' => 'padding-top: 5px; text-align: left;')));

    echo $this->html->tag('div', '', array('class' => 'spacer'));
    echo $this->Form->button('<i class="fa fa-cogs"></i> Générer <span id="nbSeancesChecked"></span>', array(
        'type' => 'submit',
        'class' => 'waiter btn btn-primary',
        'title' => "Générer le document multi-séances (Attention : Cette opération peut durer longtemps)",
        'id' => 'generer_multi_seance',
    ));
    echo $this->html->tag('/fieldset', null);
}
echo $this->Form->end();
?>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        //Lors d'action sur une checkbox :
        $('input[type=checkbox]').change(selectionChange);
        selectionChange();
        $('#SeanceModelId').select2({width: 'resolve'});
    });
    function selectionChange() {
        var nbChecked = $('input[type=checkbox].checkbox_seance_generer:checked').length;
        //Apposer ou non la class disabled au bouton selon si des checkbox sont cochées (style)
        if (nbChecked > 0) {
            $('#generer_multi_seance').removeClass('disabled');
            $("#generer_multi_seance").prop("disabled", false);
        } else {
            $('#generer_multi_seance').addClass('disabled');
            $("#generer_multi_seance").prop("disabled", true);
        }
        $('#nbSeancesChecked').text('(' + nbChecked + ')');
    }
</script>
<?php
if ($endDiv)
    echo('</div>');
?>
