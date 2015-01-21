<?php
$this->pageTitle = $this->action == 'add' ? __('Nouvelle étape', true) : __('Modification de l\'étape', true) . ' : ' . $this->data['Etape']['nom'];
echo $this->Html->tag('h3', $this->pageTitle);
$this->Html->addCrumb(__('Liste des circuits'), array('controller' => 'circuits', 'action' => 'index'));
$this->Html->addCrumb(__('Étapes du circuit'), array('action' => 'index', $circuit['Circuit']['id']));
$this->Html->addCrumb(__('Modification de l\'étape'));
echo $this->BsForm->create('Etape');
if ($this->action == 'edit') {
    echo $this->BsForm->input('Etape.id', array('type' => 'hidden'));
    echo $this->BsForm->input('Etape.ordre', array('type' => 'hidden'));
    echo $this->BsForm->input('retardInf', array('type' => 'hidden'));
}
echo $this->BsForm->input('Etape.circuit_id', array('type' => 'hidden'));
echo $this->BsForm->input('Etape.nom', array('label' => __('Nom', true)));
echo $this->BsForm->input('Etape.description', array('label' => __('Description', true), 'cols' => 100, 'rows' => 5));
echo $this->BsForm->select('Etape.type', $types, array('label' => __('Type', true), 'title' => __("- Type d'étape - \nSimple: accord requis \nConcurrent: l'accord d'un seul suffit \nCollaboratif: accord de tous requis")));
echo $this->BsForm->input('Etape.cpt_retard', array(
    'type' => 'number',
    'label' => array(
        'text' => __('Nombre de jours avant retard *', true),
        'title' => __('Nombre de jours avant la date de la séance pour déclencher l\'alerte de retard')),
    'min' => '0',
    'max' => $retard_max,
    'title' => __('Nombre de jours avant la date de la séance pour déclencher l\'alerte de retard'),
    'help' => __('La date de retard est calculée par rapport à la date de la séance délibérante du projet.') .
    (!empty($retard_max) ? __(' (Maximum : %s jours)', $retard_max) : '')
));
?>
<div class="spacer"></div>
<?php
echo $this->Html2->btnSaveCancel('', $previous) .
 $this->BsForm->end();
?>
<?php
echo $this->Html->script('Cakeflow.etapes');
?>
<script type="text/javascript">

    $('#EtapeType').select2({
        allowClear: true,
        placeholder: 'Aucun service',
        width: 'off'
    });

</script>   