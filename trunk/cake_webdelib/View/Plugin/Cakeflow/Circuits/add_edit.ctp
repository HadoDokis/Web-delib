<script>
    $(window).load(function() {
        onClickActif($('#CircuitActif').attr('checked'));
    });

    function onClickActif(actifChecked) {
        if (!actifChecked) {
            $('#CircuitDefaut').removeAttr('checked');
        }
    }
</script>
<?php
$this->Html->addCrumb(__('Liste des circuits'), array($this->request['controller'], 'action'=>'index'));
echo $this->Html->css('Cakeflow.design.css');
if (empty($this->data['Circuit']['id'])) {
    $action = 'add';
    $titre = __('Nouveau circuit de traitement', true);
} else {
    $action = 'edit';
    $titre = __('Edition du circuit de traitement : ', true) . $this->data['Circuit']['nom'];
}
$this->Html->addCrumb($titre);
echo $this->Bs->tag('h3', $titre);
echo $this->BsForm->create(null, array('action' => $action));
echo $this->BsForm->input('Circuit.nom', array('size' => '100', 'label' => __('Nom', true)));
echo $this->BsForm->input('Circuit.description', array('label' => __('Description', true), 'cols' => 100, 'rows' => 5));

echo $this->Html->tag("div", null, array("id" => "ckb_actif"));
echo $this->BsForm->checkbox('Circuit.actif', array(
    'label' => __('Actif', true),
    'onClick' => "onClickActif(this.checked);"));
echo $this->Html->tag('/div', null);
if (CAKEFLOW_GERE_DEFAUT)
    echo $this->BsForm->input('Circuit.defaut', array('label' => __('Circuit par défaut', true)));
else
    echo $this->BsForm->hidden('Circuit.defaut', array('value' => 0));

echo '<br />';

echo $this->BsForm->hidden('id');
//*echo $this->Html->tag('div', null, array('class' => 'submit'));
//echo $this->Html->tag("div", null, array("class" => "btn-group"));
//echo $this->Html->link('<i class="fa fa-arrow-left"></i> Annuler', array('action' => 'index'), array('class' => 'btn', 'escape' => false, 'title' => 'Annuler'));
//echo $this->BsForm->button('<i class="fa fa-check"></i> Valider', array('type' => 'submit', 'id' => 'boutonValider', 'class' => 'btn btn-primary', 'escape' => false, 'title' => 'Enregistrer le circuit de traitement'));

//echo $this->Html->tag('/div', null);
//echo $this->Html->tag('/div', null);
echo $this->Html2->btnSaveCancel( '',
        array('action' => 'index')).
        $this->BsForm->end();
?>