<div class="deliberations">
    <?php
    echo $this->Html->script('utils.js');
    if ((@$this->params['filtre'] != 'hide') &&
        ($this->params['action'] != 'mesProjetsRecherche') &&
        ($this->params['action'] != 'tousLesProjetsRecherche')
    )
        echo $this->element('filtre');

    if (isset($message)) echo($message);

    if ($this->action == 'autreActesAEnvoyer')
        echo('<h2>Télétransmission des actes</h2>');
    elseif ($this->action == 'toSend')
        echo('<h2>Télétransmission des délibérations</h2>');

    echo $this->Form->create('Deliberation', array('type' => 'file', 'url' => array('controller' => 'deliberations', 'action' => 'sendToTdt'), 'class' => 'waiter'));

    if (!empty($dateClassification)) {
        echo 'La Classification enregistrée date du ' . $dateClassification;
    } else {
        echo '<i class="fa fa-warning"></i> Classification non téléchargée';
    }
    echo "&nbsp;";
    echo $this->Html->link('<i class="fa fa-refresh"></i>', array('action' => 'getClassification'), array('title' => 'Télécharger/Mettre à jour les données de classification', 'escape' => false)) ?>
    <div class="spacer"></div>
    <table class="table table-striped">
        <thead>
        <tr>
            <th style="width: 2px;"><input type='checkbox' id='masterCheckbox'/></th>
            <th style="width: 20px;">Id</th>
            <th style="width: 100px;">Numéro Délibération</th>
            <th>Libellé de l'acte</th>
            <th>Titre</th>
            <th>Classification</th>
            <th>Statut</th>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach ($deliberations as $delib) {
            echo $this->Html->tag('tr', null);

            $options = array('hiddenField' => false);
            if ($delib['Deliberation']['etat'] < 5)
                $options['checked'] = true;
            else
                $options['disabled'] = true;

            echo '<td style="text-align:center;">' . $this->Form->checkbox('Deliberation.id.' . $delib['Deliberation']['id'], $options) . '</td>';
            ?>
            <td style="text-align:center"><?php echo $this->Html->link($delib['Deliberation']['id'], array('action' => 'view', $delib['Deliberation']['id'])); ?></td>
            <td><?php echo $this->Html->link($delib['Deliberation']['num_delib'], '/deliberations/downloadDelib/' . $delib['Deliberation']['id']); ?></td>
            <td><?php echo $delib['Deliberation']['objet_delib']; ?></td>
            <td><?php echo $delib['Deliberation']['titre']; ?></td>
            <td><?php
                $id_num_pref = $delib['Deliberation']['id'] . '_num_pref';
                if ($delib['Deliberation']['etat'] == 5){
                     echo $delib['Deliberation']['num_pref'] . ' - ' . $delib['Deliberation']['num_pref_libelle'] ;
                }
                else {
                    if (!empty($nomenclatures)) {
                        echo $this->Form->input('Deliberation.' . $delib['Deliberation']['id'] . '_num_pref', array(
                            'name' => $delib['Deliberation']['id'] . 'classif2',
                            'label' => false,
                            'options' => $nomenclatures,
                            'default' => $delib['Deliberation']['num_pref'],
                            'readonly' => empty($nomenclatures),
                            'empty' => true,
                            'class' => 'select2 selectone',
                            'style' => 'width:auto; max-width:500px;',
                            'div' => array('style' => 'text-align:center;font-size: 1.1em;'),
                            'escape' => false
                        ));
                    } else {
                        echo $this->Form->input('Deliberation.' . $delib['Deliberation']['id'] . '_num_pref_libelle', array(
                            'label' => false,
                            'div' => false,
                            'id' => $delib['Deliberation']['id'] . 'classif1',
                            'style' => 'width: 25em;',
                            'disabled' => true,
                            'value' => $delib['Deliberation']['num_pref'] . ' - ' . $delib['Deliberation']['num_pref_libelle']));?>
                        <br/>
                        <a class="list_form" href="#add"
                           onclick="javascript:window.open('<?php echo $this->base; ?>/deliberations/classification?id=<?php echo $delib['Deliberation']['id']; ?>', 'Classification', 'scrollbars=yes,,width=570,height=450');"
                           id="<?php echo $delib['Deliberation']['id']; ?> _classification_text">[Choisir la
                            classification]</a>
                        <?php
                        echo $this->Form->hidden('Deliberation.' . $delib['Deliberation']['id'] . '_num_pref', array(
                            'id' => $delib['Deliberation']['id'] . 'classif2',
                            'name' => $delib['Deliberation']['id'] . 'classif2',
                            'value' => $delib['Deliberation']['num_pref']
                        ));
                    }
                }
                ?></td>
            <td style="text-align: center">
                <?php
                if ($delib['Deliberation']['etat'] == 5) {
                    $statut = "<i class='fa fa-check-circle'></i> Envoyé";
                } else {
                    $statut = "Non envoyé";
                }
                echo $statut;
                ?>
            </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
    <div class="spacer"></div>
    <div class="submit">
        <?php
        if (!empty($deliberations))
            echo $this->Form->button('<i class="fa fa-cloud-upload"></i> Envoyer', array('escape' => false, 'type' => 'submit', 'class' => 'btn btn-primary'));
        if (isset($seance_id))
            echo $this->Form->hidden('Seance.id', array('value' => $seance_id));
        ?>
    </div>
    <?php echo $this->Form->end(); ?>
</div>

<script type="application/javascript">
    $(document).ready(function () {
        $(".select2.selectone").select2({
            width: "resolve",
            allowClear: true,
            placeholder: "Selectionnez un élément"
        });
    });
</script>