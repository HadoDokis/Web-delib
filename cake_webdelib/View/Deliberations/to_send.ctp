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

    echo $this->Form->create('Deliberation', array('type' => 'file', 'url' => array('controller' => 'deliberations', 'action' => 'sendToTdt')));

    if (!empty($dateClassification)){
    ?>
    La Classification enregistrée date du <?php
        echo $dateClassification . '&nbsp;';
    }else{
    ?>
    <i class="fa fa-warning"></i> Classication non téléchargée
    <?php
    }
    echo $this->Html->link('<i class="fa fa-refresh"></i>', array('action' => 'getClassification'), array('title' => 'Télécharger les données de classification', 'escape' => false)) ?>
    <br/><br/>
    <table style='width:100%'>
        <tr>
            <th style="width: 2px;"><input type='checkbox' id='masterCheckbox'/></th>
            <th style="width: 20px;">Id</th>
            <th>Numéro Délibération</th>
            <th>Libellé de l'acte</th>
            <th>Titre</th>
            <th>Classification</th>
            <th>Statut</th>
        </tr>
        <?php
        $numLigne = 1;
        foreach ($deliberations as $delib) {
            $rowClass = ($numLigne & 1) ? array('height' => '36px') : array('height' => '36px', 'class' => 'altrow');
            echo $this->Html->tag('tr', null, $rowClass);
            $numLigne++;

            $options = array('hiddenField' => false);
            if ($delib['Deliberation']['etat'] < 5)
                $options['checked'] = true;
            else
                $options['disabled'] = true;

            echo '<td style="text-align:center;">' . $this->Form->checkbox('Deliberation.id.' . $delib['Deliberation']['id'], $options) . '</td>';
            ?>
            <td style="text-align:center"><?php echo $this->Html->link($delib['Deliberation']['id'], array('action' => 'view', $delib['Deliberation']['id'])); ?></td>
            <?php echo "<td>" . $this->Html->link($delib['Deliberation']['num_delib'], '/deliberations/downloadDelib/' . $delib['Deliberation']['id']); ?>
            </td>
            <td><?php echo $delib['Deliberation']['objet_delib']; ?></td>
            <td><?php echo $delib['Deliberation']['titre']; ?></td>
            <td><?php
                $id_num_pref = $delib['Deliberation']['id'] . '_num_pref';
                if (Configure::read('TDT') == 'PASTELL') {
                    if (empty($nomenclatures)) $nomenclatures = array();
                    echo $this->Form->input('Deliberation.' . $delib['Deliberation']['id'] . '_num_pref', array(
                        'name' => $delib['Deliberation']['id'] . 'classif2',
                        'label' => false,
                        'options' => $nomenclatures,
                        'default' => $delib['Deliberation']['num_pref'],
                        'disabled' => empty($nomenclatures),
                        'empty' => true,
                        'class' => 'select2 selectone',
                        'style' => 'width:auto; max-width:500px;',
                        'div' => array('style'=>'text-align:center;font-size: 1.1em;'),
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
                       id="<?php echo $delib['Deliberation']['id']; ?> _classification_text">[Choisir la classification]</a>
                    <?php
                    echo $this->Form->hidden('Deliberation.' . $delib['Deliberation']['id'] . '_num_pref', array(
                        'id' => $delib['Deliberation']['id'] . 'classif2',
                        'name' => $delib['Deliberation']['id'] . 'classif2',
                        'value' => $delib['Deliberation']['num_pref']
                    ));
                }
                ?></td>
            <td style="text-align: center">
            <?php
            if ($delib['Deliberation']['etat'] == 5) {
                $tdt_id = $delib['Deliberation']['tdt_id'];
                echo "<a href='$host/modules/actes/actes_transac_get_status.php?transaction=$tdt_id'><i class='fa fa-check-circle'></i> Envoyé</a>";
            } else{
                if (Configure::read('USE_PASTELL') && empty($delib['Deliberation']['pastell_id']))
                    echo '<i class="fa fa-exclamation-triangle" title="Le dossier n\'est pas dans Pastell"></i> ';
                echo "Non envoyé";
            }
            ?>
            </td>
            </tr>
        <?php } ?>
    </table>
    <br/>

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
    $(document).ready(function(){
        $(".select2.selectone").select2({
            width: "resolve",
            allowClear: true,
            placeholder: "Selectionnez un élément"
        });
    });
</script>