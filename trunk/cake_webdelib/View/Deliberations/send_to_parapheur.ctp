<?php echo $this->Html->script('utils.js'); ?>
<div class="deliberations">
    <?php if (isset($message)) echo($message); ?>
    <?php if (empty($seance_id)): ?>
        <h2>Actes envoyés à la signature</h2>
    <?php else: ?>
        <h2>Signature d'actes</h2>
    <?php endif; ?>
    <?php
    echo $this->element('filtre');
    if (!empty($seance_id))
        echo $this->Form->create('Deliberation', array(
            'url' => array('controller' => 'deliberations', 'action' => 'sendToParapheur', $seance_id),
            'type' => 'file'
        ));
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
            <th style='width:65px'>
                Statut <?php echo $this->Html->link('<i class="fa fa-refresh"></i>', array('controller' => 'deliberations', 'action' => 'refreshSignature'), array('escape' => false)); ?></th>
        </tr>

        <?php
        $numLigne = 1;
        foreach ($deliberations as $delib) {
            $rowClass = ($numLigne & 1) ? array('style' => 'height:36px') : array('style' => 'height:36px', 'class' => 'altrow');
            echo $this->Html->tag('tr', null, $rowClass);
            $numLigne++;

            $options = array();
            if ($seance_id != null) {
                if (empty($delib['Deliberation']['signee'])
                    && !empty($delib['Deliberation']['num_pref'])
                    && in_array($delib['Deliberation']['parapheur_etat'], array(null, 0, -1))
                    && in_array($delib['Deliberation']['etat'], array(3, 4)))
                    $options['checked'] = true;
                else
                    $options['disabled'] = true;

                echo '<td style="text-align:center;">' . $this->Form->checkbox('Deliberation.id_' . $delib['Deliberation']['id'], $options) . '</td>';
            }

            ?>
            <td style="text-align:center"><?php echo $this->Html->link($delib['Deliberation']['id'], array('action'=>'view', $delib['Deliberation']['id'])); ?></td>
            <td>
                <?php
                //if ($delib['Deliberation']['Typeacte']['nature_id'] == 1)
                if (!empty($delib['Deliberation']['num_delib']))
                    echo $this->Html->link($delib['Deliberation']['num_delib'], array('controller' => 'models', 'action' => 'generer', $delib['Deliberation']['id'], 'null', $delib['Modeltemplate']['id'], '-1', '0', 'delib_' . $delib['Deliberation']['num_delib'], '0', '0', '0'), array('class' => 'waiter'));
                else
                    echo $this->Html->link('Acte : ' . $delib['Deliberation']['id'], array('controller' => 'models', 'action' => 'generer', $delib['Deliberation']['id'], 'null', $delib['Modeltemplate']['id'], '-1', '0', 'acte_' . $delib['Deliberation']['id'], '0', '0', '0'), array('class' => 'waiter'));
                ?>
            </td>
            <td>
                <?php echo($delib['Deliberation']['objet_delib']); ?>
            </td>
            <td style="text-align: center">
                <?php
                if (!empty($delib['Deliberation']['parapheur_bordereau']))
                    echo $this->Html->link('[Bordereau de signature]&nbsp;<i class="fa fa-download"></i>', array('action' => 'downloadBordereau', $delib['Deliberation']['id']), array('escape' => false, 'title' => 'Télécharger le bordereau de signature', 'style' => 'text-decoration: none'));
                ?>
            </td>
            <td>
                <?php
                if (empty($delib['Deliberation']['signee'])) {
                    if (empty($delib['Deliberation']['num_pref']) && Configure::read('USE_PASTELL'))
                        echo $this->Html->link('Compléter la classification', array('controller' => 'deliberations', 'action' => 'edit', $delib['Deliberation']['id']));
                    elseif ($delib['Deliberation']['parapheur_etat'] == -1)
                        echo '<i class="fa fa-info-circle" title="' . $delib['Deliberation']['parapheur_commentaire'] . '"></i>&nbsp;Signature refusée';
                    elseif ($delib['Deliberation']['parapheur_etat'] == 1)
                        echo 'En cours de signature';
                    elseif ($delib['Deliberation']['etat'] == -1)
                        echo 'Projet refusé';
                    elseif ($delib['Deliberation']['etat'] == 2)
                        echo 'A faire voter';
                    elseif ($delib['Deliberation']['etat'] == 3)
                        echo 'Projet voté';
                    elseif ($delib['Deliberation']['etat'] == 4)
                        echo 'Projet non adopté';
                    elseif ($delib['Deliberation']['etat'] == 5)
                        echo 'Projet envoyé au tdt';
                    else
                        echo 'En cours d&apos;élaboration';
                } else {
                    if (!empty($delib['Deliberation']['signature']))
                        echo 'Signé&nbsp;<a href="/deliberations/downloadSignature/' . $delib['Deliberation']['id'] . '" title="Télécharger la signature" style="text-decoration: none;"><i class="fa fa-download"></i></a>';
                    elseif (empty($delib['Deliberation']['parapheur_etat']))
                        echo 'Signé manuellement';
                    else
                        echo 'Signé';
                }
                ?>
            </td>
            </tr>
        <?php } ?>

    </table>
    <br/>
    <?php
    if (!empty($seance_id)){
        echo($this->Form->input('Pastell.circuit_id', array('class' => 'select-circuit select2', 'options' => $circuits, 'label' => 'Circuits disponibles', 'div' => false, 'style' => 'width:auto;')));
        echo $this->Form->button('<i class="fa fa-mail-forward"></i> Envoyer', array('class' => 'btn btn-inverse sans-bordure', 'escape' => false));
        echo $this->Form->end();
    }
    ?>
</div>
<script>
   $('#PastellCircuitId').select2({ width: 'resolve' });
</script>
<style>
    #DeliberationSendToParapheurForm label {
        float: none;
        padding: 0;
        text-align: left;
        width: auto;
    }

    #DeliberationSendToParapheurForm .sans-bordure {
        border-radius: 0;
        -moz-border-radius: 0;
        -webkit-border-radius: 0;
    }

    #DeliberationSendToParapheurForm .select2-container .select2-choice {
        border-radius: 0;
    }

    #DeliberationSendToParapheurForm .select-circuit {
        margin-bottom: 0;
        width: auto;
        max-width: 500px;
    }
</style>