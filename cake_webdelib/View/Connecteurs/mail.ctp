<div id="configSmtp">
<?php
$true_false = array('true' => 'Oui', 'false' => 'Non');
echo $this->BsForm->create('Connecteur', array('url' => array('controller' => 'connecteurs', 'action' => 'makeconf', 'mail')));
?>
<fieldset>
    <legend>Configuration des mails</legend>
    <?php
    echo $this->BsForm->input('mail_from',
                        array('type' => 'text',
                              "placeholder"=>"Exemple : 'Webdelib <webdelib@ma-collectivite.fr>",
                              'label' => 'Mail de expéditeur :',
                              'value' => Configure::read('MAIL_FROM'))); 
    
    ?>
       </fieldset>
    <fieldset>
        <legend>Activation du SMTP</legend>
        <?php
        echo $this->BsForm->radio('smtp_use',$true_false, array(
            'inline'=>true,
            'legend' => false,
            'value' => Configure::read('SMTP_USE') ? 'true' : 'false',
            'div' => true,
            'onChange' => 'changeActivation(this)'));
        ?>
    <div class='spacer'> </div>
    <div id='config_content' <?php echo Configure::read('SMTP_USE') === false ? 'style="display: none;"' : ''; ?>>
    <fieldset>
        <legend>Paramètrage du SMTP</legend>
<?php  
    echo $this->BsForm->input('smtp_host', 
                             array('type' => 'text', 
                                   "placeholder"=>"Exemple : smtp.maville.fr", 
                                   'label' => 'Serveur SMTP' , 
                                   'value' => Configure::read('SMTP_HOST')));
    echo $this->BsForm->input('smtp_port',
                            array('type' => 'text',
                                  "placeholder"=>"Exemple : 25",
                                  'label' => 'Port du serveur' ,
                                  'value' => Configure::read('SMTP_PORT'))); 
    echo $this->BsForm->input('smtp_timeout',
                             array('type' => 'text',
                                   "placeholder"=>"Exemple : 30",
                                   'label'  => 'Timeout',
                                   'value' => Configure::read('SMTP_TIMEOUT'))); 
    echo $this->BsForm->input('smtp_username',
                            array('type' => 'text',
                                  "placeholder"=>"nom d'utilisateur",
                                  'label' => 'Nom d\'utilisateur',
                                  'value' => Configure::read('SMTP_USERNAME'))); 
    echo $this->BsForm->input('smtp_password',
                            array('type' => 'text',
                                  "placeholder"=>"mot de passe",
                                  'label' => 'Mot de passe',
                                  'value' => Configure::read('SMTP_PASSWORD'))); 
     echo $this->BsForm->input('smtp_client',
                            array('type' => 'text',
                                  "placeholder"=>"smtp_helo_hostname",
                                  'label' => 'Client (Helo hostname)',
                                  'value' => Configure::read('SMTP_CLIENT'))); 
    
?>
    </fieldset>
</div>
  </fieldset>
<?php
echo $this->Html2->btnSaveCancel('', array('controller' => 'connecteurs', 'action' => 'index'));
echo $this->BsForm->end();
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