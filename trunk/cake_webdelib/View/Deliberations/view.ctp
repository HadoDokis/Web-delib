<?php echo $this->Html->script('ckeditor/ckeditor'); ?>
<?php echo $this->Html->script('ckeditor/adapters/jquery'); ?>
<?php echo $this->Html->script('ouvrable', true); ?>
<script type="text/javascript">
    $(document).ready(function() {
        $("div.ouvrable").ouvrable({
            arrowUp: '<?php echo $this->Html->webroot('img/icons/arrow-right.png'); ?>',
            arrowDown: '<?php echo $this->Html->webroot('img/icons/arrow-down.png'); ?>',
            initHeight: 'MIN'
        });
<?php
if ($majDeleg) {
    ?>
            function afficheMAJ() {
                $("div.nomcourante").parent().append('<?php
    echo $this->Html->tag('div', $this->Html->link(
                    $this->Html->tag("i", "", array("class" => "icon-repeat")) . "&nbsp; MàJ état délégations", "/deliberations/majEtatParapheur/" . $this->data['Deliberation']['id'], array('escape' => false, "class" => "btn btn-inverse")), array('class' => 'majDeleg', 'title'=>'Mettre à jour le statut des étapes de délégations'));
    ?>')
            }
            ;
            afficheMAJ();
    <?php
}

if (isset($visas_retard) && !empty($visas_retard)) {
    foreach ($visas_retard as $visa) {
        ?>
                $('#etape_<?php echo $visa["Visa"]['numero_traitement']; ?> .delegation').before('<?php
        echo $this->Html->link(
                $this->Html->tag("i", "", array("class" => "icon-repeat")), "/cakeflow/traitements/traiterDelegationsPassees/" . $visa["Visa"]['traitement_id'] . "/" . $visa["Visa"]['numero_traitement'], array('escape' => false, "style" => "text-decoration:none;margin-right:5px;", 'title'=> 'Mettre à jour le statut de cette étape'));
        ?>');
    <?php
    }
}
?>

    });
</script>
<style>
    #Historique dd{
        text-indent: 0%;
    }
    div.majDeleg{
        border-top: 1px dashed;
    }
    #etapes.circuit{
        padding: 0;
    }
</style>

<div id="vue_cadre">
    <dl>
<?php
if (empty($this->data['Multidelib'])) {
    if ($this->data['Deliberation']['etat'] == 3 || $this->data['Deliberation']['etat'] == 5)
        echo '<h3>D&eacute;lib&eacute;ration n&deg; ' . $this->data['Deliberation']['num_delib'] . '</h3>';
    else
        echo '<h3>Identifiant projet ' . $this->data['Typeacte']['libelle'] . ' : ' . $this->data['Deliberation']['id'] . '</h3>';
} else {
    if ($this->data['Deliberation']['etat'] == 3 || $this->data['Deliberation']['etat'] == 5)
        echo '<h3>Multi-D&eacute;lib&eacute;rations</h3>';
    else
        echo '<h3>Projet Multi-D&eacute;lib&eacute;rations</h3>';
}
?>
        <div class="imbrique">
            <div class="gauche">
                <dt>Rédacteur</dt>
                <dd><?php echo $this->data['Redacteur']['prenom'] . ' ' . $this->data['Redacteur']['nom']; ?></dd>
            </div>
            <div class="droite">
                <dt>Rapporteur</dt>
                <dd>&nbsp;<?php echo $this->data['Rapporteur']['prenom'] . ' ' . $this->data['Rapporteur']['nom'] ?></dd>
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
if (empty($this->data['Multidelib'])) {
    echo $this->Html->tag('dt', 'Libellé');
    echo $this->Html->tag('dd', '&nbsp;' . $this->data['Deliberation']['objet']);
} else {
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
    echo $this->Html->tag('h2', 'Informations du projet (communes aux délibérations)');
}
?>

            <dt>Titre</dt>
            <dd>&nbsp;<?php echo $this->data['Deliberation']['titre'] ?></dd>

            <dt>Etat</dt>
            <dd>&nbsp;<?php echo $this->data['Deliberation']['libelleEtat'] ?></dd>
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

        <div class="imbrique">
            <div class="gauche">
                <dt>Num Pref</dt>
                <dd>&nbsp;<?php echo $this->data['Deliberation']['num_pref'] ?></dd>
            </div>
            <div class="droite">
                <dt>Date Séance</dt>
                <ul>                     
<?php
foreach ($this->data['Seance'] as $seance) {
    echo '<li><b>&nbsp;' . $seance['Typeseance']['libelle'] . '</b> : ' . $this->Form2->ukToFrenchDateWithHour($seance['date']) . "</li>";
}
?>
                </ul>
            </div>
        </div>


        <div class="imbrique">
<?php echo $this->Html->tag('div', null, array('class' => 'ouvrable', 'id' => 'Circuit', 'initHeight' => 'MIN'));
echo $this->Html->tag('dt', "Circuit " . $this->data['Circuit']['libelle']);
?>
            <dd><?php echo $visu; ?></dd>
        </div>
