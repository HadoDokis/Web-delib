<p class = "error-message"><?php echo $errorMsg; ?></p>

<?php echo $this->Form->create('User',array('action'=>'login','type'=>'post')); ?>

<div id="login_form">
		<?php echo $this->Form->input('User.login', array('label' => 'Identifiant','size' => '20')); ?>
                <br />
		<?php echo $this->Form->input('User.password', array('type'=>'password', 'label' => 'Mot de passe','size' =>'20')); ?>
		<br />
    <div id="login_submit">
		<?php echo $this->Form->submit('OK', array('size'=>'20','div'=>false)); ?>
    </div>
</div>

<?php echo $this->Form->end(); ?>
