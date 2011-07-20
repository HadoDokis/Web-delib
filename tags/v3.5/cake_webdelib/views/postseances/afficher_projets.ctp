<h2>Liste des projets pour la séance du <?php echo $date_seance; ?></h2>
<?php
    $urlPage =  FULL_BASE_URL . $this->webroot;
    echo ("<br />Choix du format de sortie des &eacute;ditions : ");
    $format = $session->read('user.format.sortie');
    echo $form->select('User.Sortie', array (0=>'pdf', 1=>'odt') , $format, array('id' => "$urlPage", 'onChange'=>'changeFormat(this)'),null,false);
?>
<br /><br />
<div class="deliberations">
<table>
	<tr>
		<th>Thème</th>
		<th>Rapporteur</th>
		<th>Libellé</th>
		<th>Titre</th>
		<th>Num Delib</th>
		<th>Action</th>
	</tr>
	<?php foreach($projets as $projet): ?>
    	<tr height='36px'>
    	    <td><?php echo $projet['Theme']['libelle']; ?></td>
		    <td><?php echo $projet['Rapporteur']['nom'].' '.$projet['Rapporteur']['prenom']; ?></td>
		    <td><?php echo $projet['Deliberation']['objet']; ?></td>
		    <td><?php echo $projet['Deliberation']['titre']; ?></td>
		    <td><?php echo $projet['Deliberation']['num_delib']; ?></td>
		    <td>
	           <?php 
		        if ($projet['Seance']['pv_figes'] != 1)
		            echo $html->link(SHY,'/seances/saisirDebat/' .$projet['Deliberation']['id'], array('class'=>'link_debat', 'title'=>'Saisir les debats'), false, false);
		    
		        if ($format == 0) 
			    echo $html->link(SHY, '/deliberations/downloadDelib/'.$projet['Deliberation']['id'],  array('class'=>'link_pdf', 'title'=>'PDF'), false, false);
			else
                            echo $html->link(SHY,'/models/generer/' .$projet['Deliberation']['id'].'/null/'.$projet['Model']['id'].'/null', array('class'=>'link_pdf', 'title'=>'PDF'), false, false);

			?>

		     </td>
		</tr>
		<?php endforeach; ?>
</table>
<br/>
<div class="submit">
<?php

    echo $html->link('Retour', '/postseances/index', array('class'=>'link_annuler', 'name'=>'Retour'));
    
?>
</div>
<div class="close">
<?php
    if ($projet['Seance']['pv_figes'] != 1)
        echo $html->link('Figer les débats','/postseances/changeStatus/' . $seance_id, array('class'=>'link_clore', 'name'=>'Clore', 'title'=>'Figer les débats'), 'Etes-vous sur de vouloir figer les débats ?', false);
?>
</div>
</div>
