<?php echo $this->Html->script('calendrier.js'); ?>
<?php echo $this->Html->script('utils.js'); ?>
<?php echo $this->Html->script('deliberation.js'); ?>
<?php echo $this->Html->script('ckeditor/ckeditor'); ?>
<?php echo $this->Html->script('ckeditor/adapters/jquery'); ?>
<?php echo $this->Html->script('multidelib.js'); ?>
<?php
echo "<h1>Ajout d'un projet</h1>";
echo $this->Form->create('Deliberation', array('url' => '/deliberations/add', 'type' => 'file', 'name' => 'Deliberation'));
?>

<div class='onglet'>
    <a href="#" id="emptylink"></a>
    <?php
    echo $this->Html->link('Informations principales', '#', array('class' => 'ongletCourant', 'id' => 'lienTab1', 'onClick' => 'javascript:afficheOngletNew(document.Deliberation,1);'));
    echo $this->Html->link('Textes', '#', array('id' => 'lienTab2', 'onClick' => 'javascript:afficheOngletNew(document.Deliberation,2);'));
    echo $this->Html->link('Annexe(s)', '#', array('id' => 'lienTab3', 'onClick' => 'javascript:afficheOngletNew(document.Deliberation,3);'));
    if (!empty($infosupdefs))
        echo $this->Html->link('Informations supplémentaires', '#', array('id' => 'lienTab4', 'onClick' => 'javascript:afficheOngletNew(document.Deliberation,4);'));
    if (Configure::read('DELIBERATIONS_MULTIPLES'))
        echo $this->Html->link('Délibérations rattachées', '#', array('id' => 'lienTab5', 'onClick' => 'javascript:afficheOngletNew(document.Deliberation,5);', 'style' => 'display: none'));
    ?>
</div>
<div id="tab1">
    <fieldset id='info'>
        <div class='demi'>
            <?php echo '<b><u>Rédacteur</u></b> : <i>' . $this->Html->value('Redacteur.prenom') . ' ' . $this->Html->value('Redacteur.nom') . '</i>'; ?>
            <br/>
            <?php echo '<b><u>Service émetteur</u></b> : <i>' . $this->Html->value('Service.libelle') . '</i>'; ?>
        </div>
        <div class='demi'>
        </div>
    </fieldset>
    <div class='spacer'></div>
    <div class="gauche">
        <?php echo $this->Form->input('Deliberation.typeacte_id', array(
            'label' => 'Type d\'acte <abbr title="obligatoire">*</abbr>',
            'options' => $this->Session->read('user.Nature'),
            'empty' => true,
            'id' => 'listeTypeactesId',
            'onChange' => "updateTypeseances(this);",
            'escape' => false,
            'required'
        ));  ?>
        <div class='spacer'></div>
        <?php echo $this->Form->input('Deliberation.objet', array('type' => 'textarea', 'label' => 'Libellé <abbr title="obligatoire">*</abbr>', 'cols' => '60', 'rows' => '2', 'required')); ?>
        <div class='spacer'></div>
        <?php echo $this->Form->input('Deliberation.titre', array('type' => 'textarea', 'label' => 'Titre', 'rows' => 2)); ?>
        <div class='spacer'></div>
        <?php echo $this->Form->input('Deliberation.rapporteur_id', array(
            'label' => 'Rapporteur',
            'options' => $rapporteurs,
            'class' => 'select2 selectone',
            'empty' => true)); ?>
        <div class='spacer'></div>
        <?php echo $this->Form->input('Deliberation.theme_id', array(
            'label' => 'Thème <abbr title="obligatoire">*</abbr>',
            'empty' => true,
            'class' => 'select2 selectone',
            'escape' => false)); ?>
        <div class='spacer'></div>
        <div id="select_classification">
            <?php
            if ($USE_PASTELL) {
                echo $this->Form->input('Deliberation.num_pref_libelle', array(
                    'label' => 'Nomenclature',
                    'options' => $nomenclatures,
                    'default' => $this->Html->value('Deliberation.num_pref'),
                    'disabled' => empty($nomenclatures),
                    'empty' => true,
                    'class' => 'select2 selectone',
                    'escape' => false));
                echo $this->Html->tag('div', '', array('class' => 'spacer'));
            } else {
                echo $this->Form->input('Deliberation.num_pref_libelle', array(
                    'div' => false,
                    'label' => 'Classification',
                    'placeholder' => 'Cliquer ici pour choisir la classification',
                    'onclick' => "javascript:window.open('" . Router::url(array('controller' => 'deliberations', 'action' => 'classification')) . "', 'Select_attribut', 'scrollbars=yes,width=570,height=450');",
                    'id' => 'classif1',
                    'title' => 'Selection de la classification',
                    'readonly' => 'readonly',
                    'after' => '&nbsp;<a href="#" title="Déselectionner la classification" id="deselectClassif"><i class="fa fa-eraser"></i></a>'
                ));
                echo $this->Form->hidden('Deliberation.num_pref', array('id' => 'num_pref'));

            }
            ?>
        </div>
        <div id="dateLimite">
            <?php
            echo $this->Form->label('Deliberation.date_limite', 'Date limite');
            if (!empty($date_limite) && $date_limite != '01/01/1970')
                $value = "value='" . $date_limite . "'";
            else
                $value = "value=''";
            ?>
            <input name="date_limite" size="9" id="DeliberationDateLimite" <?php echo $value; ?> />&nbsp;<a
                href="javascript:show_calendar('Deliberation.date_limite','f');"
                title="Sélectionner une date à l'aide du calendrier"
                id="afficheCalendrier"><?php echo $this->Html->image("calendar.png", array('alt' => "afficher le calendrier")); ?></a>
        </div>
        <div class='spacer'></div>
        <div class='spacer'></div>
        <?php
        if ($DELIBERATIONS_MULTIPLES) :
            echo $this->Html->tag('label', $this->Form->input('Deliberation.is_multidelib', array(
                'type' => 'checkbox',
                'autocomplete' => 'off',
                'div' => false,
                'label' => false,
                'after' => 'Multi-Délibération'
            )), array('class' => 'checkbox'));
            ?>
            <div class='spacer'></div>
        <?php endif; ?>
        <div class='spacer'></div>
    </div>
    <div class="droite">
        <div id='selectTypeseances'></div>
        <div id='selectDatesSeances'></div>
        <div class='spacer'></div>
    </div>
