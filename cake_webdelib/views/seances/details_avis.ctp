<div class="deliberations">
<h2>Détails des projets de la séance du <?php echo $date_seance?></h2>

<table cellpadding="0" cellspacing="0">
<tr>
    <th>Résultat</th>
	<th>Theme</th>
	<th>Service emetteur</th>
	<th>Rapporteur</th>
	<th>Libelle</th>
	<th>Titre</th>
	<th>Actions</th>
</tr>
<?php foreach ($deliberations as $deliberation): ?>
<tr>
	<td>
	<?php
        if ($deliberation['Deliberation']['avis']==1)
            echo $html->image('/img/icons/thumbs_up.png', array('title'=>'Avis favorable'));
  	    elseif ($deliberation['Deliberation']['avis']==2)
    	    echo $html->image('/img/icons/thumbs_down.png', array('title'=>'Avis défavorable'));
	?>
	</td>
	<td><?php echo $deliberation['Theme']['libelle']; ?></td>
	<td><?php echo $deliberation['Service']['libelle']; ?></td>
	<td><?php echo $deliberation['Rapporteur']['nom'].' '.$deliberation['Rapporteur']['prenom']; ?></td>
	<td><?php echo $deliberation['Deliberation']['objet']; ?></td>
	<td><?php //echo $deliberation['Deliberation']['titre']; ?></td>
	<td class="actions" width="80">
		<?php echo $html->link(SHY,'/seances/saisirDebat/' .$deliberation['Deliberation']['id'], array('class'=>'link_debat', 'title'=>'Saisir les debats'), false, false); ?>
 		<?php echo $html->link(SHY,'/seances/donnerAvis/' .$deliberation['Deliberation']['id'], array('class'=>'link_donnerAvis', 'title'=>'Donner un avis'), false, false)?>
               <?php 
			if (Configure::read('USE_GEDOOO'))
			    echo $html->link(SHY,'/models/generer/' . $deliberation['Deliberation']['id'].'/null/'. $deliberation['Model']['id'], array('class'=>'link_pdf', 'title'=>'Visionner PDF'), false, false);
			else 
			    echo $html->link(SHY,'/deliberations/convert/' . $deliberation['Deliberation']['id'], array('class'=>'link_pdf', 'title'=>'Visionner PDF'), false, false);
		?>

</td>
</tr>
<?php endforeach; ?>
</table>

</div>
<br/>
<div class="submit">
<?php echo $html->link('Retour', '/seances/listerFuturesSeances', array('class'=>'link_annuler', 'name'=>'Retour'))?>
</div>
