<?php
/*
	Gestion des annexes : affichage, modification, suppression
	Paramètres :
		string	$mode = 'edit' : mode édition ('edit') ou affichage ('display'), dans ce dernier cas, les autres paramètres ne sont pas ovligatoires
		string	$ref : référence d'appartenance des annexes pour les nouvelles annexes (delibPrincipale, delibRattachee1, delibRattachee2, ...)
		array	$annexes = array() : liste des annexes a afficher
		boolean	$affichage = 'complet' : 'complet', affiche tout, y compris le javascript, 'partiel' affiche uniquement le nécessaire pour ne pas répéter des élements du dom
*/
// Initialisation des paramètres
if (empty($mode)) $mode = 'edit';
if ($mode == 'edit' && empty($ref)) return;
if (empty($annexes)) $annexes = array();
if (empty($affichage)) $affichage = 'complet';

// affichage des annexes
$tableOptions = array('style' => 'width: 100%');
if ($mode == 'edit') $tableOptions['id'] = 'tableAnnexe' . $ref;
echo $this->Html->tag('table', null, $tableOptions);
if ($mode == 'edit')
    echo $this->Html->tableHeaders(array( 
        array('No'=>array('style'=>'width: 5%')), 
        array('Nom du fichier'=>array('style'=>'width: 25%')), 
        array('Titre'=>array('style'=>'width: 25%')), 
        array('Joindre au  contrôle de légalité'=>array('style'=>'width: 20%')),
        array('Joindre à la fusion'=>array('style'=>'width: 15%')), 
        array('Action'=>array('style'=>'width: 10%'))
        ));
else
    echo $this->Html->tableHeaders(array( 
        array('No'=>array('style'=>'width: 5%')), 
        array('Nom du fichier'=>array('style'=>'width: 30%')), 
        array('Titre'=>array('style'=>'width: 30%')), 
        array('Joindre au  contrôle de légalité'=>array('style'=>'width: 20%')),
        array('Joindre à la fusion'=>array('style'=>'width: 15%'))
        ));
