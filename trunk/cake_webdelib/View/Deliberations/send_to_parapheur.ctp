<?php echo $this->Html->script('utils.js'); ?>
<div class="deliberations">
    <?php if (isset($message)) echo($message); ?>

    <?php if (empty($seance_id)): ?>
    <h2>Actes envoyés à la signature</h2>
    <?php else:  ?>
    <h2>Signature des délibérations</h2>
    <?php endif; ?>
    <?php
    echo $this->element('filtre');
    echo $this->Form->create('Deliberation', array('url' => array('action'=>'sendToParapheur', $seance_id), 'type' => 'file'));
    ?>
    <table style='width:100%'>
        <tr>
            <?php if ($seance_id != null) : ?>
            <th style="width: 2px;"><input type='checkbox' id='masterCheckbox'/></th>
            <?php endif; ?>
            <th style="width: 20px;">Id</th>
            <th>Numéro Délibération</th>
            <th>Libellé de l'acte</th>
            <th>Bordereau</th>
            <th style='width:65px'>Statut</th>
        </tr>
        <?php
        $numLigne = 1;
        foreach ($deliberations as $delib) {
            $rowClass = ($numLigne & 1) ? array('height' => '36px') : array('height' => '36px', 'class' => 'altrow');
            echo $this->Html->tag('tr', null, $rowClass);
            $numLigne++;
            if ($seance_id != null)
                if ($delib['Deliberation']['signee'] != 1
                    && in_array($delib['Deliberation']['etat'], array(3, 4))
                    && in_array($delib['Deliberation']['parapheur_etat'], array(null, -1)))
                    echo "<td>" . $this->Form->checkbox('Deliberation.id.' . $delib['Deliberation']['id'], array('checked' => true)) . "</td>";
                else
                    echo "<td>" . $this->Form->checkbox('Deliberation.id.' . $delib['Deliberation']['id'], array('checked' => false, 'disabled'=>true)) . "</td>";
            ?>

            <td style="text-align:center"><?php echo $delib['Deliberation']['id']; ?></td>
            <td>
            <?php
            echo $this->Html->link($delib['Deliberation']['num_delib'], array('controller'=>'models','action'=>'generer', $delib['Deliberation']['id'], 'null', $delib['Modeltemplate']['id'],'-1','0',$delib['Deliberation']['num_delib'],'0','0','0'), array('class'=>'delib_pdf'));
            echo "</td>";
            ?>
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
            if ($delib['Deliberation']['parapheur_etat'] == 1 && $delib['Deliberation']['etat'] >= 2) {
                echo '<td>En cours de signature</td>';
            } elseif ($delib['Deliberation']['parapheur_etat'] == 2 && $delib['Deliberation']['etat'] >= 2) {
                if (!empty($delib['Deliberation']['signature']))
                    echo '<td>Signé&nbsp;<a href="/deliberations/downloadSignature/' . $delib['Deliberation']['id'] . '" title="Télécharger la signature" style="text-decoration: none;"><i class="fa fa-download"></i></a></td>';
                else
                    echo '<td>Signé</td>';
            } elseif ($delib['Deliberation']['signee'] == 1 && empty($delib['Deliberation']['parapheur_etat'])) {
                echo '<td>Déclaré signé</td>';
            } elseif ($delib['Deliberation']['etat'] > -1 && $delib['Deliberation']['etat'] < 2) {
                echo "<td>En cours d'élaboration</td>";
            } elseif ($delib['Deliberation']['etat'] == 2) {
                echo '<td>A faire voter</td>';
            } elseif ($delib['Deliberation']['parapheur_etat'] == -1) {
                echo '<td>Signature refusée</td>';
            } elseif ($delib['Deliberation']['etat'] == -1) {
                echo '<td>Acte refusé</td>';
            } else {
                echo '<td>A faire signer</td>';
            }
            ?>
            </tr>
        <?php } ?>

    </table>
    <br/>

    <?php
    if ($seance_id != null) {
        echo('Circuit : ');
        echo($this->Form->input('Deliberation.circuit_id', array('options' => $circuits, 'label' => false, 'div' => false)) . '<br /><br />');
        echo('<div class="submit">');
        echo $this->Form->button('<i class="fa fa-cloud-upload"></i> Envoyer', array('div' => false, 'type' => 'submit', 'class' => 'btn btn-primary'));
        echo('</div>');
    }
    ?>

    <?php echo $this->Form->end(); ?>
</div>
