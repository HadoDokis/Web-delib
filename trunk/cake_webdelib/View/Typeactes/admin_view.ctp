<?php
$this->Html->addCrumb('Liste des types d\'acte', array('controller'=>$this->request['controller'],'action'=>'index'));
$this->Html->addCrumb('Types d\'acte');

$panel_left = '<b>Libelle : </b>'.(isset($typeacte['Nature']['libelle'])?isset($typeacte['Nature']['libelle']):'') . '<br>' .
              '<b>Modèle de projet : </b>'.$typeacte['Modelprojet']['name'].'<br>' .           
              '<b>Date de cr&eacute;ation : </b>'.$typeacte['Typeacte']['created'].'<br>' .         
              '<b>Gabarit : texte de projet</b> - ' .
              (!empty($typeacte['Typeacte']['gabarit_projet']) ? $this->Html->link($typeacte['Typeacte']['gabarit_projet_name'], array('action' => 'downloadGabarit', $typeacte['Typeacte']['id'], 'projet')) : '').'<br>' .
              '<b>Gabarit : note de synthèse</b> - ' . 
              (!empty($typeacte['Typeacte']['gabarit_synthese']) ? $this->Html->link($typeacte['Typeacte']['gabarit_synthese_name'], array('action' => 'downloadGabarit', $typeacte['Typeacte']['id'], 'synthese')) : '').'<br>' .
              '<b>Gabarit : texte d\'acte</b> - ' .
              (!empty($typeacte['Typeacte']['gabarit_acte'])? $this->Html->link($typeacte['Typeacte']['gabarit_acte_name'], array('action' => 'downloadGabarit', $typeacte['Typeacte']['id'], 'acte')) : '');

$panel_right = '<b>Nature : </b>' . (isset($typeacte['Nature']['libelle'])?isset($typeacte['Nature']['libelle']):'') .'<br>' .
               '<b>Modèle de document final : </b>' . $typeacte['Modeldeliberation']['name'].'<br>' .
               '<b>Date de modification : </b>' . $typeacte['Typeacte']['modified'] .'<br>';  
            
echo $this->Bs->tag('h3', 'Types d\'acte') .
     $this->Bs->panel('Fiche type d\'acte : '.(isset($typeacte['Nature']['libelle'])?isset($typeacte['Nature']['libelle']):'')) .
        $this->Bs->row() .
        $this->Bs->col('xs6').$panel_left .
        $this->Bs->close() .
        $this->Bs->col('xs6').$panel_right .
        $this->Bs->close(2) .
     $this->Bs->endPanel() .
     $this->Bs->row().
     $this->Bs->col('md4 of5') .
     $this->Bs->div('btn-group', null,array('id'=>"actions_fiche" )) .
     $this->Html2->btnCancel() .
     $this->Bs->btn('Modifier', array('controller' => 'typeactes', 'action' => 'edit', $typeacte['Typeacte']['id']), array('type' => 'primary', 'icon' => 'glyphicon glyphicon-edit', 'title' => 'Modifier')) .
     $this->Bs->close(3);
?>








