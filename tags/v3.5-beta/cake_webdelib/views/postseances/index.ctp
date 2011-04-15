<?php echo $javascript->link('utils'); ?>
<h2>S&eacute;ances </h2>
<?php    
    $urlPage =  FULL_BASE_URL . $this->webroot;
    echo ("<br />Choix du format de sortie des &eacute;ditions : ");
    $format = $session->read('user.format.sortie');
    echo $form->input('User.Sortie', array('type'=>'select', 'options'=>array(0=>'pdf', 1=>'odt'), 'default'=>$format, 'id' => "$urlPage", 'onChange'=>'changeFormat(this)','empty'=>false));
?>
<br /><br />
<div class="seances">

<table cellpadding="0" cellspacing="0" border="0">
	<tr>
		<!-- <th>ID</th> -->
		<th>Type</th>
		<th>Date S&eacute;ance</th>
		<th width='150px'>Action</th>
	</tr>
	<?php foreach ($seances as $seance): ?>
	<tr>
		<!-- <td><?php echo $seance['Seance']['id']; ?></td> -->
		<td><?php echo $seance['Typeseance']['libelle']; ?></td>
		<td><?php echo $seance['Seance']['date']; ?></td>
		<td class="actions" width="80px"> <!-- largeur en fonction des icones -->
	  	<?php echo $html->link(SHY,'/postseances/afficherProjets/' . $seance['Seance']['id'], array('class'=>'link_voir', 'title'=>'Voir les délibérations'), false, false); ?>
		<?php 
		   if ($seance['Seance']['pv_figes']==1) {
                       echo $html->link(SHY,'/postseances/downloadPV/'.$seance['Seance']['id'].'/sommaire',  array('class'=>'link_pvsommaire', 'title'=>'Génération du pv sommaire'), false, false);
                       echo $html->link(SHY,'/postseances/downloadPV/'.$seance['Seance']['id'].'/complet',  array('class'=>'link_pvcomplet', 'title'=>'Génération du pv complet'), false, false);
		   }
		   else {
                       echo $html->link(SHY,'/models/generer/null/' . $seance['Seance']['id'].'/'.$seance['Typeseance']['modelpvsommaire_id'].'/null/0/retour/1/', array('class'=>'link_pvsommaire', 'title'=>'Génération du pv sommaire'),  'Etes-vous sur de vouloir lancer la génération des documents ?', false);
                       echo $html->link(SHY,'/models/generer/null/' . $seance['Seance']['id'].'/'.$seance['Typeseance']['modelpvdetaille_id'].'/null/0/retour/1/', array('class'=>'link_pvcomplet', 'title'=>'Génération du pv détaillé'),  'Etes-vous sur de vouloir lancer la génération des documents ?', false);
		   }
                   echo $html->link(SHY,'/postseances/sendToGed/' . $seance['Seance']['id'], array('class'=>'link_sendtoged', 
                                                                                                   'title'=>'Envoie la seance a la GED'),  
                                                                                                   'Envoie les documents à la GED',  false);
		?>
		</td>
	</tr>

	<?php endforeach; ?>

</table>
</div>
