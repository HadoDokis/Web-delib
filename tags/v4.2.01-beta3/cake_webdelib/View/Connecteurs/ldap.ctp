<div id="configLdap">
    <?php
    $protocol = Configure::read('LDAP');
    $true_false = array('true' => 'Oui', 'false' => 'Non');
    echo $this->Form->create('Connecteur', array('url' => array('controller' => 'connecteurs', 'action' => 'makeconf', 'ldap')));
    ?>
    <fieldset>
        <legend>Activation du LDAP</legend>
        <?php
        echo $this->Form->input('use_ldap', array(
            'legend' => false,
            'type' => 'radio',
            'options' => $true_false,
            'value' => Configure::read('USE_LDAP') ? 'true' : 'false',
            'div' => true,
            'label' => true,
            'onChange' => 'changeActivation(this)'));
        ?>
    </fieldset>
    <div class='spacer'></div>
    <div id='config_content' <?php echo Configure::read('USE_LDAP') === false ? 'style="display: none;"' : ''; ?>>
        <div class='spacer'></div>
        <fieldset>
            <legend>Informations de connexion</legend>
            <?php
            echo $this->Form->input('ldap_protocol', array(
                'type' => 'select',
                'options' => $protocoles,
                'label' => 'Type d\'annaire',
                'value' => strtoupper($protocol)));
            echo $this->Form->input('ldap_host', array(
                'type' => 'text',
                'placeholder' => 'Exemple : ldap.x.x.x',
                'label' => 'Serveur LDAP : ',
                'style'=>'width: 60%',
                'value' => Configure::read('LDAP_HOST')));
                echo $this->Form->input('ldap_port', array(
                'type' => 'text',
                'placeholder'=>'Exemple : 386',
                'label' => 'port du serveur : ' ,
                'value' => Configure::read('LDAP_PORT')));
                
            echo $this->Form->input('ldap_login', array(
                'type' => 'text',
                'placeholder' => 'Nom d\'utilisateur',
                'label' => 'Login',
                'value' => Configure::read('LDAP_LOGIN')));
            echo $this->Form->input('ldap_password', array(
                'type' => 'password',
                'placeholder' => 'Mot de passe utilisateur',
                'label' => 'Mot de passe',
                'value' => Configure::read('LDAP_PASSWD')));
            echo $this->Form->input('ldap_uid', array(
                'type' => 'text',
                'label' => 'UID',
                'placeholder' => 'UID',
                'value' => Configure::read('LDAP_UID')));
            echo $this->Form->input('ldap_basedn', array(
                'type' => 'text',
                'label' => 'Base DN',
                'style'=>'width: 60%',
                'placeholder' => 'Exemple : OU=Utilisateurs,dc=mairie-xxx,dc=xxx',
                'value' => Configure::read('LDAP_BASE_DN')));
            echo $this->Form->input('ldap_suffix', array(
                'type' => 'text',
                'label' => 'Account Suffix',
                'style'=>'width: 60%',
                'placeholder' => 'Exemple :@mairie-xxx.xxx',
                'value' => Configure::read('LDAP_ACCOUNT_SUFFIX')));
            echo $this->Form->input('ldap_dn', array(
                'type' => 'text',
                'label' => 'DN',
                'placeholder' => 'Exemple : dn - distinguishedname',
                'value' => Configure::read('LDAP_DN')));
            
            ?>
            </fieldset>
        </div>
    <?php
    echo $this->Html->tag('div', null, array('class' => 'btn-group', 'style' => 'margin-top:10px;'));
    echo $this->Html->link('<i class="fa fa-arrow-left"></i> Annuler', array('controller' => 'connecteurs', 'action' => 'index'), array('class' => 'btn', 'escape' => false, 'title' => 'Annuler'));
    echo $this->Form->button("<i class='fa fa-save'></i> Enregistrer", array('type' => 'submit', 'id' => 'boutonValider', 'class' => 'btn btn-primary', 'escape' => false, 'title' => 'Modifier la configuration'));
    echo $this->Html->tag('/div', null);

    echo $this->Form->end();

    echo $this->Html->script('connecteurs/ldap');
    echo $this->Html->css('connecteurs');
    ?>
</div>
<script type="text/javascript">
    function changeActivation(element) {
        if ($(element).val() == 'true') {
            $('#config_content').show();
        } else {
            $('#config_content').hide();
        }
    }
</script>