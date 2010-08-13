<?php
/*
	Affiche les menus-controlleurs pour la saisie des droits
	Paramètres :
*/
echo $javascript->link('droits', true);

echo $html->tag('table', null, array('cellspacing'=>'0', 'cellpadding'=>'0', 'id'=>'tableDroits'));
foreach($listeCtrlAction as $rownum => $ctrlAction) {
	$classTd = 'niveau'.$ctrlAction['niveau'];
	if ($ctrlAction['niveau']==0) $ctrlAction['title']='<b>'.$ctrlAction['title'].'</b>';
	$indentation = str_repeat('&nbsp;&nbsp;&nbsp;', $ctrlAction['niveau']);
	$optionsCheckBox = array(
		'label' => '',
		'type' => 'checkbox',
		'id' => 'chkBoxDroits'.$rownum);
	if ($ctrlAction['nbSousElements']>0)
		$optionsCheckBox['onclick'] = 'toggleCheckBoxDroits('.$rownum.', '.$ctrlAction['nbSousElements'].');';

	echo $html->tag('tr');
		echo $html->tag('td', $indentation.$ctrlAction['title'].'&nbsp;&nbsp;&nbsp;', array('class'=>$classTd));
		if ($ctrlAction['modifiable'])
			echo $html->tag('td', $form->input('Droits.'.$ctrlAction['acosAlias'], $optionsCheckBox), array('class'=>$classTd));
		else
			echo $html->tag('td', $form->hidden('Droits.'.$ctrlAction['acosAlias']), array('class'=>$classTd));
	echo $html->tag('/tr');
}
echo $html->tag('/table');
?>
