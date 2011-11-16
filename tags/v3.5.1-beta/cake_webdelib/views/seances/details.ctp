<div class="deliberations">
<h2>D�tails des projets de la s�ance du <?php echo $date_seance?></h2>

<table width="100%" cellpadding="0" cellspacing="0">
<tr>
    <th>Etat</th>
    <th>R�sultat</th>
    <th>Th�me</th>
    <th>Service �metteur</th>
    <th>Rapporteur</th>
    <th>Libell� de l'acte</th>
    <th>Titre</th>
    <th width='20%'>Actions</th>
</tr>
<?php
       $numLigne = 1;
       foreach ($deliberations as $deliberation):
          $rowClass = ($numLigne & 1)?array('height' => '36px'):array( 'height' => '36px', 'class'=>'altrow');
       echo $html->tag('tr', null, $rowClass);
       $numLigne++;
?>
	<?php
	    if ($deliberation['Deliberation']['etat']==2){
	       echo '<td>'.$html->image('/img/icons/non_votee.png',  array('title'=> 'Projet valid�')).'</td>';
	  		echo '<td>&nbsp;</td>';
	    }
		elseif (($deliberation['Deliberation']['etat']==0) || ($deliberation['Deliberation']['etat']==1)){
		 	echo '<td>'.$html->image('/img/icons/bloque.png', array('title'=>'Projet en cours d\'�laboration')).'</td>';
			echo '<td>&nbsp;</td>';
		}
	    elseif (($deliberation['Deliberation']['etat']==3) || ($deliberation['Deliberation']['etat']==4)  || ($deliberation['Deliberation']['etat']==5)    ){
	        echo '<td>'.$html->image('/img/icons/votee.png', array('title'=>'Deliberation vot�e')).'</td>';
	        if (($deliberation['Deliberation']['etat']==3) || ($deliberation['Deliberation']['etat']==5))
	            echo '<td>'.$html->image('/img/icons/thumbs_up.png', array('title'=>'Adopt�')).'</td>';
	  	    else
	    	    echo '<td>'.$html->image('/img/icons/thumbs_down.png', array('title'=>'Non adopt�')).'</td>';
	    }
	?>
	<td><?php echo $deliberation['Theme']['libelle']; ?></td>
	<td><?php echo $deliberation['Service']['libelle']; ?></td>
	<td><?php echo $deliberation['Rapporteur']['nom'].' '.$deliberation['Rapporteur']['prenom']; ?></td>
	<td><?php echo $deliberation['Deliberation']['objet_delib']; ?></td>
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
