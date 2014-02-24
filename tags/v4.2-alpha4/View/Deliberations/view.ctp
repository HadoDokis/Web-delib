<?php echo $this->Html->script('ckeditor/ckeditor'); ?>
<?php echo $this->Html->script('ckeditor/adapters/jquery'); ?>
<?php echo $this->Html->script('ouvrable', true); ?>

<?php
$libelleCible = in_array($this->data['Deliberation']['etat'], array(3, 5)) ? 'la délibération' : 'le projet';

// Initialisation des boutons action de la vue
$linkBarre = "<div class='btn-toolbar boutons_view' role='toolbar'>";
$linkBarre .= "<div class='btn-group'>";
$linkBarre .= $this->Html->link(
    '<i class="fa fa-arrow-left"></i> Retour',
    'javascript:history.go(-1);',
    array('escape' => false, 'class' => 'btn')
);
$linkBarre .= $this->Html->link(
    '<i class="fa fa-file"></i> Générer',
    array('controller' => 'models', 'action' => 'generer', $this->data['Deliberation']['id'], 'null', $this->data['Modeltemplate']['id'], '-1', '0', 'projet' . $this->data['Deliberation']['id'], '0', '0', '0'),
    array('escape' => false, 'class' => 'btn delib_pdf', 'title' => 'Générer le document')
);
if ($userCanEdit)
    $linkBarre .= $this->Html->link(
        '<i class="fa fa-edit"></i> Modifier',
        array('action' => 'edit', $this->data['Deliberation']['id']),
        array('escape' => false, 'class' => 'btn', 'title' => 'Modifier ' . $libelleCible)
    );
if (!empty($this->data['Deliberation']['parent_id'])) {
    $linkBarre .= $this->Html->link(
        '<i class="fa fa-sitemap"></i> Fiche Multi-délib',
        array('action' => 'view', $this->data['Deliberation']['parent_id']),
        array('escape' => false, 'class' => 'btn btn-inverse', 'title' => 'Voir la fiche détaillée de la multi-délibération')
    );
}
if (!empty($versionsup)) {
    $linkBarre .= $this->Html->link(
        '<i class="fa fa-code-fork"></i> Nouvelle version',
        array('action' => 'view', $versionsup),
        array('escape' => false, 'class' => 'btn btn-danger', 'title' => 'Voir la fiche détaillée de la nouvelle version du projet')
    );
}
if ($userCanComment){
    $linkBarre .= $this->Html->link(
        '<i class="fa fa-cogs"></i> Traiter',
        array('action' => 'traiter', $this->data['Deliberation']['id']),
        array('escape' => false, 'class' => 'btn', 'title' => 'Traiter le projet '. $this->Html->value('Deliberation.objet'))
    );
    $linkBarre .= $this->Html->link(
        '<i class="fa fa-comment"></i> Commenter',
        array('controller' => 'commentaires', 'action' => 'add', $this->data['Deliberation']['id']),
        array('escape' => false, 'class' => 'btn btn-info', 'title' => 'Ajouter un commentaire')
    );

}
$linkBarre .= "</div>";
$linkBarre .= "</div>";
?>

