<div id="configSae">
    <?php
    $true_false = array('true' => 'Oui', 'false' => 'Non');
    echo $this->Form->create('Connecteur', array('url' => array('controller' => 'connecteurs', 'action' => 'makeconf', 'sae')));
    ?>
    <fieldset style="width: 100%">
        <legend>Activation du Système d'archivage électronique</legend>
        <?php
        echo $this->Form->input('use_sae', array(
            'legend' => false,
            'type' => 'radio',
            'options' => $true_false,
            'value' => Configure::read('USE_SAE') ? 'true' : 'false',
            'div' => true,
            'label' => true,
            'onChange' => 'changeActivation(this)'));
        ?>
    </fieldset>
    <div class='spacer'></div>
    <div id='config_content' <?php echo Configure::read('USE_SAE') === false ? 'style="display: none;"' : ''; ?>>
        <fieldset>
            <legend>Connecteur du SAE</legend>
            <?php
            echo $this->Form->input('sae_protocol', array(
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
            echo $this->Form->input('host', array(
                'type' => 'text',
                'placeholder' => 'WSDL de as@lae',
                'label' => 'URL',
                'value' => Configure::read('ASALAE_WSDL')));

            echo $this->Form->input('login', array(
                'type' => 'text',
                'placeholder' => 'Nom d\'utilisateur',
                'label' => 'Login',
                'value' => Configure::read('ASALAE_LOGIN')));

            echo $this->Form->input('pwd', array(
                'type' => 'password',
                'placeholder' => 'Mot de passe utilisateur',
                'label' => 'Mot de passe',
                'value' => Configure::read('ASALAE_PWD')));
            ?>
        </fieldset>
        <fieldset class="asalae-infos">
            <legend>Identification Collectivité</legend>
             <?php
        echo $this->Form->input('siren_archive', array(
                'type' => 'text',
                'placeholder' => 'SIREN Archivage',
                'label' => false,
                'value' => Configure::read('ASALAE_SIREN_ARCHIVE'),
                'before' => '<label>SIREN Archivage</label>'));
        echo $this->Form->input('numero_agrement', array(
                'type' => 'text',
                'placeholder' => 'Numéro d\'agrément',
                'label' => false,
                'value' => Configure::read('ASALAE_NUMERO_AGREMENT'),
                'before' => '<label>Numéro d\'agrément</label>'));
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