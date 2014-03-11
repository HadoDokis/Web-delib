<?php
/*
	Gestion des annexes : affichage, modification, suppression
	Paramètres :
		string	$mode = 'edit' : mode édition ('edit') ou affichage ('display'), dans ce dernier cas, les autres paramètres ne sont pas ovligatoires
		string	$ref : référence d'appartenance des annexes pour les nouvelles annexes (delibPrincipale, delibRattachee1, delibRattachee2, ...)
		array	$annexes = array() : liste des annexes a afficher
*/
// Initialisation des paramètres
if (empty($mode)) $mode = 'edit';
if ($mode == 'edit' && empty($ref)) return;
// affichage des annexes
$tableOptions = array('class' => 'table table-stripped');
if ($mode == 'edit') $tableOptions['id'] = 'tableAnnexes' . $ref;
if (empty($annexes)) {
    $annexes = array();
    $tableOptions['style'] = 'display:none;';
}
echo $this->Html->tag('table', null, $tableOptions);
echo $this->Html->tag('caption', 'Liste des annexes');
echo $this->Html->tag('thead', null);
$tableHeaders = array(
    array('No' => array('style' => 'width: 5%')),
    array('Nom du fichier' => array('style' => 'width: 25%')),
    array('Titre' => array('style' => 'width: 25%')),
    array('Joindre au contrôle de légalité' => array('style' => 'width: 20%')),
    array('Joindre à la fusion' => array('style' => 'width: 15%')),
);

if ($mode == 'edit') $tableHeaders[]['Actions'] = array('style' => 'width: 10%');

echo $this->Html->tableHeaders($tableHeaders);
echo $this->Html->tag('/thead');

echo $this->Html->tag('tbody', null);
foreach ($annexes as $rownum => $annexe) {
    $rowOptions = array();
    $rowOptions['id'] = $mode.'Annexe' . $annexe['Annex']['id'];
    $rowOptions['data-annexeid'] = $annexe['Annex']['id'];
    if (!empty($ref))
        $rowOptions['data-ref'] = $ref;
    echo $this->Html->tag('tr', null, $rowOptions);
    echo $this->Html->tag('td', $rownum + 1);
    echo $this->Html->tag('td');
    if (!empty($annexe['Annex']['edit'])) {
        echo $this->Html->link('<i class="fa fa-pencil"></i> ' . $annexe['Annex']['filename'], $annexe['Annex']['link'], array(
            'id' => 'urlWebdavAnnexe' . $annexe['Annex']['id'],
            'escape' => false,
            'class' => 'noWarn',
            'style' => 'display:none;',
            'title' => 'Modifier le fichier directement depuis votre poste en utilisant le protocol WebDAV'));
    } else {
        echo $this->Html->tag('span', $annexe['Annex']['filename'],
            array(
                'id' => 'urlWebdavAnnexe' . $annexe['Annex']['id'],
                'escape' => false,
                'class' => 'noWarn',
                'style' => 'display:none;',
                'title' => 'Edition WebDAV désactivée pour ce type de fichier'
            ));
    }
    // lien de téléchargement de la version pdf de l'annexe
    echo $this->Html->tag('span', $this->Html->link($annexe['Annex']['filename'], array('controller' => 'annexes', 'action' => 'download', $annexe['Annex']['id']), array('class' => 'noWarn', 'title' => 'Télécharger le fichier')));
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
            'class' => 'editAnnexeTitre',
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
            'div' => array('style' => 'display:none;')));
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
            'div' => array('style' => 'display:none;')));
    } else
        echo $this->Html->tag('span', $annexe['Annex']['joindre_fusion'] ? 'Oui' : 'Non');
    echo $this->Html->tag('/td');

    if ($mode == 'edit') {
        echo $this->Html->tag('td', null, array('style' => 'text-align:center'));
        echo $this->Html->tag('div', null, array('class' => "btn-group edit-delete-buttons"));

        echo $this->Html->link('<i class="fa fa-edit"></i> Modifier', 'javascript:void(0);', array(
            'title' => 'Modifier les caractéristiques de cette annexe',
            'escape' => false,
            'class' => 'btn btn-mini',
            'id' => 'modifierAnnexe' . $annexe['Annex']['id'] . $ref,
            'onClick' => 'modifierAnnexe(' . $annexe['Annex']['id'] . ')'
        ));

        echo $this->Html->link('<i class="fa fa-trash-o"></i> Supprimer', 'javascript:void(0);', array(
            'title' => 'Supprimer cette annexe',
            'class' => "btn btn-danger btn-mini",
            'id' => 'supprimerAnnexe' . $annexe['Annex']['id'] . $ref,
            'escape' => false,
            'onClick' => 'supprimerAnnexe(' . $annexe['Annex']['id'] . ')'
        ));
        echo $this->Html->tag('/div');

        echo $this->Html->link('<i class="fa fa-undo"></i> Annuler', 'javascript:void(0);', array(
            'title' => 'Annuler les modifications de cette annexe',
            'class' => 'btn btn-warning btn-mini cancel-edit-annexe',
            'escape' => false,
            'id' => 'annulerModifierAnnexe' . $annexe['Annex']['id'] . $ref,
            'onClick' => "annulerModifierAnnexe(" . $annexe['Annex']['id'] . ")",
            'style' => 'display: none;'
        ));

        echo $this->Html->link('<i class="fa fa-undo"></i> Annuler', 'javascript:void(0);', array(
            'title' => 'Annuler la suppression de cette annexe',
            'class' => 'btn btn-warning btn-mini cancel-delete-annexe',
            'escape' => false,
            'id' => 'annulerSupprimerAnnexe' . $annexe['Annex']['id'] . $ref,
            'onClick' => "annulerSupprimerAnnexe(" . $annexe['Annex']['id'] . ")",
            'style' => 'display: none;'
        ));

        echo $this->Html->tag('/td');
    }
    echo $this->Html->tag('/tr');
}
echo $this->Html->tag('/tbody');
echo $this->Html->tag('/table');

//echo $this->Html->tag('div', '', array('class' => 'spacer'));

if ($mode != 'edit') return;

// div pour la suppression des annexes
echo $this->Html->tag('div', '', array('id' => 'supprimeAnnexes' . $ref, 'style' => 'display:none'));

// div pour l'ajout des annexes
echo $this->Html->tag('div', '', array('id' => 'ajouteAnnexes' . $ref, 'style' => 'display:none'));

// lien pour ajouter une nouvelle annexes
echo $this->Html->link('<i class="fa fa-plus"></i>&nbsp;Ajouter une annexe', 'javascript:void(0)', array(
    'class' => 'btn btn-success noWarn annexeModalAddLink',
    'id' => 'annexeModalAddLink' . $ref,
    'data-ref' => $ref,
    'onclick' => 'afficherAnnexeModal(this);',
    'escape' => false
));
