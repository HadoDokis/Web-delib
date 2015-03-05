<?php 
echo $this->BsForm->create('Deliberation', array(
    'type' => 'post', 
    'url' => array('controller' => 'deliberations', 
            'action' => 'listerPresents', 
            $delib_id, 
            $seance_id)
        ));

$this->BsForm->setLeft(0);
$this->BsForm->setRight(12);

echo $this->Bs->table(
    array(
        array('title' => __('Élu')),
        array('title' => __('Présent')),
        array('title' => __('Mandataire'))
    ), array('striped')
);

foreach ($presents as $present) {
    $options = array();
    $suppleant_id = $present['Acteur']['suppleant_id'];
    if (($suppleant_id != null) || isset($present['Acteur']['is_suppleant'])) {
        if (isset($present['Suppleant']['id'])) {
            $options[$present['Acteur']['id']] = "Titulaire : " . $present['Acteur']['prenom'] . ' ' . $present['Acteur']['nom'];
            $options[$suppleant_id] = "Suppléant : " . $present['Suppleant']['prenom'] . ' ' . $present['Suppleant']['nom'];
        }
    }
    
    //cellule Élu
    if (($suppleant_id != null) || isset($present['Acteur']['is_suppleant'])) {
        $cell_elu = $this->BsForm->select('Acteur.' . $present['Acteur']['id'] . '.suppleant_id',$options, array(
                'class' => 'select2 selectone',
                'label' => false,
                'inline' => true,
                'autocomplete' => 'off',
                'default' => $present['Acteur']['id'],
                'selected' => !empty($present['Listepresence']['suppleant_id']) ? $present['Listepresence']['suppleant_id'] : NULL
            ));
    } else {
        $cell_elu = $present['Acteur']['prenom'] . ' ' . $present['Acteur']['nom'];
    }

    //cellule Présent
    $selected = $present['Listepresence']['present'];
    $cell_present = $this->BsForm->checkbox('Acteur.' . $present['Acteur']['id'] . '.present', array('label' =>false, 'checked'=>$selected)); 

    //cellule Mandataire
    if (empty($present['Acteur']['id'])) {
        $cell_mandataire = $this->Form->input("Acteur." . $present['Acteur']['id'] . '.mandataire', 
        array(
            'id' => 'liste_Acteur' . $present['Acteur']['id'] . 'Present',
            'label' => false,
            'class' => 'select2 selectone',
            'options' => $mandataires,
            'readonly' => 'readonly',
            'disabled' => $present['Listepresence']['present'] == true ? 'disabled' : null,
            'empty' => true
        ));
    } else {
        $cell_mandataire = $this->BsForm->select("Acteur." . $present['Acteur']['id'] . '.mandataire', $mandataires,
        array(
            'id' => 'liste_Acteur' . $present['Acteur']['id'] . 'Present',
            'label' => false,
            'class' => 'select2 selectone',
            'empty' => true,
            'inline' => true,
            'autocomplete' => 'off',
            'disabled' => $present['Listepresence']['present'] == true ? 'disabled' : null,
            'value' => !empty($present['Listepresence']['mandataire']) ? $present['Listepresence']['mandataire'] : false
        ));
    }
    
    echo $this->Bs->cell($cell_elu);
    echo $this->Bs->cell($cell_present);
    echo $this->Bs->cell($cell_mandataire);
}

echo $this->Bs->endTable();
echo $this->BsForm->end();
