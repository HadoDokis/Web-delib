<div class="deliberations">
<?php
if ($nbProjets > 1)
    $nb = "($nbProjets projets)";
else
    $nb = "($nbProjets projet)";

if ((@$this->params['filtre'] != 'hide')
    && ($this->params['action'] != 'mesProjetsRecherche')
    && ($this->params['action'] != 'tousLesProjetsRecherche')
) {
    echo $this->element('filtre');
    echo "<h2>$titreVue $nb</h2>";
    $endDiv = false;
} else {
    $traitement_lot = false;
    $endDiv = true;
    echo $this->Html->tag('div', null, array('class' => 'ouvrable', 'id' => $titreVue));
    echo $this->Html->tag('h2', "$titreVue $nb");
}
if (isset($traitement_lot) && ($traitement_lot == true))
    echo $this->Form->create('Deliberation', array('url' => array('controller' => 'deliberations', 'action' => 'traitementLot'), 'type' => 'post', 'class' => 'waiter'));
?>
<table style="width: 100%">
    <tr>
        <th style="width:72px">
            <?php
            if (!empty($traitement_lot)) {
                echo '<input type="checkbox" id="masterCheckbox" />';
            }
            ?>
        </th>
        <th style="width:15%; text-align: left;">Vue Synthétique</th>
        <th style="width:35%">&nbsp;</th>
        <th style="width:30%">&nbsp;</th>
        <th style="width:150px">Actions</th>
    </tr>
    <tr>
        <td colspan='5' class='Border' style="height: 1px"></td>
    </tr>

    <?php
    foreach ($this->data as $deliberation) {
        ?>
        <tr>
            <td rowspan="3" style="text-align:center;">
                <?php
                echo $this->Html->image(
                    $deliberation['iconeEtat']['image'], array(
                        'alt' => $deliberation['iconeEtat']['titre'] . ' ' . $deliberation['Deliberation']['objet'],
                        'title' => $deliberation['iconeEtat']['titre'] . ' ' . $deliberation['Deliberation']['objet']
                    )
                );

                if (!empty($traitement_lot)){
                    echo '<br/>';
           echo $this->Form->checkbox( 'Deliberation.check.id_' . $deliberation['Deliberation']['id'],
                                array('checked' => false, 'class' => 'checkbox_deliberation_generer'));
                }
                ?>

            </td>
            <td>Service émetteur :<br/>
                <?php
                if (isset($deliberation['Deliberation']['Service']['libelle']))
                    echo $deliberation['Deliberation']['Service']['libelle'];
                elseif (isset($deliberation['Service']['libelle']))
                    echo $deliberation['Service']['libelle'];
                ?></td>
            <td><?php echo $deliberation['Deliberation']['objet']; ?></td>
            <td>Séance(s) :<br/>
                <?php
                if (in_array('attribuerSeance', $deliberation['Actions'])) {
                    echo $this->Form->create('Deliberation', array('url' => array('controller' => 'deliberations', 'action' => 'attribuerSeance'), 'type' => 'post', 'class' => 'waiter'));
                    echo $this->Form->input(
                        'Deliberation.seance_id', array(
                            'type' => 'select',
                            'label' => false,
                            'options' => $deliberation['Seances'],
                            'empty' => true,
                            'multiple' => true,
                            'id' => false,
                            'class' => 'select2multiple',
                            'style' => 'width:100%;'
                        )
                    );
                    echo $this->Form->hidden('Deliberation.id', array('value' => $deliberation['Deliberation']['id']));
                    echo '<div class="spacer"></div>';
                    echo $this->Form->button("<i class='fa fa-save'></i> Sauvegarder", array('type' => 'submit', 'div' => false, 'class' => 'btn btn-primary pull-right', 'escape' => false, 'name' => 'sauvegarder'));
                    echo $this->Form->end();
                } else {
                    foreach ($deliberation['listeSeances'] as $seance)
                        echo $seance['libelle'] . (isset($seance['date']) && !empty($seance['date']) ? ' : ' . $this->Html2->ukToFrenchDateWithHour($seance['date']) : '') . '<br/>';
                }
                ?>
            </td>
            <td rowspan="3" class="actions">
                <?php
                if (in_array('view', $deliberation['Actions'])) 
                echo $this->Html->link(SHY,
                    array('controller' => 'deliberations', 'action' => 'view', $deliberation['Deliberation']['id']),
                    array('class' => 'link_voir',
                        'title' => 'Voir le projet ' . $deliberation['Deliberation']['objet'],
                        'escape' => false),
                    false);

                if (in_array('edit', $deliberation['Actions']) && empty($deliberation['Deliberation']['signee']))
                    echo $this->Html->link(SHY,
                        array('controller' => 'deliberations', 'action' => 'edit', $deliberation['Deliberation']['id']),
                        array('class' => 'link_modifier',
                            'title' => 'Modifier le projet ' . $deliberation['Deliberation']['objet'],
                            'escape' => false
                        ));

                if (in_array('delete', $deliberation['Actions']))
                    echo $this->Html->link(SHY,
                        array('controller' => 'deliberations', 'action' => 'delete', $deliberation['Deliberation']['id']),
                        array('class' => 'link_supprimer',
                            'escape' => false,
                            'title' => 'Supprimer le projet ' . $deliberation['Deliberation']['objet'],
                        ),
                        'Confirmez-vous la suppression du projet \'' . $deliberation['Deliberation']['objet'] . '\' ?');

                if (in_array('traiter', $deliberation['Actions']))
                    echo $this->Html->link(SHY,
                        array('controller' => 'deliberations', 'action' => 'traiter', $deliberation['Deliberation']['id']),
                        array(
                            'class' => "link_traiter",
                            'escape' => false,
                            'title' => 'Traiter le projet ' . $deliberation['Deliberation']['objet']));

                if (in_array('validerEnUrgence', $deliberation['Actions']))
                    echo $this->Html->link(SHY,
                        array('controller' => 'deliberations', 'action' => 'validerEnUrgence', $deliberation['Deliberation']['id']),
                        array(
                            'class' => "link_validerenurgence",
                            'title' => 'Valider en urgence le projet ' . $deliberation['Deliberation']['objet'],
                            'escape' => false),
                        'Confirmez-vous la validation en urgence du projet \'' . $deliberation['Deliberation']['id'] . '\'');

                if (in_array('goNext', $deliberation['Actions']))
                    echo $this->Html->link(SHY,
                        array('controller' => 'deliberations', 'action' => 'goNext', $deliberation['Deliberation']['id']),
                        array(
                            'class' => "link_jump",
                            'title' => 'Sauter une ou des étapes pour le projet ' . $deliberation['Deliberation']['objet'],
                            'escape' => false));
                echo '<div class="spacer"></div>';
                echo '<br/>';
                if (in_array('attribuerCircuit', $deliberation['Actions']) && empty($deliberation['Deliberation']['signee'])) {
                    $actionAttribuer = array('controller' => 'deliberations', 'action' => 'attribuercircuit', $deliberation['Deliberation']['id']);
                    if (!empty($deliberation['Deliberation']['circuit_id']))
                        $actionAttribuer[] = $deliberation['Deliberation']['circuit_id'];
                    echo $this->Html->link(SHY,
                        $actionAttribuer,
                        array('class' => 'link_circuit',
                            'escape' => false,
                            'title' => 'Attribuer un circuit pour le projet ' . $deliberation['Deliberation']['objet']),
                        false);

                }
                if (in_array('generer', $deliberation['Actions'])) {
                    echo $this->Html->link(SHY,
                        array('controller' => 'deliberations', 'action' => 'genereFusionToClient', $deliberation['Deliberation']['id']),
                        array(
                            'class' => 'link_pdf delib_pdf',
                            'escape' => false,
                            'title' => 'Générer le document PDF du projet ' . $deliberation['Deliberation']['objet']));
                }
                if(in_array('telecharger', $deliberation['Actions'])){
                    echo $this->Html->link(SHY,
                        array('controller' => 'deliberations', 'action' => 'downloadDelib', $deliberation['Deliberation']['id']),
                        array('class' => 'link_pdf',
                            'title' => 'Visionner le document PDF du projet ' . $deliberation['Deliberation']['objet'],
                            'escape' => false),
                        false);
                }
                ?>
            </td>
        </tr>
        <tr>
            <td>
                Circuit : <br/><?php echo $deliberation['Circuit']['nom']; ?>
                <br/><?php if (isset($deliberation['last_viseur']) && !empty($deliberation['last_viseur']))
                    echo 'Dernière action de : ' . $deliberation['last_viseur'] ?>
            </td>
            <td class='corps' rowspan="1"><?php echo $deliberation['Deliberation']['titre']; ?></td>
            <td>A traiter avant le :<br/><?php echo $deliberation['Deliberation']['date_limite']; ?></td>
        </tr>
        <tr>
            <td><b>projet
                    <?php
                    if (isset($deliberation['Typeacte']['libelle']))
                        $nature = $deliberation['Typeacte']['libelle'];
                    echo strtolower($nature) . ' : ' . $deliberation['Deliberation']['id'];
                    ?>
                </b>
            </td>
            <td class='corps' rowspan="1">Th&egrave;me :
                <?php
                if (isset($deliberation['Theme']['libelle']))
                    echo $deliberation['Theme']['libelle'];
                ?></td>
            <td>Classification : <?php echo $deliberation['Deliberation']['num_pref']; ?></td>
        </tr>
        <tr>
            <td colspan='5' class='Border' style="height: 1px"></td>
        </tr>
    <?php } ?>

