<script>
    $("#pourcentage").hide();
    $("#progrbar").hide();
    $("#affiche").hide();
    $("#contTemp").hide();
</script>

<?php echo $this->Html->script('utils.js'); ?>
<div class="deliberations">
    <?php
    echo $this->element('filtre');
    ?>

    <?php
    if ($this->action == 'autreActesEnvoyes')
        echo('<h2>Télétransmission des actes</h2>');
    elseif ($this->action == 'transmit')
        echo('<h2>Télétransmission des délibérations</h2>');
    ?>
    La Classification enregistrée date du <?php echo $dateClassification ?> <br/><br/>
    <table width="100%">
        <tr>
            <th><?php echo $this->Paginator->sort('id', 'Id'); ?></th>
            <th>
                <?php
                if ($this->action == 'autreActesEnvoyes')
                    echo $this->Paginator->sort('num_delib', 'N° de l\'acte') . '</th>';
                else
                    echo $this->Paginator->sort('num_delib', 'N° délibération'); ?>
            </th>
            <th><?php echo $this->Paginator->sort('objet_delib', "Libellé de l'acte"); ?></th>

            <th>
                <?php
                if ($this->action == 'autreActesEnvoyes')
                    echo $this->Paginator->sort('Deliberation.date_acte', 'Date de décision');
                else
                    echo 'Date de séance';
                ?>
            </th>
            <th><?php echo $this->Paginator->sort('num_pref', 'Classification'); ?></th>
            <th>Statut TDT <?php echo $this->Html->link('<i class="fa fa-refresh"></i>', array('action'=>'majArTdt'), array('escape'=>false, 'class'=>'waiter')); ?></th>
            <th>Courriers Ministériels <?php echo $this->Html->link('<i class="fa fa-refresh"></i>', array('action'=>'majEchangesTdtAll'), array('escape'=>false, 'class'=>'waiter')); ?></th>
        </tr>
        <?php
        $numLigne = 1;
        foreach ($deliberations as $delib) {
            $rowClass = ($numLigne & 1) ? array('height' => '36px') : array('height' => '36px', 'class' => 'altrow');
            echo $this->Html->tag('tr', null, $rowClass);
            $numLigne++;

            echo '<td>';
            echo $this->Html->link($delib['Deliberation']['id'], array('action'=>'view', $delib['Deliberation']['id']));
            echo '</td>';

            echo '<td>';
            echo $this->Html->link($delib['Deliberation']['num_delib'], array('action'=>'getTampon', $delib['Deliberation']['id']));
            echo '</td>';
            ?>
            <td><?php echo $delib['Deliberation']['objet_delib']; ?></td>
            <td>
                <?php
                if ($this->action == 'autreActesEnvoyes')
                    echo $this->Form2->ukToFrenchDateWithHour($delib['Deliberation']['date_acte']);
                else
                    echo $this->Html2->ukToFrenchDateWithHour($delib['Seance']['date']);
                ?>
            </td>
            <td><?php echo $delib['Deliberation']['num_pref']; ?></td>
            <td>
                <?php
                if (isset($delib['Deliberation']['code_retour'])) {
                    if ($delib['Deliberation']['code_retour'] == 4)
                        echo $this->Html->link("Acquittement reçu le " . $delib['Deliberation']['tdt_dateAR'], array('action'=>'getBordereauTdt', $delib['Deliberation']['id']), array('title'=>'Télécharger le bordereau d\'acquittement de transaction'));
                    elseif ($delib['Deliberation']['code_retour'] == 3)
                        echo 'Transmis';
                    elseif ($delib['Deliberation']['code_retour'] == 2)
                        echo 'En attente de transmission';
                    elseif ($delib['Deliberation']['code_retour'] == 1)
                        echo 'Posté';
                }else{
                    if (!empty($delib['Deliberation']['tdt_dateAR'])){
                        echo $this->Html->link("Acquittement reçu le " . $delib['Deliberation']['tdt_dateAR'], array('action'=>'getBordereauTdt', $delib['Deliberation']['id']), array('title'=>'Télécharger le bordereau d\'acquittement de transaction'));
                    }else{
                        echo 'En attente de réception';
                    }
                }
                ?>
            </td>
            <td>
                <?php
                if (!empty($delib['TdtMessage'])) {
                    foreach ($delib['TdtMessage'] as $message) {
                            $url_newMessage = array('action'=>'downloadTdtMessage', $message['message_id']);

                        $libelle = 'Message ' . $message['message_id'];
                        if ($message['type_message'] == 2)
                            $libelle = "Courrier simple";
                        if ($message['type_message'] == 3){
                            $libelle = "Demande de pièces complémentaires";
                        }
                        if ($message['type_message'] == 4)
                            $libelle = "Lettre d'observation";
                        if ($message['type_message'] == 5)
                            $libelle = 'Déféré au tribunal administratif';
                        if (!empty($libelle)){
                            if($message['type_reponse'] == 7
                                OR $message['type_reponse'] == 8) $libelle .=' (reçu)';
                            else $libelle .=' (envoyé)';
                            echo $this->Html->link($libelle, $url_newMessage) . "<br />";
                        }
                            
                    }
                }
                ?>
            </td>
            </tr>
        <?php } ?>

    </table>
    <div class='paginate'>
        <!-- Affiche les numéros de pages -->
        <?php echo $this->Paginator->numbers(); ?>
        <!-- Affiche les liens des pages précédentes et suivantes -->
        <?php
        echo $this->Paginator->prev('« Précédent ', null, null, array('tag' => 'span', 'class' => 'disabled'));
        echo $this->Paginator->next(' Suivant »', null, null, array('tag' => 'span', 'class' => 'disabled'));
        ?>
        <!-- Affiche X de Y, où X est la page courante et Y le nombre de pages -->
        <?php echo $this->Paginator->counter(array('format' => 'Page %page% sur %pages%')); ?>
    </div>
</div>
