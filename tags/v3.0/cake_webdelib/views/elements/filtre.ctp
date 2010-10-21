<?php
/**
 * Affichage du filtre
 *
*/

$criteres = $session->read('Filtre.Criteres');
if (empty($criteres)) return;

echo $javascript->link('filtre.js');

echo $html->div('filtre');
	echo $form->create(null, array('action'=>$this->action, 'id'=>'filtreForm'));
		echo $html->tag('div', null, array('class' => 'filtreFonc'));
			// affichage du bouton afficher-masquer le filtre
			if ($session->read('Filtre.Fonctionnement.affiche')) {
				$iconeBoutonBasculeCriteres = 'icons/filtreDown.png';
				echo $form->hidden('filtreFonc.affiche', array('value'=>true));
			} else {
				$iconeBoutonBasculeCriteres = 'icons/filtreUp.png';
				echo $form->hidden('filtreFonc.affiche', array('value'=>false));
			}
			echo $html->image($iconeBoutonBasculeCriteres, array(
				'id'=>'boutonBasculeCriteres',
				'border'=>"0", 'height'=>"28", 'width'=>"28",
				'title'=>__('Afficher-masquer les critères du filtre', true),
				'onClick'=>"basculeCriteres();",
				'onMouseOver'=>"this.style.cursor='pointer'"));
			// affichage du bouton on/off
			if ($session->read('Filtre.Fonctionnement.actif'))
				echo $html->image('icons/filtreOn.png', array(
					'id'=>'boutonOnOff',
					'border'=>"0", 'height'=>"28", 'width'=>"28",
					'title'=>__('Filtre actif, cliquer ici pour annuler le filtre', true),
					'onClick'=>"razFiltre();",
					'onMouseOver'=>"this.style.cursor='pointer'"));
			else
				echo $html->image('icons/filtreOff.png', array(
					'id'=>'boutonOnOff',
					'border'=>"0", 'height'=>"28", 'width'=>"28",
					'title'=>__('Filtre inactif', true)));
			// affichage du bouton pour applique le filtre
			echo $html->image('icons/filtre.png', array(
				'id'=>'filtreButton',
				'border'=>"0", 'height'=>"28", 'width'=>"28",
				'title'=>__('Changer les critères du filtre puis cliquer ici pour appliquer les changements', true),
				));
		echo $html->tag('/div');

		if ($session->read('Filtre.Fonctionnement.affiche'))
			$styleDisplay = "display:display;";
		else
			$styleDisplay = "display:none;";
		echo $html->div('filtreCriteres', null, array('id' => 'filtreCriteres', 'style'=>$styleDisplay));
			foreach($criteres as $nom => $critere) {
				$options = $critere['inputOptions'];
				$options['onChange'] = "critereChange();";
				if (array_key_exists('type', $options) && $options['type'] == 'date')
					echo $html->div($critere['classeDiv'], $datePicker->picker('Critere.'.$nom, $options));
				else
					echo $html->div($critere['classeDiv'], $form->input('Critere.'.$nom, $options));
				if ($critere['retourLigne'])
					echo '<div class="spacer"></div>';
			}
		echo '</div>';
	echo $form->end();
	echo '<div class="spacer"></div>';
echo '</div>';
?>
