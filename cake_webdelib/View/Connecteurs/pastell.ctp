<div id="configPastell">
<?php
if (empty($flux_pastell)) {
    echo '<div class="alert">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <strong>Attention !</strong> Connecteur Pastell désactivé. Le fichier pastell.inc est introuvable.
    </div>';
}
$true_false = array('true' => 'Oui', 'false' => 'Non');
echo $this->BsForm->create('Connecteur', array('url' => array('controller' => 'connecteurs', 'action' => 'makeconf', 'pastell'), 'type' => 'file'));
?>
<fieldset>
    <legend>Activation de Pastell</legend>
    <?php
    echo $this->BsForm->radio('use_pastell',$true_false, array(
        'value' => Configure::read('USE_PASTELL') ? 'true' : 'false',
        'onChange' => 'changeActivation(this)'));
    ?>
</fieldset>
<div class='spacer'> </div>
<div id='config_content' <?php echo Configure::read('USE_PASTELL') === false ? 'style="display: none;"' : ''; ?>>
    <fieldset class="Pastell-infos">
        <legend>Paramètrage de PASTELL</legend>
        <?php
        echo $this->BsForm->input('host', array('type' => 'text',
            'placeholder' => 'Exemple : pastell.maville.fr',
            'label' => 'Serveur PASTELL : ',
            'value' => Configure::read('PASTELL_HOST'))) . '<br>';
        echo $this->BsForm->input('login', array('type' => 'text',
            'placeholder' => 'nom d\'utilisateur',
            'label' => 'Nom d\'utilisateur',
            'value' => Configure::read('PASTELL_LOGIN'))) . '<br>';
        echo $this->BsForm->input('pwd', array('type' => 'password',
            'placeholder' => 'mot de passe',
            'label' => 'Mot de passe',
            'value' => Configure::read('PASTELL_PWD'))) . '<br>';

        ?>
    </fieldset>
    <fieldset class="Pastell-infos">
        <legend>Configuration des flux</legend>
        <?php
            echo $this->BsForm->input('type', array('type' => 'text',
            'placeholder' => 'exemple : actes-generique',
            'label' => 'Type technique',
            'value' => Configure::read('PASTELL_TYPE')));
        ?>
    </fieldset>
</div>
<div class='spacer'> </div>
<?php
echo $this->Html2->btnSaveCancel('', array('controller' => 'connecteurs', 'action' => 'index'));
echo $this->BsForm->end();
?>
</div>
<script type="text/javascript">
    function changeActivation(element) {
        if ($(element).val() == 'true') {
            $('#config_content').show();
        } else {
            $('#config_content').hide();
        }
    }
</script>