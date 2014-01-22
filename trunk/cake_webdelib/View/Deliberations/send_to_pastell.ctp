<?php echo $this->Html->script('utils.js'); ?>
<div class="deliberations">
    <?php if (isset($message)) echo($message); ?>
    <h2>Actes envoyés à la signature</h2>
    <?php echo $this->Form->create('Deliberation', array(
        'url' => array('controller'=>'deliberations', 'action'=>'sendToPastell', $seance_id),
        'type' => 'file'
    )); ?>
    <table style='width:100%'>
        <tr>
            <th style='width:25px'><input type="checkbox" id="masterCheckbox"></th>
            <th>Identifiant</th>
            <th>Numéro de l'acte</th>
            <th>Libellé de l'acte</th>
            <th>Titre</th>
            <th style='width:65px'>Statut <?php echo $this->Html->link('<i class="fa fa-refresh"></i>', array('controller'=>'deliberations', 'action'=>'refreshPastell'), array('escape'=>false)); ?></th>
        </tr>

        <?php
        $numLigne = 1;
        foreach ($deliberations as $delib) {
            $rowClass = ($numLigne & 1) ? array('style' => 'height:36px') : array('style' => 'height:36px', 'class' => 'altrow');
            echo $this->Html->tag('tr', null, $rowClass);
            $numLigne++;

            if (empty($delib['Deliberation']['pastell_id'])
                && !empty($delib['Deliberation']['num_pref'])
                && ($delib['Deliberation']['etat'] >= 3
                    || ($delib['Deliberation']['etat'] == 2 && $delib['Deliberation']['Typeacte']['nature_id'] > 1)))
                echo '<td style="text-align:center;">' . $this->Form->checkbox('Deliberation.id_' . $delib['Deliberation']['id'], array('checked' => true)) . '</td>';
            else
                echo '<td></td>';

            echo '<td>' . $delib['Deliberation']['id'] . '</td>';


            echo '<td>';
            if ($delib['Deliberation']['Typeacte']['nature_id'] == 1)
                echo $this->Html->link($delib['Deliberation']['num_delib'], array('controller'=>'models', 'action'=>'generer', $delib['Deliberation']['id'], 'null', $delib['Modeltemplate']['id'], '-1', '0', 'delib_'.$delib['Deliberation']['num_delib'], '0', '0', '0'), array('class'=>'waiter'));
            else
                echo $this->Html->link('Acte : ' . $delib['Deliberation']['id'], array('controller'=>'models', 'action'=>'generer', $delib['Deliberation']['id'], 'null', $delib['Modeltemplate']['id'], '-1', '0', 'acte_'.$delib['Deliberation']['id'], '0', '0', '0'), array('class'=>'waiter'));
            ?>
            </td>
            <td>
                <?php echo($delib['Deliberation']['objet_delib']); ?>
            </td>
            <td>
                <?php echo($delib['Deliberation']['titre']); ?>
            </td>
            <?php

            if (($delib['Deliberation']['pastell_id'] != null) && ($delib['Deliberation']['etat_parapheur'] != -1)) {
                if ($delib['Deliberation']['signee'] == 1) {
                    if ($delib['Deliberation']['signature'] == null)
                        $message = " signé manuellement";
                    else
                        $message = " signé électroniquement";
                } else {
                    $message = " En cours de signature";
                }

                if (!empty($delib['Deliberation']['tdt_id']))
                    $message = " Tdt : en cours";
                if (!empty($delib['Deliberation']['dateAR']))
                    $message = " Tdt : Reçu le " . $delib['Deliberation']['dateAR'];
                if ($delib['Deliberation']['etat_parapheur'] != NULL)
                    $message = " SAE : archivé";

                echo "<td>$message</td>";
            } elseif (($delib['Deliberation']['pastell_id'] == null) && ($delib['Deliberation']['etat_parapheur'] == -1)) {
                $refus = $delib['Deliberation']['commentaire_refus_parapheur'];

                echo "<td title='$refus'>";
                echo $this->Html->image('icons/commentaire_refus.png');
                echo " refusé dans le i-parapheur</td>";
            } elseif ($delib['Deliberation']['etat'] > -1 && $delib['Deliberation']['etat'] < 2 && $delib['Deliberation']['Typeacte']['nature_id'] == 1) {
                echo "<td>En cours d'élaboration</td>";
            } elseif ($delib['Deliberation']['etat'] >= 1 && $delib['Deliberation']['etat'] < 3 && $delib['Deliberation']['Typeacte']['nature_id'] == 1) {
                echo '<td>A faire voter</td>';
            } elseif ($delib['Deliberation']['num_pref'] == null) {
                $lien = $this->Html->link('Compléter la classification', array('controller'=>'deliberations', 'action'=>'edit', $delib['Deliberation']['id']));
                echo "<td>$lien</td>";
            } elseif ($delib['Deliberation']['Typeacte']['nature_id'] > 1 && $delib['Deliberation']['etat'] < 2) {
                echo "<td>Acte à valider : en cours d'élaboration</td>";
            } else {
                echo "<td>A envoyer dans Pastell</td>";
            }
            ?>
            </tr>
        <?php } ?>

    </table>
    <br/>
    <?php
    echo('Circuits disponibles : ');
    echo($this->Form->input('Pastell.circuit_id', array('options' => $circuits, 'label' => false, 'div' => false)) . '<br /><br />');
    echo("<div class='submit'>");
    echo $this->Form->submit('Envoyer', array('div' => false));
    echo $this->Form->end();
    echo("</div>");

    ?>
</div>
