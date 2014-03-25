<div class='spacer'></div>
<?php
echo $this->Form->create('Connecteur', array('url' => array('controller' => 'connecteurs', 'action' => 'makeconf', 'cmis')));
$notif = array('true' => 'Oui', 'false' => 'Non');
?>
<fieldset>
    <legend>Activation du service GED - Export CMIS</legend>
    <?php
    echo $this->Form->input('use_ged', array(
        'legend' => false,
        'type' => 'radio',
        'options' => array('true' => 'Oui', 'false' => 'Non'),
        'value' => Configure::read('USE_GED') ? 'true' : 'false',
        'div' => true,
        'label' => true,
        'onChange' => "if(this.value=='true') $('#affiche').show(); else $('#affiche').hide(); "
    ));
    ?>
</fieldset>
<div class='spacer'></div>
<div id='affiche' <?php echo Configure::read('USE_GED') === false ? 'style="display: none;"' : ''; ?>>
    <fieldset>
        <legend>Paramètrage de la GED</legend>
        <?php
        echo $this->Form->input('ged_url', array(
                'type' => 'text',
                "placeholder" => "Exemple : http://x.x.ma-ville.fr",
                'label' => 'Serveur de la GED : ',
                'value' => Configure::read('GED_HOST')));
        echo $this->Form->input('ged_login', array(
                'type' => 'text',
                "placeholder" => "Nom d'utilisateur",
                'label' => false,
                'value' => Configure::read('GED_LOGIN'),
                'before' => '<label>Nom d\'utilisateur</label>'));
        echo $this->Form->input('ged_passwd',
                array('type' => 'text',
                    "placeholder" => "Mot de passe",
                    'label' => false,
                    'value' => Configure::read('GED_PWD'),
                    'before' => '<label>Mot de passe</label>'));
        echo $this->Form->input('ged_repo',
                array('type' => 'text',
                    "placeholder" => "Exemple : /Sites/Web-delib",
                    'label' => false,
                    'value' => Configure::read('GED_REPO'),
                    'before' => '<label>Répertoire de stockage</label>'));
        echo $this->Form->input('ged_xml_version', array(
            'type' => 'select',
            'label' => false,
            'options' => array(1 => 1, 2 => 2),
            'selected' => Configure::read('GED_XML_VERSION'),
            'autocomplete' => 'off',
            'before' => '<label>Version du schéma XML</label>'));
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
