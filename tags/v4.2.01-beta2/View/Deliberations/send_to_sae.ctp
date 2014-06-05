<div class="deliberations">
    <h2>Versement SAE</h2>
    <?php echo $this->Form->create('Deliberation', array('type' => 'file', 'url' => array('action'=>'sendToSae'))); ?>
    <table style='width:100%'>
        <tr>
            <th style="width: 2px;"><input type='checkbox' id='masterCheckbox'/></th>
            <th style="width: 30px;">Id</th>
            <th>Numéro Délibération</th>
            <th>Libellé de l'acte</th>
            <th>Classification</th>
            <th>Statut</th>
        </tr>
        <?php
        $numLigne = 1;
        foreach ($deliberations as $delib) {
            $rowClass = ($numLigne & 1) ? array('height' => '36px') : array('height' => '36px', 'class' => 'altrow');
            echo $this->Html->tag('tr', null, $rowClass);
            $numLigne++;

            if ($delib['Deliberation']['sae_etat'] == null)
                echo("<td>" . $this->Form->checkbox('Deliberation.id_' . $delib['Deliberation']['id']) . "</td>");
            else
                echo("<td></td>");
            ?>
        <td>
                <?php echo($delib['Deliberation']['id']); ?>
            </td>

            <td>
                <?php echo $this->Html->link($delib['Deliberation']['num_delib'], '/deliberations/downloadDelib/' . $delib['Deliberation']['id']);?>
            </td>
            <td>
                <?php echo($delib['Deliberation']['objet_delib']); ?>
            </td>
            <td style="text-align:center">
                <?php
                    if (Configure::read('SAE') == 'PASTELL' && empty($delib['Deliberation']['sae_etat'])) {
                        
                        if (empty($nomenclatures)) $nomenclatures = array();
                        echo $this->Form->input('Deliberation.' . $delib['Deliberation']['id'] . '_num_pref', array(
                            'name' => $delib['Deliberation']['id'] . 'classif2',
                            'label' => false,
                            'options' => $nomenclatures,
                            'default' => $delib['Deliberation']['num_pref'],
                            'readonly' => empty($nomenclatures),
                            'empty' => true,
                            'class' => 'select2 selectone',
                            'style' => 'width:auto; max-width:400px;',
                            'div' => array('style' => 'text-align:center;font-size: 1.1em;'),
                            'escape' => false
                        ));
                    }else
                    echo !empty($delib['Deliberation']['num_pref']) ? $delib['Deliberation']['num_pref'] . ' - ' . $delib['Deliberation']['num_pref_libelle'] : '<em>-- Manquante --</em>';
                ?>
            </td>

            <?php
            if ($delib['Deliberation']['sae_etat'] == 1) {
                echo("<td>Versé au SAE</td>");
            } else {
                echo("<td>&nbsp;</td>");
            }
            ?>
            </tr>
        <?php } ?>

    </table>
    <div class='paginate'>
        <!-- Affiche les numéros de pages -->
        <?php
        echo $this->Paginator->prev('« Précédent ', null, null, array('tag' => 'span', 'class' => 'disabled'));
        echo $this->Paginator->numbers();

        ?>
        <!-- Affiche les liens des pages précédentes et suivantes -->
        <?php

        echo $this->Paginator->next(' Suivant »', null, null, array('tag' => 'span', 'class' => 'disabled'));
        ?>
        <!-- Affiche X de Y, où X est la page courante et Y le nombre de pages -->
        <?php echo $this->Paginator->counter(array('format' => 'Page %page% sur %pages%')); ?>
    </div>

    <br/>

    <div class="submit">
        <?php
        if (!empty($deliberations))
            echo $this->Form->button('<i class="fa fa-cloud-upload"></i> Envoyer', array('escape' => false, 'type' => 'submit', 'class' => 'btn btn-primary'));
        ?>
    </div>

    <?php $this->Form->end(); ?>
</div>
<script type="application/javascript">
    /**
     * Actions au chargement de la page
     */
    $(document).ready(function () {
        $('#ParapheurCircuitId').select2({ width: 'resolve' });
        $('.selectone').select2({
            width: 'resolve',
            allowClear: true,
            placeholder: 'Aucune classification'
        });
        $('input[type="checkbox"]').change(changeSelection);
        changeSelection();
    });
</script>
<style>
    .select2-container .select2-choice {
        border-radius: 0;
    }
</style>
