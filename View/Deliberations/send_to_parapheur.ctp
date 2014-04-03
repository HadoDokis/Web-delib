<?php echo $this->Html->script('utils.js'); ?>
<div class="deliberations">
    <?php if (isset($message)) echo($message); ?>
    <?php if (empty($seance_id)): ?>
        <h2>Délibérations signées</h2>
    <?php else: ?>
        <h2>Signature des délibérations</h2>
    <?php endif; ?>
    <?php
    echo $this->element('filtre');
    if (!empty($seance_id))
        echo $this->Form->create('Deliberation', array(
            'url' => array('controller' => 'deliberations', 'action' => 'sendToParapheur', $seance_id),
            'type' => 'file'
        ));
    ?>
    <table class='table table-striped'>
        <thead>
        <tr>
            <?php if ($seance_id != null) : ?>
                <th style="width: 2px;"><input type='checkbox' id='masterCheckbox'/></th>
            <?php endif; ?>
            <th style="width: 20px;">Id</th>
            <th>Numéro Délibération</th>
            <th>Libellé de l'acte</th>
            <th>Classification</th>
            <th>Bordereau</th>
            <th style='width:210px'>
                Statut <?php //echo $this->Html->link('<i class="fa fa-refresh"></i>', array('controller' => 'deliberations', 'action' => 'refreshSignature'), array('escape' => false)); ?></th>
        </tr>
        </thead>
        <tbody>
        <?php
        $numLigne = 1;
        foreach ($deliberations as $delib) {
            echo $this->Html->tag('tr', null);
            $numLigne++;

            $options = array();
            if ($seance_id != null) {
                if (empty($delib['Deliberation']['signee'])
                    && in_array($delib['Deliberation']['parapheur_etat'], array(null, 0, -1))
                    && in_array($delib['Deliberation']['etat'], array(3, 4))
                )
                    $options['checked'] = true;
                else
                    $options['disabled'] = true;

                echo '<td style="text-align:center;">' . $this->Form->checkbox('Deliberation.id_' . $delib['Deliberation']['id'], $options) . '</td>';
            }
            ?>
            <td style="text-align:center"><?php echo $this->Html->link($delib['Deliberation']['id'], array('action' => 'view', $delib['Deliberation']['id'])); ?></td>
            <td>
                <?php
                if (!empty($delib['Deliberation']['num_delib']))
                    echo $this->Html->link($delib['Deliberation']['num_delib'], array('controller' => 'deliberations', 'action' => 'genereFusionToClient', $delib['Deliberation']['id']), array('class' => 'waiter'));
                else
                    echo $this->Html->link('Acte : ' . $delib['Deliberation']['id'], array('controller' => 'deliberations', 'action' => 'genereFusionToClient', $delib['Deliberation']['id']), array('class' => 'waiter'));
                ?>
            </td>
            <td>
                <?php echo($delib['Deliberation']['objet_delib']); ?>
            </td>

            <td style="text-align:center">
                <?php
                if ($seance_id == null)
                    echo !empty($delib['Deliberation']['num_pref']) ? $delib['Deliberation']['num_pref'] . ' - ' . $delib['Deliberation']['num_pref_libelle'] : '<em>-- Manquante --</em>';
                else {
                    $id_num_pref = $delib['Deliberation']['id'] . '_num_pref';
                    if (Configure::read('TDT') == 'PASTELL') {
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
                ?>
            </td>

            <td style="text-align: center">
                <?php
                if (!empty($delib['Deliberation']['parapheur_bordereau']))
                    echo $this->Html->link('<i class="fa fa-file-o"></i> Bordereau de signature', array('action' => 'downloadBordereau', $delib['Deliberation']['id']), array('escape' => false, 'title' => 'Télécharger le bordereau de signature', 'style' => 'text-decoration: none'));
                ?>
            </td>
            <td>
                <?php
                switch ($delib['Deliberation']['parapheur_etat']) {
                    case -1 :
                        echo '<i class="fa fa-exclamation-triangle" title="' . $delib['Deliberation']['parapheur_commentaire'] . '"></i>&nbsp;Retour parapheur : refusée';
                        break;
                    case 1 :
                        echo '<i class="fa fa-clock-o"></i> En cours de signature';
                        break;
                    case 2 :
                        echo '<i class="fa fa-check"></i> Approuvé dans le parapheur&nbsp;';
                        if (!empty($delib['Deliberation']['signee'])) {
                            if (!empty($delib['Deliberation']['signature']))
                                echo '(<a href="/deliberations/downloadSignature/' . $delib['Deliberation']['id'] . '" title="Télécharger la signature" style="text-decoration: none;">Signature</a>)';
                            else
                                echo '(Visa)';
                        }
                        break;
                    default : //0 ou null
                        if (!empty($delib['Deliberation']['signee'])) {
                            if (!empty($delib['Deliberation']['signature']))
                                echo '<i class="fa fa-check"></i> Signée&nbsp;<a href="/deliberations/downloadSignature/' . $delib['Deliberation']['id'] . '" title="Télécharger la signature" style="text-decoration: none;"><i class="fa fa-download"></i></a>';
                            else
                                echo '<i class="fa fa-check"></i> Signée manuellement';
                        } else {
                            switch ($delib['Deliberation']['etat']) {
                                case -1 :
                                    echo '<i class="fa fa-times"></i> Projet refusé';
                                    break;
                                case 2 :
                                    echo '<i class="fa fa-clock-o"></i> A faire voter';
                                    break;
                                case 3 :
                                    echo '<i class="fa fa-thumbs-up"></i> Projet voté';
                                    break;
                                case 4 :
                                    echo '<i class="fa fa-thumbs-down"></i> Projet non adopté';
                                    break;
                                case 5 :
                                    echo '<i class="fa fa-certificate"></i> Projet envoyé au tdt';
                                    break;
                                default :
                                    echo '<i class="fa fa-pencil"></i> En cours d&apos;élaboration';
                            }
                        }
                }
                ?>
            </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
    <?php
    if (!empty($seance_id) && !empty($deliberations)) {
        echo '<div id="select-circuit">';
        echo($this->Form->input('Parapheur.circuit_id', array('class' => 'select-circuit select2', 'options' => $circuits, 'label' => array('text' => 'Circuits disponibles', 'class' => 'circuits_label'), 'div' => false)));
        echo $this->Form->button('<i class="fa fa-mail-forward"></i> Envoyer', array('class' => 'btn btn-inverse sans-arrondi', 'escape' => false));
        echo '</div>';
        echo $this->Form->end();
    }
    echo $this->Html->link('<i class="fa fa-arrow-left"></i> Retour', $previous, array('escape' => false, 'class' => 'btn'));
    ?>
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

    /**
     * Afficher/Masquer la sélection de circuit selon si la selection est vide ou non
     */
    function changeSelection() {
        if ($('input[type="checkbox"]:checked').length > 0) {
            $('#select-circuit').show();
        } else {
            $('#select-circuit').hide();
        }
    }
</script>

<style>
    .select2-container .select2-choice {
        border-radius: 0;
    }
</style>