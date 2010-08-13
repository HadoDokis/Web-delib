<script>
    document.getElementById("pourcentage").style.display='none';
    document.getElementById("progrbar").style.display='none';
    document.getElementById("affiche").style.display='none';
    document.getElementById("contTemp").style.display='none';
</script>
<?php echo $javascript->link('droits.js'); ?>

<h1>Gestion des droits des Profils et des Utilisateurs</h1>

<div class='inav'>
<table class='table_action' cellspacing='0' cellpadding='0' style="width:1100px;">
<?php echo $form->create('Droits',array('action'=>'edit','type'=>'post','id'=>'frmAppliquer')); ?>
<td>
	<?php echo $form->hidden('Droits.strDroits');?>
	<?php $onclick = "javascript:appliquerModifications($nbMenuControllers, $nbProfilsUsers)"; ?>
	<?php echo $form->submit('Sauvegarder', array('div'=>false, 'class'=>'bt_save_border', 'name'=>'sauvegarder', 'onclick'=>$onclick)); ?>
	&nbsp;
	<?php echo $html->link('Annuler', '/pages/gestion_utilisateurs', array('class'=>'link_annuler', 'name'=>'Annuler'), 'Voulez-vous quitter? Attention, en quittant vous perdrez vos éventuelles modifications.'); ?>
</td>
<?php echo $form->end(); ?>
<td>&nbsp;&nbsp;</td>
<td>
	<b>Filtrer les profils</b> :
	<?php echo $form->input('Droits.filtreProfil', array('label'=>'', 'options'=>$filtreProfils,'onchange' => "filtreProfil(this, $nbProfilsUsers);", 'div'=>false, 'empty'=>''));?>
	- <b>Filtrer les menus.modules</b> :
	<?php echo $form->input('Droits.filtreMenu', array('label'=>'', 'options'=>$filtreMenu,'onchange' => "filtreMenu(this, $nbMenuControllers, $nbProfilsUsers);", 'div'=>false, 'empty'=>''));?>
</td>
</table>
</div>
<br>

<table cellspacing='0' cellpadding='0' class='inav' id='tableDroits'>
	<thead>
		<?php $droits->afficheEnTeteTableau($menuControllersTree, $nbProfilsUsers); ?>
	</thead>
	<?php $droits->afficheCorpsTableau($profilsUsersTree, $structColonnes, $nbMenuControllers, $tabDroits); ?>
</table>
