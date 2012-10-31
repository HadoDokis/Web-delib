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
<br/><br/><br/><br/>

<div class="submit">
	<?php echo $this->Form->submit('Ajouter', array('div'=>false, 'class'=>'bt_save_border', 'name'=>'Ajouter'));?>
	<?php echo $this->Html->link('Annuler', '/themes/index', array('class'=>'link_annuler', 'name'=>'Annuler'))?>
</div>
<?php echo $this->Form->end(); ?>
