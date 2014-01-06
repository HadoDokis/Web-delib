<?php echo $this->Html->script('utils.js'); ?>
<div class="deliberations">
    <?php if (isset($message)) echo($message); ?>
    <h2>Verser les délibérations dans As@lae</h2>
    <?php echo $this->Form->create('Deliberation', array('type' => 'file', 'url' => array('action'=>'verserAsalae'))); ?>
    <table style='width:100%'>
        <tr>
            <th style="width: 2px;"><input type='checkbox' id='masterCheckbox'/></th>
            <th>Numéro Délibération</th>
            <th>Libellé de l'acte</th>
            <th>Titre</th>
            <th>statut</th>
        </tr>
        <?php
        $numLigne = 1;
        foreach ($deliberations as $delib) {
            $rowClass = ($numLigne & 1) ? array('height' => '36px') : array('height' => '36px', 'class' => 'altrow');
            echo $this->Html->tag('tr', null, $rowClass);
            $numLigne++;

            if ($delib['Deliberation']['etat_asalae'] == null)
                echo("<td>" . $this->Form->checkbox('Deliberation.id_' . $delib['Deliberation']['id']) . "</td>");
            else
                echo("<td></td>");

            echo "<td>" . $this->Html->link($delib['Deliberation']['num_delib'], '/deliberations/downloadDelib/' . $delib['Deliberation']['id']);
            ?>
            </td>
            <td>
                <?php echo($delib['Deliberation']['objet_delib']); ?>
            </td>
            <td>
                <?php echo($delib['Deliberation']['titre']); ?>
            </td>

            <?php
            if ($delib['Deliberation']['etat_asalae'] == 1) {
                echo("<td>Délibération archivée dans AS@LAE</td>");
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
