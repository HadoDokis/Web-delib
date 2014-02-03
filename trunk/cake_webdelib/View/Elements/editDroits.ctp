<?php
/*
	Affiche les menus-controlleurs pour la saisie des droits
	Paramètres :
*/
echo $this->Html->script('droits', true);

echo $this->Html->tag('table', null, array('cellspacing'=>'0', 'cellpadding'=>'0', 'id'=>'tableDroits'));
?>
<tr>
    <td><em><label for="masterCheckbox" style="width: auto; font-size: 11px;">Tout cocher / décocher</label></em></td>
    <td><input type="checkbox" id="masterCheckbox" /></td>
</tr>
<?php
foreach($listeCtrlAction as $rownum => $ctrlAction) {
	$classTd = 'niveau'.$ctrlAction['niveau'];
    $icon = '';
    if ($ctrlAction['niveau']==0)
        $icon = '<i class="fa fa-angle-down"></i>&nbsp;&nbsp;';
    if ($ctrlAction['niveau']==1)
        $icon = '<i class="fa fa-angle-right"></i>&nbsp;&nbsp;';

    $ctrlAction['title']='<label for="chkBoxDroits'.$rownum.'">'.$icon.$ctrlAction['title'].'</label>';

	$optionsCheckBox = array(
		'label' => false,
		'type' => 'checkbox',
		'id' => 'chkBoxDroits'.$rownum
    );

	if ($ctrlAction['nbSousElements']>0)
		$optionsCheckBox['onclick'] = 'toggleCheckBoxDroits('.$rownum.', '.$ctrlAction['nbSousElements'].');';

	echo $this->Html->tag('tr');
		echo $this->Html->tag('td', $ctrlAction['title'], array('class'=>$classTd.' td-droit'));
		if ($ctrlAction['modifiable'])
			echo $this->Html->tag('td', $this->Form->input('Droits.'.$ctrlAction['acosAlias'], $optionsCheckBox), array('class'=>$classTd));
		else
			echo $this->Html->tag('td', $this->Form->hidden('Droits.'.$ctrlAction['acosAlias']), array('class'=>$classTd));
	echo $this->Html->tag('/tr');
}
echo $this->Html->tag('/table');