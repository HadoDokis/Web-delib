<?php
echo $this->Bs->tag('h3', 'Modification de la collectivité');
echo $this->BsForm->create('Collectivite', array(
     'url' => array('action' => 'edit', $this->Html->value('Collectivite.id'))));
 if (isset($entities)) {
    echo $this->BsForm->select('Collectivite.id_entity', $entities,
            array(
                'selected' => $selected, 
                'label' => 'Nom')
            );
} else {
    echo $this->BsForm->input('Collectivite.nom', array('label' => 'Nom'));
}

echo $this->BsForm->input('Collectivite.adresse', array('label' => 'Adresse')) .
$this->BsForm->input('Collectivite.CP', array('label' => 'Code Postal')) .
$this->BsForm->input('Collectivite.ville', array('label' => 'Ville')) .
$this->BsForm->input('Collectivite.telephone', array('label' => 'Num téléphone')) .
$this->BsForm->hidden('Collectivite.id') .
$this->Html2->btnSaveCancel( '', $previous).     
$this->BsForm->end().
$this->Bs->scriptBlock("$('#CollectiviteIdEntity').select2({
        width: 'resolve'
    })");
