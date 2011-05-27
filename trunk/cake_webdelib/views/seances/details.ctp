<div class="deliberations">
<h2>Détails des projets de la séance du <?php echo $date_seance?></h2>

<table width="100%" cellpadding="0" cellspacing="0">
<tr>
    <th>Etat</th>
    <th>Résultat</th>
    <th>Thème</th>
    <th>Service émetteur</th>
    <th>Rapporteur</th>
    <th>Libellé</th>
    <th>Titre</th>
    <th width='20%'>Actions</th>
</tr>
<?php foreach ($deliberations as $deliberation): ?>
<tr height="36px">
	<?php
	    if ($deliberation['Deliberation']['etat']==2){
	       echo '<td>'.$html->image('/img/icons/non_votee.png',  array('title'=> 'Projet validé')).'</td>';
	  		echo '<td>&nbsp;</td>';
	    }
		elseif (($deliberation['Deliberation']['etat']==0) || ($deliberation['Deliberation']['etat']==1)){
		 	echo '<td>'.$html->image('/img/icons/bloque.png', array('title'=>'Projet en cours d\'élaboration')).'</td>';
			echo '<td>&nbsp;</td>';
		}
	    elseif (($deliberation['Deliberation']['etat']==3) || ($deliberation['Deliberation']['etat']==4)  || ($deliberation['Deliberation']['etat']==5)    ){
	        echo '<td>'.$html->image('/img/icons/votee.png', array('title'=>'Deliberation votée')).'</td>';
	        if (($deliberation['Deliberation']['etat']==3) || ($deliberation['Deliberation']['etat']==5))
	            echo '<td>'.$html->image('/img/icons/thumbs_up.png', array('title'=>'Adopté')).'</td>';
	  	    else
	    	    echo '<td>'.$html->image('/img/icons/thumbs_down.png', array('title'=>'Non adopté')).'</td>';
	    }
	?>
	<td><?php echo $deliberation['Theme']['libelle']; ?></td>
	<td><?php echo $deliberation['Service']['libelle']; ?></td>
	<td><?php echo $deliberation['Rapporteur']['nom'].' '.$deliberation['Rapporteur']['prenom']; ?></td>
	<td><?php echo $deliberation['Deliberation']['objet']; ?></td>
	<td><?php echo $deliberation['Deliberation']['titre']; ?></td>
	<td class="actions" width="80">
		<?php echo $html->link(SHY,'/seances/saisirDebat/' .$deliberation['Deliberation']['id'], array('class'=>'link_debat', 'title'=>'Saisir les debats'), false, false); ?>
		<?php 
		if (!$USE_GEDOOO)
		    echo $html->link(SHY,'/deliberations/convert/' .$deliberation['Deliberation']['id'], array('class'=>'link_pdf', 'title'=>'PDF'), false, false);
		else
		    echo $html->link(SHY,'/models/generer/' .$deliberation['Deliberation']['id'].'/null/'.$deliberation['Model']['id'], array('class'=>'link_pdf', 'title'=>'PDF'), false, false);
		    ?>
	    <?php
            if ($deliberation['Deliberation']['nature_id'] ==1) 
            echo $html->link(SHY,'/seances/voter/' .$deliberation['Deliberation']['id'], array('class'=>'link_voter', 'title'=>'Voter les projets'), false, false)?>
	</td>
</tr>
<?php endforeach; ?>
</table>

</div>
<br/>
<div class="submit">
<?php echo $html->link('Retour', '/seances/listerFuturesSeances', array('class'=>'link_annuler', 'name'=>'Retour'))?>
</div>
