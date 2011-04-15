<?php echo $javascript->link('vote'); ?>
<?php
    if (isset($message))
        echo '<div id="flashMessage" class="message">'.$message.'</div>	'; 
?>
<cake:nocache>
<h2> Vote pour le projet : "<?php echo $deliberation['Deliberation']['objet']?>"</h2>

<h3>Liste des présents : <?php echo $form->input('Vote.listePresents', array('label'=>false, 'div'=>false, 'options'=>array(1=>'masquée', 2=>'affichée'), 'default'=>1, 'onchange' => "affichageListePresents(this);", 'empty'=>false)); ?></h3>
<div id='saisiePresents'>
	<?php echo $this->requestAction('/deliberations/listerPresents/'.$deliberation['Deliberation']['id'], array('return'));?>
</div>

<?php echo $form->create('Seances',array('url'=>'/seances/voter/'.$deliberation['Deliberation']['id'],'type'=>'post')); ?>
	<h3>Saisie du vote : <?php echo $form->input('Vote.typeVote', array('label'=>false, 'div'=>false, 'options'=>array(1 => 'Détail des voix', 2 => 'Total des voix', 3 => 'Résultat'), 'default'=>1, 'onchange' => "affichageTypeVote(this);", 'empty'=>false)); ?></h3>
	<div id='voteDetail'>
		<table id="tableDetailVote" cellpadding="0" cellspacing="0">
			<tr>
				<th>Elus</th>
				<th colspan=4>Vote</th>
			</tr>
			<tr>
				<th></th>
				<th>Oui</th>
				<th>Non</th>
				<th>Abstention</th>
				<th>Pas de participation</th>
			</tr>
			<?php foreach ($presents as $present): ?>
			<tr>
	 			<td>
		 	 	<?php
		  			echo $present['Acteur']['prenom'].' '.$present['Acteur']['nom'];
	 				if ($present['Listepresence']['mandataire'] != '0')
						echo ' (donne mandat &agrave; '.$present['Listepresence']['mandataire'].')';
		 			elseif ($present['Listepresence']['present']=='0')
		 			    echo ' (Absent)';
 			 	?>
				</td>
				<td>
				<?php
					if  ($present['Listepresence']['present'] == '1' OR $present['Listepresence']['mandataire']!='0')
						echo $form->input('detailVote.'.$present['Acteur']['id'], array('fieldset'=>false, 'label'=>false, 'legend'=>false, 'div'=>false, 'type'=>'radio', 'options'=>array('3'=>'', '2'=>'', '4'=>'', '5'=>''), 'separator'=>'</td><td>', 'onclick'=>"javascript:vote();"));
					else
						echo '</td><td></td><td></td><td>';
				?>
				</td>
			</tr>
			<?php endforeach; ?>
			<tr  bgcolor='#efefef'>
				<td>
					<?php echo $form->button('Vote.reset', array('type' => 'reset', 'value' => 'Remise à zéro'));?>
				</td>
		   		<td><input type="radio" name="global" id="oui" onclick="javascript:vote_global('3');" value="3"  .></td>
		   		<td><input type="radio" name="global" id="non" onclick="javascript:vote_global('2');" value="2"  /></td>
	   			<td><input type="radio" name="global" id="abst" onclick="javascript:vote_global('4');" value="4"  /></td>
	   			<td><input type="radio" name="global" id="sans_part" onclick="javascript:vote_global('5');" value="5"  /></td>
			</tr>

			<tr>
				<td></td>
				<td>Oui</td>
				<td>Non</td>
				<td>Abstention</td>
				<td>Pas de participation</td>
			</tr>
			<tr>
				<td>Total</td>
				<td><?php echo $form->input('Vote.res1', array('label'=>false, 'size' => '3', 'value' => '', 'disabled' => 'true'));?></td>
				<td><?php echo $form->input('Vote.res0', array('label'=>false, 'size' => '3', 'value' => '', 'disabled' => 'true'));?></td>
				<td><?php echo $form->input('Vote.res2', array('label'=>false, 'size' => '3', 'value' => '', 'disabled' => 'true'));?></td>
				<td><?php echo $form->input('Vote.res3', array('label'=>false, 'size' => '3', 'value' => '', 'disabled' => 'true'));?></td>
			</tr>
		</table>
	</div>

	<div id='voteTotal'>
		<table cellpadding="0" cellspacing="0">
			<tr>
				<th></th>
				<th>Oui</th>
				<th>Non</th>
				<th>Abstention</th>
				<th>Pas de participation</th>
			</tr>
			<tr>
				<td>Nombre total des voix</td>
				<td><?php echo $form->input('Deliberation.vote_nb_oui', array('label'=>false, 'size' => '3'));?></td>
				<td><?php echo $form->input('Deliberation.vote_nb_non', array('label'=>false, 'size' => '3'));?></td>
				<td><?php echo $form->input('Deliberation.vote_nb_abstention', array('label'=>false, 'size' => '3'));?></td>
				<td><?php echo $form->input('Deliberation.vote_nb_retrait', array('label'=>false, 'size' => '3'));?></td>
			</tr>
		</table>
	</div>

	<div id='voteResultat'>
		<?php echo $form->input('Deliberation.etat', array('fieldset'=>false, 'legend'=>false, 'label'=>false, 'div'=>false, 'type'=>'radio', 'options'=>array('3'=>'Adopté', '4'=>'Rejeté'))); ?>
	</div>

	<br/><br/>
	<div class="optional">
		<?php echo $form->input('Deliberation.vote_commentaire', array('label'=>'Commentaire', 'type'=>'textarea', 'rows'=>'5', 'cols' => '60'));?>
	</div>

	<br/>
	<div class="submit">
		<?php echo $form->submit('Enregistrer le vote', array('div'=>false, 'class'=>'bt_add', 'name'=>'modifier'));?>
		<?php echo $html->link('Annuler', '/seances/details/'.$deliberation['Deliberation']['seance_id'], array('class'=>'link_annuler', 'name'=>'Annuler'))?>
	</div>
<?php echo $form->end(); ?>

<div class='spacer'></div>

</cake:nocache>