<div id="vue_cadre">
<?php
$style = $this->data['Deliberation']['etat'] < 0 ? ' style="background-color:#FF3e3e"' : '';
echo "<h2 $style>";
if (empty($this->data['Multidelib'])) {
    if ($this->data['Deliberation']['etat'] >= 3)
        echo 'Déliberation n&deg;' . $this->data['Deliberation']['num_delib'] . ' : ' . $this->data['Deliberation']['objet'];
    else
        echo 'Projet n&deg;' . $this->data['Deliberation']['id'] . ' : ' . $this->data['Deliberation']['objet'];
} else {
    if ($this->data['Deliberation']['etat'] == 3 || $this->data['Deliberation']['etat'] == 5)
        echo 'Multi-délibérations';
    else
        echo 'Projet multi-délibérations';
}
echo '</h2>';
echo $linkBarre;
?>
<hr style='margin-top: 9px;'/>
<dl>
    <div class="imbrique">
        <div class="gauche">
            <dt>Rédacteur</dt>
            <dd><?php echo $this->data['Redacteur']['prenom'] . ' ' . $this->data['Redacteur']['nom']; ?></dd>
        </div>
        <div class="droite">
            <?php if (!empty($this->data['Rapporteur']['id'])) : ?>
                <dt>Rapporteur</dt>
                <dd>&nbsp;<?php echo $this->data['Rapporteur']['prenom'] . ' ' . $this->data['Rapporteur']['nom'] ?></dd>
            <?php endif; ?>
        </div>
    </div>

    <div class="imbrique">
        <div class="gauche">
            <dt>Date création</dt>
            <dd>&nbsp;<?php echo $this->data['Deliberation']['created'] ?></dd>
        </div>
        <div class="droite">
            <dt>Date modification</dt>
            <dd>&nbsp;<?php echo $this->data['Deliberation']['modified'] ?></dd>
        </div>
    </div>

    <div class="imbrique">
        <?php
        if (!empty($this->data['Multidelib'])) {
            echo $this->Html->tag('h3', 'Informations globales');
        }

        echo $this->Html->tag('dt', 'Libellé');
        echo $this->Html->tag('dd', '&nbsp;' . $this->data['Deliberation']['objet']);
        ?>
        <?php if (!empty($this->data['Deliberation']['titre'])) : ?>
            <dt>Titre</dt>
            <dd>&nbsp;<?php echo $this->data['Deliberation']['titre'] ?></dd>
        <?php endif; ?>
        <dt>Etat</dt>
        <dd>
            <?php
            echo $this->data['Deliberation']['libelleEtat'];
            if (!empty($versionsup)) {
                echo ' (Nouvelle version : ' . $this->Html->link('Projet ' . $versionsup, array('action' => 'view', $versionsup)) . ')';
            }
            ?>
        </dd>
    </div>

    <div class="imbrique">
        <div class="gauche">
            <dt>Thème</dt>
            <dd><?php echo $this->data['Theme']['libelle']; ?></dd>
        </div>
        <div class="droite">
            <dt>Service émetteur</dt>
            <dd><?php echo $this->data['Service']['libelle']; ?></dd>
        </div>
    </div>

    <div class="row imbrique" style="margin-left: 0px;">
        <div class="gauche">
            <dt>Num Pref</dt>
            <dd>&nbsp;<?php echo $this->data['Deliberation']['num_pref'] ?></dd>
        </div>
        <div class="droite">
            <dt>Date Séance</dt>
            <dd>
                <ul class="fix">
                    <?php
                    foreach ($this->data['listeSeances'] as $seance)
                        echo '<li><b>&nbsp;' . $seance['libelle'] . '</b>' . (isset($seance['date']) && !empty($seance['date']) ? ' : ' . $this->Html2->ukToFrenchDateWithHour($seance['date']) : '') . '</li>';
                    ?>
                </ul>
            </dd>
        </div>
    </div>
    <?php
    echo $this->Html->tag('div', null, array('id' => 'textes'));
    echo $this->Html->tag('dt', 'Textes');
    echo $this->element('viewTexte', array('type' => 'projet', 'delib' => $this->data['Deliberation']));
    echo $this->element('viewTexte', array('type' => 'synthese', 'delib' => $this->data['Deliberation']));
    if (empty($this->data['Multidelib']))
        echo $this->element('viewTexte', array('type' => 'deliberation', 'delib' => $this->data['Deliberation']));
    if (empty($this->data['Deliberation']['texte_projet_name'])
        && empty($this->data['Deliberation']['texte_synthese_name'])
        && empty($this->data['Deliberation']['deliberation_name']))
        echo $this->Html->tag('dd', '<em>-- Aucun texte --</em>', array('style'=>'text-indent:0;'));
    echo '</div>';
    if (!empty($visu)) {
        echo $this->Html->tag('div', null, array('class' => 'ouvrable', 'id' => 'Circuit'));
        echo $this->Html->tag('dt', "Circuit : " . $this->data['Circuit']['libelle']);
        echo '<dd>' . $visu . '</dd>';
        echo $this->Html->tag('/div', null);
    }
    ?>

    <?php
    if (empty($this->data['Deliberation']['parent_id']) && !empty($infosupdefs)) {
        echo $this->Html->tag('div', null, array('class' => 'ouvrable', 'id' => 'Infosupps'));
        echo $this->Html->tag('dt', "Informations Supplémentaires");
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
                        echo $this->Form->input($infosupdef['Infosupdef']['code'], array('label' => false, 'div' => false, 'type' => 'textarea', 'style' => 'display:none;', 'value' => $this->data['Infosup'][$infosupdef['Infosupdef']['code']]));
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
        echo('</div>');
    }
    ?>

    <?php
    if (empty($this->data['Multidelib']) && !empty($this->data['Annex'])) {
        echo $this->Html->tag('div', null, array('class' => 'ouvrable', 'id' => 'Annexes'));
        echo $this->Html->tag('dt', "Annexes");
        echo '<dd>';
        foreach ($this->data['Annex'] as $annexe) {
            echo '<br>';
            if ($annexe['titre'])
                echo '<strong>Titre :</strong> ' . $annexe['titre'];
            echo '<br><strong>Nom fichier :</strong> ' . $annexe['filename'];
            echo '<br><strong>Joindre au contrôle de légalité :</strong> ' . ($annexe['joindre_ctrl_legalite'] ? 'oui' : 'non');
            echo '<br>' . $this->Html->link('Telecharger', array('controller' => 'annexes', 'action' => 'download', $annexe['id'])) . '<br>';
        }
        echo '</dd>';
        echo('</div>');
    }
    ?>

    <?php
    if ($tab_anterieure != null) {
        echo "<dt>Versions Antérieures</dt>";
        foreach ($tab_anterieure as $anterieure) {
            echo "<dd>&nbsp;<a href=" . $anterieure['lien'] . "> Version du " . $anterieure['date_version'] . " <b>[ID : " . $anterieure['id'] . "]</b></a></dd>";
        }
    }

    if (!empty($commentaires)) {
        echo $this->Html->tag('div', null, array('class' => 'ouvrable', 'id' => 'Commentaires'));
        echo $this->Html->tag('dt', "Commentaires");
        foreach ($commentaires as $commentaire) {
            echo '<dd>' . $this->Html2->ukToFrenchDateWithHour($commentaire['Commentaire']['created']) . ' [' . $commentaire['Commentaire']['prenomAgent'] . ' ' . $commentaire['Commentaire']['nomAgent'] . ']&nbsp;';
            echo $commentaire['Commentaire']['texte'];
            echo '</dd>';
        }
        echo('</div>');
    }

    if (!empty($historiques)) {
        echo $this->Html->tag('div', null, array('class' => 'ouvrable', 'id' => 'Historique'));
        echo $this->Html->tag('dt', "Historique");

        foreach ($historiques as $historique) {
            echo '<dd>' . $this->Html2->ukToFrenchDateWithHour($historique['Historique']['created']) . ' ' . nl2br($historique['Historique']['commentaire']);
            echo '</dd>';
        }
        echo('</div>');
    }

    if (!empty($this->data['Multidelib'])) {
        echo $this->element('viewDelibRattachee', array(
            'delib' => $this->data['Deliberation'],
            'annexes' => $this->data['Annex'],
            'natureLibelle' => $this->data['Typeacte']['libelle']));
        foreach ($this->data['Multidelib'] as $delibRattachee) {
            echo $this->element('viewDelibRattachee', array(
                'delib' => $delibRattachee,
                'annexes' => $delibRattachee['Annex'],
                'natureLibelle' => $this->data['Typeacte']['libelle']));
        }
    }
    ?>


