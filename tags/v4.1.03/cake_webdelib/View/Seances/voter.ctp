<?php echo $this->Html->script('vote'); ?>
<?php
    if (isset($message))
        echo '<div id="flashMessage" class="message">'.$message.'</div>	'; 
?>

<cake:nocache>
<h2> Vote pour le projet : "<?php echo $deliberation['Deliberation']['objet_delib']?>"</h2>

<h3>Liste des présents : <?php echo $this->Form->input('Vote.listePresents', array('label'=>false, 'div'=>false, 'options'=>array(1=>'masquée', 2=>'affichée'), 'default'=>1, 'onchange' => "affichageListePresents(this);", 'empty'=>false)); ?></h3>
<div id='saisiePresents'>
	<?php echo $this->requestAction('/deliberations/listerPresents/'.$deliberation['Deliberation']['id']."/$seance_id", array('return'));?>
</div>

<?php echo $this->Form->create('Seances',array('type'=>'post', 'url' => array('controller' => 'seances', 'action' => 'voter', $deliberation['Deliberation']['id'], $seance_id))); ?>
	<h3>Saisie du vote : <?php 
        $options = array(1 => 'Détail des voix', 2 => 'Total des voix', 3 => 'Résultat');
        echo $this->Form->select('Vote.typeVote', $options, array('label'=>false, 
                                                                    'div'=>false, 
                                                                    //'default'=>1,
            'autocomplete'=>'off',
            'value'=>2, 'onchange' => "affichageTypeVote(this);", 'empty'=>false)); ?></h3>

	<div id='voteDetail'>
		<table id="tableDetailVote">
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
			<?php 
                        foreach ($presents as $present): ?>
			<tr class=<?php echo 'typeacteur_'.$present['Acteur']['typeacteur_id']; ?> >
	 			<td>
		 	 	<?php
                                        if($present['Listepresence']['present']==true && empty($present['Listepresence']['suppleant_id']))
                                            echo $present['Acteur']['prenom'].' '.$present['Acteur']['nom'];
                                        elseif ($present['Listepresence']['present']==true && !empty($present['Listepresence']['suppleant_id']))
                                            echo $present['Listepresence']['suppleant'];
                                        elseif ($present['Listepresence']['present']==false && !empty($present['Listepresence']['mandataire'])){
                                            echo $present['Acteur']['prenom'].' '.$present['Acteur']['nom'];
                                            echo ' (donne mandat &agrave; '.$present['Listepresence']['mandataire'].')';
                                        }
	 				elseif ($present['Listepresence']['present']==false){
                                            echo $present['Acteur']['prenom'].' '.$present['Acteur']['nom'];
                                            echo ' (Absent)';
                                        }
                                        
 			 	?>
				</td>
				<td>
				<?php
					if($present['Listepresence']['present'] == false && empty($present['Listepresence']['mandataire']))
                                            echo '</td><td></td><td></td><td>';
                                        else
						echo $this->Form->input('detailVote.'.$present['Acteur']['id'], array('fieldset'=>false, 'label'=>false, 'legend'=>false, 'div'=>false, 'type'=>'radio', 'options'=>array('3'=>'', '2'=>'', '4'=>'', '5'=>''), 'separator'=>'</td><td>', 'onclick'=>"javascript:vote();"));
						
				?>
				</td>
			</tr>
			<?php endforeach; ?>
			<tr style="background-color: #efefef">
				<td>Raccourcis pour les votes</td>
				<td>Oui</td>
				<td>Non</td>
				<td>Abstention</td>
				<td>Pas de participation</td>
            </tr>
			<tr>
				<td>Tous les présents</td>
		   		<td><input type="radio" name="racc_tous" value="3" onclick="vote_global(this, 'tous');"/></td>
		   		<td><input type="radio" name="racc_tous" value="2" onclick="vote_global(this, 'tous');"/></td>
	   			<td><input type="radio" name="racc_tous" value="4" onclick="vote_global(this, 'tous');"/></td>
	   			<td><input type="radio" name="racc_tous" value="5" onclick="vote_global(this, 'tous');"/></td>
			</tr> 
                        <?php
                            foreach ($typeacteurs as $typeacteur_id => $typeacteur_nom) {
                                echo ('<tr>');
                                echo ("<td>$typeacteur_nom </td>");
				$scope = 'typeacteur_'.$typeacteur_id;
		   		echo ('<td><input type="radio" name="racc_'.$scope.'" value="3" onclick="vote_global(this, \''.$scope.'\');"/></td>');
                                echo ('<td><input type="radio" name="racc_'.$scope.'" value="2" onclick="vote_global(this, \''.$scope.'\');"/></td>');
                                echo ('<td><input type="radio" name="racc_'.$scope.'" value="4" onclick="vote_global(this, \''.$scope.'\');"/></td>');
                                echo ('<td><input type="radio" name="racc_'.$scope.'" value="5" onclick="vote_global(this, \''.$scope.'\');"/></td>');
                                echo ('</tr>');
                            }
                        ?>
			<tr>
				<td></td>
				<td>Oui</td>
				<td>Non</td>
				<td>Abstention</td>
				<td>Pas de participation</td>
			</tr>
			<tr>
				<td>Total</td>
				<td><?php echo $this->Form->input('Vote.res3', array('label'=>false, 'size' => '3', 'value' => '', 'disabled' => 'true'));?></td>
				<td><?php echo $this->Form->input('Vote.res2', array('label'=>false, 'size' => '3', 'value' => '', 'disabled' => 'true'));?></td>
				<td><?php echo $this->Form->input('Vote.res4', array('label'=>false, 'size' => '3', 'value' => '', 'disabled' => 'true'));?></td>
				<td><?php echo $this->Form->input('Vote.res5', array('label'=>false, 'size' => '3', 'value' => '', 'disabled' => 'true'));?></td>
			</tr>
		</table><br />
		<?php echo $this->Form->button('Remise à zéro des votes', array('type'=>'reset', 'onclick' => "$('#tableDetailVote input[type=radio]').removeAttr('checked');",'class'=>'btn small btn-small'));?>
	</div>

	<div id='voteTotal'>
		<table>
			<tr>
				<th></th>
				<th>Oui</th>
				<th>Non</th>
				<th>Abstention</th>
				<th>Pas de participation</th>
			</tr>
			<tr>
				<td>Nombre total des voix</td>
				<td><?php echo $this->Form->input('Deliberation.vote_nb_oui', array('label'=>false, 'size' => '3'));?></td>
				<td><?php echo $this->Form->input('Deliberation.vote_nb_non', array('label'=>false, 'size' => '3'));?></td>
				<td><?php echo $this->Form->input('Deliberation.vote_nb_abstention', array('label'=>false, 'size' => '3'));?></td>
				<td><?php echo $this->Form->input('Deliberation.vote_nb_retrait', array('label'=>false, 'size' => '3'));?></td>
			</tr>
		</table>
	</div>

	<div id='voteResultat'>
		<?php echo $this->Form->input('Deliberation.etat', array('fieldset'=>false, 'legend'=>false, 'label'=>false, 'div'=>false, 'type'=>'radio', 'options'=>array('3'=>'Adopté', '4'=>'Rejeté'))); ?>
	</div>

	<br/><br/>
	<div class="optional">
		<?php
            echo $this->Form->input('Deliberation.vote_commentaire', array('label'=>'Commentaire',
                    'style'=>'width:50%; max-width:90%; padding: 5px; max-height: 200px;',
                    'type'=>'textarea', 'rows'=>'6', 'cols' => '60', 'maxlength' => '500',
                    'after' => '<div style="display:inline-block">&nbsp;&nbsp;&nbsp;(max. <span style="display:inline-block" id="charLeft"></span>/500 caractères)</div>'));
        ?>
        <script>
            $(document).ready(function() {
                $('#charLeft').append($('#DeliberationVoteCommentaire').val().length);
                $('#DeliberationVoteCommentaire').keyup(function() {
                    var len = this.value.length;
                    if (len >= 500) {
                        this.value = this.value.substring(0, 500);
                    }
                    $('#charLeft').text(this.value.length);
                });
            });
        </script>
        </div>

	<br/>
	<div class="submit">
            <?php
            echo $this->Html->tag('div', null, array("class" => "btn-group"));
            echo $this->Html->link('<i class="icon-circle-arrow-left"></i> Annuler', '/seances/details/'.$seance_id, array('class' => 'btn', 'escape' => false, 'title' => 'Annuler', 'name' => 'Annuler'));
            echo $this->Form->button('<i class="icon-save"></i> Enregistrer le vote', array('type' => 'submit', 'name' => 'modifier', 'class' => 'btn btn-primary', 'escape' => false, 'title' => 'Enregistrer le vote'));
            echo $this->Html->tag('/div', null);
            ?>
	</div>
<?php echo $this->Form->end(); ?>

<div class='spacer'></div>

</cake:nocache>
