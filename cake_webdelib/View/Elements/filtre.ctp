<?php
/**
 * Affichage du filtre
 *
*/

$criteres = $this->Session->read('Filtre.Criteres');
if (empty($criteres)) return;

echo $this->Html->script('filtre.js');

echo $this->Html->div('filtre');
	echo $this->Form->create(null, array('url' => $this->Session->read('Filtre.url'), 'id'=>'filtreForm'));
		echo $this->Html->tag('div', null, array('class' => 'filtreFonc'));
			// affichage du bouton afficher-masquer le filtre
			if ($this->Session->read('Filtre.Fonctionnement.affiche')) {
				$iconeBoutonBasculeCriteres = 'glyphicon glyphicon-filter';
				echo $this->Form->hidden('filtreFonc.affiche', array('value'=>true));
			} else {
				$iconeBoutonBasculeCriteres = 'glyphicon glyphicon-filter';
				echo $this->Form->hidden('filtreFonc.affiche', array('value'=>false));
			}
			echo $this->Bs->btn('filtrer', '#', array('type'=>'default',
                                'icon'=> $iconeBoutonBasculeCriteres,
				'id'=>'boutonBasculeCriteres',
                                'escape'=> false,
				'title'=>__('Afficher-masquer les critères du filtre', true),
				'onClick'=>"basculeCriteres();"));
			// affichage du bouton on/off
			if ($this->Session->read('Filtre.Fonctionnement.actif'))
				echo $this->Html->image('icons/filtreOn.png', array(
					'id'=>'boutonOnOff',
                    'class'=>'filtreOn',
					'title'=>__('Filtre actif, cliquer ici pour annuler le filtre', true),
					'onClick'=>"razFiltre();"));
			else
				echo $this->Html->image('icons/filtreOff.png', array(
					'id'=>'boutonOnOff',
                    'class'=>'filtreOff',
					'title'=>__('Filtre inactif', true)));
			// affichage du bouton pour appliquer le filtre
			echo $this->Html->image('icons/filtre.png', array(
				'id'=>'filtreButton',
				'title'=>__('Changer les critères du filtre puis cliquer ici pour appliquer les changements', true)));
		echo $this->Html->tag('/div');

		echo $this->Html->div('filtreCriteres', null, array('id'=>'filtreCriteres'));
		echo '<div class="spacer"></div>';
            $note1 = false;
			foreach($criteres as $nom => $critere) {
				$options = $critere['inputOptions'];
                $options['onChange'] = "critereChange(this);";
                if (array_key_exists('type', $options)){
                    switch ($options['type']){
                        case 'text':
                            $options['onKeyUp'] = "critereChange(this);";
                            $options['onPaste'] = "critereChange(this);";
                            $options['label'] .= ' *'; // note
                            $note1 = true;
                            echo $this->Html->div($critere['classeDiv'], $this->Form->input('Critere.'.$nom, $options));

                            break;
                        case 'date':
                            //TODO à faire
                            //echo $this->Html->div($critere['classeDiv'], $datePicker->picker('Critere.'.$nom, $options));
                            break;
                        default:
                            echo $this->Html->div($critere['classeDiv'], $this->Form->input('Critere.'.$nom, $options));
                    }
                }else{
                    echo $this->Html->div($critere['classeDiv'], $this->Form->input('Critere.'.$nom, $options));
                }
				if ($critere['retourLigne'])
                    echo '<div class="spacer"></div>';
			}
            echo '<div class="spacer"></div>';
//notes
            if ($note1){
                echo $this->Html->tag('em',"* Le caractère '%' est employé comme métacaractère (joker), il remplace un ou plusieurs caractères et ignore la casse. Exemple : \"dupon%\" (commence par Dupon)");
                echo '<div class="spacer"></div>';
            }
			echo $this->Form->submit('Appliquer le filtre');
		echo '</div>';
	echo $this->Form->end();
	echo '<div class="spacer"></div>';
echo '</div>';