</div>

<?php
echo $this->Html->tag('div', null, array('class' => 'ouvrable', 'id' => 'textes'));
echo $this->Html->tag('dt', "Textes");
echo $this->element('viewTexte', array('type' => 'projet', 'delib' => $this->data['Deliberation']));
echo $this->element('viewTexte', array('type' => 'synthese', 'delib' => $this->data['Deliberation']));
if (empty($this->data['Multidelib']))
    echo $this->element('viewTexte', array('type' => 'deliberation', 'delib' => $this->data['Deliberation']));
echo ('</div>');
?>

<?php
if (!empty($infosupdefs)) {
    echo $this->Html->tag('div', null, array('class' => 'ouvrable', 'id' => 'Infosupps'));
    echo $this->Html->tag('dt', "Informations Suppl&eacute;mentaires");
    echo '<dd><br>';
    foreach ($infosupdefs as $infosupdef) {
        echo $infosupdef['Infosupdef']['nom'] . ' : ';
        if (array_key_exists($infosupdef['Infosupdef']['code'], $this->data['Infosup'])) {
            if ($infosupdef['Infosupdef']['type'] == 'richText') {
                if (!empty($this->data['Infosup'][$infosupdef['Infosupdef']['code']])) {
                    echo $this->Html->link('[Afficher le texte]', 'javascript:afficheMasqueTexteEnrichi(\'afficheMasque' . $infosupdef['Infosupdef']['code'] . '\', \'' . $infosupdef['Infosupdef']['code'] . '\')', array(
                        'id' => 'afficheMasque' . $infosupdef['Infosupdef']['code'], 'affiche' => 'masque'));
                    echo '<div class="annexesGauche"></div>';
                    echo '<div class="fckEditorProjet">';
                    echo $this->Form->input($infosupdef['Infosupdef']['code'], array('label' => '', 'type' => 'textarea', 'style' => 'display:none;', 'value' => $this->data['Infosup'][$infosupdef['Infosupdef']['code']]));
                    echo '</div>';
                    echo '<div class="spacer"></div>';
                }
            }
            else
                echo $this->data['Infosup'][$infosupdef['Infosupdef']['code']];
        }
        echo '<br>';
    }
    echo '</dd>';
    echo ('</div>');
}
?>

<?php
if (empty($this->data['Multidelib']) && !empty($this->data['Annex'])) {
    echo $this->Html->tag('div', null, array('class' => 'ouvrable', 'id' => 'Annexes'));
    echo $this->Html->tag('dt', "Annexes");
    echo '<dd><br>';
    foreach ($this->data['Annex'] as $annexe) {
        if ($annexe['titre'])
            echo '<br>Titre : ' . $annexe['titre'];
        echo '<br>Nom fichier : ' . $annexe['filename'];
        echo '<br>Joindre au contrôle de légalité : ' . ($annexe['joindre_ctrl_legalite'] ? 'oui' : 'non');
        echo '<br>' . $this->Html->link('Telecharger', '/annexes/download/' . $annexe['id']) . '<br>';
    }
    echo '</dd>';
    echo ('</div>');
}
?>

<?php
if ($tab_anterieure != null) {
    echo"<dt>Versions Antérieures</dt>";
    foreach ($tab_anterieure as $anterieure) {
        echo "<dd>&nbsp;<a href=" . $anterieure['lien'] . "> Version du " . $anterieure['date_version'] . " <b>[ID : " . $anterieure['id'] . "]</b></a></dd>";
    }
}
?>

<?php
if (!empty($commentaires)) {
    echo $this->Html->tag('div', null, array('class' => 'ouvrable', 'id' => 'Commentaires'));
    echo $this->Html->tag('dt', "Commentaires");
    foreach ($commentaires as $commentaire) {
        echo '<dd><u>' . $commentaire['Commentaire']['prenomAgent'] . ' ' . $commentaire['Commentaire']['nomAgent'] . ' </u><br/>';
        echo $commentaire['Commentaire']['texte'];
        echo '</dd>';
    }
    echo ('</div>');
}
?>
<?php
if (!empty($historiques)) {
    echo $this->Html->tag('div', null, array('class' => 'ouvrable', 'id' => 'Historique'));
    echo $this->Html->tag('dt', "Historique");

    foreach ($historiques as $historique) {
        echo '<dd>' . $this->Html2->ukToFrenchDateWithHour($historique['Historique']['created']) . ' ' . nl2br($historique['Historique']['commentaire']);
        echo '</dd>';
    }
    echo ('</div>');
}
?>


</dl>
<div id="actions_fiche">
    <?php echo $this->Html->link('<i class="icon-circle-arrow-left"></i> Retour', $previous, array('class' => 'btn', 'escape' => false, 'title' => 'Retour fiche')); ?>
</div>
    
</div>

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
            $('#' + inputId).ckeditor(function() {
                this.destroy();
            });
            $('#' + inputId).hide();
            lienAfficherMasquer.attr('affiche', 'masque');
            lienAfficherMasquer.html('[Afficher le texte]');
        }
    }
</script>
