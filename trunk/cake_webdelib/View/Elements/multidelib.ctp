<?php
// initialisation des boutons pour les délibération
$links = array(
    'modifier' => array('title' => SHY, 'url' => '#', 'escapeTitle' => false, 'htmlAttributes' => array('class' => 'link_modifier', 'escape' => false, 'title' => 'Modifier')),
    'annulerModifier' => array('title' => SHY, 'url' => '#', 'escapeTitle' => false, 'htmlAttributes' => array('class' => 'link_modifier_back', 'escape' => false, 'title' => 'Annuler les modifications', 'style' => 'display: none;')),
    'supprimer' => array('title' => SHY, 'url' => '#', 'escapeTitle' => false, 'htmlAttributes' => array('class' => 'link_supprimer', 'title' => 'Supprimer', 'escape' => false)),
    'annulerSupprimer' => array('title' => SHY, 'url' => '#', 'escapeTitle' => false, 'htmlAttributes' => array('class' => 'link_supprimer_back', 'escape' => false, 'title' => 'Annuler la suppression', 'style' => 'display: none;')),
);

if (isset($this->data['Deliberation']['id'])) {
    echo $this->Html->tag('fieldset', null, array('id' => 'delibRattachee' . $this->data['Deliberation']['id']));
    echo $this->Html->tag('legend', '&nbsp;Délibération : ' . $this->data['Deliberation']['id'] . '&nbsp;');
}

echo $this->Form->input('Deliberation.objet_delib', array('type' => 'textarea', 'label' => 'Libellé<abbr title="obligatoire">(*)</abbr>', 'cols' => '60', 'rows' => '2'));
echo $this->Html->tag('div', '', array('class' => 'spacer'));

// div pour recevoir le texte de la délib
echo $this->Html->tag('div', '', array('id' => 'texteDelibOngletDelib'));
echo $this->Html->tag('div', '', array('class' => 'spacer'));
echo $this->Html->tag('label', 'Annexe(s)');
echo '<div class="fckEditorProjet">';
echo $this->Html->tag('div', '', array('id' => 'delibPrincipaleAnnexeRatt'));
echo '</div>';
echo $this->Html->tag('div', '', array('class' => 'spacer'));

echo $this->Html->tag('/fieldset');
echo $this->Html->tag('div', '', array('class' => 'spacer'));

