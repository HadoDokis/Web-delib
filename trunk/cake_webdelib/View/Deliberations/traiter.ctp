<div id='loading' style="display:none;">&nbsp;</div>
<div id="buttons">
<?php
echo $this->Html->script('utils.js');
echo $this->Html->script('noback.js');
echo $this->Html->script('ckeditor/ckeditor');
echo $this->Html->script('ckeditor/adapters/jquery');

// Initialisation des boutons action de la vue
$linkBarre  = "<div class='btn-toolbar' id='traiterActions' role='toolbar'>";
$linkBarre .= "<div class='btn-group'>";
$linkBarre .= $this->Html->link(
    '<i class="fa fa-arrow-left"></i> Revenir',
    array('action' => 'mesProjetsATraiter'),
    array('escape' => false, 'class' => 'btn')
);
if ($Droits->check($this->Session->read('user.User.id'), 'Deliberations:edit'))
    $linkBarre .= $this->Html->link(
        '<i class="fa fa-edit"></i> Modifier',
        array('action' => 'edit', $deliberation['Deliberation']['id']),
        array('escape' => false, 'class' => 'btn')
    );
$linkBarre .= $this->Html->link(
    '<i class="fa fa-file"></i> Générer',
    array('controller' => 'models', 'action' => 'generer', $deliberation['Deliberation']['id'], 'null', $deliberation['Modeltemplate']['id'], '-1', '0', 'retour', '0', '0', '0'),
    array('escape' => false, 'class' => 'btn delib_pdf', 'title' => 'Générer le document du projet')
);
$linkBarre .= $this->Html->link(
    '<i class="fa fa-comment"></i> Commenter',
    array('controller' => 'commentaires', 'action' => 'add', $deliberation['Deliberation']['id']),
    array('escape' => false, 'class' => 'btn')
);
$linkBarre .= "</div>";
$linkBarre .= "<div class='btn-group'>";
$linkBarre .= $this->Html->link(
    '<i class="fa fa-reply"></i> Retourner à',
    array('action' => 'retour', $deliberation['Deliberation']['id']),
    array('escape' => false, 'class' => 'btn')
);
if ($Droits->check($this->Session->read('user.User.id'), 'Deliberations:rebond'))
    $linkBarre .= $this->Html->link(
        '<i class="fa fa-share"></i> Envoyer à',
        array('action' => 'rebond', $deliberation['Deliberation']['id']),
        array('escape' => false, 'class' => 'btn')
    );
$linkBarre .= "</div>";
$linkBarre .= "<div class='btn-group'>";
$linkBarre .= $this->Html->link(
    '<i class="fa fa-thumbs-up"></i> Valider',
    array('action' => 'traiter', $deliberation['Deliberation']['id'], '1'),
    array('escape' => false, 'class' => 'btn btn-success')
);
$linkBarre .= $this->Html->link(
    '<i class="fa fa-thumbs-down"></i> Refuser',
    array('action' => 'traiter', $deliberation['Deliberation']['id'], '0'),
    array('escape' => false, 'class' => 'btn btn-danger')
);
$linkBarre .= "</div>";
$linkBarre .= "</div>";

// affichage  du titre
$listeIds = $deliberation['Deliberation']['id'];
foreach ($deliberation['Multidelib'] as $delibRattachee) {
    $listeIds .= ', ' . $delibRattachee['id'];
}
echo $this->Html->tag('h3', '[' . $deliberation['Typeacte']['libelle'] . '] - Traitement du projet "'.$deliberation['Deliberation']['objet'].'" (id : ' . $listeIds . ')');

echo $linkBarre;
echo $this->Html->tag('div', null, array('id' => "vue_cadre"));

?>
<br/>