if (isset($annexes)) {
    foreach ($annexes as $rownum => $annexe) {
        $rowClass = array();
        if ($rownum & 1) $rowClass['class'] = 'altrow';
        if ($mode == 'edit') {
            $rowClass['style'] = 'height: 36px';
            $rowClass['id'] = 'afficheAnnexe' . $annexe['Annex']['id'];
            $rowClass['data-annexeid'] = $annexe['Annex']['id'];
        }
        echo $this->Html->tag('tr', null, $rowClass);
        echo $this->Html->tag('td', $rownum + 1);
        echo $this->Html->tag('td');
        if ($mode == 'edit') {
            if (isset($annexe['Annex']['edit']) && $annexe['Annex']['edit']) {
                echo  $this->Html->link('modifier : '.$annexe['Annex']['filename'] , $annexe['Annex']['link'] , array(
                        'id'=>'urlWebdavAnnexe'.$annexe['Annex']['id'],
                        'style'=>'display:none;',
                        'title'=>'Modifier le fichier'));
            }
        } 
        // lien de téléchargement de la version pdf de l'annexe
        echo $this->Html->tag('span', $this->Html->link($annexe['Annex']['filename'], '/annexes/download/' . $annexe['Annex']['id'], array('class' => 'noWarn', 'title' => 'Télécharger le fichier')));
        echo $this->Html->tag('/td');
        echo $this->Html->tag('td');
        if ($mode == 'edit') {
            echo $this->Html->tag('span', $annexe['Annex']['titre'], array(
                'id' => 'afficheAnnexeTitre' . $annexe['Annex']['id'],
                'data-valeurinit' => $annexe['Annex']['titre']));
            echo $this->Form->input('AnnexesAModifier.' . $annexe['Annex']['id'] . '.titre', array(
                'id' => 'modifieAnnexeTitre' . $annexe['Annex']['id'],
                'label' => false,
                'value' => $annexe['Annex']['titre'],
                'maxlength' => '200',
                'disabled' => true,
                'style' => 'display:none;'));
        } else
            echo $this->Html->tag('span', $annexe['Annex']['titre']);
        echo $this->Html->tag('/td');

        echo $this->Html->tag('td');
        if ($mode == 'edit') {
            echo $this->Html->tag('span', $annexe['Annex']['joindre_ctrl_legalite'] ? 'Oui' : 'Non', array(
                'id' => 'afficheAnnexeCtrl' . $annexe['Annex']['id'],
                'data-valeurinit' => $annexe['Annex']['joindre_ctrl_legalite']));
            echo $this->Form->input('AnnexesAModifier.' . $annexe['Annex']['id'] . '.joindre_ctrl_legalite', array(
                'id' => 'modifieAnnexeCtrl' . $annexe['Annex']['id'],
                'label' => false,
                'type' => 'checkbox',
                'checked' => ($annexe['Annex']['joindre_ctrl_legalite'] == 1),
                'disabled' => 'disabled',
                'style' => 'display:none;'));
        } else
            echo $this->Html->tag('span', $annexe['Annex']['joindre_ctrl_legalite'] ? 'Oui' : 'Non');
        echo $this->Html->tag('/td');
        echo $this->Html->tag('td');
        if ($mode == 'edit') {
            echo $this->Html->tag('span', $annexe['Annex']['joindre_fusion'] ? 'Oui' : 'Non', array(
                'id' => 'afficheAnnexeFusion' . $annexe['Annex']['id'],
                'data-valeurinit' => $annexe['Annex']['joindre_fusion']));
            echo $this->Form->input('AnnexesAModifier.' . $annexe['Annex']['id'] . '.joindre_fusion', array(
                'id' => 'modifieAnnexeFusion' . $annexe['Annex']['id'],
                'label' => false,
                'type' => 'checkbox',
                'checked' => ($annexe['Annex']['joindre_fusion'] == 1),
                'disabled' => 'disabled',
                'style' => 'display:none;'));
        } else
            echo $this->Html->tag('span', $annexe['Annex']['joindre_fusion'] ? 'Oui' : 'Non');
        echo $this->Html->tag('/td');

        if ($mode == 'edit') {
            echo $this->Html->tag('td', null,array('style'=>'text-align:center'));
            echo $this->Html->link(SHY, 'javascript:void(0);', array('title' => 'Supprimer',
                'class' => "link_supprimer",
                'escape' => false,
                'onClick' => 'supprimerAnnexe(this, ' . $annexe['Annex']['id'] . ')'), false);
            echo $this->Html->link(SHY, 'javascript:void(0);', array('title' => 'Annuler la suppression',
                'class' => 'link_supprimer_back',
                'escape' => false,
                'onClick' => "annulerSupprimerAnnexe(this, " . $annexe['Annex']['id'] . ")",
                'style' => 'display: none;'), false);
            echo '&nbsp;&nbsp;';
            echo $this->Html->link(SHY, 'javascript:void(0);', array('title' => 'Modifier',
                'escape' => false,
                'class' => 'link_modifier',
                'onClick' => 'modifierAnnexe(this, ' . $annexe['Annex']['id'] . ')'), false);
            echo $this->Html->link(SHY, 'javascript:void(0);', array('title' => 'Annuler la modification',
                'class' => 'link_modifier_back',
                'escape' => false,
                'onClick' => "annulerModifierAnnexe(this, " . $annexe['Annex']['id'] . ")",
                'style' => 'display: none;'), false);
            echo $this->Html->tag('/td');
        }
        echo $this->Html->tag('/tr');
    }
}
echo $this->Html->tag('/table');
echo $this->Html->tag('div', '', array('class' => 'spacer'));

if ($mode != 'edit') return;

// div pour la suppression des annexes
if ($affichage == 'complet')
    echo $this->Html->tag('div', '', array('id' => 'supprimeAnnexes'));

// template pour l'ajout des annexes
if ($affichage == 'complet') {
    echo $this->Html->tag('div', null, array('id' => 'ajouteAnnexeTemplate', 'style' => 'width:800px; display:none; margin-bottom: 20px;'));
    echo $this->Html->tag('fieldset');
    echo $this->Html->tag('legend', 'Nouvelle annexe');
    echo $this->Form->hidden('Annex.0.ref', array('disabled' => 'disabled'));
    echo $this->Form->input('Annex.0.file', array('label' => 'Annexe<abbr title="obligatoire">(*)</abbr>', 'type' => 'file', 'disabled' => 'disabled'));
    echo $this->Html->tag('div', '', array('class' => 'spacer'));
    echo $this->Form->input('Annex.0.titre', array('label' => 'Titre', 'value' => '', 'disabled' => 'disabled'));
    echo $this->Html->tag('div', '', array('class' => 'spacer'));
    echo $this->Form->input('Annex.0.ctrl', array('label' => array('text' => 'Joindre au controle de légalité', 'style' => 'width:auto'), 'type' => 'checkbox', 'checked' => false, 'disabled' => 'disabled'));
    echo $this->Html->tag('div', '', array('class' => 'spacer'));
    echo $this->Form->input('Annex.0.fusion', array('label' => array('text' => 'Joindre à la fusion', 'style' => 'width:auto'), 'type' => 'checkbox', 'checked' => true, 'disabled' => 'disabled'));
    echo $this->Html->tag('div', '', array('class' => 'spacer'));
    echo $this->Html->link('Annuler', '#self', array('class' => 'btn btn-link', 'onClick' => 'javascript:$(this).parent().parent().remove();'));
    echo $this->Html->tag('/fieldset');
    echo $this->Html->tag('/div');
}

