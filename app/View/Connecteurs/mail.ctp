<div class='spacer'> </div>
<?php

if (is_null(Configure::read('SMTP_USE'))){
    echo "<strong>La directive &quot;Configure::write('SMTP_USE')&quot; est introuvable dans le fichier de configuration webdelib.inc</strong>";
    echo '<br><br>';
    echo $this->Html->link('< Retour', array('action'=>'index'), array('class'=>'btn'));
}else{

    echo $this->Form->create('Connecteur', array('url'=>'/connecteurs/makeconf/mail'));

    $notif = array('true' => 'Oui&nbsp;', 'false'=>'Non&nbsp;');
    echo $this->Form->input('smtp_use', array( 'before'  => '<label style="text-align: left;">Utilisation du SMTP de la collectivité&nbsp;</label>',
                                               'legend'  => false,
                                               'type'    => 'radio',
                                               'options' => $notif,
                                               'value'   => Configure::read('SMTP_USE') ? 'true' : 'false',
                                               'div'     => false,
                                               'default' => 'false',
                                               'label'   => false,
                                               'onClick' => "if (this.value=='true') $('#affiche').show(); else $('#affiche').hide(); " ));
 ?>
    <div class='spacer'> </div>
    <div id='affiche' <?php echo is_null(Configure::read('SMTP_USE')) || !Configure::read('SMTP_USE') ? 'style="display: none;"' : ''; ?>>
    <fieldset>
        <legend>Paramètrage du SMTP</legend>
<?php  
    echo $this->Form->input('smtp_host', 
                             array('type' => 'text', 
                                   "placeholder"=>"Exemple : smtp.maville.fr", 
                                   'label' => 'Serveur SMTP : ' , 
                                   'value' => Configure::read('SMTP_HOST')));
    echo $this->Form->input('smtp_port',
                            array('type' => 'text',
                                  "placeholder"=>"Exemple : 25",
                                  'label' => 'port du serveur : ' ,
                                  'value' => Configure::read('SMTP_PORT'))); 
    echo $this->Form->input('smtp_timeout',
                             array('type' => 'text',
                                   "placeholder"=>"Exemple : 30",
                                   'label'  => false,
                                   'before' => '<label>Timeout</label> ' ,
                                   'value' => Configure::read('SMTP_TIMEOUT'))); 
    echo $this->Form->input('smtp_username',
                            array('type' => 'text',
                                  "placeholder"=>"nom d'utilisateur",
                                  'label' => false,
                                  'value' => Configure::read('SMTP_USERNAME'),
                                  'before' => '<label>Nom d\'utilisateur</label>')); 
    echo $this->Form->input('smtp_password',
                            array('type' => 'text',
                                  "placeholder"=>"mot de passe",
                                  'label' => false,
                                  'value' => Configure::read('SMTP_PASSWORD'),
                                  'before' => '<label>Mot de passe</label>')); 
    echo $this->Form->input('mail_from',
                            array('type' => 'text',
                                  "placeholder"=>"exemple : 'Webdelib <webdelib@ma-collectivite.fr>",
                                  'label' => false,
                                  'value' => Configure::read('MAIL_FROM'),
                                  'before' => '<label>Mail de expéditeur</label>')); 
?>
    </fieldset>
</div>
    <div class='spacer'> </div>
<?php
    echo $this->Html2->boutonsSaveCancel('','/connecteurs/index');
    echo $this->Form->end();
}
?>