</table>

<?php 
if (isset($traitement_lot) && ($traitement_lot == true)) {
    echo $this->html->tag('div', '', array('class' => 'spacer'));
    $actions_possibles['generation'] = 'Génération';
    echo $this->html->tag('fieldset', null, array('id' => 'generation-multiseance'));
    echo $this->Form->input('Deliberation.action', array(
        'options' => $actions_possibles,
        'empty' => true,
        'div' => array('class' => 'pull-left'),
        'label' => false,
        'after' => '<i class="fa fa-arrow-right" style="margin-left: 10px"></i>'
    ));
    echo $this->Form->input('Deliberation.modele', array(
        'options' => $modeles,
        'empty' => true,
        'div' => array('id'=>'divmodeles', 'class' => 'pull-left', 'style' => 'display:none;margin-left: 10px'),
        'label' => false,
        'after' => '<i class="fa fa-arrow-right" style="margin-left: 10px"></i>'
    ));
    echo $this->Form->button('<i class="fa fa-cogs"></i> Executer<span id="nbDeliberationsChecked"></span>', array(
        'type' => 'submit',
        'class' => 'btn btn-primary pull-left',
        'title' => "Executer",
        'id' => 'generer_multi_delib',
        'style' => 'margin-left: 10px'
    ));
    echo $this->html->tag('div', '', array('class' => 'spacer'));
    echo $this->html->tag('/fieldset', null);
}

