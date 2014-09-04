    <?php
    $true_false = array('true' => 'Oui', 'false' => 'Non');
    echo $this->BsForm->create('Connecteur', array('url' => array('controller' => 'connecteurs', 'action' => 'makeconf', 'signature'), 'type' => 'file'));
    ?>
    <fieldset>
        <legend>Activation de la signature électronique</legend>
        <?php
        echo $this->BsForm->radio('use_signature', $true_false, array(
            'legend' => false,
            'value' => Configure::read('USE_PARAPHEUR') ? 'true' : 'false',
            'onChange' => 'changeActivation(this)'));
        ?>
    </fieldset>
    <div class='spacer'></div>
    <div id='config_content' <?php echo Configure::read('USE_PARAPHEUR') === false ? 'style="display: none;"' : ''; ?>>
        <fieldset>
            <legend>Connecteur de signature</legend>
            <?php
            echo $this->BsForm->input('signature_protocol', array(
                'type' => 'select',
                'options' => $protocoles,
                'label' => 'Type de connecteur',
                'onChange' => 'changeProtocol()',
                'value' => Configure::read('PARAPHEUR')));
            ?>
        </fieldset>
        <div class='spacer'></div>
        <fieldset class='pastell-infos'>
            <legend>Configuration des flux</legend>
            <?php
            echo $this->BsForm->input('pastell_parapheur_type', array(
                'type' => 'text',
                'label' => 'Type technique (Parapheur)',
                'placeholder' => 'Actes',
                'value' => Configure::read('PASTELL_PARAPHEUR_TYPE'),
            ));
            ?>
        </fieldset>
        <fieldset class="iparapheur-infos">
            <legend>Informations d'authentification</legend>
            <?php
            echo $this->BsForm->input('host', array(
                'type' => 'text',
                'placeholder' => 'https://i-parapheur.x.x.org',
                'label' => 'URL',
                'value' => Configure::read('IPARAPHEUR_HOST')));

            echo $this->BsForm->input('login', array(
                'type' => 'text',
                'placeholder' => 'Nom d\'utilisateur',
                'label' => 'Login',
                'value' => Configure::read('IPARAPHEUR_LOGIN')));

            echo $this->BsForm->input('pwd', array(
                'type' => 'password',
                'placeholder' => 'Mot de passe utilisateur',
                'label' => 'Mot de passe',
                'value' => Configure::read('IPARAPHEUR_PWD')));
            ?>
        </fieldset>
        <fieldset class='iparapheur-infos'>
            <legend>Configuration des flux</legend>
            <?php
            echo $this->BsForm->input('type', array(
                'type' => 'text',
                'label' => 'Type technique (Parapheur)',
                'placeholder' => 'Actes',
                'value' => Configure::read('IPARAPHEUR_TYPE'),
            ));
            ?>
        </fieldset>
        <fieldset class='iparapheur-infos'>
            <legend>Certificat d'authentification</legend>
            <?php
            echo $this->BsForm->input('clientcert', array(
                'type' => 'file',
                'label' => 'Certificat (p12)'
            ));
            echo $this->BsForm->input('certpwd', array(
                'type' => 'password',
                'placeholder' => "Mot de passe du certificat",
                'value' => Configure::read('IPARAPHEUR_CERTPWD'),
                'label' => 'Mot de passe'));
            ?>
        </fieldset>
    </div>
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

function changeProtocol() {
    var protocol = $('#ConnecteurSignatureProtocol').val();
    if (protocol == 'PASTELL') {
        $('.pastell-infos').show();
    } else {
        $('.pastell-infos').hide();
    }
    if (protocol == 'IPARAPHEUR') {
        $('.iparapheur-infos').show();
    } else {
        $('.iparapheur-infos').hide();
    }
}
</script>