// affichage des délibérations rattachées
if (isset($this->data['Multidelib'])) {
    foreach ($this->data['Multidelib'] as $delib) {
        echo $this->Html->tag('fieldset', null, array('id' => 'delibRattachee' . $delib['id']));
        echo $this->Html->tag('legend', '&nbsp;Délibération rattachée : ' . $delib['id'] . '&nbsp;');
        //Pour la modification
        echo $this->Form->hidden('Multidelib.' . $delib['id'] . '.id', array('value' => $delib['id'], 'disabled' => false));
        // info pour la suppression
        echo $this->Form->hidden('MultidelibASupprimer.' . $delib['id'], array('value' => $delib['id'], 'disabled' => true));
        // affichage de la délibération rattachée
        echo $this->Html->tag('div', null, array('id' => 'delibRattacheeDisplay' . $delib['id']));
        // affichage libellé
        echo $this->Html->tag('label', 'Libellé <abbr title="obligatoire">(*)</abbr>');
        echo $this->Html->tag('span', $delib['objet_delib'], array('id' => 'Multidelib' . $delib['id'] . 'libelle'));
        echo $this->Html->tag('div', '', array('class' => 'spacer'));
        // affichage texte de délibération
        echo $this->Html->tag('label', 'Texte acte');
        if (Configure::read('GENERER_DOC_SIMPLE'))
            echo $this->Html->tag('span', $delib['deliberation']);
        else
            echo $this->Html->tag('span', $delib['deliberation_name']);

        echo $this->Html->tag('div', '', array('class' => 'spacer'));
        // affichage des annexes
        echo $this->Html->tag('label', 'Annexe(s)');
        echo '<div class="fckEditorProjet">';
        //$annexeOptions = array('mode' => 'display');
        //if (isset($delib['Annex'])) $annexeOptions['annexes'] = $delib['Annex'];
        echo $this->element('annexe', array_merge(array('mode' => 'display'), $annexes));
        echo '</div>';
        echo $this->Html->tag('div', '', array('class' => 'spacer'));
        echo $this->Html->tag('/div');
        // modification de la délibération rattachée
        echo $this->Html->tag('div', null, array('id' => 'delibRattacheeForm' . $delib['id'], 'style' => 'display: none'));
        //echo $this->Form->hidden('Multidelib.'.$delib['id'].'.id', array('value'=>$delib['id'], 'disabled'=>true));
        // saisie libellé
        echo $this->Html->tag('label', 'Libellé <abbr title="obligatoire">(*)</abbr>');
        echo $this->Form->input('Multidelib.' . $delib['id'] . '.objet_delib', array(
            'type' => 'textarea',
            'label' => false,
            'cols' => '60',
            'rows' => '1',
            'value' => $delib['objet_delib'],
            'disabled' => false));
        echo $this->Html->tag('div', '', array('class' => 'spacer'));
        // saisie texte de délibération
        echo $this->Html->tag('label', 'Texte délibération');
        if (Configure::read('GENERER_DOC_SIMPLE')) {
            echo '<div class="fckEditorProjet">';
            echo $this->Form->input('Multidelib.' . $delib['id'] . '.deliberation', array(
                'label' => '',
                'type' => 'textarea',
                'value' => $delib['deliberation'],
                'disabled' => true));
            echo '</div>';
        } else {
            if (empty($delib['deliberation_name']))
                echo $this->Form->input("Multidelib." . $delib['id'] . ".deliberation", array('label' => false, 'type' => 'file', 'title' => 'Texte d&eacute;lib&eacute;ration', 'disabled' => true));
            else {
                $url = Configure::read('PROTOCOLE_DL') . "://" . $_SERVER['SERVER_NAME'] . "/files/generee/projet/" . $delib['id'] . "/deliberation.odt";
                echo $this->Html->tag('span', '', array('id' => 'MultidelibDeliberationAdd' . $delib['id'], 'style' => 'display: none;'));
                echo $this->Html->tag('span', null, array('id' => 'MultidelibDeliberationDisplay' . $delib['id']));
                echo "<a href='$url'>" . $delib['deliberation_name'] . "</a>";
                echo '&nbsp;&nbsp;';
                echo $this->Html->link(
                    '<i class="fa fa-trash-o"></i> Supprimer',
                    'javascript:supprimerTextDelibDelibRattachee(' . $delib['id'] . ')',
                    array('escape' => false, 'class' => 'btn btn-danger btn-mini'),
                    'Voulez-vous vraiment supprimer le fichier ?');
                echo $this->Html->tag('/span');
            }
        }
        echo $this->Html->tag('div', '', array('class' => 'spacer'));
        // saisie des annexes
        echo $this->Html->tag('label', 'Annexe(s)');
        echo '<div class="fckEditorProjet">';
        $annexeOptions = array('ref' => 'delibRattachee' . $delib['id'], 'affichage' => 'partiel');
        if (isset($delib['Annex'])) $annexeOptions['annexes'] = $delib['Annex'];
        echo $this->element('annexe', $annexeOptions);
        echo '</div>';
        echo $this->Html->tag('/div');
        echo $this->Html->tag('div', '', array('class' => 'spacer'));
        // affichage des boutons action
        echo $this->Html->tag('div', null, array('id' => 'delibRattacheeAction' . $delib['id'], 'class' => 'action'));
        $links['modifier']['url'] = '#delibRattachee' . $delib['id'];
        $links['modifier']['htmlAttributes']['onclick'] = 'modifierDelibRattachee(this, ' . $delib['id'] . ')';
        $links['annulerModifier']['url'] = '#delibRattachee' . $delib['id'];
        $links['annulerModifier']['htmlAttributes']['onclick'] = 'annulerModifierDelibRattachee(this, ' . $delib['id'] . ')';
        $links['supprimer']['url'] = '#delibRattachee' . $delib['id'];
        $links['supprimer']['htmlAttributes']['onclick'] = 'supprimerDelibRattachee(this, ' . $delib['id'] . ')';
        $links['annulerSupprimer']['url'] = '#delibRattachee' . $delib['id'];
        $links['annulerSupprimer']['htmlAttributes']['onclick'] = 'annulerSupprimerDelibRattachee(this, ' . $delib['id'] . ')';
        echo $this->Menu->linkBarre($links);
        echo $this->Html->tag('/div');
        echo $this->Html->tag('/fieldset');
        echo $this->Html->tag('div', '', array('class' => 'spacer'));
    }
}