if (!empty($listeLiens)) {
    echo '<div role="toolbar" class="btn-toolbar" style="text-align: center;"><div class="btn-group">';
    if (in_array('add', $listeLiens)) {
        echo $this->Html->link('<i class=" fa fa-plus"></i> Ajouter un projet',
            array("action" => "add"),
            array('class' => 'btn btn-primary',
                'escape' => false,
                'title' => 'Créer un nouveau projet',
                'style' => 'margin-top: 10px;'));
    }
    if (in_array('mesProjetsRecherche', $listeLiens)) {
        echo '<ul class="actions">';
        echo '<li>' . $this->Html->link('Nouvelle recherche', '/deliberations/mesProjetsRecherche', array('class' => 'btn', 'escape' => false, 'alt' => 'Nouvelle recherche parmi mes projets', 'title' => 'Nouvelle recherche parmi mes projets')) . '</li>';
        echo '</ul>';
    }
    if (in_array('tousLesProjetsRecherche', $listeLiens)) {
        echo '<ul class="actions">';
        echo '<li>' . $this->Html->link('Nouvelle recherche', '/deliberations/tousLesProjetsRecherche', array('class' => 'btn', 'escape' => false, 'alt' => 'Nouvelle recherche parmi tous les projets', 'title' => 'Nouvelle recherche parmi tous les projets')) . '</li>';
        echo '</ul>';
    }
    echo "</div></div>";
}
?>
<?php
if ($endDiv)
    echo('</div>');
?>
<script type="text/javascript">
    $(document).ready(function () {
        //Lors d'action sur une checkbox :
        $('input[type=checkbox]').change(selectionChange);
        $('#DeliberationAction').select2({
            width: 'resolve',
            placeholder: 'Selectionner une action'
        }).change(selectionChange).trigger('change');
        $('#DeliberationModele').select2({width: 'resolve', placeholder: 'Selectionner un modèle'});
    });
    function selectionChange() {
        var nbChecked = $('input[type=checkbox].checkbox_deliberation_generer:checked').length;
        //Apposer ou non la class disabled au bouton selon si des checkbox sont cochées (style)
        if (nbChecked > 0 && $('#DeliberationAction').val() != '') {
            $('#generer_multi_delib').removeClass('disabled');
            $("#generer_multi_delib").prop("disabled", false);
        } else {
            $('#generer_multi_delib').addClass('disabled');
            $("#generer_multi_delib").prop("disabled", true);
        }
        if($('#DeliberationAction').val() == 'generation'){
            $('#divmodeles').show();
        }else {
            $('#divmodeles').hide();
        }
        $('#nbDeliberationsChecked').text('(' + nbChecked + ')');
    }
</script>