<?php echo $this->Html->script('ckeditor/ckeditor'); ?>
<?php echo $this->Html->script('ckeditor/adapters/jquery'); ?>
<?php echo $this->Html->script('calendrier.js'); ?>
<?php echo $this->Html->script('utils.js'); ?>
<?php echo $this->Html->script('deliberation'); ?>
<?php echo $this->Html->script('multidelib'); ?>
<?php
echo $this->Html->tag('h1', "Modification du projet : " . $this->Html->value('Deliberation.id'));
echo $this->Form->create('Deliberation', array('url' => array('action' => 'edit', $this->Html->value('Deliberation.id')), 'type' => 'file', 'name' => 'Deliberation'));
?>

<div class='onglet'>
    <a href="#" id="emptylink"></a>
    <a href="javascript:afficheOnglet(1)"
       id='lienTab1' <?php echo !isset($lienTab) || (isset($lienTab) && ($lienTab == 1 || empty($lienTab))) ? 'class="ongletCourant"' : '' ?>>Informations
        principales</a>
    <a href="javascript:afficheOnglet(2)"
       id='lienTab2' <?php echo isset($lienTab) && $lienTab == 2 ? 'class="ongletCourant"' : '' ?>>Textes</a>
    <a href="javascript:afficheOnglet(3)"
       id='lienTab3' <?php echo isset($lienTab) && $lienTab == 3 ? 'class="ongletCourant"' : '' ?>>Annexe(s)</a>
    <?php if (!empty($infosupdefs)): ?>
        <a href="javascript:afficheOnglet(4)"
           id='lienTab4' <?php echo isset($lienTab) && $lienTab == 4 ? 'class="ongletCourant"' : '' ?>>Informations
            supplémentaires</a>
    <?php endif; ?>
    <?php if (Configure::read('DELIBERATIONS_MULTIPLES')): ?>
        <a href="javascript:afficheOnglet(5)" style="display: none"
           id='lienTab5' <?php echo isset($lienTab) && $lienTab == 5 ? 'class="ongletCourant"' : '' ?>>Délibérations
            rattachées</a>
    <?php endif; ?>
</div>

