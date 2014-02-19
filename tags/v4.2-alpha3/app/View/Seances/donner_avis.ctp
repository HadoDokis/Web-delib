<h2>Donner un avis pour le projet : "<?php echo $this->Html->value('Deliberation.objet')?>"</h2>
<?php echo $this->Form->create('Deliberation', array(
    'url'=>'/seances/donnerAvis/'.$this->Html->value('Deliberation.id')."/$seance_id",
    'type'=>'post')); ?>

	<div class="demi">
        <div class="required inline">
		<?php echo $this->Form->label('Deliberation.avis', 'Donner un avis <abbr title="obligatoire">*</abbr>'); ?>
        <?php echo $this->Form->input('Deliberation.avis', array(
            'fieldset'=>false,
            'legend'=>false,
            'label'=>false,
            'div' => false,
            'options'=>$avis,
            'type'=>'radio',
            'class'=>'btn',
            'style'=>'margin-left:5px;margin-right:5px;',
            'value' => $avis_selected)); ?>
	    </div>
    </div>
	<div class="spacer"></div>

    <div>
    <?php echo $this->Form->input('Deliberation.commentaire', array(
        'fieldset'=>false,
        'legend'=>false,
        'label'=> 'Commentaire',
        'maxlength'=> '1000',
        'value'=>$commentaire,
        'type'=>'textarea')); ?>
    </div>
	<div class="spacer"></div>

	<div class="demi" id="selectSeance">
		<?php echo $this->Form->input('Deliberation.seance_id', array(
            'label'    => 'Attribuer une nouvelle séance',
            'div'      => false,
            'multiple' => true,
            'options'  => $seances,
            'selected' => $seances_selected)); ?>
	</div>
	<div class="spacer"></div>

	<div class="submit">
		<?php echo $this->Form->hidden('Deliberation.id')?>
         <?php $this->Html2->boutonsAddCancel('', '/seances/detailsAvis/'.$seance_id); ?>
	</div>

<?php echo $this->Form->end(); ?>
