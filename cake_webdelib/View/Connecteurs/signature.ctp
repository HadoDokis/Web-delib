<?php
if (empty($flux_pastell)){
    echo '<div class="alert">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <strong>Attention !</strong> Connecteur Pastell désactivé. Le fichier pastell.inc est introuvable.
    </div>';
    unset($protocoles['pastell']);
}
?>
<div class='spacer'></div>
<div id="configSignature">
    <?php
    $protocol = Configure::read('PARAPHEUR');
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
                'label' => 'Protocole',
                'onChange' => 'changeProtocol()',
                'value' => strtolower($protocol)));
            ?>
        </fieldset>
        <div class='spacer'></div>
        <fieldset>
            <legend>Informations d'authentification</legend>
            <?php
            echo $this->Form->input('host', array(
                'type' => 'text',
                'placeholder' => 'http://'.strtolower($protocol).'.x.x.org',
                'label' => 'URL',
                'value' => Configure::read($protocol.'_HOST')));

            echo $this->Form->input('login', array(
                'type' => 'text',
                'placeholder' => 'Nom d\'utilisateur',
                'label' => 'Login',
                'value' => Configure::read($protocol.'_LOGIN')));

            echo $this->Form->input('pwd', array(
                'type' => 'password',
                'placeholder' => 'Mot de passe utilisateur',
                'label' => 'Mot de passe',
                'value' => Configure::read($protocol.'_PWD')));
            ?>
        </fieldset>
        <fieldset>
            <legend>Configuration des flux</legend>
            <?php
            echo $this->Form->input('pastelltype', array(
                'type' => 'select',
                'options' => $flux_pastell,
                'title' => 'Pour modifier les flux Pastell, éditer le fichier de configuration pastell.inc',
                'value' => Configure::read('PASTELL_TYPE'),
                'label' => 'Type de flux (Pastell)',
                'div' => array('id'=> 'pastell_type','style' => ($protocol != 'PASTELL') ? 'display: none;' : '')
            ));
            echo $this->Form->input('type', array(
                'type' => 'text',
                'label' => 'Type technique (Parapheur)',
                'placeholder' => 'Exemple : Actes',
                'value' => Configure::read('IPARAPHEUR_TYPE'),
            ));
            ?>
        </fieldset>
        <fieldset id='infos_certificat'<?php if ($protocol != 'IPARAPHEUR') echo ' style="display: none;"'; ?>>
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
    echo $this->Html->script('connecteurs/signature');
    echo $this->Html->css('connecteurs');
    ?>
</div>