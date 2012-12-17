<h2> Donner un avis pour le projet : "<?php echo $this->Html->value('Deliberation.objet')?>"</h2>
<?php echo $this->Form->create('Deliberation',array('url'=>'/seances/donnerAvis/'.$this->Html->value('Deliberation.id')."/$seance_id",
                                              'type'=>'post')); ?>

	<div class="demi">
		<?php echo $this->Form->label('Deliberation.avis', 'Donner un avis <acronym title="obligatoire">*</acronym>'); ?>
		<?php echo $this->Form->input('Deliberation.avis', array('fieldset'=>false, 'legend'=>false, 'label'=>false, 'options'=>$avis, 'type'=>'radio', 'value' => $avis_selected));?>
	<br/>
	<br/>
	</div>
	<br/>

	<div class="spacer"></div>
         <div>
	   <?php echo $this->Form->label('Deliberation.commentaire', 'Commentaire'); ?>
           <?php echo $this->Form->input('Deliberation.commentaire', array('fieldset'=>false, 
                                                                           'legend'=>false, 
                                                                           'label'=>false,  
                                                                           'value'=>$commentaire,  
                                                                           'type'=>'textarea'));?>
       </div>
	<div class="spacer"></div>

	<div class="demi" id="selectSeance">
		<?php echo $this->Form->label('Deliberation.seance_id', 'Attribuer une nouvelle séance');?>
		<?php echo $this->Form->input('Deliberation.seance_id', array('label'    => false, 
                                                                        'div'      => false, 
                                                                        'multiple' => true, 
                                                                        'options'  => $seances, 
                                                                        'selected' => $seances_selected));?>
		<br/>
		<br/>
	</div>
	<br/>

	<div class="spacer"></div>

	<br/>
	<br/>
	<div class="submit">
		<?php echo $this->Form->hidden('Deliberation.id')?>
		<?php echo $this->Form->submit('Sauvegarder', array('div'=>false,'class'=>'bt_save_border', 'name'=>'Sauvegarder'));?>
		<?php echo $this->Html->link('Annuler', '/seances/detailsAvis/'.$this->Html->value('Seance.id'), array('class'=>'link_annuler', 'name'=>'Annuler'))?>
	</div>

<?php echo $this->Form->end(); ?>
