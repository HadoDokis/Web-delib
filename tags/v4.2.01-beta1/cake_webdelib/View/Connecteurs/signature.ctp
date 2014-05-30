<div id="configSignature">
    <?php
    $true_false = array('true' => 'Oui', 'false' => 'Non');
    echo $this->Form->create('Connecteur', array('url' => array('controller' => 'connecteurs', 'action' => 'makeconf', 'signature'), 'type' => 'file'));
    ?>
    <fieldset>
        <legend>Activation de la signature électronique</legend>
        <?php
        echo $this->Form->input('use_signature', array(
            'legend' => false,
            'type' => 'radio',
            'options' => $true_false,
            'value' => Configure::read('USE_PARAPHEUR') ? 'true' : 'false',
            'div' => true,
            'label' => true,
            'onChange' => 'changeActivation(this)'));
        ?>
    </fieldset>
    <div class='spacer'></div>
    <div id='config_content' <?php echo Configure::read('USE_PARAPHEUR') === false ? 'style="display: none;"' : ''; ?>>
        <fieldset>
            <legend>Connecteur de signature</legend>
            <?php
            echo $this->Form->input('signature_protocol', array(
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
            echo $this->Form->input('pastell_parapheur_type', array(
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
            echo $this->Form->input('host', array(
                'type' => 'text',
                'placeholder' => 'https://i-parapheur.x.x.org',
                'label' => 'URL',
                'value' => Configure::read('IPARAPHEUR_HOST')));

            echo $this->Form->input('login', array(
                'type' => 'text',
                'placeholder' => 'Nom d\'utilisateur',
                'label' => 'Login',
                'value' => Configure::read('IPARAPHEUR_LOGIN')));

            echo $this->Form->input('pwd', array(
                'type' => 'password',
                'placeholder' => 'Mot de passe utilisateur',
                'label' => 'Mot de passe',
                'value' => Configure::read('IPARAPHEUR_PWD')));
            ?>
        </fieldset>
        <fieldset class='iparapheur-infos'>
            <legend>Configuration des flux</legend>
            <?php
            echo $this->Form->input('type', array(
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
            echo $this->Form->input('clientcert', array(
                'type' => 'file',
                'label' => 'Certificat (p12)'
            ));
            echo $this->Form->input('certpwd', array(
                'type' => 'password',
                'placeholder' => "Mot de passe du certificat",
                'value' => Configure::read('IPARAPHEUR_CERTPWD'),
                'label' => 'Mot de passe'));
            ?>
        </fieldset>
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
</div>
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