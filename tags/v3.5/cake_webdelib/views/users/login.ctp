<p class = "error-message"><?php echo $errorMsg; ?></p>

<?php echo $form->create('User',array('action'=>'login','type'=>'post')); ?>

<div id="login_form">
	<p>
		<?php echo $form->input('User.login', array('label' => 'Identifiant','size' => '20')); ?>
	</p>
	<p>
		<?php echo $form->input('User.password', array('type'=>'password', 'label' => 'Mot de passe','size' =>'20')); ?>
	</p>
	<p class="centre">
		<?php echo $form->submit('OK', array('size'=>'20','div'=>false)); ?>
	</p>
</div>

<?php echo $form->end(); ?>
