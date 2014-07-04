<h2>Post-séances </h2>
<table class="table table-striped">
    <thead>
    <tr>
        <th width="50%">Type de séance</th>
        <th width="20%">Date de la séance</th>
        <th width="30%">Actions</th>
    </tr>
    </thead>
    <tbody>
    <?php
    $numLigne = 1;
    foreach ($seances as $seance):
        echo $this->Html->tag('tr', null);
        $numLigne++;
        ?>
        <td><b><?php echo $seance['Typeseance']['libelle']; ?></b></td>
        <td><?php echo $seance['Seance']['date']; ?></td>
        <td class="actions">
            <?php echo $this->Html->link(SHY, '/postseances/afficherProjets/' . $seance['Seance']['id'], array('class' => 'link_voir', 'escape' => false, 'title' => 'Voir les actes'), false); ?>
            <?php
            if (($seance['Seance']['pv_figes'] == 1) && ($format == 0)) {
                echo $this->Html->link(SHY, '/postseances/downloadPV/' . $seance['Seance']['id'] . '/sommaire', array('class' => 'link_pvsommaire', 'escape' => false, 'title' => 'Génération du pv sommaire'), false);
                echo $this->Html->link(SHY, '/postseances/downloadPV/' . $seance['Seance']['id'] . '/complet', array('class' => 'link_pvcomplet', 'escape' => false, 'title' => 'Génération du pv complet'), false);
            } else {
                echo $this->Html->link(SHY,
                    array('controller' => 'seances', 'action' => 'genereFusionToClient', $seance['Seance']['id'], 'pvsommaire'),
                    array(
                        'class' => 'link_pvsommaire waiter',
                        'data-modal' => 'Génération du PV sommaire en cours',
                        'title' => 'Nouvelle méthode génération du pv sommaire pour la séance du ' . $seance['Seance']['date'],
                        'escape' => false,
                    ));
                echo $this->Html->link(SHY,
                    array('controller' => 'seances', 'action' => 'genereFusionToClient', $seance['Seance']['id'], 'pvdetaille'),
                    array(
                        'class' => 'link_pvcomplet waiter',
                        'escape' => false,
                        'data-modal' => 'Génération du PV complet en cours',
                        'title' => 'Nouvelle méthode génération du pv complet pour la séance du ' . $seance['Seance']['date'],
                    ));
            }
            if ($use_tdt) {
                echo $this->Html->link(SHY, '/deliberations/toSend/' . $seance['Seance']['id'], array(
                    'class' => 'link_tdt',
                    'escape' => false,
                    'title' => 'Envoie au TdT'), false);
                echo $this->Html->link(SHY, '/deliberations/transmit/' . $seance['Seance']['id'], array(
                    'class' => 'link_tdt_transmit',
                    'escape' => false,
                    'title' => 'délibérations envoyees au TdT'), false);
            }
            if (in_array('ged', $seance['Seance']['Actions']))
                echo $this->Html->link(SHY, '/postseances/sendToGed/' . $seance['Seance']['id'], array(
                        'class' => 'link_sendtoged',
                        'escape' => false,
                        'title' => 'Envoie la seance a la GED'),
                    'Envoyer les documents à la GED ?');
            ?>
        </td>
        </tr>

    <?php endforeach; ?>
    </tbody>
</table>