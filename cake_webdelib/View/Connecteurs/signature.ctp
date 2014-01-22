<div class='spacer'></div>
<div id="configSignature">
    <?php
    $protocol = Configure::read('SIGNATURE_PROTOCOL');
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
            'value' => Configure::read('USE_SIGNATURE') ? 'true' : 'false',
            'div' => true,
            'label' => true,
            'onChange' => 'changeActivation(this)'));
        ?>
    </fieldset>
    <div class='spacer'></div>
    <div id='config_content' <?php echo Configure::read('USE_SIGNATURE') === false ? 'style="display: none;"' : ''; ?>>
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
                'placeholder' => 'http://x.x.x.org',
                'label' => 'URL',
                'value' => Configure::read($protocol . '_HOST')));

            echo $this->Form->input('login', array(
                'type' => 'text',
                'placeholder' => 'Nom d\'utilisateur',
                'label' => 'Login',
                'value' => Configure::read($protocol . '_LOGIN')));

            echo $this->Form->input('pwd', array(
                'type' => 'text',
                'placeholder' => 'Mot de passe utilisateur',
                'label' => 'Mot de passe',
                'value' => Configure::read($protocol . '_PWD')));

            echo $this->Form->input('type', array(
                'type' => 'text',
                'label' => 'Type technique',
                'placeholder' => 'Exemple : Actes, actes-generique...',
                'value' => Configure::read($protocol . '_TYPE'),
            ));
            ?>
        </fieldset>
        <fieldset
            id='infos_certificat'<?php if ($protocol != 'IPARAPHEUR') echo ' style="display: none;"'; ?>>
            <legend>Certificat d'authentification</legend>
            <?php
            echo $this->Form->input('clientcert', array(
                'type' => 'file',
                'label' => 'Certificat (p12)'
            ));
            echo $this->Form->input('certpwd', array(
                'type' => 'text',
                'placeholder' => "Mot de passe du certificat",
                'value' => Configure::read('IPARAPHEUR_CERTPWD'),
                'label' => 'Mot de passe'));
            ?>
        </fieldset>
        <div class='spacer'></div>
    </div>
    <?php
    foreach ($protocoles as $id_p => $p) {
        echo $this->Form->hidden($id_p . '_host', array('value' => Configure::read(strtoupper($id_p) . '_HOST')));
        echo $this->Form->hidden($id_p . '_login', array('value' => Configure::read(strtoupper($id_p) . '_LOGIN')));
        echo $this->Form->hidden($id_p . '_pwd', array('value' => Configure::read(strtoupper($id_p) . '_PWD')));
        echo $this->Form->hidden($id_p . '_type', array('value' => Configure::read(strtoupper($id_p) . '_TYPE')));
    }
    echo $this->Html->tag('div', null, array('class' => 'btn-group', 'style' => 'margin-top:10px;'));
    echo $this->Html->link('<i class="fa fa-arrow-left"></i> Annuler', array('controller' => 'connecteurs', 'action' => 'index'), array('class' => 'btn', 'escape' => false, 'title' => 'Annuler'));
    echo $this->Form->button("<i class='fa fa-save'></i> Enregistrer", array('type' => 'submit', 'id' => 'boutonValider', 'class' => 'btn btn-primary', 'escape' => false, 'title' => 'Modifier la configuration'));
    echo $this->Html->tag('/div', null);

    echo $this->Form->end();

    echo $this->Html->script('connecteurs/signature');
    echo $this->Html->css('connecteurs');
    ?>
</div>