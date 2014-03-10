<?php
/**
 * Déliberation principale
 */
if (!empty($this->data['Deliberation']['id'])) {
    echo $this->Html->tag('fieldset', null, array('id' => 'delibRattachee' . $this->data['Deliberation']['id'], 'style' => 'outline 1px solid #ccc; padding:10px;'));
    echo $this->Html->tag('legend', 'Délibération principale : ' . $this->data['Deliberation']['id']);
    echo $this->Form->input('Deliberation.objet_delib', array('type' => 'textarea', 'label' => 'Libellé&nbsp;<abbr title="obligatoire">*</abbr>', 'cols' => '60', 'rows' => '2'));
    // div pour recevoir le texte de la délib
    echo $this->Html->tag('div', '', array('id' => 'texteDelibOngletDelib'));
    echo $this->Html->tag('div', '', array('id' => 'delibPrincipaleAnnexeRatt'));
    echo $this->Html->tag('div', $this->Html->tag('small', '* Note : les modifications apportées ici ne prendront effet que lors de la sauvegarde du projet.'), array('class'=>'text-right'));
    echo $this->Html->tag('/fieldset');
}


/**
 * Délibérations rattachées
 */
echo $this->element('editDelibRattachee');
echo $this->Html->tag('div', '', array('id' => 'ajouteMultiDelib'));

echo $this->Html->tag('div', '', array('class' => 'spacer'));
// lien pour ajouter une nouvelle délibération rattachée
echo $this->Html->tag('div',$this->Html->link('<i class="fa fa-plus"></i>&nbsp;Ajouter une délibération rattachée', 'javascript:ajouterMultiDelib()', array('class' => 'btn btn-inverse noWarn', 'id' => 'ajouterMultiDelib', 'escape' => false)), array('class' => 'text-center'));

echo $this->element('templateAjoutDelibRattachee');

echo $this->Html->tag('div', '', array('class' => 'spacer'));