// Ajout des délibérations
// template pour l'ajout
echo $this->Html->tag('div', null, array('id' => 'ajouteMultiDelibTemplate', 'style' => 'width:800px; display:none'));
echo $this->Html->tag('fieldset', null, array('id' => 'delibRattachee0'));
echo $this->Html->tag('legend', 'Nouvelle délibération rattachée ');
echo $this->Html->tag('div', null, array('id' => 'delibRattacheeForm0'));
// saisie libellé
echo $this->Html->tag('label', 'Libellé <abbr title="obligatoire">(*)</abbr>');
echo $this->Form->input('Multidelib.0.objet', array(
    'type' => 'textarea',
    'label' => false,
    'value' => '',
    'cols' => '60', 'rows' => '1',
    'disabled' => true));
echo $this->Html->tag('div', '', array('class' => 'spacer'));
// saisie texte de délibération
echo $this->Html->tag('label', 'Texte acte');
if (Configure::read('GENERER_DOC_SIMPLE')) {
    echo '<div class="fckEditorProjet">';
    echo $this->Form->input('Multidelib.0.deliberation', array(
        'label' => false,
        'type' => 'textarea',
        'value' => '',
        'disabled' => true));
    echo '</div>';
} else
    echo $this->Form->input("Multidelib.0.deliberation", array(
        'label' => false,
        'type' => 'file',
        'size' => '60',
        'disabled' => true));
echo $this->Html->tag('/div');
echo $this->Html->tag('div', '', array('class' => 'spacer'));

echo $this->Html->tag('label', 'Annexe(s)');
echo '<div class="fckEditorProjet">';
// div pour l'ajout des annexes
echo $this->Html->tag('div', '', array('id' => 'ajouteAnnexesRef'));

// lien pour ajouter une nouvelle annexes
echo $this->Html->tag('div', '', array('class' => 'spacer'));
echo $this->Html->link('Ajouter une annexe', 'javascript:ajouterAnnexe(\'#ref#\')', array('class' => 'link_annexe', 'id' => 'lienAjouteAnnexesRef'));
echo '</div>';
echo $this->Html->tag('div', '', array('class' => 'spacer'));

// affichage des boutons action
echo $this->Html->tag('div', null, array('id' => 'delibRattacheeAction0', 'class' => 'action'));
echo $this->Html->link('Annuler', '#self', array('class' => 'btn btn-link', 'onClick' => 'javascript:$(this).parent().parent().parent().remove();'));
echo $this->Html->tag('/div');
echo $this->Html->tag('/fieldset');
echo $this->Html->tag('/div');

// div pour l'ajout les délibérations rattachées
echo $this->Html->tag('div', '', array('id' => 'ajouteMultiDelib'));

