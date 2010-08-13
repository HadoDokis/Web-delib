<h2>Ajout d'un th&egrave;me</h2>
<?php echo $form->create('Theme', array('url' => '/themes/add/','type'=>'post')); ?>
<div class="optional">
 	<?php echo $form->input('Theme.libelle', array('label'=>'Libellé <acronym title="obligatoire">(*)</acronym>','size' => '50'));?>
</div>
<div class="optional">
        <?php echo $form->input('Theme.order', array('label'=>'Crit&egrave;re de tri','size' => '10'));?>
</div>
<div>
	<?php echo $form->input('Theme.parent_id', array('label'=>'Appartient &agrave; ', 'options'=>$themes, 'empty'=>true, 'escape'=>false))?>
</div>
<br/><br/><br/><br/>

<div class="submit">
	<?php echo $form->submit('Ajouter', array('div'=>false, 'class'=>'bt_save_border', 'name'=>'Ajouter'));?>
	<?php echo $html->link('Annuler', '/themes/index', array('class'=>'link_annuler', 'name'=>'Annuler'))?>
</div>
<?php echo $form->end(); ?>
