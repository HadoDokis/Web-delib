<div class="deliberations">

<?php 
    if ((@$this->params['filtre'] != 'hide' ) &&
        ($this->params['action'] !='mesProjetsRecherche') && 
        ($this->params['action'] !='tousLesProjetsRecherche') )
        echo $this->element('filtre'); 
    echo '<h2>'.$titreVue.'</h2>';
?>

<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<th width='5%' align="right">Vue </th>
		<th width='15%' align="left">synth&eacute;tique</th>
		<th width='50%'> &nbsp;</th>
		<th width='18%' >&nbsp;</th>
		<th width='10%'>Actions</th>
	</tr>
	<tr>
		<td colspan='5' class='Border' height='1' >
		</td>
	</tr>

<?php foreach ($this->data as $deliberation){ ?>
	<tr>
		<td rowspan=3 style="text-align:center;">
		    <br />
		    <?php echo $html->image($deliberation['iconeEtat']['image'], array('title'=>$deliberation['iconeEtat']['titre']));?>
		</td>
		<td>Service &eacute;metteur :<br/><?php echo $deliberation['Service']['libelle'];?></td>
		<td><?php echo $deliberation['Deliberation']['objet'];?></td>
		<td>S&eacute;ance :<br />
		<?php
			if (in_array('attribuerSeance', $deliberation['Actions'])) {
				echo $form->create('Deliberation',array('url'=>'/deliberations/attribuerSeance','type'=>'post'));
					echo $form->input('Deliberation.seance_id',array('type'=>'select', 'label'=>'', 'options'=>$date_seances,'empty'=>true));
					echo $form->hidden('Deliberation.id',array('value'=> $deliberation['Deliberation']['id']));
					echo $form->submit(' ', array('div'=>false, 'class'=>'bt_save', 'name'=>'sauvegarder'));
				echo $form->end();
			} else {
                echo $deliberation['Seance']['libelle'].'<br />';	
				echo $deliberation['Seance']['date'];
			}
		?>
		</td>
		<td rowspan=3 class="actions">
		<?php
			if (in_array('view', $deliberation['Actions']))
				echo $html->link(SHY, '/deliberations/view/' . $deliberation['Deliberation']['id'], array('class'=>'link_voir', 'title'=>'Voir'), false, false);
			if (in_array('edit', $deliberation['Actions']))
				echo $html->link(SHY,'/deliberations/edit/' . $deliberation['Deliberation']['id'], array('class'=>'link_modifier', 'title'=>'Modifier'), false, false);
			if (in_array('delete', $deliberation['Actions']))
				echo $html->link(SHY,'/deliberations/delete/' . $deliberation['Deliberation']['id'], array('class'=>'link_supprimer', 'title'=>'Supprimer'), 'Etes-vous sur de vouloir supprimer le projet "' . $deliberation['Deliberation']['objet']. '" ?', false);
			if (in_array('traiter', $deliberation['Actions']))
				echo $html->link(SHY,"/deliberations/traiter/" . $deliberation['Deliberation']['id'], array('class'=>"link_traiter", 'title'=>'Traiter le projet de délibération'),false, false);
			if (in_array('validerEnUrgence', $deliberation['Actions']))
				echo $html->link(SHY,"/deliberations/validerEnUrgence/" . $deliberation['Deliberation']['id'], array('class'=>"link_validerenurgence", 'title'=>'Valider en urgence'), 'Confirmez-vous la validation en urgence du projet \''.$deliberation['Deliberation']['id'].'\'', false);
			
			 if (in_array('goNext', $deliberation['Actions']))
			     echo $html->link(SHY,"/deliberations/goNext/" . $deliberation['Deliberation']['id'], array('class'=>"link_jump", 'title'=>'Sauter une étape'), 'Voulez-vous vraiment sauter une étape ?', false);
			
			echo '<br/><br/>';
			if (in_array('attribuerCircuit', $deliberation['Actions'])) {
				$actionAttribuer = '/deliberations/attribuercircuit/' . $deliberation['Deliberation']['id'];
				$actionAttribuer .= $deliberation['Deliberation']['circuit_id'] ? '/'.$deliberation['Deliberation']['circuit_id'] : '';
				echo $html->link(SHY, $actionAttribuer, array('class'=>'link_circuit', 'title'=>'Attribuer un circuit'), false, false);
			}
			if (in_array('generer', $deliberation['Actions'])) {
			    if ($deliberation['Seance']['traitee']==0)
		                echo $html->link(SHY,'/models/generer/' . $deliberation['Deliberation']['id'].'/null/'. $deliberation['Model']['id'], array('class'=>'link_pdf', 'title'=>'Visionner PDF'), false, false);
			     else
			         echo $html->link(SHY, '/deliberations/downloadDelib/'.$deliberation['Deliberation']['id'], array('class'=>'link_pdf', 'title'=>'Visionner PDF'), false, false);
			}
		?>
		</td>
	</tr>
	<tr>
		<td>Circuit : <br /><?php echo $deliberation['Circuit']['libelle']; ?></td>
		<td class='corps' rowspan=1 ><?php echo $deliberation['Deliberation']['titre']; ?></td>
		<td>A traiter avant le :<br /><?php echo $deliberation['Deliberation']['date_limite']; ?></td>
	</tr>
	<tr>
		<td>projet <?php echo strtolower($deliberation['Nature']['libelle']) .' : '.$deliberation['Deliberation']['id']; ?></td>
		<td class='corps' rowspan=1 >Th&egrave;me : 
                <?php echo $deliberation['Theme']['libelle'] ?></td>
		<td>Classification : <?php echo $deliberation['Deliberation']['num_pref'];  ?></td>
	</tr>
	<tr>
		<td colspan='5' class='Border' height='1' >
		</td>
	</tr>
<?php } ?>

</table>

<?php if (!empty($listeLiens)) {
	echo '<ul class="actions">';
		if (in_array('add', $listeLiens))
			echo '<li>'.$html->link('Ajouter un projet', '/deliberations/add', array('class'=>'link_add', 'title'=>'ajouter un projet')).'</li>';
		if (in_array('mesProjetsRecherche', $listeLiens))
			echo '<li>'.$html->link('Nouvelle recherche', '/deliberations/mesProjetsRecherche', array('class'=>'link_add', 'title'=>'Nouvelle recherche parmi mes projets')).'</li>';
		if (in_array('tousLesProjetsRecherche', $listeLiens))
			echo '<li>'.$html->link('Nouvelle recherche', '/deliberations/tousLesProjetsRecherche', array('class'=>'link_add', 'title'=>'Nouvelle recherche parmi tous les projets')).'</li>';
	echo '</ul>';
} ?>

</div>
<?php $form->end(); ?>
