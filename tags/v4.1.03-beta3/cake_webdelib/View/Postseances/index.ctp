<?php echo $this->Html->script('utils'); ?>
<h2>S&eacute;ances </h2>
<div class="seances">

<table width='100%' cellpadding="0" cellspacing="0" border="0">
	<tr>
		<!-- <th>ID</th> -->
		<th width='40%'>Type</th>
		<th width='40%'>Date S&eacute;ance</th>
		<th width='20%'>Action</th>
	</tr>
<?php
       $numLigne = 1;
       foreach ($seances as $seance):
          $rowClass = ($numLigne & 1)?array('height' => '36px'):array( 'height' => '36px', 'class'=>'altrow');
       echo $this->Html->tag('tr', null, $rowClass);
       $numLigne++;
?>

		<!-- <td><?php echo $seance['Seance']['id']; ?></td> -->
		<td><b><?php echo $seance['Typeseance']['libelle']; ?></b></td>
		<td><?php echo $seance['Seance']['date']; ?></td>
		<td class="actions">
	  	<?php echo $this->Html->link(SHY,'/postseances/afficherProjets/' . $seance['Seance']['id'], array('class'=>'link_voir', 'escape' => false,  'title'=>'Voir les actes'), false); ?>
		<?php 
		   if (($seance['Seance']['pv_figes']==1) && ($format==0)) {
                       echo $this->Html->link(SHY,'/postseances/downloadPV/'.$seance['Seance']['id'].'/sommaire',  array('class'=>'link_pvsommaire', 'escape' => false,  'title'=>'Génération du pv sommaire'),false);
                       echo $this->Html->link(SHY,'/postseances/downloadPV/'.$seance['Seance']['id'].'/complet',  array('class'=>'link_pvcomplet',  'escape' => false, 'title'=>'Génération du pv complet'), false);
		   }
		   else {
                       echo $this->Html->link(SHY,'/models/generer/null/' . $seance['Seance']['id'].'/'.$seance['Typeseance']['modelpvsommaire_id']."/$format/0/retour/1/0/1", array('class'=>'link_pvsommaire', 'escape' => false, 'title'=>'Génération du pv sommaire'),  'Etes-vous sur de vouloir lancer la génération des documents ?');
                       echo $this->Html->link(SHY,'/models/generer/null/' . $seance['Seance']['id'].'/'.$seance['Typeseance']['modelpvdetaille_id']."/$format/0/retour/1/0/1", array('class'=>'link_pvcomplet', 'escape' => false, 'title'=>'Génération du pv détaillé'),  'Etes-vous sur de vouloir lancer la génération des documents ?');
		   }
                   echo $this->Html->link(SHY,'/deliberations/toSend/'.$seance['Seance']['id'], array('class'=>'link_tdt', 'escape' => false, 
                                                                                                   'title'=>'Envoie au TdT'), false);
                   echo $this->Html->link(SHY,'/deliberations/transmit/'.$seance['Seance']['id'], array('class'=>'link_tdt_transmit', 'escape' => false, 
                                                                                                  'title'=>'délibérations envoyees au TdT'), false);
                   if (in_array('ged', $seance['Seance']['Actions']))
                   echo $this->Html->link(SHY,'/postseances/sendToGed/' . $seance['Seance']['id'], array('class'=>'link_sendtoged',  'escape' => false, 
                                                                                                   'title'=>'Envoie la seance a la GED'),  
                                                                                                   'Envoie les documents à la GED');
		?>
		</td>
	</tr>

	<?php endforeach; ?>

</table>
</div>
