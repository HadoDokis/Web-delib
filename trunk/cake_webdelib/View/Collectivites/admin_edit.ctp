<?php
echo $this->Bs->tag('h3', 'Modification de la collectivité');
echo $this->Bs->container(array('class'=>'-fluid')) .
 $this->Bs->row() .
 $this->Bs->col('lg8') .
 $this->BsForm->create('Collectivite', array('url' => array('action' => 'edit', $this->Html->value('Collectivite.id'))));
if (isset($entities)) {
    echo $this->BsForm->input('Collectivite.id_entity', array('options' => $entities, 'selected' => $selected, 'label' => 'Nom'));
} else
    echo $this->BsForm->input('Collectivite.nom', array('label' => 'Nom'));

echo $this->BsForm->input('Collectivite.adresse', array('label' => 'Adresse')) .
$this->BsForm->input('Collectivite.CP', array('label' => 'Code Postal')) .
$this->BsForm->input('Collectivite.ville', array('label' => 'Ville')) .
$this->BsForm->input('Collectivite.telephone', array('label' => 'Num téléphone')) .
$this->BsForm->hidden('Collectivite.id') .
$this->Bs->div('btn-group col-md-offset-' . $this->BsForm->getLeft()) .
$this->Bs->btn($this->Bs->icon('arrow-left') . ' Retour', $previous, array('type' => 'default', 'escape' => false, 'title' => 'Annuler les modifications')) .
$this->Bs->btn('Enregistrer', null, array('tag' => 'button', 'type' => 'primary', 'icon' => 'glyphicon glyphicon-floppy-disk', 'escape' => false, 'title' => 'Enregistrer les modifications')) .
$this->Bs->close().
$this->BsForm->end() .
$this->Bs->close() .
$this->Bs->close() .
$this->Bs->close();
$this->Bs->scriptBlock("$('#CollectiviteIdEntity').select2({
        width: 'resolve'
    })");
