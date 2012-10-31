<h2>Sélectionner les personnes non-votantes</h2>
<?php $form->create('Seance',array('url'=>'/seances/addListUsers','type'=>'post')); ?>

<div class="required">
 	<?php echo $form->input('User.id', array('label'=>'Utilisateurs', 'options'=>$users, 'default'=>$selectedUsers, 'multiple' => 'multiple', 'class' => 'selectMultiple', 'empty'=>true));?>
</div>
<br/>
<div class="submit">
	<?php echo $form->hidden('Seance.id');?>
	<?php echo $form->submit('Ajouter', array('div'=>false,'class'=>'bt_add', 'name'=>'Ajouter'));?>
	<?php echo $html->link('Annuler', '/seances/listerFuturesSeances', array('class'=>'link_annuler', 'title'=>'Annuler'))?>
</div>
<?php $form->end(); ?>
