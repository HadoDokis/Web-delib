<?php echo $javascript->link('utils'); ?>
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
       echo $html->tag('tr', null, $rowClass);
       $numLigne++;
?>

		<!-- <td><?php echo $seance['Seance']['id']; ?></td> -->
		<td><b><?php echo $seance['Typeseance']['libelle']; ?></b></td>
		<td><?php echo $seance['Seance']['date']; ?></td>
		<td class="actions">
	  	<?php echo $html->link(SHY,'/postseances/afficherProjets/' . $seance['Seance']['id'], array('class'=>'link_voir', 'title'=>'Voir les actes'), false, false); ?>
		<?php 
		   if (($seance['Seance']['pv_figes']==1) && ($format==0)) {
                       echo $html->link(SHY,'/postseances/downloadPV/'.$seance['Seance']['id'].'/sommaire',  array('class'=>'link_pvsommaire', 'title'=>'G�n�ration du pv sommaire'), false, false);
                       echo $html->link(SHY,'/postseances/downloadPV/'.$seance['Seance']['id'].'/complet',  array('class'=>'link_pvcomplet', 'title'=>'G�n�ration du pv complet'), false, false);
		   }
		   else {
                       echo $html->link(SHY,'/models/generer/null/' . $seance['Seance']['id'].'/'.$seance['Typeseance']['modelpvsommaire_id']."/$format/0/retour/1/", array('class'=>'link_pvsommaire', 'title'=>'G�n�ration du pv sommaire'),  'Etes-vous sur de vouloir lancer la g�n�ration des documents ?', false);
                       echo $html->link(SHY,'/models/generer/null/' . $seance['Seance']['id'].'/'.$seance['Typeseance']['modelpvdetaille_id']."/$format/0/retour/1/", array('class'=>'link_pvcomplet', 'title'=>'G�n�ration du pv d�taill�'),  'Etes-vous sur de vouloir lancer la g�n�ration des documents ?', false);
		   }
                   echo $html->link(SHY,'/deliberations/toSend/' . $seance['Seance']['id'], array('class'=>'link_tdt', 
                                                                                                   'title'=>'Envoie au TdT'), false, false);
                   echo $html->link(SHY,'/postseances/sendToGed/' . $seance['Seance']['id'], array('class'=>'link_sendtoged', 
                                                                                                   'title'=>'Envoie la seance a la GED'),  
                                                                                                   'Envoie les documents � la GED',  false);
		?>
		</td>
	</tr>

	<?php endforeach; ?>

</table>
</div>