<div id="tab1"  <?php echo isset($lienTab) && $lienTab != 1 ? 'style="display:none"' : '' ?>>

    <fieldset id='info'>
        <div class='demi'>
            <?php echo '<b><u>Rédacteur</u></b> : <i>' . $this->Html->value('Redacteur.prenom') . ' ' . $this->Html->value('Redacteur.nom') . '</i>'; ?>
            <br/>
            <?php echo '<b><u>Service émetteur</u></b> : <i>' . $this->Html->value('Service.libelle') . '</i>'; ?>
        </div>
        <div class='demi'>
            <?php echo '<b><u>Date création</u></b> : <i>' . $this->Html->value('Deliberation.created') . '</i>'; ?>
            <br/>
            <?php echo '<b><u>Date de modification</u></b> : <i>' . $this->Html->value('Deliberation.modified') . '</i>'; ?>
        </div>
    </fieldset>
    <div class='spacer'></div>
    <div class="gauche">
        <?php echo $this->Form->input('Deliberation.typeacte_id', array(
            'label' => 'Type d\'acte <abbr title="obligatoire">*</abbr>',
            'options' => $this->Session->read('user.Nature'),
            'empty' => false,
            'id' => 'listeTypeactesId',
            'onChange' => "updateTypeseances(this);",
            'escape' => false,
            'class' => 'select2 selectone',
            'required'
        ));  ?>
        <div class='spacer'></div>
        <?php echo $this->Form->input('Deliberation.objet', array('type' => 'textarea', 'label' => 'Libellé <abbr title="obligatoire">*</abbr>', 'cols' => '60', 'rows' => '2', 'required')); ?>
        <div class='spacer'></div>
        <?php echo $this->Form->input('Deliberation.titre', array('type' => 'textarea', 'label' => 'Titre', 'cols' => '60', 'rows' => '2')); ?>
        <div class='spacer'></div>
        <?php echo $this->Form->input('Deliberation.rapporteur_id', array('label' => 'Rapporteur', 'options' => $rapporteurs, 'empty' => true, 'class' => 'select2 selectone')); ?>
        <div class='spacer'></div>
        <?php echo $this->Form->input('Deliberation.theme_id', array('label' => 'Thème <abbr title="obligatoire">*</abbr>', 'options' => $themes, 'default' => $this->Html->value('Deliberation.theme_id'), 'empty' => false, 'escape' => false, 'class' => 'select2 selectone')); ?>
        <div class='spacer'></div>

        <div id="select_classification">
            <?php
            if ($USE_PASTELL) {
                if (empty($nomenclatures)) $nomenclatures = array();
                echo $this->Form->input('Deliberation.num_pref', array(
                    'label' => 'Classification',
                    'options' => $nomenclatures,
                    'default' => $this->Html->value('Deliberation.num_pref'),
                    'disabled' => empty($nomenclatures),
                    'empty' => true,
                    'class' => 'select2 selectone',
                    'escape' => false
                ));
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
            $value = '';
            if (!empty($this->data['Deliberation']['date_limite']) && $this->data['Deliberation']['date_limite'] != '01/01/1970')
                $value = $this->data['Deliberation']['date_limite'];
            ?>
            <input name="date_limite" size="9" value="<?php echo $value; ?>" />&nbsp;<a
                href="javascript:show_calendar('Deliberation.date_limite','f');"
                id="afficheCalendrier"><?php echo $this->Html->image("calendar.png", array('style' => "border:'0'")); ?></a>
        </div>

        <div class='spacer'></div>
        <?php
        if ($DELIBERATIONS_MULTIPLES) :
            echo $this->Html->tag('label',
                $this->Form->input('Deliberation.is_multidelib', array(
                    'type' => 'checkbox',
                    'autocomplete' => 'off',
                    'div' => false,
                    'label' => false,
                    'disabled' => !empty($this->data['Multidelib']) || !empty($this->data['Deliberation']['parent_id']),
                    'checked' => !empty($this->data['Multidelib']) || !empty($this->data['Deliberation']['is_multidelib']) || !empty($this->data['Deliberation']['parent_id']),
                    'after' => 'Multi-Délibération'
                )), array('class' => 'checkbox'));
            ?>
            <div class='spacer'></div>
        <?php endif; ?>
    </div>
    <div class="droite">
        <div id='selectTypeseances'>
            <?php
            if (!empty($typeseances)) :
                echo $this->Form->input('Typeseance', array(
                    'options' => $typeseances,
                    'type' => 'select',
                    'label' => 'Types de séance',
                    'onchange' => "updateDatesSeances(this);",
                    'multiple' => true,
                    'selected' => isset($typeseances_selected) ? $typeseances_selected : ''));
                ?>
                <script>
                    $("#TypeseanceTypeseance").select2({
                        width: "80%",
                        allowClear: true,
                        placeholder: "Selection vide"
                    });
                </script>
                <div class='spacer'></div>
            <?php endif; ?>
        </div>
        <div id='selectDatesSeances'>
            <?php
            if (!empty($seances)) :
                echo $this->Form->input('Seance', array(
                    'options' => $seances,
                    'type' => 'select',
                    'label' => 'Dates de séance',
                    'multiple' => true,
                    'selected' => isset($seances_selected) ? $seances_selected : ''));
                ?>
                <script>
                    $("#SeanceSeance").select2({
                        width: "80%",
                        allowClear: true,
                        placeholder: "Selection vide"
                    });
                </script>
            <?php endif; ?>
        </div>
    </div>
</div>

<div id="tab2" <?php echo isset($lienTab) && $lienTab == 2 ? '' : 'style="display: none;"' ?>>
    <?php echo $this->element('texte', array('key' => 'texte_projet')); ?>
    <div class='spacer'></div>

    <?php echo $this->element('texte', array('key' => 'texte_synthese')); ?>
    <div class='spacer'></div>

    <div id='texteDelibOngletTextes'>
        <div id='texteDeliberation'>
            <?php echo $this->element('texte', array('key' => 'deliberation')); ?>
        </div>
    </div>
    <?php
    echo $this->Html->tag('div', '', array('class' => 'spacer'));
    echo $this->Html->tag('p', 'Note : les modifications apportées ici ne prendront effet que lors de la sauvegarde du projet.');
    ?>
    <div class='spacer'></div>
</div>

<div id="tab3" <?php echo isset($lienTab) && $lienTab == 3 ? '' : 'style="display: none;"' ?>>
    <div id='DelibOngletAnnexes'>
        <div id="DelibPrincipaleAnnexes">
            <?php echo $this->element('annexe', array_merge(array('ref' => 'delibPrincipale'), array('annexes' => $annexes))); ?>
        </div>
    </div>
    <?php
    echo $this->Html->tag('div', '', array('class' => 'spacer'));
    echo $this->Html->tag('p', 'Note : les modifications apportées ici ne prendront effet que lors de la sauvegarde du projet.');
    ?>
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

            if ($infosupdef['Infosupdef']['type'] == 'file' && $disabled) continue;

            $fieldName = 'Infosup.' . $infosupdef['Infosupdef']['code'];
            $fieldId = 'Infosup' . Inflector::camelize($infosupdef['Infosupdef']['code']);
            echo "<div class='required'>";
            echo $this->Form->label($fieldName, $infosupdef['Infosupdef']['nom'], array('for' => 'infosup_' . $infosupdef['Infosupdef']['code']));
            if ($infosupdef['Infosupdef']['type'] == 'text') {
                echo $this->Form->input($fieldName, array('label' => false, 'type' => 'textarea', 'title' => $infosupdef['Infosupdef']['commentaire'], 'readonly' => $disabled, 'id' => 'infosup_' . $infosupdef['Infosupdef']['code']));
            } elseif ($infosupdef['Infosupdef']['type'] == 'boolean') {
                if (!$disabled)
                    echo $this->Form->input($fieldName, array('label' => false, 'type' => 'checkbox', 'title' => $infosupdef['Infosupdef']['commentaire'], 'div' => array('class' => 'input'), 'id' => 'infosup_' . $infosupdef['Infosupdef']['code']));
                else {
                    echo $this->Form->input($fieldName, array('label' => false, 'type' => 'checkbox', 'title' => $infosupdef['Infosupdef']['commentaire'], 'disabled' => $disabled, 'div' => array('class' => 'input'), 'id' => 'infosup_' . $infosupdef['Infosupdef']['code']));
                    echo $this->Form->input($fieldName, array('type' => 'hidden', 'id' => false));
                }
            } elseif ($infosupdef['Infosupdef']['type'] == 'date') {
                $fieldSelector = preg_replace("#[^a-zA-Z]#", "", $fieldId);
                echo $this->Form->input($fieldName, array('type' => 'text', 'readonly' => $disabled, 'div' => false, 'label' => false, 'size' => '9', 'id' => $fieldSelector, 'title' => $infosupdef['Infosupdef']['commentaire']));
                echo '&nbsp;';
                if (!$disabled)
                    echo $this->Html->link($this->Html->image("calendar.png", array('style' => "border:'0'")), "javascript:show_calendar('Deliberation.$fieldSelector', 'f');", array('escape' => false), false);
                else
                    echo($this->Html->image("calendar.png", array('style' => "border:'0'")));
            } elseif ($infosupdef['Infosupdef']['type'] == 'richText') {
                echo '<div class="annexesGauche"></div>';
                if (!$disabled) {
                    echo '<div class="fckEditorProjet">';
                    echo $this->Form->input($fieldName, array('label' => false, 'type' => 'textarea', 'id' => 'infosup_' . $infosupdef['Infosupdef']['code']));
                    echo $this->Fck->load($fieldId);
                    echo '</div>';
                    echo '<div class="spacer"></div>';
                } else {
                    echo $this->Form->input($fieldName, array('label' => false, 'type' => 'textarea', 'readonly' => true, 'id' => 'infosup_' . $infosupdef['Infosupdef']['code']));
                }
            } elseif ($infosupdef['Infosupdef']['type'] == 'file') {
                if (empty($this->data['Infosup'][$infosupdef['Infosupdef']['code']]))
                    echo $this->Form->input($fieldName, array('label' => false, 'type' => 'file', 'size' => '60', 'title' => $infosupdef['Infosupdef']['commentaire'], 'disabled' => $disabled, 'id' => 'infosup_' . $infosupdef['Infosupdef']['code']));
                else {
                    $name = $this->data['Infosup'][$infosupdef['Infosupdef']['code']];
                    if (is_array($name)) $name = $name['name'];
                    echo '<span id="' . $infosupdef['Infosupdef']['code'] . 'InputFichier" style="display: none;"></span>';
                    echo '<span id="' . $infosupdef['Infosupdef']['code'] . 'AfficheFichier">';
                    echo '[' . $this->Html->link($name, '/infosups/download/' . $this->data['Deliberation']['id'] . '/' . $infosupdef['Infosupdef']['id'], array('title' => $infosupdef['Infosupdef']['commentaire'])) . ']';
                    echo '&nbsp;&nbsp;';
                    if (!$disabled)
                        echo $this->Html->link('Supprimer', "javascript:infoSupSupprimerFichier('" . $infosupdef['Infosupdef']['code'] . "', '" . $infosupdef['Infosupdef']['commentaire'] . "')", null, 'Voulez-vous vraiment supprimer le fichier joint ?\n\nAttention : ne prendra effet que lors de la sauvegarde\n');
                    echo '</span>';
                }
            } elseif ($infosupdef['Infosupdef']['type'] == 'odtFile') {
                if (empty($this->data['Infosup'][$infosupdef['Infosupdef']['code']])
                    || empty($this->data['Infosup'][$infosupdef['Infosupdef']['code']]['tmp_name'])
                    || isset($errors_Infosup[$infosupdef['Infosupdef']['code']])
                )
                    echo $this->Form->input($fieldName, array('label' => false, 'type' => 'file', 'title' => $infosupdef['Infosupdef']['commentaire'], 'disabled' => $disabled, 'id' => 'infosup_' . $infosupdef['Infosupdef']['code']));
                else {
                    echo '<span id="' . $infosupdef['Infosupdef']['code'] . 'InputFichier" style="display: none;"></span>';
                    echo '<span id="' . $infosupdef['Infosupdef']['code'] . 'AfficheFichier">';
                        $name = $this->data['Infosup'][$infosupdef['Infosupdef']['code']];
                        if (!$disabled) {
                            $url = Configure::read('PROTOCOLE_DL') . "://" . $_SERVER['SERVER_NAME'] . "/files/generee/projet/" . $this->data['Deliberation']['id'] . "/$name";
                            echo $this->Form->hidden($fieldName);
                        } else
                            $url = "http://" . $_SERVER['SERVER_NAME'] . "/files/generee/projet/" . $this->data['Deliberation']['id'] . "/$name";
                        echo "[<a href='$url'>$name</a>]";
                    echo '&nbsp;&nbsp;';
                    if (!$disabled)
                        echo $this->Html->link('Supprimer', "javascript:infoSupSupprimerFichier('" . $infosupdef['Infosupdef']['code'] . "', '" . $infosupdef['Infosupdef']['commentaire'] . "')", null, 'Voulez-vous vraiment supprimer le fichier joint ?\n\nAttention : ne prendra effet que lors de la sauvegarde\n');
                    echo '</span>';
                }
            } elseif ($infosupdef['Infosupdef']['type'] == 'list') {
                if (!$disabled) {
                    echo $this->Form->input($fieldName, array('label' => false, 'options' => $infosuplistedefs[$infosupdef['Infosupdef']['code']], 'empty' => true, 'title' => $infosupdef['Infosupdef']['commentaire'], 'readonly' => $disabled, 'class' => 'select2 selectone', 'id' => 'infosup_' . $infosupdef['Infosupdef']['code']));
                } else {
                    echo $this->Form->input($fieldName, array('label' => false, 'options' => $infosuplistedefs[$infosupdef['Infosupdef']['code']], 'empty' => true, 'title' => $infosupdef['Infosupdef']['commentaire'], 'disabled' => $disabled, 'id' => 'infosup_' . $infosupdef['Infosupdef']['code']));
                    echo $this->Form->input($fieldName, array('id' => false, 'type' => 'hidden'));
                }
            } elseif ($infosupdef['Infosupdef']['type'] == 'listmulti') {
                $selected_values = !empty($this->request->data['Infosup'][$infosupdef['Infosupdef']['code']]) ? $this->request->data['Infosup'][$infosupdef['Infosupdef']['code']] : null;
                if (!$disabled) {
                    echo $this->Form->input($fieldName, array('selected' => $selected_values, 'label' => false, 'options' => $infosuplistedefs[$infosupdef['Infosupdef']['code']], 'empty' => true, 'title' => $infosupdef['Infosupdef']['commentaire'], 'multiple' => true, 'class' => 'select2 selectmultiple', 'id' => 'infosup_' . $infosupdef['Infosupdef']['code']));
                } else {
                    echo $this->Form->input($fieldName, array('selected' => $selected_values, 'label' => false, 'options' => $infosuplistedefs[$infosupdef['Infosupdef']['code']], 'empty' => true, 'title' => $infosupdef['Infosupdef']['commentaire'], 'disabled' => true, 'multiple' => true, 'class' => 'select2 selectmultiple', 'id' => 'infosup_' . $infosupdef['Infosupdef']['code']));
                    if (!empty($selected_values))
                        echo $this->Form->input($fieldName, array('selected' => $selected_values, 'id' => false, 'multiple' => true, 'options' => $infosuplistedefs[$infosupdef['Infosupdef']['code']], 'type' => 'select', 'hidden' => false, 'style' => 'display:none;', 'label' => false, 'div' => false));
                }
            }
            echo '</div>';
            echo "<div class='spacer'></div>";
        };
        ?>
    </div>
