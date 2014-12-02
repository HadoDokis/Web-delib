<?php

echo $this->Html->script('/libs/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js');
echo $this->Html->script('/libs/bootstrap-datetimepicker/js/locales/bootstrap-datetimepicker.fr.js');
echo $this->Html->css('/libs/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css');

$this->Html->addCrumb('Liste des acteurs', array($this->request['controller'], 'action'=>'index'));

if ($this->Html->value('Acteur.id')) {
    echo $this->Bs->tag('h3', 'Modification d\'un acteur');
    $this->Html->addCrumb('Modification d\'un acteur');
    echo $this->BsForm->create('Acteur', array('url' => array('controller' => 'acteurs', 'action' => 'edit', $this->Html->value('Acteur.id')), 'type' => 'post', 'name' => 'ActeurBsForm'));
} else {
    echo $this->Bs->tag('h3', 'Ajout d\'un acteur');
    $this->Html->addCrumb('Ajout d\'un acteur');
    echo $this->BsForm->create('Acteur', array('url' => array('controller' => 'acteurs', 'action' => 'add'), 'type' => 'post', 'name' => 'ActeurBsForm'));
}
echo $this->Bs->row();
echo $this->Bs->col('lg6').
$this->Html->tag('div', null, array('class' => 'panel panel-default')) .
$this->Html->tag('div', 'Identité', array('class' => 'panel-heading')) .
$this->Html->tag('div', null, array('class' => 'panel-body')) .
$this->BsForm->input('Acteur.salutation', array('label' => 'Civilité')).
$this->BsForm->input('Acteur.nom', array('label' => 'Nom <abbr title="obligatoire">*</abbr>')).
$this->BsForm->input('Acteur.prenom', array('label' => 'Prénom <abbr title="obligatoire">*</abbr>')).
$this->BsForm->input('Acteur.titre', array('label' => 'Titre')).
$this->Bs->close(2);  

echo $this->Html->tag('div', null, array('class' => 'panel panel-default')) .
$this->Html->tag('div', 'Adresse postale', array('class' => 'panel-heading')) .
$this->Html->tag('div', null, array('class' => 'panel-body')) .
        $this->BsForm->input('Acteur.adresse1', array('label' => 'Adresse 1')).
        $this->BsForm->input('Acteur.adresse2', array('label' => 'Adresse 2')).
        $this->BsForm->input('Acteur.cp', array('label' => 'Code postal')).
        $this->BsForm->input('Acteur.ville', array('label' => 'Ville')).
$this->Bs->close(2);

echo $this->Html->tag('div', null, array('class' => 'panel panel-default')) .
$this->Html->tag('div', 'Contacts', array('class' => 'panel-heading')) .
$this->Html->tag('div', null, array('class' => 'panel-body')) .
$this->BsForm->input('Acteur.telfixe', array('label' => 'Téléphone fixe')).
$this->BsForm->input('Acteur.telmobile', array('label' => 'Téléphone mobile')).
$this->BsForm->input('Acteur.email', array('label' => 'Email')).
$this->Bs->close(3);

echo $this->Bs->col('lg6');

echo $this->Html->tag('div', null, array('class' => 'panel panel-default')) .
$this->Html->tag('div', 'Suppléant', array('class' => 'panel-heading')) .
$this->Html->tag('div', null, array('class' => 'panel-body'));
        if ($this->Html->value('Acteur.suppleant_id'))
            $suppleant_id = $this->Html->value('Acteur.suppleant_id');
        else
            $suppleant_id = null;
        echo $this->BsForm->input('Acteur.suppleant_id', array('empty' => true, 'label' => 'Élus', 'selected' => $suppleant_id, 'options' => $acteurs)).
$this->Bs->close(2);

echo $this->Html->tag('div', null, array('class' => 'panel panel-default')) .
$this->Html->tag('div', 'Type', array('class' => 'panel-heading')) .
$this->Html->tag('div', null, array('class' => 'panel-body')).
$this->BsForm->select('Acteur.typeacteur_id', $typeacteurs, array(//data[Acteur][typeacteur_id]
        'onchange'=>'afficheInfosElus(this);',
     'id'=>'ActeurTypeacteurId',
     'label'=>'Type d\'acteur',
    'selected'=> (empty($acteur['Acteur']['typeacteur_id'])?$typeacteurs[$acteur['Acteur']['typeacteur_id']]:null)
         )); 

echo $this->BsForm->input('Acteur.position', array('label' => 'Ordre dans le conseil', 'size' => '3')) .
$this->BsForm->select('Service.Service',$services,  array('label' => 'Délégation(s)', 'default' => $selectedServices, 'multiple' => 'multiple', 'class' => 'selectMultiple', 'empty' => true, 'escape' => false)); 
echo $this->BsForm->datetimepicker('date', array('language'=>'fr', 'autoclose'=>'true','format' => 'dd/mm/yyyy','startView'=>'decade','minView'=>'day'), array(
    'label' => 'Date de naissance',
    'title' => 'Choisissez une date',
    'style' => 'cursor:pointer',
    'help' => 'Cliquez sur le champs ci-dessus pour choisir la date',
    'readonly' => 'readonly',
    'value'=>isset($date)?$date:'')).
$this->Bs->close(2);

echo $this->Html->tag('div', null, array('class' => 'panel panel-default')) .
$this->Html->tag('div', 'Autres informations', array('class' => 'panel-heading')) .
$this->Html->tag('div', null, array('class' => 'panel-body')).
$this->BsForm->input('Acteur.note', array('type' => 'textarea', 'label' => 'Note', 'cols' => '30')).
$this->Bs->close(4);      

if ($this->action == 'edit')
    echo $this->BsForm->hidden('Acteur.id');
echo $this->Html2->btnSaveCancel('', $previous).
        $this->BsForm->end();
?>
<script>
    window.onload = initAffichageInfosElus;

    function afficheInfosElus(typeActeur) {
        divElement = document.getElementById("infoElus");

        if ((typeActeur.value.length == 0) || (typeActeur.value == null)) {
            divElement.style.display = 'none';
        } else {
            if (typeActeur.options[typeActeur.selectedIndex].className == '1')
                divElement.style.display = '';
            else
                divElement.style.display = 'none';
        }
    }

    function initAffichageInfosElus() {
        selectTypeActeur = document.getElementById("ActeurTypeacteurId");
        afficheInfosElus(selectTypeActeur);
    }
    $(document).ready(function () {
        $('#ServiceService').select2({
            allowClear: true,
            placeholder: 'Aucun service',
            width: 'resolve',
            formatSelection: function (object, container) {
                return $.trim(object.text);
            }
        });

        $("#ActeurSuppleantId, #ActeurTypeacteurId").select2({
            width: "resolve",
            allowClear: true,
            placeholder: "Aucune sélection"
        });
    });
</script>