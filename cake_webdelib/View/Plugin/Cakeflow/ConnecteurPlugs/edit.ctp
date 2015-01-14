<?php

echo $this->Html->css("Cakeflow.design.css");
$this->Html->addCrumb(__('Gestion des connecteurs'), array('plugin' => '', 'controller' => 'connecteurs', 'action' => 'index'));
$this->Html->addCrumb(__('Modifier un connecteur'));
?>

<?php

echo $this->BsForm->create();

$name_content = '';
$name_content .= $this->Html->tag('legend', $connecteurEdit['ConnecteurPlug']['nom_u']);
$content = json_decode($connecteurEdit['ConnecteurPlug']['monfichier'], true);
foreach ($content['Connecteur'] as $nom => $champ) {
    $name_content .= $this->BsForm->input($nom, $champ);
}
$fieldset = $this->Html->tag('fieldset', $name_content);
echo $this->Html->tag('div', $fieldset, array('id' => 'name_content'));

echo $this->Html2->btnSaveCancel('', array('plugin' => null, 'controller' => 'connecteurs', 'action' => 'index'));

echo $this->BsForm->end();
?>

