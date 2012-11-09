<script>

    $(document).ready(function(){
         onchangeCircuitDefault();
    });

function onchangeCircuitDefault() {
    selected_default_circuit_id = $('#default_circuit').val();
    if (selected_default_circuit_id == null) 
        selected_default_circuit_id  = <?php if (is_int($selectedCircuits)) echo $selectedCircuits; else echo 'null';?>;
    $('#default_circuit').empty();
    $('#all_circuits').find("option:selected").each(function(index, element) {
        $(element).clone().appendTo('#default_circuit');
    }) ; 
    $('#default_circuit').val(selected_default_circuit_id );   
}
</script>

<?php echo $this->element('onglets', array('listeOnglets' => array(
	'Informations principales',
	'Droits', 
        'Types d\'acte'))); ?>

<?php
	if($this->Html->value('User.id')) {
		echo "<h2>Modification d'un utilisateur</h2>";
		echo $this->Form->create('User', array('url' => '/users/edit/'.$this->Html->value('User.id'),'type'=>'post', 'name' =>'userEdit'));
	}
	else {
		echo "<h2>Ajout d'un utilisateur</h2>";
		echo $this->Form->create('User', array('url' => '/users/add','type'=>'post'));
	}
?>
	<div id='tab1'>
		<fieldset>
			<legend>Identification de connexion</legend>
			<div class="tiers">
				<?php echo $this->Form->input('User.login',array('label'=>'Login <acronym title="obligatoire">*</acronym>'));?> <br />
			</div>
	<?php if(!$this->Html->value('User.id')) {
			echo "<div class='tiers'>";
		 		echo $this->Form->input('User.password',array('type'=>'password', 'label'=>'Password <acronym title="obligatoire">*</acronym>'));
			echo "</div>";
			echo "<div class='tiers'>";
		 		echo $this->Form->input('User.password2',array('type'=>'password', 'label'=>'Confirmez le password <acronym title="obligatoire">*</acronym>'));
			echo "</div>";
	} ?>
		</fieldset>

		<div class="spacer"></div>

		<fieldset>
			<legend>Identité et contacts</legend>
			<div class="demi">
	 			<?php echo $this->Form->input('User.nom', array('label'=>'Nom <acronym title="obligatoire">*</acronym>','size' => '30'));?> <br />
		 		<?php echo $this->Form->input('User.prenom', array('label'=>'Prénom <acronym title="obligatoire">*</acronym>','size' => '30'));?>
			</div>
			<div class="demi">
	 			<?php echo $this->Form->input('User.telfixe',array('label'=>'Tel fixe'));?>
				<br />
			 	<?php echo $this->Form->input('User.telmobile',array('label'=>'Tel mobile'));?>
				<br />
	 			<?php echo $this->Form->input('User.email', array('label'=>'Email','size' => '30'));?>
			</div>
		</fieldset>


		<div class="spacer"></div>

		<fieldset>
			<legend>Autres informations</legend>
			<div class="demi">
			 	<?php echo $this->Form->input('User.profil_id', array('label'=>'Profil utilisateur <acronym title="obligatoire">*</acronym>','options'=>$profils, 'empty'=>false));?>
				<br /><br />
				    <?php echo $this->Form->input('User.accept_notif', array('before'=>'<label for="UserAcceptNotif">Notification email</label>', 'legend'=>false, 'type'=>'radio', 'options'=>$notif, 'div'=>false, 'label'=>false,  'onClick'=>"if(this.value==1) $('#mails').show(); else $('#mails').hide(); " ));?>
				<br /><br />
                                 
                                <?php   
                                     if($this->data['User']['accept_notif']==0)
                                         echo ("<fieldset id='mails' style='display:none;'>"); 
                                     else 
                                         echo ("<fieldset id='mails'>"); 
                                ?>
 
			             <legend>Réception des mails</legend>
				    <?php echo $this->Form->input('User.mail_insertion', array('before'=>'<label for="UserAcceptInsertion">Insertion</label>', 'legend'=>false, 'type'=>'radio', 'options'=>$notif, 'div'=>false, 'label'=>false));?>
				<br /><br />
				    <?php echo $this->Form->input('User.mail_traitement', array('before'=>'<label for="UserAcceptTraitement">Traitement</label>', 'legend'=>false, 'type'=>'radio', 'options'=>$notif, 'div'=>false, 'label'=>false));?>
				<br /><br />
				    <?php echo $this->Form->input('User.mail_refus', array('before'=>'<label for="UserAcceptRefus">Refus</label>', 'legend'=>false, 'type'=>'radio', 'options'=>$notif, 'div'=>false, 'label'=>false));?>
                                </fieldset>
				<br /><br />
				<?php 
                                      echo $this->Form->input('Circuit.Circuit', 
                                                        array('label'=>'Circuits visibles par l\'utilisateur',
                                                              'options'=>$circuits, 
                                                              'onchange'=>'onchangeCircuitDefault();',
                                                              'multiple'=>true, 
                                                              'id'=>'all_circuits', 
                                                              'size' => 15,
                                                              'empty'=>true));

		                       echo ("<div class='spacer'></div>");

                                       echo $this->Form->input('User.circuit_defaut_id', array('type'     =>'select', 
                                                                                         'id'       => 'default_circuit', 
                                                                                         'label'    => "Circuit par défaut", 
                                                                                         'options'  => array()));
?> 
				<br /><br />
			</div>
			<div class="demi">
				 <?php
				 	if (!isset($selectedServices) && empty($selectedServices)) $selectedServices=false;
				 	echo $this->Form->input('Service.Service', array('label'=>'Service(s) <acronym title="obligatoire">*</acronym>',
                                                                                         'options'=>$services,
                                                                                         'default'=>$selectedServices,
                                                                                         'multiple' => 'multiple', 
                                                                                         'size' => '15', 
                                                                                         'class' => 'selectMultiple', 
                                                                                         'escape'=>false));
				       echo $this->Form->error('User.Service', 'Sélectionnez un ou plusieurs services');
		                       echo ("<div class='spacer'></div>");
	 			       echo $this->Form->input('User.note', array('type'=>'textarea', 'cols' => '30', 'rows' => '2'));?>
			</div>
		</fieldset>
	</div>

	<div id='tab2' style="display: none;">
		<?php
			if ($this->Html->value('User.id'))
				echo $this->element('editDroits');
			else {
				echo $this->Html->para(null, __('Sauvegardez puis &eacute;ditez &agrave; nouveau l\'utilisateur pour modifier ses droits.', true));
				echo $this->Html->para(null, __('Les nouveaux utilisateurs h&eacute;ritent des droits des profils auxquels ils sont rattach&eacute;s.', true));
			}
		?>
	</div>

<div id='tab3' style="display: none;">
    <?php 
        foreach ($natures as $nature){
            echo $this->Form->checkbox('Nature.id_'.$nature['Typeacte']['id'], array('checked'=> $nature['Nature']['check']));
            echo $nature['Typeacte']['libelle'].'<br>';
        } 
    ?>
</div>

<br/>

<div class="submit">
	<?php if ($this->action=='edit') echo $this->Form->hidden('User.id');?>
	<?php echo $this->Form->submit('Sauvegarder', array('div'=>false, 'class'=>'bt_save_border', 'name'=>'Ajouter'));?>
	<?php echo $this->Html->link('Annuler', '/users/index', array('class'=>'link_annuler', 'name'=>'Annuler'))?>
</div>
<?php echo $this->Form->end(); ?>
