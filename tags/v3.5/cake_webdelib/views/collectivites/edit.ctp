<h2>Modification de la collectivit&eacute;</h2>
<?php echo $form->create('Collectivite',array('url'=>'/collectivites/edit/'.$html->value('Collectivite.id'),'type'=>'file')); ?>
<div class="optional"> 
 	<?php echo $form->input('Collectivite.nom',array('label'=>'Nom de la collectivité'));?>
</div>
<br />
<div class="optional">
 		<?php echo $form->input('Collectivite.adresse', array('label'=>'Adresse','size' => '30'));?>
	<br />
 		<?php echo $form->input('Collectivite.CP', array('label'=>'Code Postal'));?>
	<br />
	 	<?php echo $form->input('Collectivite.ville', array('label'=>'Ville'));?>
</div>
<br />
<div > 
 		<?php echo $form->input('Collectivite.telephone', array('label'=>'Num téléphone'));?>
</div>
<br/><br/><br/><br/><br/>
<div class="submit">
	<?php echo $form->hidden('Collectivite.id')?>
	<?php echo $form->submit('Modifier', array('div'=>false, 'class'=>'bt_save_border', 'name'=>'Ajouter'));?>
	<?php echo $html->link('Annuler', '/collectivites/index', array('class'=>'link_annuler', 'name'=>'Annuler'))?>
</div>
<?php echo $form->end(); ?>
