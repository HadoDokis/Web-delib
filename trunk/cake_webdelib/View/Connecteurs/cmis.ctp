<div class='spacer'></div>
<?php
echo $this->BsForm->create('Connecteur', array('url' => array('controller' => 'connecteurs', 'action' => 'makeconf', 'cmis')));
$notif = array('true' => 'Oui', 'false' => 'Non');
?>
<fieldset>
    <legend>Activation du service GED - Export CMIS</legend>
    <?php
    echo $this->BsForm->radio('use_ged',array('true' => 'Oui', 'false' => 'Non'), array(
        'value' => Configure::read('USE_GED') ? 'true' : 'false',
        'onChange' => "if(this.value=='true') $('#affiche').show(); else $('#affiche').hide(); "
    ));
    ?>
</fieldset>
<div class='spacer'></div>
<div id='affiche' <?php echo Configure::read('USE_GED') === false ? 'style="display: none;"' : ''; ?>>
    <fieldset>
        <legend>Paramètrage de la GED</legend>
        <?php
        echo $this->BsForm->input('ged_url', array(
                'type' => 'text',
                "placeholder" => "Exemple : http://x.x.ma-ville.fr",
                'label' => 'Serveur de la GED : ',
                'value' => Configure::read('CMIS_HOST')));
        echo $this->BsForm->input('ged_login', array(
                'type' => 'text',
                "placeholder" => "Nom d'utilisateur",
                'label' => 'Nom d\'utilisateur',
                'value' => Configure::read('CMIS_LOGIN')));
        echo $this->BsForm->input('ged_passwd',
                array('type' => 'password',
                    "placeholder" => "Mot de passe",
                    'label' => 'Mot de passe',
                    'value' => Configure::read('CMIS_PWD')));
        echo $this->BsForm->input('ged_repo',
                array('type' => 'text',
                    "placeholder" => "Exemple : /Sites/Web-delib",
                    'label' => 'Répertoire de stockage',
                    'value' => Configure::read('CMIS_REPO')));
        echo $this->BsForm->input('ged_xml_version', array(
            'type' => 'select',
            'label' => 'Version du schéma XML',
            'options' => array(1 => 1, 2 => 2, 3 => 3),
            'selected' => Configure::read('GED_XML_VERSION'),
            'autocomplete' => 'off'));
        ?>
    </fieldset>
</div>
   <?php
echo $this->Html2->btnSaveCancel('', array('controller' => 'connecteurs', 'action' => 'index'));
echo $this->BsForm->end();
    ?>