</dl>
<hr/>
<div id="actions_fiche">
    <?php echo $linkBarre; ?>
</div>

</div>
<script type="text/javascript">
    $(document).ready(function () {
        $("div.ouvrable").ouvrable({
            arrowUp: '<?php echo $this->Html->webroot('img/icons/arrow-right.png'); ?>',
            arrowDown: '<?php echo $this->Html->webroot('img/icons/arrow-down.png'); ?>',
            initHeight: 'MIN'
        });

        <?php
        if ($etat>=2){ //Enlever l'affichage en gras de la derniere étape si le traitement est fdini
            echo "$('.nomcourante').attr('class', 'nom');";
        }
        if ($majDeleg) {
        ?>

        function afficheMAJ() {
            $("div.nomcourante").parent().append('<?php
            echo $this->Html->tag('div',
                $this->Html->link(
                    $this->Html->tag("i", "", array("class" => "fa fa-repeat")) . " Mise à jour", array('controller'=>'deliberations', 'action'=>'majEtatParapheur', $this->data['Deliberation']['id']), array('escape' => false, "class" => "btn btn-inverse")), array('class' => 'majDeleg', 'title'=>'Mettre à jour le statut des étapes de délégations'));
        ?>')
        }

        afficheMAJ();
        <?php
    }

    if (isset($visas_retard) && !empty($visas_retard)) {
        foreach ($visas_retard as $visa) {
            ?>
        $('#etape_<?php echo $visa["Visa"]['numero_traitement']; ?> .delegation').before('<?php
        echo $this->Html->link(
                $this->Html->tag("i", "", array("class" => "fa fa-repeat")), "/cakeflow/traitements/traiterDelegationsPassees/" . $visa["Visa"]['traitement_id'] . "/" . $visa["Visa"]['numero_traitement'], array('escape' => false, "style" => "text-decoration:none;margin-right:5px;", 'title'=> 'Mettre à jour le statut de cette étape'));
        ?>');
        <?php
        }
    }
    ?>

    });

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
</script>
<style>
    #Historique dd {
        text-indent: 0%;
    }

    div.majDeleg {
        border-top: 1px dashed;
    }

    #etapes.circuit {
        padding: 0;
    }

    div.boutons_view {
        text-align: center;
    }
</style>