<?php endif; ?>

<?php if (Configure::read('DELIBERATIONS_MULTIPLES')) : ?>
    <div id="tab5" <?php echo isset($lienTab) && $lienTab == 5 ? '' : 'style="display: none;"' ?>>
        <?php
        if (empty($this->data['Deliberation']['parent_id']))
            echo $this->element('multidelib');
        else {
            echo $this->Html->tag('strong', 'Délibération parent : ' . $this->data['Deliberation']['parent_id']);
            echo '<div class="btn-group">';
            echo $this->Html->link('<i class="fa fa-search"></i> Voir', array('action' => 'view', $this->data['Deliberation']['parent_id']), array('escape' => false, 'class' => 'btn btn-mini'));
            echo $this->Html->link('<i class="fa fa-edit"></i> Modifier', array('action' => 'edit', $this->data['Deliberation']['parent_id']), array('escape' => false, 'class' => 'btn btn-mini'));
            echo '</div>';
            echo '<div class="spacer"></div>';
        }
        ?>
    </div>
<?php endif; ?>

<div class="spacer" style="border-top: solid 1px #e0ef90;"></div>

<div class="submit">
    <?php
    echo $this->Form->hidden('Deliberation.id');
    echo $this->Form->hidden('redirect', array('value' => $redirect));

    echo $this->Html->tag("div", null, array("class" => "btn-group"));
    echo $this->Html->link('<i class="fa fa-arrow-left"></i> Annuler', $redirect, array('class' => 'btn', 'escape' => false, 'title' => 'Annuler', 'name' => 'Annuler'));
    echo $this->Form->button('<i class="fa fa-save"></i> Sauvegarder', array('type' => 'submit', 'id' => 'boutonValider', 'class' => 'btn btn-primary', 'escape' => false, 'title' => 'Enregistrer le projet'));
    echo $this->Html->tag('/div', null);
    ?>

