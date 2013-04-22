<div class="deliberations">
<h2>Détails des projets de la séance du <?php echo $date_seance?></h2>

<table width='100%' cellpadding="0" cellspacing="0">
<tr>
    <th>Résultat</th>
	<th>Theme</th>
	<th>Service emetteur</th>
	<th>Rapporteur</th>
	<th>Libellé de l'acte</th>
	<th>Titre</th>
	<th width='20%'>Actions</th>
</tr>
<?php foreach ($deliberations as $deliberation): ?>
<tr height='36px'>
	<td>
	<?php
        if ($deliberation['Deliberation']['avis']==1)
            echo $this->Html->image('/img/icons/thumbs_up.png', array('title'=>'Avis favorable'));
  	    elseif ($deliberation['Deliberation']['avis']==2)
    	    echo $this->Html->image('/img/icons/thumbs_down.png', array('title'=>'Avis défavorable'));
	?>
	</td>
	<td><?php echo $deliberation['Theme']['libelle']; ?></td>
	<td><?php echo $deliberation['Service']['libelle']; ?></td>
	<td><?php echo $deliberation['Rapporteur']['nom'].' '.$deliberation['Rapporteur']['prenom']; ?></td>
	<td><?php echo $deliberation['Deliberation']['objet_delib']; ?></td>
	<td><?php //echo $deliberation['Deliberation']['titre']; ?></td>
	<td class="actions" width="80">
		<?php echo $this->Html->link(SHY,
                                       '/seances/saisirDebat/'.$deliberation['Deliberation']['id'], 
                                       array('class'=>'link_debat', 
                                             'escape' => false,
                                             'title'=>'Saisir les debats'), 
                                       false); ?>
 		<?php echo $this->Html->link(SHY,
                                       '/seances/donnerAvis/'.$deliberation['Deliberation']['id']."/$seance_id", 
                                       array('class'=>'link_donnerAvis', 
                                             'escape' => false,
                                             'title'=>'Donner un avis'), 
                                       false)?>
               <?php 
			if (Configure::read('USE_GEDOOO'))
			    echo $this->Html->link(SHY,
                                             '/models/generer/'.$deliberation['Deliberation']['id'].'/null/'.$deliberation['Model']['id'], 
                                             array('class'=>'link_pdf', 
                                                   'escape' => false,
                                                   'title'=>'Visionner PDF'), 
                                             false);
			else 
			    echo $this->Html->link(SHY, 
                                             '/deliberations/convert/'.$deliberation['Deliberation']['id'], 
                                             array('class'=>'link_pdf',  
                                                   'escape' => false,
                                                   'title'=>'Visionner PDF'), 
                                             false);
		?>

</td>
</tr>
<?php endforeach; ?>
</table>

</div>
<br/>
<div class="submit">
    <?php echo $this->Html->link('<i class="icon-circle-arrow-left"></i> Retour', 
                           '/seances/listerFuturesSeances', 
                           array('class'=>'btn', 'escape' => false,
                                 'name'=>'Retour'))?>
</div>
