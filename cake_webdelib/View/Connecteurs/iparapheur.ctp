<div class='spacer'></div>
<div id="connecteurIparapheur">
    <?php
    echo $this->Form->create('Connecteur', array('url' => '/connecteurs/makeconf/iparapheur', 'type' => 'file'));
    $notif = array('true' => 'Oui', 'false' => 'Non');
    echo $this->Form->input('use_paraph', array(
        'legend' => "Utilisation du i-parapheur électronique",
        'type' => 'radio',
        'options' => $notif,
        'value' => Configure::read('USE_PARAPH') ? 'true' : 'false',
        'div' => true,
        'label' => true,
        'onClick' => "if(this.value=='true') $('#affiche').show(); else $('#affiche').hide();"));
    ?>
    <div class='spacer'></div>
    <div id='affiche' <?php echo Configure::read('USE_PARAPH') === false ? 'style="display: none;"' : ''; ?>>
        <fieldset>
            <legend>Adresse de la plateforme I-Parapheur</legend>
            <?php
            echo $this->Form->input('wsaction', array(
                'type' => 'text',
                "placeholder" => "Exemple : https://parapheur.demonstrations.adullact.org/alfresco",
                'label' => 'WsAction',
                'value' => Configure::read('WSACTION')));
            echo $this->Form->input('wsto', array(
                'type' => 'text',
                "placeholder" => "Exemple : https://parapheur.demonstrations.adullact.org/ws-iparapheur",
                'label' => 'WsTo',
                'value' => Configure::read('WSTO')));
            ?>

        </fieldset>
        <fieldset>
            <legend>Certificat d'authentification</legend>
            <?php
            echo $this->Form->input('certificat', array(
                'type' => 'file',
                'label' => 'Certificat (p12)'
            ));

            echo $this->Form->input('passphrase', array(
                'type' => 'text',
                'placeholder' => "Mot de passe du certificat",
                'value' => Configure::read('PASSPHRASE'),
                'label' => 'Mot de passe'));
            ?>
        </fieldset>
        <fieldset>
            <legend>Informations d'authentification</legend>
            <?php
            echo $this->Form->input('login', array(
                'type' => 'text',
                'placeholder' => 'Nom d\'utilisateur du parapheur',
                'label' => 'Nom d\'utilisateur',
                'value' => Configure::read('HTTPAUTH')));

            echo $this->Form->input('pass', array(
                'type' => 'text',
                'placeholder' => 'Mot de passe de l\'utilisateur',
                'label' => "Mot de passe",
                'value' => Configure::read('HTTPPASSWD')));

            echo $this->Form->input('typetech', array(
                'type' => 'text',
                'label' => 'Type technique',
                'placeholder' => 'Type de circuit du parapheur',
                'value' => Configure::read('TYPETECH'),
            ));
            ?>
        </fieldset>
        <div class='spacer'> </div>
    </div>
    <?php
    echo $this->Html2->boutonsSaveCancel('', '/connecteurs/index');
    echo $this->Form->end();
    ?>
</div>