<dl>
    <div class="imbrique">
        <?php
        if (empty($deliberation['Multidelib'])) {
            echo $this->Html->tag('dt', 'Libellé');
            echo $this->Html->tag('dd', '&nbsp;' . $deliberation['Deliberation']['objet']);
        } else {
            echo $this->element('viewDelibRattachee', array(
                'delib' => $deliberation['Deliberation'],
                'annexes' => $deliberation['Annex'],
                'natureLibelle' => $deliberation['Typeacte']['libelle']));
            foreach ($deliberation['Multidelib'] as $delibRattachee) {
                echo $this->element('viewDelibRattachee', array(
                    'delib' => $delibRattachee,
                    'annexes' => $delibRattachee['Annex'],
                    'natureLibelle' => $deliberation['Typeacte']['libelle']));
            }
            echo $this->Html->tag('h2', 'Informations du projet (communes aux délibérations)');
        }
        ?>
        <dt>Titre</dt>
        <dd>&nbsp;<?php echo $deliberation['Deliberation']['titre'] ?></dd>
    </div>

    <div class="imbrique">
        <div class="gauche">
            <dt>Thème</dt>
            <dd>&nbsp;<?php echo $deliberation['Theme']['libelle'] ?><br></dd>
        </div>
        <div class="droite">
            <dt>Service émetteur</dt>
            <dd>&nbsp;<?php echo $deliberation['Service']['libelle'] ?></dd>
        </div>
    </div>


    <div class="imbrique">
        <div class="gauche">
            <dt>Num Pref</dt>
            <dd>&nbsp;<?php echo $deliberation['Deliberation']['num_pref'] ?></dd>
        </div>
        <div class="droite">
            <dt>Date Séance</dt>
            <dd>
                <?php
                if (isset($deliberation['Seance'][0])) {
                    foreach ($deliberation['Seance'] as $seance) {
                        echo($seance['Typeseance']['libelle'] . " : ");
                        echo($this->Html2->ukToFrenchDateWithHour($seance['date']) . '<br>');
                    }
                }
                ?>
            </dd>
        </div>
    </div>

    <div class="imbrique">
        <dt>Circuit : <?php echo $deliberation['Circuit']['libelle'] ?></dt>
        <dd>
            <?php echo $visu; ?>
        </dd>
    </div>

    <div class="imbrique">
        <div class="gauche">
            <dt>Rédacteur</dt>
            <dd>
                &nbsp;<?php echo $this->Html->link($deliberation['Redacteur']['prenom'] . ' ' . $deliberation['Redacteur']['nom'], '/users/view/' . $deliberation['Redacteur']['id']) ?></dd>
        </div>
        <div class="droite">
            <dt>Rapporteur</dt>
            <dd>
                &nbsp;<?php echo $this->Html->link($deliberation['Rapporteur']['prenom'] . ' ' . $deliberation['Rapporteur']['nom'], '/acteurs/view/' . $deliberation['Rapporteur']['id']) ?></dd>
        </div>
    </div>

    <div class="imbrique">
        <div class="gauche">
            <dt>Date création</dt>
            <dd>&nbsp;<?php echo $deliberation['Deliberation']['created'] ?></dd>
        </div>
        <div class="droite">
            <dt>Date modification</dt>
            <dd>&nbsp;<?php echo $deliberation['Deliberation']['modified'] ?></dd>
        </div>
    </div>

    <?php
    echo $this->element('viewTexte', array('type' => 'projet', 'delib' => $deliberation['Deliberation']));
    echo $this->element('viewTexte', array('type' => 'synthese', 'delib' => $deliberation['Deliberation']));
    if (empty($deliberation['Multidelib']))
        echo $this->element('viewTexte', array('type' => 'deliberation', 'delib' => $deliberation['Deliberation']));

    if ($tab_anterieure != null) {
        echo "<dt>Versions Antérieures</dt>";
        foreach ($tab_anterieure as $anterieure) {
            echo "<dd>&nbsp;<a href=" . $anterieure['lien'] . ">Version du " . $anterieure['date_version'] . "</a></dd>";
        }
    }

    if (!empty($commentaires)) {
        echo "<dt>Commentaires</dt><br />";
        foreach ($commentaires as $commentaire) {
            echo '<dd>' . $this->Html2->ukToFrenchDateWithHour($commentaire['Commentaire']['created']) . ' [' . $commentaire['Commentaire']['prenomAgent'] . ' ' . $commentaire['Commentaire']['nomAgent'] . ']&nbsp;';
            echo $commentaire['Commentaire']['texte'] . ' ';
            if ($commentaire['Commentaire']['agent_id'] == $this->Session->read('user.User.id'))
                echo $this->Html->link('Supprimer', '/commentaires/delete/' . $commentaire['Commentaire']['id'] . '/' . $deliberation['Deliberation']['id']);
            else
                echo $this->Html->link('Prendre en compte', '/commentaires/prendreEnCompte/' . $commentaire['Commentaire']['id'] . '/' . $deliberation['Deliberation']['id']);
            echo '</dd>';
        }
    }

    if (!empty($infosupdefs)) {
        echo '<dt>Informations Suppl&eacute;mentaires </dt>';
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
                } else
                    echo $this->data['Infosup'][$infosupdef['Infosupdef']['code']];
            }
            echo '<br>';
        }
        echo '</dd>';
    }

    if (!empty($historiques)) {
        echo $this->Html->tag('div', null, array('id' => 'Historique'));
        echo $this->Html->tag('dt', "Historique");

        foreach ($historiques as $historique) {
            echo '<dd>' . $this->Html2->ukToFrenchDateWithHour($historique['Historique']['created']) . ' ' . nl2br($historique['Historique']['commentaire']);
            echo '</dd>';
        }
        echo('</div>');
    }
    ?>

    <?php
    if (empty($deliberation['Multidelib']) && !empty($deliberation['Annex'])) {
        echo '<dt>Annexes</dt>';
        echo '<dd><br>';
        foreach ($deliberation['Annex'] as $annexe) {
            if ($annexe['titre'])
                echo '<br>Titre : ' . $annexe['titre'];
            echo '<br>Nom fichier : ' . $annexe['filename'];
            echo '<br>Joindre au contrôle de légalité : ' . ($annexe['joindre_ctrl_legalite'] ? 'oui' : 'non');
            echo '<br>' . $this->Html->link('Telecharger', '/annexes/download/' . $annexe['id']) . '<br>';
        }
        echo '</dd>';
    }
    ?>