</div>

<?php if (!empty($infosupdefs)): ?>
    <div id="tab4" <?php echo isset($lienTab) && $lienTab == 4 ? '' : 'style="display: none;"' ?>>
        <?php
        foreach ($infosupdefs as $infosupdef) {
            // Amélioration 4.1 : on ne peut modifier une infosup qu'en fonction du profil
            $disabled = true;
            foreach ($infosupdef['Profil'] as $profil)
                if ($profil['id'] == $profil_id)
                    $disabled = false;

            if ($disabled) continue;

            $fieldName = 'Infosup.' . $infosupdef['Infosupdef']['code'];
            $fieldId = 'Infosup' . Inflector::camelize($infosupdef['Infosupdef']['code']);
            if ($infosupdef['Infosupdef']['type'] == 'text') {
                echo $this->Form->input($fieldName, array('label' => false, 'type' => 'textarea', 'title' => $infosupdef['Infosupdef']['commentaire'], 'readonly' => $disabled));
            } elseif ($infosupdef['Infosupdef']['type'] == 'boolean') {
                echo $this->Form->input($fieldName, array('label' => false, 'type' => 'checkbox', 'title' => $infosupdef['Infosupdef']['commentaire'], 'div' => array('class' => 'input')));
            } elseif ($infosupdef['Infosupdef']['type'] == 'date') {
                $fieldSelector = preg_replace("#[^a-zA-Z]#", "", $fieldId);
                echo $this->Form->input($fieldName, array('type' => 'text', 'div' => false, 'label' => false, 'size' => '9', 'id' => $fieldSelector, 'title' => $infosupdef['Infosupdef']['commentaire']));
            } elseif ($infosupdef['Infosupdef']['type'] == 'richText') {
                echo $this->Form->input($fieldName, array('label' => false, 'type' => 'textarea'));
            } elseif ($infosupdef['Infosupdef']['type'] == 'file') {
                if (empty($this->data['Infosup'][$infosupdef['Infosupdef']['code']]))
                    echo $this->Form->input($fieldName, array('label' => false, 'type' => 'file', 'size' => '60', 'title' => $infosupdef['Infosupdef']['commentaire'], 'readonly' => $disabled));
            } elseif ($infosupdef['Infosupdef']['type'] == 'odtFile') {
                if (empty($this->data['Infosup'][$infosupdef['Infosupdef']['code']])
                    || empty($this->data['Infosup'][$infosupdef['Infosupdef']['code']]['tmp_name'])
                    || isset($errors_Infosup[$infosupdef['Infosupdef']['code']])
                )
                    echo $this->Form->input($fieldName, array('label' => false, 'type' => 'file', 'size' => '60', 'title' => $infosupdef['Infosupdef']['commentaire'], 'readonly' => $disabled));
                else {
                    echo $this->Form->hidden($fieldName);
                }
            } elseif ($infosupdef['Infosupdef']['type'] == 'list') {
                echo $this->Form->input($fieldName, array('label' => false, 'options' => $infosuplistedefs[$infosupdef['Infosupdef']['code']], 'empty' => true, 'title' => $infosupdef['Infosupdef']['commentaire'], 'readonly' => $disabled));
            }
        };
        ?>
    </div>
<?php endif; ?>

<div class="spacer" style="border-top: solid 1px #e0ef90;"></div>

<div class="submit">
    <?php
    echo $this->Html->tag('div', null, array('class' => 'btn-group'));
    echo $this->Html->link('<i class="fa fa-arrow-left"></i> Annuler', $previous, array('class' => 'btn', 'escape' => false, 'title' => 'Annuler'));
    echo $this->Form->button('<i class="fa fa-save"></i> Sauvegarder', array('type' => 'submit', 'id' => 'boutonValider', 'class' => 'btn btn-primary', 'escape' => false, 'title' => 'Enregistrer le circuit de traitement'));
    echo $this->Html->tag('/div', null);
    ?>
</div>
<?php echo $this->Form->end(); ?>
<script type="text/javascript">
    <?php echo "var allowedMulti = ". json_encode($typesactemulti). ";\n"; ?>
    //Pour savoir quel onglet on a coché
    function afficheOngletNew(obj, afficheTextId) {
        $(obj).append('<input type="hidden" name="lienTab" value="' + afficheTextId + '" />');
        $(obj).submit();
    }
    $(document).ready(function () {
        $('#listeTypeactesId').change(function () {
            if (jQuery.inArray(parseInt($(this).val()), allowedMulti) === -1) {
                $('#DeliberationIsMultidelib').prop('checked', false).parent().hide();
            } else {
                $('#DeliberationIsMultidelib').parent().show();
            }
        }).change();
    });
</script>