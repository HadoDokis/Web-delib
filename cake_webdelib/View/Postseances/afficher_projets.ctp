<h2>Liste des projets pour la séance du <?php echo $date_seance; ?></h2>

<br><br>
<div class="deliberations">
    <table style='width:100%'>
        <tr>
            <th>Thème</th>
            <th>Rapporteur</th>
            <th>Libellé de l'acte</th>
            <th>Titre</th>
            <th>Num Delib</th>
            <th>Action</th>
        </tr>
        <?php foreach($projets as $projet): ?>
            <tr style='height:36px'>
                <td><?php echo $projet['Theme']['libelle']; ?></td>
                <td><?php echo $projet['Rapporteur']['nom'].' '.$projet['Rapporteur']['prenom']; ?></td>
                <td><?php echo $projet['Deliberation']['objet_delib']; ?></td>
                <td><?php echo $projet['Deliberation']['titre']; ?></td>
                <td><?php echo $projet['Deliberation']['num_delib']; ?></td>
                <td>
               <?php
                    if ($pv_figes != 1)
                        echo $this->Html->link(SHY, array('controller'=>'seances', 'action'=>'saisirDebat', $projet['Deliberation']['id'],$seance_id), array('class'=>'link_debat', 'escape' => false, 'title'=>'Saisir les debats'));
                    if ($format == 0)
                        echo $this->Html->link(SHY, array('controller'=>'deliberations', 'action'=>'downloadDelib',$projet['Deliberation']['id']), array('class'=>'link_pdf', 'escape' => false, 'title'=>'PDF'));
                    else
                        echo $this->Html->link(SHY, array('controller'=>'models','action'=>'generer', $projet['Deliberation']['id'],'null',$projet['Modeltemplate']['id'], '1', '0', 'deliberation_'.$projet['Deliberation']['num_delib'], '0', '0', '0'), array('class'=>'link_pdf waiter', 'escape' => false, 'title'=>'PDF'));
                ?>

                 </td>
            </tr>
            <?php endforeach; ?>
    </table>
    <br/>
    <div style="text-align: left; float: left">
    <?php
        echo $this->Html->link('<i class="fa fa-arrow-left"></i> Retour', array('controller'=>'postseances', 'action'=>'index'), array('class'=>'btn', 'escape' => false, 'name'=>'Retour'));
    ?>
    </div>
    <div style="text-align: right; float: right">
    <?php
        if ($pv_figes != 1){
            echo $this->Html->link('<i class="fa fa-check"></i> Figer les débats',array('controller'=>'postseances', 'action'=>'changeStatus', $seance_id), array('class'=>'btn btn-primary', 'escape' => false, 'name'=>'Clore', 'title'=>'Figer les débats'), 'Etes-vous sur de vouloir figer les débats ?', false);
        }
    ?>
    </div>
</div>
