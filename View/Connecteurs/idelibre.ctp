<div class='spacer'></div>
<?php
$true_false = array('true' => 'Oui', 'false' => 'Non');
echo $this->Form->create('Connecteur', array('url' => array('controller' => 'connecteurs', 'action' => 'makeconf', 'idelibre'), 'type' => 'file'));
?>
<div id="configIdelibre">
    <fieldset>
        <legend>Activation du service I-delibRE</legend>
        <?php
        echo $this->Form->input('use_idelibre', array(
            'legend' => false,
            'type' => 'radio',
            'options' => $true_false,
            'value' => Configure::read('USE_IDELIBRE') ? 'true' : 'false',
            'div' => true,
            'label' => true,
            'onChange' => 'changeActivation(this)'
        ));
        ?>
    </fieldset>
    <div class='spacer'></div>
    <div id='config_content' <?php echo Configure::read('USE_IDELIBRE') === false ? 'style="display: none;"' : ''; ?>>
        <fieldset>
            <legend>Informations d'authentification</legend>
            <?php
            echo $this->Form->input('idelibre_host', array(
                'type' => 'text',
                "placeholder" => "https://idelibre.adullact.org",
                'label' => 'URL',
                'value' => Configure::read('IDELIBRE_HOST')
            ));

            echo $this->Form->input('idelibre_conn', array(
                'type' => 'text',
                'placeholder' => 'Nom de la connexion',
                'title' => 'Nom de la variable de connexion dans le fichier database.php de i-delibRE',
                'label' => 'Connexion de la collectivité',
                'value' => Configure::read('IDELIBRE_CONN')
            ));

            echo $this->Form->input('idelibre_login', array(
                'type' => 'text',
                'placeholder' => 'Nom d\'utilisateur',
                'label' => 'Login',
                'value' => Configure::read('IDELIBRE_LOGIN')
            ));

            echo $this->Form->input('idelibre_pwd', array(
                'type' => 'password',
                'placeholder' => 'Mot de passe utilisateur',
                'label' => 'Mot de passe',
                'value' => Configure::read('IDELIBRE_PWD')));
            ?>
        </fieldset>
        <fieldset id='infos_certificat'>
            <legend>Certificat de connexion</legend>
            <?php
            echo $this->Form->input('idelibre_use_cert', array(
                'legend' => false,
                'type' => 'radio',
                'options' => $true_false,
                'value' => Configure::read('IDELIBRE_USE_CERT') ? 'true' : 'false',
                'div' => true,
                'label' => true,
                'onChange' => 'changeActivationCert(this)'
            ));
            echo $this->Html->tag('div', null, array('id' => 'idelibre_cert', 'style' => (Configure::read('IDELIBRE_USE_CERT')) ? '':'display:none'));
            echo $this->Html->tag('hr', '');
            echo $this->Form->input('clientcert', array(
                'type' => 'file',
                'label' => 'Certificat (p12)'
            ));
            echo $this->Form->input('idelibre_certpwd', array(
                'type' => 'password',
                'placeholder' => "Mot de passe du certificat",
                'value' => Configure::read('IDELIBRE_CERTPWD'),
                'label' => 'Mot de passe'));
            echo $this->Html->tag('/div');
            ?>
        </fieldset>
        <fieldset id='infos_proxy'>
            <legend>Proxy</legend>
            <?php
                echo $this->Form->input('use_proxy', array(
                    'legend' => false,
                    'type' => 'radio',
                    'options' => $true_false,
                    'value' => Configure::read('IDELIBRE_USEPROXY') ? 'true' : 'false',
                    'default' => 'false',
                    'label' => true,
                    'onClick' => "if(this.value=='true') $('#proxy_host').show(); else $('#proxy_host').hide(); "));
                ?>
                <div class='spacer'></div>
                <div id="proxy_host" <?php if (!Configure::read('IDELIBRE_USEPROXY')) echo ' style="display: none;"'; ?>>
                    <?php
                    echo $this->Form->input('proxy_host', array(
                        'type' => 'text',
                        'placeholder' => 'http://x.x.x.x:8080',
                        'value' => Configure::read('IDELIBRE_PROXYHOST'),
                        'label' => 'Adresse du proxy'));
                    ?> </div>
        </fieldset>
    </div>
</div>
<div class='spacer'></div>
<?php
echo $this->Html->tag('div', null, array('class' => 'btn-group', 'style' => 'margin-top:10px;'));
echo $this->Html->link('<i class="fa fa-arrow-left"></i> Annuler', array('controller' => 'connecteurs', 'action' => 'index'), array('class' => 'btn', 'escape' => false, 'title' => 'Annuler'));
echo $this->Form->button("<i class='fa fa-save'></i> Enregistrer", array('type' => 'submit', 'id' => 'boutonValider', 'class' => 'btn btn-primary', 'escape' => false, 'title' => 'Modifier la configuration'));
echo $this->Html->tag('/div', null);
echo $this->Form->end();
echo $this->Html->css('connecteurs');
?>
<script type="text/javascript">
function changeActivation(element) {
    if ($(element).val() == 'true') {
        $('#config_content').show();
    } else {
        $('#config_content').hide();
    }
}

function changeActivationCert(element) {
    if ($(element).val() == 'true') {
        $('#idelibre_cert').show();
    } else {
        $('#idelibre_cert').hide();
    }
}
</script>