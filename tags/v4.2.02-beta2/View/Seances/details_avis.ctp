<div class="deliberations">
    <h2>Détails des projets de la séance du <?php echo $date_seance ?></h2>

    <table width='100%' cellpadding="0" cellspacing="0">
        <tr>
            <th width='5%'>Résultat</th>
            <th>Theme</th>
            <th>Service emetteur</th>
            <th>Rapporteur</th>
            <th>Libellé de l'acte</th>
            <th>Titre</th>
            <th width='4%'>Id.</th>
            <th width='120'>Actions</th>
        </tr>
        <?php foreach ($deliberations as $deliberation): ?>
            <tr height='36px'>
                <td style="text-align: center">
                    <?php
                    if ($deliberation['Deliberation']['avis'] === true)
                        echo $this->Html->image('/img/icons/thumbs_up.png', array('title' => 'Avis favorable'));
                    elseif ($deliberation['Deliberation']['avis'] === false)
                        echo $this->Html->image('/img/icons/thumbs_down.png', array('title' => 'Avis défavorable'));
                    ?>
                </td>
                <td><?php echo $deliberation['Theme']['libelle']; ?></td>
                <td><?php echo $deliberation['Service']['libelle']; ?></td>
                <td><?php echo $deliberation['Rapporteur']['nom'] . ' ' . $deliberation['Rapporteur']['prenom']; ?></td>
                <td><?php echo $deliberation['Deliberation']['objet_delib']; ?></td>
                <td><?php echo $deliberation['Deliberation']['titre']; ?></td>
                <td style="text-align: center"><?php echo $deliberation['Deliberation']['id']; ?></td>
                <td class="actions">
                    <?php
                    echo $this->Html->link(SHY,
                        array('controller' => 'seances', 'action' => 'saisirDebat', $deliberation['Deliberation']['id'], $seance_id),
                        array(
                            'class' => 'link_debat',
                            'escape' => false,
                            'title' => 'Saisir les debats'));
                    echo $this->Html->link(SHY,
                        array('controller' => 'seances', 'action' => 'donnerAvis', $deliberation['Deliberation']['id'], $seance_id),
                        array(
                            'class' => 'link_donnerAvis',
                            'escape' => false,
                            'title' => 'Donner un avis'));
                    echo $this->Html->link(SHY,
                        array('controller' => 'deliberations', 'action' => 'genereFusionToClient', $deliberation['Deliberation']['id']),
                        array(
                            'class' => 'link_pdf delib_pdf',
                            'escape' => false,
                            'title' => 'Générer le PDF du projet ' . $deliberation['Deliberation']['objet']));
                    ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
<div class="spacer"></div>
<div class="submit">
    <?php echo $this->Html->link('<i class="fa fa-arrow-left"></i> Retour',
        '/seances/listerFuturesSeances',
        array('class' => 'btn', 'escape' => false,
            'name' => 'Retour'))?>
</div>