// div pour l'ajout des annexes
echo $this->Html->tag('div', '', array('id' => 'ajouteAnnexes' . $ref));

// lien pour ajouter une nouvelle annexes
echo $this->Html->tag('div', '', array('class' => 'spacer'));
echo $this->Html->link('Ajouter une annexe', 'javascript:ajouterAnnexe(\'' . $ref . '\')', array('class' => 'link_annexe noWarn'));

if ($affichage != 'complet') return;
?>
<script>
    // variables globales
    var nbAnnexeAAjouter = 0;

    // Fonction d'ajout d'une nouvelle annexe : duplique le div ajouteAnnexeTemplate et incrémente l'indexe
    function ajouterAnnexe(ref) {
        nbAnnexeAAjouter++;
        var addDiv = $('#ajouteAnnexes' + ref);
        var newTemplate = $('#ajouteAnnexeTemplate').clone();
        newTemplate.attr('id', newTemplate.attr('id').replace('Template', nbAnnexeAAjouter));
        newTemplate.find('#Annex0Ref').val(ref);
        newTemplate.find('input').each(function () {
            $(this).removeAttr('disabled');
            $(this).attr('id', $(this).attr('id').replace('0', nbAnnexeAAjouter));
            $(this).attr('name', $(this).attr('name').replace('0', nbAnnexeAAjouter));
        });
        newTemplate.find('label').each(function () {
            $(this).attr('for', $(this).attr('for').replace('0', nbAnnexeAAjouter));
        });
        newTemplate.find('legend').text('Nouvelle annexe');
        addDiv.append(newTemplate);
        newTemplate.show();
    }

    // Fonction de suppression d'une annexe
    function supprimerAnnexe(obj, annexeId) {
        $('#afficheAnnexe' + annexeId).addClass('aSupprimer');
        var supAnnexe = $(document.createElement('input')).attr({
            id: 'supprimeAnnexe' + annexeId,
            name: 'data[AnnexesASupprimer][' + annexeId + ']',
            type: 'hidden', value: annexeId});
        $('#supprimeAnnexes').append(supAnnexe);
        $(obj).hide();
        $(obj).next().show();
        $(obj).next().next().hide();
        $(obj).next().next().next().hide();
    }

    // Fonction de d'annulation de suppression d'une annexe
    function annulerSupprimerAnnexe(obj, annexeId) {
        $('#afficheAnnexe' + annexeId).removeClass('aSupprimer');
        $('#supprimeAnnexe' + annexeId).remove();
        $(obj).hide();
        $(obj).prev().show();
        $(obj).next().show();
    }

    // Fonction de modification de l'annexe
    function modifierAnnexe(obj, annexeId) {
        var trObj = $('#afficheAnnexe' + annexeId);
        trObj.find('span').each(function () {
            $(this).hide();
        });
        trObj.find('input').each(function () {
            $(this).removeAttr('disabled');
            $(this).show();
        });
        $('#urlWebdavAnnexe' + annexeId).show();

        $('#afficheAnnexe' + annexeId).addClass('aModifier');

        $(obj).hide();
        $(obj).next().show();
        $(obj).prev().hide();
        $(obj).prev().prev().hide();
    }

    // Fonction d'annulation de la modification de l'annexe
    function annulerModifierAnnexe(obj, annexeId) {
        $('#modifieAnnexeTitre' + annexeId).val($('#afficheAnnexeTitre' + annexeId).attr('data-valeurinit'));
        $('#modifieAnnexeCtrl' + annexeId).attr('checked', $('#afficheAnnexeCtrl' + annexeId).attr('data-valeurinit') == 1);
        $('#modifieAnnexeFusion' + annexeId).attr('checked', $('#afficheAnnexeFusion' + annexeId).attr('data-valeurinit') == 1);
        var trObj = $('#afficheAnnexe' + annexeId);
        trObj.find('span').each(function () {
            $(this).show();
        });
        trObj.find('input').each(function () {
            $(this).attr('disabled', 'disabled');
            $(this).hide();
        });
        $('#urlWebdavAnnexe' + annexeId).hide();

        trObj.removeClass('aModifier');

        $(obj).hide();
        $(obj).prev().show();
        $(obj).prev().prev().prev().show();
    }

</script>
