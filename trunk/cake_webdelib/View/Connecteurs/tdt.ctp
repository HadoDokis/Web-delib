<?php
$this->Html->addCrumb('Mes connecteurs', array('controller'=>'connecteurs'));
$this->Html->addCrumb($titre);

echo $this->Bs->tag('h3', $titre);

$true_false = array('true' => 'Oui', 'false' => 'Non');
echo $this->BsForm->create('Connecteur', array('url' => array('controller' => 'connecteurs', 'action' => 'makeconf', 'tdt'), 'type' => 'file'));
?>
    <fieldset>
        <legend>Activation du TDT</legend>
        <?php
        echo $this->BsForm->radio('use_tdt', $true_false, array(
            'value' => Configure::read('USE_TDT') ? 'true' : 'false',
            'onChange' => 'changeActivation(this)'));
        ?>
    </fieldset>
    <div class='spacer'></div>
    <div id='config_content' <?php echo Configure::read('USE_TDT') === false ? 'style="display: none;"' : ''; ?>>
        <fieldset>
            <legend>Tiers de Télétransmission</legend>
            <?php
            echo $this->BsForm->input('tdt_protocol', array(
                'type' => 'select',
                'options' => $protocoles,
                'label' => 'Protocole',
                'onChange' => 'changeProtocol()',
                'value' => Configure::read('TDT')
                ));
            ?>
        </fieldset>
        <div class='spacer'></div>
        <fieldset class="s2low-infos">
            <legend>Informations de connexion</legend>
             <?php
            echo $this->BsForm->input('host', array(
                'type' => 'text',
                'placeholder' => 'https://www.s2low.org',
                'label' => 'URL du serveur',
                'value' => Configure::read('S2LOW_HOST')
                ));
            ?>
        </fieldset>
        <fieldset class="s2low-infos">
            <legend>Certificat d'authentification</legend>
            <?php
            echo $this->BsForm->input('clientcert', array(
                'type' => 'file',
                'label' => 'Certificat (p12)'
            ));
            echo $this->BsForm->input('certpwd', array(
                'type' => 'password',
                'placeholder' => "Fourni avec votre certificat",
                'value' => Configure::read('S2LOW_CERTPWD'),
                'label' => 'Mot de passe'));
            ?>
        </fieldset>
        <div class="s2low-infos"<?php if (Configure::read('TDT') != 'S2LOW') echo ' style="display: none;"'; ?>>
            <fieldset>
                <legend>Proxy</legend>
                <?php
                echo $this->BsForm->radio('use_proxy',$true_false, array(
                    'value' => Configure::read('S2LOW_USEPROXY') ? 'true' : 'false',
                    'onClick' => "if(this.value=='true') $('#proxy_host').show(); else $('#proxy_host').hide(); "));
                ?>
                <div class='spacer'></div>
                <div id="proxy_host" <?php if (!Configure::read('S2LOW_USEPROXY')) echo ' style="display: none;"'; ?>>
                    <?php
                    echo $this->BsForm->input('proxy_host', array(
                        'type' => 'text',
                        'placeholder' => 'http://x.x.x.x:8080',
                        'value' => Configure::read('S2LOW_PROXYHOST'),
                        'label' => 'Adresse du proxy'));
                    ?> </div>
                <div class='spacer'></div>
                <legend>Mail sécurisé</legend>
                <?php
                echo $this->BsForm->radio('use_mails',$true_false, array(
                    'options' => $true_false,
                    'value' => Configure::read('S2LOW_MAILSEC') ? 'true' : 'false',
                    'default' => 'false',
                    'onClick' => "if(this.value=='true') $('#mails_password').show(); else $('#mails_password').hide(); ",
                ));
                ?>
                <div class='spacer'></div>
                <div
                    id="mails_password" <?php if (!Configure::read('S2LOW_MAILSEC')) echo ' style="display: none;"'; ?>>
                    <?php
                    echo $this->BsForm->input('mails_password', array(
                        'type' => 'password',
                        'placeholder' => 'Mot de passe mail sécurisé',
                        'value' => Configure::read('S2LOW_MAILSECPWD'),
                        'label' => 'Mot de passe'
                    ));
                    ?>
                </div>
            </fieldset>
        </div>
        <div class='spacer'></div>
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
    var protocol = $('#ConnecteurTdtProtocol').val();
    if (protocol == 'PASTELL') {
        $('.pastell-infos').show();
    } else {
        $('.pastell-infos').hide();
    }
    if (protocol == 'S2LOW') {
        $('.s2low-infos').show();
    } else {
        $('.s2low-infos').hide();
    }
}
</script>