<?php echo $this->Html->css('login'); ?>
<?php if (!empty($errorMsg)): ?>
    <div class="alert alert-error">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <strong>Erreur!</strong> <?php echo $errorMsg; ?>
    </div>
<?php endif; ?>
<div class="login_form">
    <h1><?php echo $this->Html->image('webdelib_petit.png', array('id' => 'logo', 'alt' => 'Webdelib')); ?></h1>
    <div class="spacer"></div>
    <p class="text-center"><strong>Bienvenue !</strong> Veuillez saisir votre identifiant et votre mot de passe.</p>
    <div class="spacer"></div>
    <?php echo $this->Form->create('User', array('action' => 'login', 'type' => 'post', 'class' => 'form-horizontal', 'inputDefaults' => array(
        'label' => false,
        'div' => false
    ))); ?>
    <div class="control-group">
        <label class="control-label" for="User.login">Identifiant</label>
        <div class="controls">
            <?php echo $this->Form->input('User.login', array('type' => 'text', 'escape' => false, 'placeholder' => 'Identifiant')); ?>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="User.password">Mot de passe</label>
        <div class="controls">
            <?php echo $this->Form->input('User.password', array('type' => 'password', 'escape' => false, 'placeholder' => 'Mot de passe')); ?>
        </div>
    </div>
    <div class="control-group">
        <div class="controls">
            <?php echo $this->Form->button('Connexion', array('div' => false, 'class' => 'btn')); ?>
        </div>
    </div>
    <?php echo $this->Form->end(); ?>
</div>
<script>$('input, text').placeholder();</script>


