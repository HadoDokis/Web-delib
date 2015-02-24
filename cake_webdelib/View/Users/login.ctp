<?php echo $this->Session->flash('auth'); ?>
<?php if (!empty($errorMsg)): ?>
    <div class="alert alert-danger alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <strong>Erreur!</strong> <?php echo $errorMsg; ?>
    </div>
<?php endif; ?>
<div class="login_form">
    <h1><?php echo $this->Html->image('webdelib_petit.png', array('id' => 'logo', 'alt' => 'Webdelib')); ?></h1>
    <div class="spacer"></div>
    <p class="text-center"><strong>Bienvenue !</strong> Veuillez saisir votre identifiant et votre mot de passe.</p>
    <div class="spacer"></div>
    <?php echo $this->Form->create('User', array(
                                            'action' => 'login', 
                                            'role'=>'form',
                                            'type' => 'post', 
                                            'class' => 'form-horizontal', 
                                            'inputDefaults' => array(
                                            'label' => false,
                                            'div' => false
                                   ))); ?>
    <div class="form-group">
        <label class="col-sm-offset-1 col-sm-3 control-label" for="User.login">Identifiant</label>
        <div class="col-sm-7">
            <?php echo $this->Form->input('username', array('type' => 'text', 'escape' => false, 'placeholder' => 'Identifiant', 'class'=>'form-control')); ?>
        </div>
    </div>
    
    <div class="form-group">
        <label class="col-sm-offset-1 col-sm-3 control-label" for="User.password">Mot de passe</label>
        <div class="col-sm-7">
            <?php echo $this->Form->input('password', array('type' => 'password', 'escape' => false, 'placeholder' => 'Mot de passe', 'class'=>'form-control')); ?>
        </div>
    </div>
    
    <div class="form-group">
        <div class="col-sm-offset-4 col-sm-7">
            <?php echo $this->Form->end(array('div' => false,'type'=>'submit', 'class' => 'btn btn-primary','label'=>__('Connexion'))); ?>   
        </div>
    </div>
</div>
<script>$('input, text').placeholder();</script>