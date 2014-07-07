<div id="configPastell">
<?php
if (empty($flux_pastell)) {
    echo '<div class="alert">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <strong>Attention !</strong> Connecteur Pastell désactivé. Le fichier pastell.inc est introuvable.
    </div>';
}
$true_false = array('true' => 'Oui', 'false' => 'Non');
echo $this->Form->create('Connecteur', array('url' => array('controller' => 'connecteurs', 'action' => 'makeconf', 'pastell'), 'type' => 'file'));
?>
<fieldset>
    <legend>Activation de Pastell</legend>
    <?php
    echo $this->Form->input('use_pastell', array(
        'legend' => false,
        'type' => 'radio',
        'options' => $true_false,
        'value' => Configure::read('USE_PASTELL') ? 'true' : 'false',
        'div' => true,
        'label' => true,
        'onChange' => 'changeActivation(this)'));
    ?>
</fieldset>
<div class='spacer'> </div>
<div id='config_content' <?php echo Configure::read('USE_PASTELL') === false ? 'style="display: none;"' : ''; ?>>
    <fieldset class="Pastell-infos">
        <legend>Paramètrage de PASTELL</legend>
        <?php
        echo $this->Form->input('host', array('type' => 'text',
            'placeholder' => 'Exemple : pastell.maville.fr',
            'label' => 'Serveur PASTELL : ',
            'value' => Configure::read('PASTELL_HOST'))) . '<br>';
        echo $this->Form->input('login', array('type' => 'text',
            'placeholder' => 'nom d\'utilisateur',
            'label' => false,
            'value' => Configure::read('PASTELL_LOGIN'),
            'before' => '<label>Nom d\'utilisateur</label>')) . '<br>';
        echo $this->Form->input('pwd', array('type' => 'password',
            'placeholder' => 'mot de passe',
            'label' => false,
            'value' => Configure::read('PASTELL_PWD'),
            'before' => '<label>Mot de passe</label>')) . '<br>';

        ?>
    </fieldset>
    <fieldset class="Pastell-infos">
        <legend>Configuration des flux</legend>
        <?php
            echo $this->Form->input('type', array('type' => 'text',
            'placeholder' => 'exemple : actes-generique',
            'label' => false,
            'value' => Configure::read('PASTELL_TYPE'),
            'before' => '<label>Type technique</label>'));
        ?>
    </fieldset>
</div>
<div class='spacer'> </div>
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
    function changeActivation(element) {
        if ($(element).val() == 'true') {
            $('#config_content').show();
        } else {
            $('#config_content').hide();
        }
    }
</script>