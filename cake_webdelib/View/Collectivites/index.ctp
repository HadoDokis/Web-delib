<?php
echo $this->Bs->tag('h3', 'Information de votre collectivité') .
 $this->Bs->table(array(array('title' => 'Collectivité'),
    array('title' => 'Logo'),
    array('title' => 'Actions'),
)) .
 $this->Bs->tableCells(array(
    $collectivite['Collectivite']['nom'] . $this->Html->tag(null, '<br />') .
    $collectivite['Collectivite']['adresse'] . $this->Html->tag(null, '<br />') .
    $collectivite['Collectivite']['CP'] . ' ' . $collectivite['Collectivite']['ville'] . $this->Html->tag(null, '<br />') .
    $collectivite['Collectivite']['telephone'],
    $this->Html->image($logo_path, array('alt' => 'logo de la collectivité', 'style' => 'max-width: 500px')),
    $this->Bs->div('btn-group').
    $this->Bs->btn( null, array('controller' => 'collectivites', 'action' => 'edit'), array('type' => 'primary', 'icon'=>'glyphicon glyphicon-edit', 'title' => 'Modifier')) .
    $this->Bs->btn( null, array('controller' => 'collectivites', 'action' => 'setLogo'), array('type' => 'default', 'icon'=>'glyphicon glyphicon-picture', 'title' => 'Changer de logo (page de connexion)')).
    $this->Bs->close()
)).
$this->Bs->endTable();
?>