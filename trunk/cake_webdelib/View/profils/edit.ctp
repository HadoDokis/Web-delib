<?php echo $this->element('onglets', array('listeOnglets' => array(
	'Informations principales',
	'Droits'))); ?>
	
<h2>Modification d'un profil</h2>

<?php echo $form->create('Profil', array('controller'=>'profils','action' => 'edit','type' => 'post')); ?>

	<div id='tab1'>
		<div class="optional"> 
		 	<?php echo $form->input('Profil.libelle', array('label'=>'Libellé','size' => '60'));?>
		</div>
		<br/>
		<div>	
			<?php
				if ($selectedProfil==0) $selectedProfil='';
				echo $form->input('Profil.parent_id', array('label'=>'Appartient à', 'type'=>'select', 'options'=>$profils,'default'=>$selectedProfil, 'empty'=>''));
			?>
		</div>
	</div>
    
	
	<div id='tab2' style="display: none;">
		<?php
			echo $this->element('editDroits');
		?>
	</div>
	
<br/><br/><br/><br/><br/>

<script>
    document.getElementById("pourcentage").style.display='none';
    document.getElementById("progrbar").style.display='none';
    document.getElementById("affiche").style.display='none';
    document.getElementById("contTemp").style.display='none';
</script>

<div class="submit"> 
	<?php echo $form->hidden('Profil.id',array('label'=>'$nbsp;'))?>
	<?php echo $form->submit('Modifier', array('div'=>false,'class'=>'bt_save_border', 'name'=>'Ajouter'));?>
	<?php echo $html->link('Annuler', '/profils/index', array('class'=>'link_annuler', 'name'=>'Annuler'))?>
</div>
<?php echo $form->end(); ?>
