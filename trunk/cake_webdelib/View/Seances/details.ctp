<div class="deliberations">
<h2>Détails des projets de la séance du <?php echo $date_seance?></h2>

<table style="width: 100%">
<tr>
    <th>Id. </th>
    <th>Etat</th>
    <th>Résultat</th>
    <th>Thème</th>
    <th>Service émetteur</th>
    <th>Rapporteur</th>
    <th>Libellé de l'acte</th>
    <th>Titre</th>
    <th>N° Délibération</th>
    <th style="min-width: 150px;">Actions</th>
</tr>
<?php
       $numLigne = 1;
       foreach ($deliberations as $deliberation):
          $rowClass = ($numLigne & 1) ? array('style' => 'height: 36px'):array( 'style' => 'height: 36px', 'class'=>'altrow');
       echo $this->Html->tag('tr', null, $rowClass);
       $numLigne++;
?>
<tr style="height: 36px," <?php if ($numLigne & 1) echo 'class="altrow"'?>>
    <td><?php echo $deliberation['Deliberation']['id']; ?></td>
	<?php
	    if ($deliberation['Deliberation']['etat']==2){
	       echo '<td>'.$this->Html->image('/img/icons/non_votee.png',  array('title'=> 'Projet validé')).'</td>';
	  		echo '<td>&nbsp;</td>';
	    }
		elseif (($deliberation['Deliberation']['etat']==0) || ($deliberation['Deliberation']['etat']==1)){
		 	echo '<td>'.$this->Html->image('/img/icons/bloque.png', array('title'=>'Projet en cours d\'élaboration')).'</td>';
			echo '<td>&nbsp;</td>';
		}
	    elseif (($deliberation['Deliberation']['etat']==3) || ($deliberation['Deliberation']['etat']==4)  || ($deliberation['Deliberation']['etat']==5)    ){
	        echo '<td>'.$this->Html->image('/img/icons/votee.png', array('title'=>'Deliberation votée')).'</td>';
	        if (($deliberation['Deliberation']['etat']==3) || ($deliberation['Deliberation']['etat']==5))
	            echo '<td>'.$this->Html->image('/img/icons/thumbs_up.png', array('title'=>'Adopté')).'</td>';
	  	    else
	    	    echo '<td>'.$this->Html->image('/img/icons/thumbs_down.png', array('title'=>'Non adopté')).'</td>';
	    }
	?>
	<td><?php echo $deliberation['Theme']['libelle']; ?></td>
	<td><?php echo $deliberation['Service']['libelle']; ?></td>
	<td><?php echo $deliberation['Rapporteur']['nom'].' '.$deliberation['Rapporteur']['prenom']; ?></td>
	<td><?php echo $deliberation['Deliberation']['objet_delib']; ?></td>
	<td><?php echo $deliberation['Deliberation']['titre']; ?></td>
    <td><?php if (!empty($deliberation['Deliberation']['num_delib'])) echo $deliberation['Deliberation']['num_delib']; ?></td>
	<td class="actions" style="width: 80px;">
            <?php echo $this->Html->link(SHY,'/seances/saisirDebat/' .$deliberation['Deliberation']['id'].'/'.$seance_id, array('class'=>'link_debat', 'escape' => false, 'title'=>'Saisir les debats'), false); ?>
            <?php
            if ( $seance['Typeseance']['action']<2 && $deliberation['Deliberation']['is_delib']) 
                echo $this->Html->link( SHY,
                                        '/seances/voter/' .$deliberation['Deliberation']['id'].'/'.$seance_id, 
                                        array('class' => 'link_voter', 
                                        'title' => 'Voter les projets',
                                        'escape' => false),
                                        false)?>
            <?php 
                echo $this->Html->link(SHY,'/models/generer/' .$deliberation['Deliberation']['id'].'/null/'.$deliberation['Model']['id'], array('class'=>'link_pdf', 'escape' => false, 'title'=>'PDF'), false);
            ?>

    </td>
</tr>
<?php endforeach; ?>
</table>

</div>
<br/>
<div class="submit">
<?php echo $this->Html->link('<i class="icon-circle-arrow-left"></i> Retour', '/seances/listerFuturesSeances', array('class'=>'btn', 'name'=>'Retour','escape'=>false))?>
</div>
