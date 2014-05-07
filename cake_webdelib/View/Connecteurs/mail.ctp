<div id="configSmtp">
<?php
$true_false = array('true' => 'Oui', 'false' => 'Non');
echo $this->Form->create('Connecteur', array('url' => array('controller' => 'connecteurs', 'action' => 'makeconf', 'mail')));
?>
<fieldset>
    <legend>Configuration des mails</legend>
    <?php
    echo $this->Form->input('mail_from',
                        array('type' => 'text',
                              "placeholder"=>"Exemple : 'Webdelib <webdelib@ma-collectivite.fr>",
                              'label' => false,
                              'value' => Configure::read('MAIL_FROM'),
                              'before' => '<label>Mail de expéditeur : </label>')); 
    
    ?>
       </fieldset>
    <fieldset>
        <legend>Activation du SMTP</legend>
        <?php
        echo $this->Form->input('smtp_use', array(
            'legend' => false,
            'type' => 'radio',
            'options' => $true_false,
            'value' => Configure::read('SMTP_USE') ? 'true' : 'false',
            'div' => true,
            'label' => true,
            'onChange' => 'changeActivation(this)'));
        ?>
    <div class='spacer'> </div>
    <div id='config_content' <?php echo Configure::read('SMTP_USE') === false ? 'style="display: none;"' : ''; ?>>
    <fieldset>
        <legend>Paramètrage du SMTP</legend>
<?php  
    echo $this->Form->input('smtp_host', 
                             array('type' => 'text', 
                                   "placeholder"=>"Exemple : smtp.maville.fr", 
                                   'label' => 'Serveur SMTP' , 
                                   'value' => Configure::read('SMTP_HOST')));
    echo $this->Form->input('smtp_port',
                            array('type' => 'text',
                                  "placeholder"=>"Exemple : 25",
                                  'label' => 'Port du serveur' ,
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
     echo $this->Form->input('smtp_client',
                            array('type' => 'text',
                                  "placeholder"=>"smtp_helo_hostname",
                                  'label' => false,
                                  'value' => Configure::read('SMTP_CLIENT'),
                                  'before' => '<label>Client (Helo hostname)</label>')); 
    
?>
    </fieldset>
</div>
  </fieldset>
<?php
echo $this->Html->tag('div', null, array('class' => 'btn-group', 'style' => 'margin-top:10px;'));
echo $this->Html->link('<i class="fa fa-arrow-left"></i> Annuler', array('controller' => 'connecteurs', 'action' => 'index'), array('class' => 'btn', 'escape' => false, 'title' => 'Annuler'));
echo $this->Form->button("<i class='fa fa-save'></i> Enregistrer", array('type' => 'submit', 'id' => 'boutonValider', 'class' => 'btn btn-primary', 'escape' => false, 'title' => 'Modifier la configuration'));
echo $this->Html->tag('/div', null);
echo $this->Form->end();
echo $this->Html->css('connecteurs');
?>
<script type="text/javascript">
    $(document).ready(function(){
    changeProtocol();
});
function changeActivation(element) {
    if ($(element).val() == 'true') {
        $('#config_content').show();
    } else {
        $('#config_content').hide();
    }
}
</script>