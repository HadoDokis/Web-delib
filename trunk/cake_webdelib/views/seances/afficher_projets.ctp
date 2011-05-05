<h2>Liste des projets pour la séance du <?php echo $date_seance; ?></h2>
<table>
	<tr>
		<th width='4%'>Ordre</th>
		<?php echo ('<th width="13%">'.$html->link('Thème', "/deliberations/sortby/$seance_id/theme_id",null,'Etes-vous sur de vouloir trier par theme ?'). "</th>"); ?>
		<?php echo ('<th width="13%">'.$html->link('Service émetteur', "/deliberations/sortby/$seance_id/service_id",null,'Etes-vous sur de vouloir trier par service ?'). "</th>"); ?>
		<?php echo ('<th width="5%">'.$html->link('Rapporteur', "/deliberations/sortby/$seance_id/rapporteur_id", null,'Etes-vous sur de vouloir trier par rapporteur ?'). "</th>"); ?>
		<?php echo ('<th>'.$html->link('Libellé', "/deliberations/sortby/$seance_id/objet",null,'Etes-vous sur de vouloir trier par libelle ?'). "</th>"); ?>
		<?php echo ('<th width="10%">'.$html->link('Titre', "/deliberations/sortby/$seance_id/titre",null,'Etes-vous sur de vouloir trier par titre ?'). "</th>"); ?>
		<th width='4%'>Id.</th>
		<th width='2%'>&nbsp;&nbsp;</th>
		<th width='2%'>&nbsp;&nbsp;</th>
	</tr>

	<?php foreach($projets as $projet):
	$id_delib=$projet['Deliberation']['id'];
	$urlPage =  FULL_BASE_URL . $this->webroot;?>

    	<tr>
    	    <td><?php 
	            //echo $projet['Deliberation']['position']; 
		    echo $form->input('Deliberation.position', array('options'=>$lst_pos, 'selected'=>$projet['Deliberation']['position'], 'id'=>"$urlPage", 'onChange'=>"changePosition(this,$id_delib)", 'empty'=>false, 'label'=>false, 'div'=>false));
		 ?>
            </td>
            <td><?php echo '['.$projet['Theme']['order'].'] '.$projet['Theme']['libelle']; ?></td>
	    <td><?php echo $projet['Service']['libelle']; ?></td>
	    <td><?php echo $form->input('Deliberation.rapporteur_id', array('label'=>false, 'options'=>$rapporteurs, 'default'=>$projet['Deliberation']['rapporteur_id'], 'id'=>"$urlPage",'onChange'=>"changeRapporteur(this,$id_delib)", 'empty'=>empty($projet['Deliberation']['rapporteur_id'])));?></td>
	    <td><?php echo $projet['Deliberation']['objet']; ?></td>
	    <td><?php echo $projet['Deliberation']['titre']; ?></td>
	    <td><?php echo $projet['Deliberation']['id']; ?></td>
	    <?php
	        if($projet['Deliberation']['position']!= 1)
	            echo ('<td>'.$html->link(SHY, '/deliberations/positionner/'.$projet['Deliberation']['id'].'/-1', array('class'=>'link_monter', 'title'=>'Monter'), false, false).'</td>');
	        else
	           echo("<td>&nbsp;</td>");
			if($projet['Deliberation']['position']!= $lastPosition)
	                    echo ('<td>'.$html->link(SHY, '/deliberations/positionner/'.$projet['Deliberation']['id'].'/1', array('class'=>'link_descendre', 'title'=>'Descendre'), false, false).'</td>');
		        else
		            echo("<td>&nbsp;</td>");
             ?>
	</tr>
	<?php endforeach; ?>
</table>
<br/>
<div class="submit">
<?php echo $html->link('Retour', '/seances/listerFuturesSeances', array('class'=>'link_annuler', 'name'=>'Retour'))?>
</div>
