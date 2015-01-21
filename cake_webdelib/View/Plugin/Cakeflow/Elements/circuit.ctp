<?php
echo $this->Html->css('/cakeflow/css/circuit');
echo $this->Html->script('/cakeflow/js/init_cakeflow');
$circuit = '';
foreach ($etapes as $etape) {
    $etape_ = $this->Html->div('nom', '[' . $etape['Etape']['ordre'] . '] - ' . $etape['Etape']['nom']);
    $etape_.= $this->Html->div('type', $etape['Etape']['libelleType']);
    $utilisateurs = '';
    foreach ($etape['Composition'] as $composition) {
        $typeValidation = CAKEFLOW_GERE_SIGNATURE ? ', ' . $composition['libelleTypeValidation'] : '';
        $utilisateurs .= $this->Html->div('utilisateur', $composition['libelleTrigger'] . $typeValidation);
    }
    $etape_ .= $this->Html->tag('div', $utilisateurs, array('class' => 'utilisateurs'));
    $circuit .= $this->Html->tag('div', $etape_, array('class' => 'etape', 'id' => 'etape_' . $etape['Etape']['id']));
}
echo $this->Html->tag('div', $circuit, array('class' => 'circuit', 'id' => 'etapes'));
?>