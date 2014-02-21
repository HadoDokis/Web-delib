<div class="seances">
    <?php
    $endDiv = false;
    if (@$this->params['filtre'] == 'hide') {
        $endDiv = true;
        echo $this->Html->tag('div', null, array('class' => 'ouvrable', 'id' => 'seanceATraiter'));
        echo $this->Html->tag('h2', "Séances &agrave; traiter");
    } else {
        echo $this->Html->tag('h2', "Séances &agrave; traiter");
        echo $this->Form->create('Seance', array('url' => '/seances/multiodj/', 'type' => 'file'));
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
                        array('checked' => false, 'class' => 'choix_seance_generer'))
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
            <td><?php
                echo $this->Html->link($seance['Seance']['date'], "/seances/edit/" . $seance['Seance']['id']);
                ?></td>
            <td class="actions" style="width:110px;"> <!-- largeur en fonction des icones -->
                <?php
                echo $this->Html->link(SHY,
                    '/seances/afficherProjets/' . $seance['Seance']['id'],
                    array('class' => 'link_classer_odj',
                        'title' => 'Voir l\'ordre des projets de la séance du ' . $seance['Seance']['date'],
                        'escape' => false
                    ), false);
                $urlConvoc = '/seances/sendConvocations/' . $seance['Seance']['id'] . '/' . $seance['Typeseance']['modelconvocation_id'];
                $urlOdj = '/seances/sendOrdredujour/' . $seance['Seance']['id'] . '/' . $seance['Typeseance']['modelordredujour_id'];
                $urlConvocUnique = '/models/generer/null/' . $seance['Seance']['id'] . '/' . $seance['Typeseance']['modelconvocation_id'] . "/$format/0/convoc/0/1/1";
                $urlOdjUnique = '/models/generer/null/' . $seance['Seance']['id'] . '/' . $seance['Typeseance']['modelordredujour_id'] . "/$format/0/odj/0/1/1";
if (Configure::read('AFFICHE_CONVOCS_ANONYME'))
    echo $this->Html->link('NEW', 'genereFusionToClient/'.$seance['Seance']['id'].'/convocation', array(
        'class' => 'waiter link_convocation_unique',
        'title' => "Nouvelle méthode apercu d'une convocation pour la séance du " . $seance['Seance']['date'],
        'escape' => false,
    ), false);
                if (Configure::read('AFFICHE_CONVOCS_ANONYME'))
                    echo $this->Html->link(SHY, $urlConvocUnique, array(
                        'class' => 'link_convocation_unique',
                        'title' => "Apercu d'une convocation pour la séance du " . $seance['Seance']['date'],
                        'escape' => false,
                    ), false);
                echo $this->Html->link(SHY, $urlConvoc, array(
                    'class' => 'link_convocation',
                    'title' => 'Générer la liste des convocations pour la séance du ' . $seance['Seance']['date'],
                    'escape' => false,
                ), false);
if (Configure::read('AFFICHE_CONVOCS_ANONYME'))
    echo $this->Html->link('NEW', 'genereFusionToClient/'.$seance['Seance']['id'].'/ordredujour', array(
        'class' => 'waiter link_ordre_jour_unique',
        'escape' => false,
        'title' => "Nouvelle méthode apercu de l'ordre jour pour la séance du " . $seance['Seance']['date'],
    ), false);
                if (Configure::read('AFFICHE_CONVOCS_ANONYME'))
                    echo $this->Html->link(SHY, $urlOdjUnique, array(
                        'class' => 'link_ordre_jour_unique',
                        'escape' => false,
                        'title' => "Apercu de l'ordre jour pour la séance du " . $seance['Seance']['date'],
                    ), false);
                echo $this->Html->link(SHY, $urlOdj, array(
                    'class' => 'link_ordre_jour',
                    'title' => 'Générer l\'ordre du jour détaillé pour la séance du ' . $seance['Seance']['date'],
                    'escape' => false,
                ), false);

                echo $this->Html->link(SHY, '/seances/sendToIdelibre/' . $seance['Seance']['id'], array(
                    'class' => 'link_tablet',
                    'title' => 'Envoyer à Idelibre la séance du ' . $seance['Seance']['date'],
                    'escape' => false,
                ), false);
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
                    ), false);

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
                        ), false);
                elseif ($seance['Typeseance']['action'] == 2)
                    echo $this->Html->link(SHY,
                        array('action' => 'details', $seance['Seance']['id']),
                        array(
                            'class' => 'link_actes',
                            'escape' => false,
                            'title' => 'Afficher les projets pour la séance du ' . $seance['Seance']['date']
                        ), false);

                echo $this->Html->link(SHY,
                    array('action' => 'saisirCommentaire', $seance['Seance']['id']),
                    array(
                        'class' => 'link_commentaire_seance',
                        'title' => 'Saisir un commentaire pour la séance du ' . $seance['Seance']['date'],
                        'escape' => false
                    ), false);
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
                    array('controller' => 'models', 'action' => 'generer', 'null', $seance['Seance']['id'], $seance['Typeseance']['modelpvsommaire_id'], $format, '1', 'pv_sommaire', '1', '1', '1'),
                    array(
                        'class' => 'link_pvsommaire',
                        'title' => 'Génération du pv sommaire pour la séance du ' . $seance['Seance']['date'],
                        'escape' => false,
                    ), false);
                echo $this->Html->link(SHY,
                    array('controller' => 'models', 'action' => 'generer', 'null', $seance['Seance']['id'], $seance['Typeseance']['modelpvdetaille_id'], $format, '1', 'pv_complet', '1', '1', '1'),
                    array(
                        'class' => 'link_pvcomplet',
                        'escape' => false,
                        'title' => 'Génération du pv complet pour la séance du ' . $seance['Seance']['date'],
                    ), false);

                echo $this->Html->link(SHY,
                    array('action' => 'clore', $seance['Seance']['id']),
                    array(
                        'class' => 'link_clore_seance',
                        'title' => 'Clôture de la séance du ' . $seance['Seance']['date'],
                        'escape' => false,
                    ), 'Etes-vous sur de vouloir clôturer la séance ?', false);
                ?>
            </td>
            </tr>
        <?php endforeach; ?>

    </table>
    <div class='spacer'></div>
    <?php
    if (!$endDiv) {
        if (isset($models) && !empty($models)) {
            echo $this->Form->input('Seance.model_id', array('options' => $models, 'label' => 'Modèle'));
            echo $this->Form->button('<i class="fa fa-cogs"></i> Générer', array('type' => 'submit', 'class' => 'btn btn-primary', 'title' => "Lancer la génération multi-séances (Attention : l'exécution peut être longue)", 'id' => 'generer_multi_seance'));
        }
    }
    echo $this->Form->end();
    ?>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        //Désactiver le bouton submit au démarrage
        $("#generer_multi_seance").prop("disabled", true);
        //Lors d'action sur une checkbox :
        $('input[type=checkbox]').change(selectionChange);
        selectionChange();
    });
    function selectionChange() {
        //Apposer ou non la class disabled au bouton selon si des checkbox sont cochées (style)
        if (!$('input[type=checkbox].choix_seance_generer:checked').length) {
            $('#generer_multi_seance').addClass('disabled');
            $("#generer_multi_seance").prop("disabled", true);
        } else {
            $('#generer_multi_seance').removeClass('disabled');
            $("#generer_multi_seance").prop("disabled", false);
        }
    }
</script>
<?php
if ($endDiv)
    echo('</div>');
?>
