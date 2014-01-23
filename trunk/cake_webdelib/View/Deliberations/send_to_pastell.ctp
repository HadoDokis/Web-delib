<?php echo $this->Html->script('utils.js'); ?>
<div class="deliberations">
    <?php if (isset($message)) echo($message); ?>
    <h2>Signature d'acte</h2>
    <?php echo $this->Form->create('Deliberation', array(
        'url' => array('controller'=>'deliberations', 'action'=>'sendToPastell', $seance_id),
        'type' => 'file'
    )); ?>
    <table style='width:100%'>
        <tr>
            <th style="width: 2px;"><input type='checkbox' id='masterCheckbox'/></th>
            <th style="width: 20px;">Id</th>
            <th>Numéro Délibération</th>
            <th>Libellé de l'acte</th>
            <th>Bordereau</th>
            <th style='width:65px'>Statut <?php echo $this->Html->link('<i class="fa fa-refresh"></i>', array('controller'=>'deliberations', 'action'=>'refreshPastell'), array('escape'=>false)); ?></th>
        </tr>

        <?php
        $numLigne = 1;
        foreach ($deliberations as $delib) {
            $rowClass = ($numLigne & 1) ? array('style' => 'height:36px') : array('style' => 'height:36px', 'class' => 'altrow');
            echo $this->Html->tag('tr', null, $rowClass);
            $numLigne++;

            $options = array();
            if (empty($delib['Deliberation']['parapheur_id'])
                && !empty($delib['Deliberation']['num_pref'])
                && ($delib['Deliberation']['etat'] >= 3
                    || ($delib['Deliberation']['etat'] == 2 && $delib['Deliberation']['Typeacte']['nature_id'] > 1)))
                $options['checked'] = true;
            else
                $options['disabled'] = true;

            echo '<td style="text-align:center;">' . $this->Form->checkbox('Deliberation.id_' . $delib['Deliberation']['id'], $options) . '</td>';
?>
            <td style="text-align:center"><?php echo $delib['Deliberation']['id']; ?></td>
            <td>
                <?php
            //if ($delib['Deliberation']['Typeacte']['nature_id'] == 1)
            if (!empty($delib['Deliberation']['num_delib']))
                echo $this->Html->link($delib['Deliberation']['num_delib'], array('controller'=>'models', 'action'=>'generer', $delib['Deliberation']['id'], 'null', $delib['Modeltemplate']['id'], '-1', '0', 'delib_'.$delib['Deliberation']['num_delib'], '0', '0', '0'), array('class'=>'waiter'));
            else
                echo $this->Html->link('Acte : ' . $delib['Deliberation']['id'], array('controller'=>'models', 'action'=>'generer', $delib['Deliberation']['id'], 'null', $delib['Modeltemplate']['id'], '-1', '0', 'acte_'.$delib['Deliberation']['id'], '0', '0', '0'), array('class'=>'waiter'));
            ?>
            </td>
            <td>
                <?php echo($delib['Deliberation']['objet_delib']); ?>
            </td>
            <td style="text-align: center">
                <?php
                if (!empty($delib['Deliberation']['bordereau']))
                    echo $this->Html->link('[Bordereau de signature]&nbsp;<i class="fa fa-download"></i>', array('action'=>'downloadBordereau', $delib['Deliberation']['id']), array('escape'=>false, 'title'=>'Télécharger le bordereau de signature', 'style'=>'text-decoration: none'));
                ?>
            </td>
            <?php

            if (!empty($delib['Deliberation']['parapheur_id']) && $delib['Deliberation']['parapheur_etat'] != -1) {
                if ($delib['Deliberation']['signee'] == 1) {
                    if (empty($delib['Deliberation']['signature']))
                        $message = " signé manuellement";
                    else
                        $message = " signé électroniquement";
                } else {
                    $message = " En cours de signature";
                }

                if (!empty($delib['Deliberation']['tdt_id']))
                    $message = " Tdt : en cours";
                if (!empty($delib['Deliberation']['tdt_dateAR']))
                    $message = " Tdt : Reçu le " . $delib['Deliberation']['tdt_dateAR'];
                if (!empty($delib['Deliberation']['sae_etat']))
                    $message = " SAE : archivé";

                echo "<td>$message</td>";
            } elseif (empty($delib['Deliberation']['parapheur_id']) && $delib['Deliberation']['parapheur_etat'] == -1) {
                $refus = $delib['Deliberation']['parapheur_commentaire'];

                echo "<td title='$refus'>";
                echo $this->Html->image('icons/commentaire_refus.png');
                echo " refusé dans le i-parapheur</td>";
            } elseif ($delib['Deliberation']['etat'] >= 0 && $delib['Deliberation']['etat'] <= 1 && $delib['Deliberation']['Typeacte']['nature_id'] == 1) {
                echo "<td>En cours d'élaboration</td>";
            } elseif ($delib['Deliberation']['etat'] == 2 && $delib['Deliberation']['Typeacte']['nature_id'] == 1) {
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
    echo($this->Form->input('Pastell.circuit_id', array('class'=>'select-circuit select2','options' => $circuits, 'label' => 'Circuits disponibles', 'div' => false, 'style'=>'width:auto;')));
    echo $this->Form->button('<i class="fa fa-mail-forward"></i> Envoyer', array('class' => 'btn btn-inverse sans-bordure', 'escape'=>false));
    echo $this->Form->end();
    ?>
</div>
<style>
    label{
        float: none;
        padding: 0;
        text-align: left;
        width: auto;
    }
    .sans-bordure{
        border-radius: 0;
        -moz-border-radius: 0;
        -webkit-border-radius: 0;
    }
    .select2-container .select2-choice{
        border-radius: 0;
    }
    .select-circuit{
        margin-bottom:0;
        width:auto;
        max-width:500px;
    }
</style>