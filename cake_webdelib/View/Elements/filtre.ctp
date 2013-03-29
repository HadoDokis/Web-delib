<?php
/**
 * Affichage du filtre
 *
*/

$criteres = $this->Session->read('Filtre.Criteres');
if (empty($criteres)) return;

echo $this->Html->script('filtre.js');

echo $this->Html->div('filtre');

	echo $this->Form->create(null, array('url' => array('controller'=> 'deliberations', 'action'=>$this->action), 'id'=>'filtreForm'));
		echo $this->Html->tag('div', null, array('class' => 'filtreFonc'));
			// affichage du bouton afficher-masquer le filtre
			if ($this->Session->read('Filtre.Fonctionnement.affiche')) {
				$iconeBoutonBasculeCriteres = 'icons/filtreDown.png';
				echo $this->Form->hidden('filtreFonc.affiche', array('value'=>true));
			} else {
				$iconeBoutonBasculeCriteres = 'icons/filtreUp.png';
				echo $this->Form->hidden('filtreFonc.affiche', array('value'=>false));
			}
			echo $this->Html->image($iconeBoutonBasculeCriteres, array(
				'id'=>'boutonBasculeCriteres',
				'border'=>"0", 'height'=>"28", 'width'=>"28",
				'title'=>__('Afficher-masquer les critères du filtre', true),
				'onClick'=>"basculeCriteres();",
				'onMouseOver'=>"this.style.cursor='pointer'"));
			// affichage du bouton on/off
			if ($this->Session->read('Filtre.Fonctionnement.actif'))
				echo $this->Html->image('icons/filtreOn.png', array(
					'id'=>'boutonOnOff',
					'border'=>"0", 'height'=>"28", 'width'=>"28",
					'title'=>__('Filtre actif, cliquer ici pour annuler le filtre', true),
					'onClick'=>"razFiltre();",
					'onMouseOver'=>"this.style.cursor='pointer'"));
			else
				echo $this->Html->image('icons/filtreOff.png', array(
					'id'=>'boutonOnOff',
					'border'=>"0", 'height'=>"28", 'width'=>"28",
					'title'=>__('Filtre inactif', true)));
			// affichage du bouton pour appliquer le filtre
			echo $this->Html->image('icons/filtre.png', array(
				'id'=>'filtreButton',
				'border'=>"0", 'height'=>"28", 'width'=>"28",
				'title'=>__('Changer les critères du filtre puis cliquer ici pour appliquer les changements', true),
				));
		echo $this->Html->tag('/div');

		echo $this->Html->div('filtreCriteres', null, array('id'=>'filtreCriteres'));
		echo '<div class="spacer"></div>';
			foreach($criteres as $nom => $critere) {
				$options = $critere['inputOptions'];
				$options['onChange'] = "critereChange();";
				if (array_key_exists('type', $options) && $options['type'] == 'date')
				    echo $this->Html->div($critere['classeDiv'], $datePicker->picker('Critere.'.$nom, $options));
				else
				    echo $this->Html->div($critere['classeDiv'], $this->Form->input('Critere.'.$nom, $options));
				if ($critere['retourLigne'])
			            echo '<div class="spacer"></div>';
			}
		        echo '<div class="spacer"></div>';
			echo $this->Form->submit('Appliquer le filtre');
		echo '</div>';
	echo $this->Form->end();
	echo '<div class="spacer"></div>';
echo '</div>';
?>
<style>
select[multiple], select[size] {
    width: 220px;
}
#filtreCriteres{
    padding-left: 5px;
}
</style>