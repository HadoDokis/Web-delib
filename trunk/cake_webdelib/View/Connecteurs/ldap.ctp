<div id="configLdap">
    <?php
    $protocol = Configure::read('LDAP');
    $true_false = array('true' => 'Oui', 'false' => 'Non');
    echo $this->BsForm->create('Connecteur', array('url' => array('controller' => 'connecteurs', 'action' => 'makeconf', 'ldap')));
    ?>
    <fieldset>
        <legend>Activation du LDAP</legend>
        <?php
        echo $this->BsForm->radio('use_ldap',$true_false, array(
            'value' => Configure::read('USE_LDAP') ? 'true' : 'false',
            'onChange' => 'changeActivation(this)'));
        ?>
    </fieldset>
    <div class='spacer'></div>
    <div id='config_content' <?php echo Configure::read('USE_LDAP') === false ? 'style="display: none;"' : ''; ?>>
        <div class='spacer'></div>
        <fieldset>
            <legend>Informations de connexion</legend>
            <?php
            echo $this->BsForm->select('ldap_protocol',$protocoles, array(
                'type' => 'select',
                'label' => 'Type d\'annaire',
                'value' => strtoupper($protocol)));
            echo $this->BsForm->input('ldap_host', array(
                'type' => 'text',
                'placeholder' => 'Exemple : ldap.x.x.x',
                'label' => 'Serveur LDAP : ',
                'value' => Configure::read('LDAP_HOST')));
            echo $this->BsForm->input('ldap_port', array(
                'type' => 'text',
                'placeholder'=>'Exemple : 389',
                'label' => 'port du serveur : ' ,
                'value' => Configure::read('LDAP_PORT')));
                
            echo $this->BsForm->input('ldap_login', array(
                'type' => 'text',
                'placeholder' => 'Nom d\'utilisateur',
                'label' => 'Login',
                'value' => Configure::read('LDAP_LOGIN')));
            echo $this->BsForm->input('ldap_password', array(
                'type' => 'password',
                'placeholder' => 'Mot de passe utilisateur',
                'label' => 'Mot de passe',
                'value' => Configure::read('LDAP_PASSWD')));
            echo $this->BsForm->input('ldap_uid', array(
                'type' => 'text',
                'label' => 'UID',
                'placeholder' => 'UID',
                'value' => Configure::read('LDAP_UID')));
            echo $this->BsForm->input('ldap_basedn', array(
                'type' => 'text',
                'label' => 'Base DN',
                'placeholder' => 'Exemple : OU=Utilisateurs,dc=mairie-xxx,dc=xxx',
                'value' => Configure::read('LDAP_BASE_DN')));
            echo $this->BsForm->input('ldap_suffix', array(
                'type' => 'text',
                'label' => 'Account Suffix',
                'placeholder' => 'Exemple :@mairie-xxx.xxx',
                'value' => Configure::read('LDAP_ACCOUNT_SUFFIX')));
            echo $this->BsForm->input('ldap_dn', array(
                'type' => 'text',
                'label' => 'DN',
                'placeholder' => 'Exemple : dn - distinguishedname',
                'value' => Configure::read('LDAP_DN')));
            
            ?>
            </fieldset>
        </div>
    <?php
echo $this->Html2->btnSaveCancel('', array('controller' => 'connecteurs', 'action' => 'index'));
echo $this->BsForm->end();
echo $this->Html->script('connecteurs/ldap');
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