<div id="configSae">
    <?php
    $true_false = array('true' => 'Oui', 'false' => 'Non');
    echo $this->BsForm->create('Connecteur', array('url' => array('controller' => 'connecteurs', 'action' => 'makeconf', 'sae')));
    ?>
    <fieldset style="width: 100%">
        <legend>Activation du Système d'archivage électronique</legend>
        <?php
        echo $this->BsForm->radio('use_sae',$true_false, array(
            'value' => Configure::read('USE_SAE') ? 'true' : 'false',
            'onChange' => 'changeActivation(this)'));
        ?>
    </fieldset>
    <div class='spacer'></div>
    <div id='config_content' <?php echo Configure::read('USE_SAE') === false ? 'style="display: none;"' : ''; ?>>
        <fieldset>
            <legend>Connecteur du SAE</legend>
            <?php
            echo $this->BsForm->input('sae_protocol', array(
                'type' => 'select',
                'options' => $protocoles,
                'label' => 'Type de connecteur',
                'onChange' => 'changeProtocol()',
                'value' => Configure::read('SAE')));
            ?>
        </fieldset>
        <div class='spacer'></div>
        <fieldset class="asalae-infos">
            <legend>Informations de connexion</legend>
            <?php
            echo $this->BsForm->input('host', array(
                'type' => 'text',
                'placeholder' => 'WSDL de as@lae',
                'label' => 'URL',
                'value' => Configure::read('ASALAE_WSDL')));

            echo $this->BsForm->input('login', array(
                'type' => 'text',
                'placeholder' => 'Nom d\'utilisateur',
                'label' => 'Login',
                'value' => Configure::read('ASALAE_LOGIN')));

            echo $this->BsForm->input('pwd', array(
                'type' => 'password',
                'placeholder' => 'Mot de passe utilisateur',
                'label' => 'Mot de passe',
                'value' => Configure::read('ASALAE_PWD')));
            ?>
        </fieldset>
        <fieldset class="asalae-infos">
            <legend>Identification Collectivité</legend>
             <?php
        echo $this->BsForm->input('siren_archive', array(
                'type' => 'text',
                'placeholder' => 'SIREN Archivage',
                'label' => 'SIREN Archivage',
                'value' => Configure::read('ASALAE_SIREN_ARCHIVE')));
        echo $this->BsForm->input('numero_agrement', array(
                'type' => 'text',
                'placeholder' => 'Numéro d\'agrément',
                'label' => 'Numéro d\'agrément',
                'value' => Configure::read('ASALAE_NUMERO_AGREMENT')));
        ?>
        </fieldset>
    </div>
   <?php
echo $this->Html2->btnSaveCancel('', array('controller' => 'connecteurs', 'action' => 'index'));
echo $this->BsForm->end();
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
    var protocol = $('#ConnecteurSaeProtocol').val();
    if (protocol == 'PASTELL') {
        $('.pastell-infos').show();
    } else {
        $('.pastell-infos').hide();
    }
    if (protocol == 'ASALAE') {
        $('.asalae-infos').show();
    } else {
        $('.asalae-infos').hide();
    }
}
</script>