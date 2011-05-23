<?php echo $form->create('Seances',array('url'=>'/seances/saisirSecretaire/'.$seance_id,'type'=>'post')); ?>
	<fieldset>
	<legend>Choix du Pr&eacute;sident de s&eacute;ance</legend>
		<?php echo $form->input('Acteur.president_id', array('label'=>'Pr&eacute;sident de s&eacute;ance', 
                                                                     'options'=>$acteurs, 
                                                                     'default'=>$selectedPresident, 
                                                                     'empty'=>true, 
                                                                     'div'=>false));?>
	</fieldset>
       <br />
	<fieldset>
	<legend>Choix du secr&eacute;taire de s&eacute;ance</legend>
		<?php echo $form->input('Acteur.secretaire_id', array('label'=>'Secr&eacute;taire de s&eacute;ance', 
                                                                      'options'=>$acteurs, 
                                                                      'default'=>$selectedActeurs, 
                                                                      'empty'=>true, 
                                                                      'div'=>false));?>
	</fieldset>
       <br />
    <?php echo $form->submit('Sauvegarder', array('div'=>false, 'class'=>'bt_save_border', 'name'=>'Sauvegarder'));?>
<?php echo $form->end(); ?>