</div>

<?php echo $this->Form->end(); ?>

<?php echo $this->element('annexeModal'); ?>
<script type="text/javascript">
    <?php
    echo "var gabarits = ". json_encode($gabarits_acte). ";\n";
    echo "var extensions = ". json_encode($extensions).";\n";
    echo "var extensionsFusion = ". json_encode($extensionsFusion).";\n";
    echo "var extensionsCtrl = ". json_encode($extensionsCtrl).";\n";
    ?>
    var current_gabarit_name;
    $(document).ready(function () {
        // Gestion des gabarits selon le type d'acte
        current_gabarit_name = gabarits[$('#listeTypeactesId').val()];
        $('#listeTypeactesId').change(function () {
            current_gabarit_name = gabarits[$('#listeTypeactesId').val()];
            //Le type d'acte possède un gabarit de texte d'acte
            if (current_gabarit_name != undefined) {
                $('#ajouteMultiDelib .gabarit_name_multidelib').text(current_gabarit_name);
                $('#ajouteMultiDelib .MultidelibGabaritBloc').show();
                $('#ajouteMultiDelib .texte_acte_multidelib').each(function () {
                    //Le formulaire d'upload est vide
                    if ($(this).val() == '') {
                        //Désactivation et masquage du champ d'upload
                        $(this).prop('disabled', true).hide();
                        $(this).closest('.delibRattachee').find('.MultidelibGabaritBloc').show();
                        $(this).closest('.delibRattachee').find('input.gabarit_acte_multidelib').prop('disabled', false);
                    } else { //Formulaire d'upload non
                        //Activationvide
                        $(this).prop('disabled', false).show();
                        $(this).closest('.delibRattachee').find('input.gabarit_acte_multidelib').prop('disabled', true);
                        $(this).closest('.delibRattachee').find('.MultidelibGabaritBloc').hide();
                    }
                });
            } else { // Pas de gabarit associé au type d'acte
                $('#ajouteMultiDelib .MultidelibGabaritBloc').hide();
                $('#ajouteMultiDelib .delibRattachee input.gabarit_acte_multidelib').prop('disabled', true);
                $('#ajouteMultiDelib .texte_acte_multidelib').prop('disabled', false).show();
            }
        });

        // Gestion des formats d'annexe (affichage checkboxes)
        $("#Annex0File").change(function(){
            if ($(this).val() != '') {
                var tmpArray = $(this).val().split('.');
                var extension = tmpArray[tmpArray.length - 1];
                if ($.inArray(extension, extensions) === -1) {
                    $.jGrowl("Les fichiers " + extension + " ne sont pas autorisés.", {header: "<strong>Erreur :</strong>"});
                    $(this).val(null);
                    return false;
                } else {
                    if ($.inArray(extension, extensionsFusion) === -1){
                        $('#Annex0Fusion').prop('checked', false).closest('div').hide();
                    }else{
                        $('#Annex0Fusion').closest('div').show();
                    }
                    if ($.inArray(extension, extensionsCtrl) === -1){
                        $('#Annex0Ctrl').prop('checked', false).closest('div').hide();
                    }else{
                        $('#Annex0Ctrl').closest('div').show();
                    }
                }

            }
        });


    })
</script>