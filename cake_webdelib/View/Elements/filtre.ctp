<?php
/**
 * Affichage du filtre
 *
*/
$this->append('filtre');

$criteres = $this->Session->read('Filtre.Criteres');
if (empty($criteres)) return;

echo $this->Html->div('filtre');


	echo $this->BsForm->create(null, array('url' => $this->Session->read('Filtre.url'), 'id'=>'filtreForm'));
		/*echo $this->Html->tag('div', null, array('class' => 'filtreFonc'));
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
                
                */
        $filtre='';
        $note1 = false;
        $newLine=true;
        $line=array(1=>'6',2=>'6',3=>'4');
        foreach($criteres as $nom => $options) {
            
            $filtre .= ($newLine==true?$this->Bs->row():'').$this->Bs->col('xs'.(!empty($options['column'])?$line[$options['column']]:$line[2]));
            //$options['onChange'] = "critereChange(this);";

            if (array_key_exists('type', $options['inputOptions'])) {

                    switch ($options['inputOptions']['type']){
                        case 'text':
                            $options['inputOptions']['onKeyUp'] = "critereChange(this);";
                            $options['inputOptions']['onPaste'] = "critereChange(this);";
                            $options['inputOptions']['label'] .= ' *'; // note
                            $note1 = true;
                            $filtre .= $this->BsForm->input('Critere.'.$nom, $options['inputOptions']);

                            break;
                        case 'date':
                            //TODO à faire
                            //echo $this->Html->div($critere['classeDiv'], $datePicker->picker('Critere.'.$nom, $options));
                            break;
                        default:
                            $filtre .=  $this->BsForm->input('Critere.'.$nom, $options['inputOptions']);
                    }
            }   
            else {
                $filtre .=  $this->BsForm->input('Critere.'.$nom, $options['inputOptions']);
            }
            $newLine++;  
            if ($options['retourLigne']) {
                $filtre .= $this->Bs->close(2);
                $newLine=true;
            } else {
                $filtre .= $this->Bs->close();
                $newLine=false;
            }
    }
        
        if ($note1){
            $filtre .=  $this->Html->tag('em',"* Le caractère '%' est employé comme métacaractère (joker), il remplace un ou plusieurs caractères et ignore la casse. Exemple : \"dupon%\" (commence par Dupon)");
        }
        $filtre .=  $this->BsForm->submit('Appliquer le filtre');
	$filtre .=  $this->BsForm->end().$this->Bs->tag('br /');


echo $this->Bs->div('well', $filtre,  array('id'=>'filtreCriteres','style'=>'display: none')).$this->Bs->close();

$this->end();