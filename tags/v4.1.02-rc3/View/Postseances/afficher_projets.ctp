<h2>Liste des projets pour la séance du <?php echo $date_seance; ?></h2>

<br /><br />
<div class="deliberations">
<table width='100%'>
	<tr>
		<th>Thème</th>
		<th>Rapporteur</th>
		<th>Libellé de l'acte</th>
		<th>Titre</th>
		<th>Num Delib</th>
		<th>Action</th>
	</tr>
	<?php foreach($projets as $projet): ?>
    	<tr height='36px'>
    	    <td><?php echo $projet['Theme']['libelle']; ?></td>
		    <td><?php echo $projet['Rapporteur']['nom'].' '.$projet['Rapporteur']['prenom']; ?></td>
		    <td><?php echo $projet['Deliberation']['objet_delib']; ?></td>
		    <td><?php echo $projet['Deliberation']['titre']; ?></td>
		    <td><?php echo $projet['Deliberation']['num_delib']; ?></td>
		    <td>
	           <?php 
		        if ($pv_figes != 1)
		            echo $this->Html->link(SHY,'/seances/saisirDebat/' .$projet['Deliberation']['id'], array('class'=>'link_debat', 'escape' => false, 'title'=>'Saisir les debats'));
		    
		        if ($format == 0) 
				    echo $this->Html->link(SHY, '/deliberations/downloadDelib/'.$projet['Deliberation']['id'],  array('class'=>'link_pdf', 'escape' => false, 'title'=>'PDF'));
				else
                    echo $this->Html->link(SHY,'/models/generer/' .$projet['Deliberation']['id'].'/null/'.$projet['Model']['id']."/$format", array('class'=>'link_pdf', 'escape' => false, 'title'=>'PDF'));

			?>

		     </td>
		</tr>
		<?php endforeach; ?>
</table>
<br/>
<div class="submit">
<?php

    echo $this->Html->link('<i class="icon-circle-arrow-left"></i> Retour', '/postseances/index', array('class'=>'btn', 'escape' => false, 'name'=>'Retour'));
    
?>
</div>
<div class="close">
<?php
    if ($pv_figes != 1){
        echo $this->Html->link('<i class="icon-ok-sign"></i> Figer les débats','/postseances/changeStatus/' . $seance_id, array('class'=>'btn btn-primary', 'escape' => false, 'name'=>'Clore', 'title'=>'Figer les débats'), 'Etes-vous sur de vouloir figer les débats ?', false);
    }
        ?>
</div>
</div>