// lien pour ajouter une nouvelle délibération rattachée
echo $this->Html->tag('div', '', array('class' => 'spacer'));
echo $this->Html->link('Ajouter une délibération rattachée', 'javascript:ajouterMultiDelib()', array('class' => 'link_annexe'));
echo $this->Html->tag('div', '', array('class' => 'spacer'));
echo $this->Html->tag('p', 'Note : les modifications apportées ici ne prendront effet que lors de la sauvegarde du projet.');
?>
<script>
    // variables globales
    var iMultiDelibAAjouter = 1000;

    // Fonction d'ajout d'une nouvelle deliberation : duplique le div ajouteMultiDelibTemplate et incrémente l'indexe
    function ajouterMultiDelib() {
        iMultiDelibAAjouter++;
        var newTemplate = $('#ajouteMultiDelibTemplate').clone();
        newTemplate.attr('id', newTemplate.attr('id').replace('Template', iMultiDelibAAjouter));
        newTemplate.find('textarea').each(function () {
            $(this).removeAttr('disabled');
            $(this).attr('id', $(this).attr('id').replace('0', iMultiDelibAAjouter));
            $(this).attr('name', $(this).attr('name').replace('0', iMultiDelibAAjouter));
            $(this).attr('name', $(this).attr('name').replace('objet', 'objet_delib'));
        });
        newTemplate.find('input').each(function () {
            $(this).removeAttr('disabled');
            $(this).attr('id', $(this).attr('id').replace('0', iMultiDelibAAjouter));
            $(this).attr('name', $(this).attr('name').replace('0', iMultiDelibAAjouter));
        });

        newTemplate.find('#ajouteAnnexesRef').attr('id', 'ajouteAnnexesdelibRattachee' + iMultiDelibAAjouter);
        var lienAjouteAnnexe = newTemplate.find('#lienAjouteAnnexesRef');
        lienAjouteAnnexe.attr('href', lienAjouteAnnexe.attr('href').replace('#ref#', 'delibRattachee' + iMultiDelibAAjouter));
        lienAjouteAnnexe.removeAttr('id');


        $('#ajouteMultiDelib').append(newTemplate);
        <?php
        if (Configure::read('GENERER_DOC_SIMPLE')){
            echo "$('#Multidelib'+iMultiDelibAAjouter+'Deliberation').ckeditor();\n";
        }
        ?>
        newTemplate.show();
    }

    // Fonction de modification d'une délibération rattachée
    function modifierDelibRattachee(obj, delibId) {
        $('#delibRattacheeDisplay' + delibId).hide();
        $('#delibRattacheeForm' + delibId).show();

        $('#Multidelib' + delibId + 'Id').removeAttr('disabled');
        $('#Multidelib' + delibId + 'Objet').removeAttr('disabled').show();
        $('#Multidelib' + delibId + 'Deliberation').removeAttr('disabled').show();
        if ($('#Multidelib' + delibId + 'Deliberation').length)
            $('#Multidelib' + delibId + 'Deliberation').ckeditor();

        $('#Multidelib' + delibId + 'ObjetDelib').val($('#Multidelib' + delibId + 'libelle').text());

        $(obj).hide();
        $(obj).next().show();
        $(obj).next().next().hide();
    }

    // Fonction d'annulation des modifications d'une délibération rattachée
    function annulerModifierDelibRattachee(obj, delibId) {
        $('#delibRattacheeDisplay' + delibId).show();

        $('#delibRattacheeForm' + delibId).hide();
        $('#Multidelib' + delibId + 'Id').attr('disabled', true);
        $('#Multidelib' + delibId + 'Objet').attr('disabled', true);
        $('#Multidelib' + delibId + 'Deliberation').attr('disabled', true);
        if ($('#Multidelib' + delibId + 'Deliberation').length)
            $('#Multidelib' + delibId + 'Deliberation').ckeditor(function () {
                this.destroy();
            });

        $('#Multidelib' + delibId + 'ObjetDelib').val($('#Multidelib' + delibId + 'libelle').text());

        var tabAnnexe = $('#tableAnnexedelibRattachee' + delibId);
        tabAnnexe.find('tr').each(function () {
            if ($(this).hasClass('aSupprimer')) {
                var annexeId = $(this).attr('data-annexeid');
                var boutonAnnulerSupprimer = $(this).find('.link_supprimer_back');
                annulerSupprimerAnnexe(boutonAnnulerSupprimer, annexeId);
            }
            if ($(this).hasClass('aModifier')) {
                var annexeId = $(this).attr('data-annexeid');
                var boutonAnnulerModifier = $(this).find('.link_modifier_back');
                annulerModifierAnnexe(boutonAnnulerModifier, annexeId);
            }
        });

        $(obj).hide();
        $(obj).prev().show();
        $(obj).next().show();
    }

    // Fonction de suppression d'une délibération rattachée
    function supprimerDelibRattachee(obj, delibId) {
        $('#delibRattacheeDisplay' + delibId).addClass('aSupprimer');

        $('#MultidelibASupprimer' + delibId).removeAttr('disabled');
        $(obj).hide();
        $(obj).next().show();
        $(obj).prev().prev().hide();
    }

    // Fonction de d'annulation de suppression d'une annexe
    function annulerSupprimerDelibRattachee(obj, delibId) {
        $('#delibRattacheeDisplay' + delibId).removeClass('aSupprimer');

        $('#MultidelibASupprimer' + delibId).attr('disabled', true);
        $(obj).hide();
        $(obj).prev().show();
        $(obj).prev().prev().prev().show();
    }

    // Fonction de suppression du texte de délibération sous forme de fichier joint
    function supprimerTextDelibDelibRattachee(delibId) {
        $('#MultidelibDeliberationDisplay' + delibId).hide();
        $('#MultidelibDeliberationAdd' + delibId)
            .html('<input type="file" id="Multidelib' + delibId + 'Deliberation" value="" title="" size="60" name="data[Multidelib][' + delibId + '][deliberation]"></input>')
            .show();
    }
</script>
