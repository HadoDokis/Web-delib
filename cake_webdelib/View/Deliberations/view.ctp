<?php echo $this->Html->script('ckeditor/ckeditor'); ?>
<?php echo $this->Html->script('ckeditor/adapters/jquery'); ?>
<?php echo $this->Html->script('ouvrable', true); ?>

<?php
// Initialisation des boutons action de la vue
$linkBarre  = "<div class='btn-toolbar' id='traiterActions' role='toolbar'>";
$linkBarre .= "<div class='btn-group'>";
$linkBarre .= $this->Html->link(
    '<i class="fa fa-arrow-left"></i> Revenir',
    'javascript:history.go(-1);',
    array('escape' => false, 'class' => 'btn')
);
if ($Droits->check($this->Session->read('user.User.id'), 'Deliberations:edit'))
    $linkBarre .= $this->Html->link(
        '<i class="fa fa-edit"></i> Modifier',
        array('action' => 'edit', $this->data['Deliberation']['id']),
        array('escape' => false, 'class' => 'btn')
    );
$linkBarre .= $this->Html->link(
    '<i class="fa fa-file"></i> Générer',
    array('controller' => 'models', 'action' => 'generer', $this->data['Deliberation']['id'], 'null', $this->data['Modeltemplate']['id'], '-1', '0', 'retour', '0', '0', '0'),
    array('escape' => false, 'class' => 'btn delib_pdf', 'title' => 'Générer le document du projet')
);
$linkBarre .= $this->Html->link(
    '<i class="fa fa-comment"></i> Commenter',
    array('controller' => 'commentaires', 'action' => 'add', $this->data['Deliberation']['id']),
    array('escape' => false, 'class' => 'btn')
);
$linkBarre .= "</div>";
$linkBarre .= "</div>";
?>

<div id="vue_cadre">
<?php
if (empty($this->data['Multidelib'])) {
    if ($this->data['Deliberation']['etat'] == 3 || $this->data['Deliberation']['etat'] == 5)
        echo '<h3>D&eacute;lib&eacute;ration n&deg; ' . $this->data['Deliberation']['num_delib'] . '</h3>';
    else
        echo '<h3>Projet "' . $this->data['Deliberation']['objet'] . '" (Id: ' . $this->data['Deliberation']['id'] . ', Type: "'.$this->data['Typeacte']['libelle'].'")</h3>';
} else {
    if ($this->data['Deliberation']['etat'] == 3 || $this->data['Deliberation']['etat'] == 5)
        echo '<h3>Multi-D&eacute;lib&eacute;rations</h3>';
    else
        echo '<h3>Projet Multi-D&eacute;lib&eacute;rations</h3>';
}
echo $linkBarre;
?>
    <dl>
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
foreach ($this->data['Seance'] as $seance) {
    echo '<li><b>&nbsp;' . $seance['Typeseance']['libelle'] . '</b> : ' . $this->Form2->ukToFrenchDateWithHour($seance['date']) . "</li>";
}
                        ?>
                    </ul>
                </dd>
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
                    echo '<div class="spacer"></div>';
                    echo '<div class="fckEditorProjet">';
                    echo $this->Form->input($infosupdef['Infosupdef']['code'], array('label' => false, 'div' => false, 'type' => 'textarea', 'style' => 'display:none;', 'value' => $this->data['Infosup'][$infosupdef['Infosupdef']['code']]));
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
    echo '<dd>';
    foreach ($this->data['Annex'] as $annexe) {
        if ($annexe['titre'])
            echo '<br><strong>Titre :</strong> ' . $annexe['titre'];
        echo '<br><strong>Nom fichier :</strong> ' . $annexe['filename'];
        echo '<br><strong>Joindre au contrôle de légalité :</strong> ' . ($annexe['joindre_ctrl_legalite'] ? 'oui' : 'non');
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
        echo '<dd>'.$this->Html2->ukToFrenchDateWithHour($commentaire['Commentaire']['created']) . ' [' . $commentaire['Commentaire']['prenomAgent'] . ' ' . $commentaire['Commentaire']['nomAgent'] . ']&nbsp;';
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

<?php
    echo $linkBarre;
//    echo $this->Html->link('<i class="fa fa-arrow-left"></i> Retour', 'javascript:history.go(-1)', array('class' => 'btn', 'escape' => false, 'title' => 'Retour fiche')); ?>
</div>

</div>
<script type="text/javascript">
    $(document).ready(function() {
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
                    $this->Html->tag("i", "", array("class" => "fa fa-repeat")) . " Mise à jour", "/deliberations/majEtatParapheur/" . $this->data['Deliberation']['id'], array('escape' => false, "class" => "btn btn-inverse")), array('class' => 'majDeleg', 'title'=>'Mettre à jour le statut des étapes de délégations'));
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
            $('#' + inputId).ckeditor(function() {
                this.destroy();
            });
            $('#' + inputId).hide();
            lienAfficherMasquer.attr('affiche', 'masque');
            lienAfficherMasquer.html('[Afficher le texte]');
        }
    }
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
    div#traiterActions {
        text-align: center;
        /*margin: 20px;*/
    }
    h3{
        text-align: center;
    }
</style>
