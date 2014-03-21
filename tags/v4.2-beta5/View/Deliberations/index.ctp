<script>
    function choixModele(o) {
        if (o.value == 'generation') {
            $('#divmodeles').show();
            $('#divmodeles').css('display', 'inline');
        }
        else {
            $('#divmodeles').hide();
        }
    }
</script>
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
    echo $this->Form->create('Deliberation', array('url' => array('controller' => 'deliberations', 'action' => 'traitementLot'), 'type' => 'post'));
?>
<table>
    <tr>
        <th style="width:72px">
            <?php
            if (!empty($traitement_lot)) {
                echo '<input type="checkbox" id="masterCheckbox" />';
                echo '<br/>';
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
                <br/>
                <?php
                echo $this->Html->image(
                    $deliberation['iconeEtat']['image'], array(
                        'alt' => $deliberation['iconeEtat']['titre'] . ' ' . $deliberation['Deliberation']['objet'],
                        'title' => $deliberation['iconeEtat']['titre'] . ' ' . $deliberation['Deliberation']['objet']
                    )
                );

                if (isset($traitement_lot) && ($traitement_lot == true))
                    echo $this->Form->input('Deliberation_check.id_' . $deliberation['Deliberation']['id'], array('type' => 'checkbox', 'label' => false, 'div' => false));
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
                    echo $this->Form->create('Deliberation', array('url' => array('controller' => 'deliberations', 'action' => 'attribuerSeance'), 'type' => 'post'));
                    echo $this->Form->input(
                        'Deliberation.seance_id', array(
                            'type' => 'select',
                            'label' => '',
                            'options' => $deliberation['Seances'],
                            'empty' => 'Choisir une séance',
                            'empty' => false,
                            'multiple' => true,
                            'id' => false,
                            'style' => 'width:100%;'
                        )
                    );
                    echo $this->Form->hidden('Deliberation.id', array('value' => $deliberation['Deliberation']['id']));
                    echo $this->Form->button("<i class='fa fa-save'></i> Sauvegarder", array('type' => 'submit', 'div' => false, 'class' => 'btn', 'escape' => false, 'name' => 'sauvegarder'));
                    echo $this->Form->end();
                } else {
                    foreach ($deliberation['listeSeances'] as $seance)
                        echo $seance['libelle'] . (isset($seance['date']) && !empty($seance['date']) ? ' : ' . $this->Html2->ukToFrenchDateWithHour($seance['date']) : '') . '<br/>';
                }
                ?>
            </td>
            <td rowspan="3" class="actions">
                <br/>
                <?php
                if (in_array('view', $deliberation['Actions']))
                    echo $this->Html->link(SHY,
                        array('controller' => 'deliberations', 'action' => 'view', $deliberation['Deliberation']['id']),
                        array('class' => 'link_voir',
                            'title' => 'Voir le projet ' . $deliberation['Deliberation']['objet'],
                            'escape' => false),
                        false);

                if (in_array('edit', $deliberation['Actions']) && ($deliberation['Deliberation']['signee'] != 1))
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
                if (in_array('attribuerCircuit', $deliberation['Actions']) && ($deliberation['Deliberation']['signee'] != 1)) {
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
                    if (empty($deliberation['Deliberation']['delib_pdf']))
                        echo $this->Html->link(SHY,
                            array('controller' => 'deliberations', 'action' => 'genereFusionToClient', $deliberation['Deliberation']['id']),
                            array(
                                'class' => 'link_pdf delib_pdf',
                                'escape' => false,
                                'title' => 'Générer le document PDF du projet ' . $deliberation['Deliberation']['objet']));
                    else
                        echo $this->Html->link(SHY,
                            array('controller' => 'deliberations', 'action' => 'downloadDelib', $deliberation['Deliberation']['id']),
                            array('class' => 'link_pdf delib_pdf',
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

<?php if (!empty($listeLiens)) {
    if (in_array('add', $listeLiens)) {
        echo "<div style='text-align:center;'>";
        echo $this->Html->link('<i class=" fa fa-plus"></i> Ajouter un projet',
            array("action" => "add"),
            array('class' => 'btn btn-primary',
                'escape' => false,
                'title' => 'Créer un nouveau projet',
                'style' => 'margin-top: 10px;'));
        echo "</div>";
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
}
if (isset($traitement_lot) && ($traitement_lot == true)) {
    $actions_possibles['generation'] = 'Génération';
    echo "<div id='actions_bottom'>";
    echo $this->Form->input('Deliberation.action', array('options' => $actions_possibles,
        'div' => false,
        'onChange' => 'javascript:choixModele(this);',
        'empty' => 'Selectionner une action'));

    echo $this->Form->input('Deliberation.name', array('options' => $modeles,
        'div' => array('id' => 'divmodeles', 'style' => 'display:none;'),
        'label' => false,
        'empty' => 'Selectionner un modèle'));

    echo $this->Form->button("<i class='fa fa-cogs'></i> Executer",
        array('div' => false,
            'class' => 'btn',
            'escape' => false,
            'id' => 'btn_executer',
            'type' => 'submit'));
    echo '</div>';
    echo $this->Form->end();
}
?>
</div>
<?php
if ($endDiv)
    echo('</div>');
?>