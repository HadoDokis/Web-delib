<h2>Ajout d'un th&egrave;me</h2>
<?php echo $this->Form->create('Theme', array('url' => '/themes/add/','type'=>'post')); ?>
<div class="optional">
 	<?php echo $this->Form->input('Theme.libelle', array('label'=>'Libellé <acronym title="obligatoire">(*)</acronym>','size' => '50', 'maxlength' => '500'));?>
</div>
<div class="optional">
        <?php echo $this->Form->input('Theme.order', array('label'=>'Crit&egrave;re de tri','size' => '10'));?>
</div>
<div>
	<?php echo $this->Form->input('Theme.parent_id', array('label'=>'Appartient &agrave; ', 'options'=>$themes, 'empty'=>true, 'escape'=>false))?>
</div>

<div class="submit">
    <?php $this->Html2->boutonsAddCancel(); ?>
</div>
<?php echo $this->Form->end(); ?>