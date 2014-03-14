<div class='spacer'></div>
<?php
echo $this->Form->create('Connecteur', array('url' => array('controller' => 'connecteurs', 'action' => 'makeconf', 'idelibre'), 'type' => 'file'));
?>
<div id="configIdelibre">
    <fieldset>
        <legend>Activation du service I-delibRE</legend>
        <?php
        echo $this->Form->input('use_idelibre', array(
            'legend' => false,
            'type' => 'radio',
            'options' => array('true' => 'Oui', 'false' => 'Non'),
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
                "placeholder" => "http://idelibre-server.ma-ville.fr",
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
                'options' => array('true' => 'Oui', 'false' => 'Non'),
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
echo $this->Html->script('connecteurs/idelibre');
?>
