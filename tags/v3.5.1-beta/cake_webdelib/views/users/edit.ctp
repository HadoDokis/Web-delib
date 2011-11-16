<?php echo $this->element('onglets', array('listeOnglets' => array(
	'Informations principales',
	'Droits', 
        'Natures'))); ?>

<?php
	if($html->value('User.id')) {
		echo "<h2>Modification d'un utilisateur</h2>";
		echo $form->create('User', array('url' => '/users/edit/'.$html->value('User.id'),'type'=>'post'));
	}
	else {
		echo "<h2>Ajout d'un utilisateur</h2>";
		echo $form->create('User', array('url' => '/users/add','type'=>'post'));
	}
?>
	<div id='tab1'>
		<fieldset>
			<legend>Identification de connexion</legend>
			<div class="tiers">
				<?php echo $form->input('User.login',array('label'=>'Login <acronym title="obligatoire">*</acronym>'));?> <br />
			</div>
	<?php if(!$html->value('User.id')) {
			echo "<div class='tiers'>";
		 		echo $form->input('User.password',array('type'=>'password', 'label'=>'Password <acronym title="obligatoire">*</acronym>'));
			echo "</div>";
			echo "<div class='tiers'>";
		 		echo $form->input('User.password2',array('type'=>'password', 'label'=>'Confirmez le password <acronym title="obligatoire">*</acronym>'));
			echo "</div>";
	} ?>
		</fieldset>

		<div class="spacer"></div>

		<fieldset>
			<legend>Identité et contacts</legend>
			<div class="demi">
	 			<?php echo $form->input('User.nom', array('label'=>'Nom <acronym title="obligatoire">*</acronym>','size' => '30'));?> <br />
		 		<?php echo $form->input('User.prenom', array('label'=>'Prénom <acronym title="obligatoire">*</acronym>','size' => '30'));?>
			</div>
			<div class="demi">
	 			<?php echo $form->input('User.telfixe',array('label'=>'Tel fixe'));?>
				<br />
			 	<?php echo $form->input('User.telmobile',array('label'=>'Tel mobile'));?>
				<br />
	 			<?php echo $form->input('User.email', array('label'=>'Email','size' => '30'));?>
			</div>
		</fieldset>


		<div class="spacer"></div>

		<fieldset>
			<legend>Autres informations</legend>
			<div class="demi">
			 	<?php echo $form->input('User.profil_id', array('label'=>'Profil utilisateur <acronym title="obligatoire">*</acronym>','options'=>$profils, 'empty'=>''));?>
				<br /><br />
				    <?php echo $form->input('User.accept_notif', array('before'=>'<label for="UserAcceptNotif">Notification email</label>', 'legend'=>false, 'type'=>'radio', 'options'=>$notif, 'div'=>false, 'label'=>false,  'onClick'=>"if(this.value==1) $('#mails').show(); else $('#mails').hide(); " ));?>
				<br /><br />
                                 
                                <?php   
                                     if($this->data['User']['accept_notif']==0)
                                         echo ("<fieldset id='mails' style='display:none;'>"); 
                                     else 
                                         echo ("<fieldset id='mails'>"); 
                                ?>
 
			             <legend>Réception des mails</legend>
				    <?php echo $form->input('User.mail_insertion', array('before'=>'<label for="UserAcceptInsertion">Insertion</label>', 'legend'=>false, 'type'=>'radio', 'options'=>$notif, 'div'=>false, 'label'=>false));?>
				<br /><br />
				    <?php echo $form->input('User.mail_traitement', array('before'=>'<label for="UserAcceptTraitement">Traitement</label>', 'legend'=>false, 'type'=>'radio', 'options'=>$notif, 'div'=>false, 'label'=>false));?>
				<br /><br />
				    <?php echo $form->input('User.mail_refus', array('before'=>'<label for="UserAcceptRefus">Refus</label>', 'legend'=>false, 'type'=>'radio', 'options'=>$notif, 'div'=>false, 'label'=>false));?>
                                </fieldset>
				<br /><br />
				<?php echo $form->input('User.circuit_defaut_id', array('label'=>'Circuit par d&eacute;faut','options'=>$circuits, 'empty'=>true));?>
				<br /><br />
	 			<?php echo $form->input('User.note', array('type'=>'textarea', 'cols' => '30', 'rows' => '2'));?>
			</div>
			<div class="demi">
				 <?php
				 	if (!isset($selectedServices) && empty($selectedServices)) $selectedServices=false;
				 	echo $form->input('Service.Service', array('label'=>'Service(s) <acronym title="obligatoire">*</acronym>','options'=>$services,'default'=>$selectedServices,'multiple' => 'multiple', 'class' => 'selectMultiple', 'escape'=>false));
					echo $form->error('User.Service', 'Sélectionnez un ou plusieurs services');
				 ?>
			</div>
		</fieldset>
	</div>

	<div id='tab2' style="display: none;">
		<?php
			if ($html->value('User.id'))
				echo $this->element('editDroits');
			else {
				echo $html->para(null, __('Sauvegardez puis &eacute;ditez &agrave; nouveau l\'utilisateur pour modifier ses droits.', true));
				echo $html->para(null, __('Les nouveaux utilisateurs h&eacute;ritent des droits des profils auxquels ils sont rattach&eacute;s.', true));
			}
		?>
	</div>

<div id='tab3' style="display: none;">
    <?php 
        foreach ($natures as $nature){
            echo $form->checkbox('Nature.id_'.$nature['Nature']['id'], array('checked'=> $nature['Nature']['check']));
            echo $nature['Nature']['libelle'].'<br>';
        } 
    ?>
</div>

<br/>

<div class="submit">
	<?php if ($this->action=='edit') echo $form->hidden('User.id');?>
	<?php echo $form->submit('Sauvegarder', array('div'=>false, 'class'=>'bt_save_border', 'name'=>'Ajouter'));?>
	<?php echo $html->link('Annuler', '/users/index', array('class'=>'link_annuler', 'name'=>'Annuler'))?>
</div>
<?php echo $form->end(); ?>