</dl>
</div>
<?php echo $linkBarre; ?>
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
            $('#' + inputId).ckeditor(function () {
                this.destroy();
            });
            $('#' + inputId).hide();
            lienAfficherMasquer.attr('affiche', 'masque');
            lienAfficherMasquer.html('[Afficher le texte]');
        }
    }
</script>
<script type="text/javascript">
    $(document).ready(function () {
        <?php
        if ($majDeleg) {
            ?>
        function afficheMAJ() {
            $("div.nomcourante").parent().append('<?php
    echo $this->Html->tag('div', $this->Html->link(
                    $this->Html->tag("i", "", array("class" => "fa fa-repeat")) . " Mise à jour", "/deliberations/majEtatParapheur/" . $deliberation['Deliberation']['id'], array('escape' => false, "class" => "btn btn-inverse")), array('class' => 'majDeleg', 'title'=>'Mettre à jour le statut des étapes de délégations'));
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
                $this->Html->tag("i", "", array("class" => "fa fa-repeat")), "/cakeflow/traitements/traiterDelegationsPassees/" . $visa["Visa"]['traitement_id'] . "/" . $visa["Visa"]['numero_traitement'] . '/traiter', array('escape' => false, "style" => "text-decoration:none;margin-right:5px;", 'title'=> 'Mettre à jour le statut de cette étape'));
        ?>');
        <?php
        }
    }
    ?>
    });
</script>
<style>
    #Historique dd {
        text-indent: 0%;
    }

    div.majDeleg {
        border-top: 1px dashed;
    }

    div#traiterActions {
        text-align: center;
        /*margin: 20px;*/
    }
    h3{
        text-align: center;
    }
</style>