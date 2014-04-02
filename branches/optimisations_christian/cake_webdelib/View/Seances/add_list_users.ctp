<h2>Sélectionner les personnes non-votantes</h2>
<?php $this->Form->create('Seance',array('url'=>'/seances/addListUsers','type'=>'post')); ?>

<div class="required">
 	<?php echo $this->Form->input('User.id', array('label'=>'Utilisateurs', 'options'=>$users, 'default'=>$selectedUsers, 'multiple' => 'multiple', 'class' => 'selectMultiple', 'empty'=>true));?>
</div>
<br/>
<div class="submit">
	<?php echo $this->Form->hidden('Seance.id');?>
	<?php echo $this->Form->submit('Ajouter', array('div'=>false,'class'=>'bt_add', 'name'=>'Ajouter'));?>
	<?php echo $this->Html->link('Annuler', '/seances/listerFuturesSeances', array('class'=>'link_annuler', 'title'=>'Annuler'))?>
</div>
<?php $this->Form->end(); ?>