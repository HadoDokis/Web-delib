<?php
/**
 * Affichage du filtre
 *
*/
$this->append('filtre');

$criteres = $this->Session->read('Filtre.Criteres');
if (empty($criteres)) return;

    $filtre =  $this->BsForm->create(null, array(
        'url' => $this->Session->read('Filtre.url'), 
        'id'=>'filtreForm'));
    $note1 = false;
    $newLine=true;
    $line=array(1=>'6',2=>'6',3=>'4');
    //debug($criteres);
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
                        $filtre .= $this->Html->script('/components/smalot-bootstrap-datetimepicker/js/bootstrap-datetimepicker.min');
                        $filtre .= $this->Html->script('/components/smalot-bootstrap-datetimepicker/js/locales/bootstrap-datetimepicker.fr');
                        $filtre .=  $this->Html->css('/components/smalot-bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css');
                        $filtre .=  $this->BsForm->datetimepicker('Critere.'.$nom, $options['Datepicker'],$options['inputOptions']);
                        break;
                    case 'checkbox':
                        $filtre .=  $this->BsForm->checkbox('Critere.'.$nom,$options['inputOptions']);
                        break;
                    case 'select':
                        $filtre .= $this->BsForm->select('Critere.'.$nom,$options['attribute'],$options['inputOptions']);
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

    /*if ($note1){
        $filtre .= $this->Html->tag('em',__("* Vous pouvez indiquer seulement les premières lètres du terme recherchés, par exemple \"dup\" pour trouver \"Dupon\""));    
    }*/

    $filtre .= $this->Bs->div('btn-group col-md-offset-' . $this->BsForm->getLeft(), null).
    $this->Bs->btn('Réinitialiser','#' ,array(
        'type' => 'danger',
        'icon'=>'glyphicon glyphicon-close', 
        'escape' => false,
        'disabled'=> ($this->Session->read('Filtre.Fonctionnement.actif')? false:'disabled'),
        'title'=>__('Filtre actif, cliquer ici pour annuler le filtre'),
        'onClick'=>"razFiltre();"
    )). $this->Bs->btn( 'Appliquer le filtre', null, 
            array(
                'tag'=>'button',
                'type' => 'success',
                'id' => 'boutonValider',
                'icon' => 'glyphicon glyphicon-filter', 
                'escape' => false, 
                'title' => __('Appliquer le filtre'))) .
    $this->Bs->close();

    $filtre .=  $this->Form->hidden('filtreFonc.affiche', array('value'=>true));
    $filtre .=  $this->BsForm->end().$this->Bs->tag('br /');

       /* echo $this->Bs->div('panel panel-default', null,  array('id'=>'filtreCriteres','style'=>$this->Session->read('Filtre.Fonctionnement.affiche')?'':'display: none')).
$this->Bs->div('panel-body', $filtre).$this->Bs->close(2);*/

echo $this->Bs->div('well', $filtre,  array('id'=>'filtreCriteres','style'=>$this->Session->read('Filtre.Fonctionnement.affiche')?'':'display: none')).$this->Bs->close();

$this->end();