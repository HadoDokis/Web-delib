<?php
echo $this->element('filtre');

if ($this->action == 'autreActesEnvoyes') {
    echo $this->Bs->tag('h3', 'Télétransmission des actes');
} elseif ($this->action == 'transmit') {
    echo $this->Bs->tag('h3', 'Télétransmission des délibérations');
}
echo $this->Bs->div('alert alert-info', __('La Classification enregistrée date du'). ' ' .$dateClassification).
$this->Bs->div();



$title=array(
    array('title' => $this->Paginator->sort('id', 'Id')),
    array('title' => $this->Paginator->sort('num_delib', 
            ($this->action == 'autreActesEnvoyes'?__('N° de l\'acte'):__('N° délibération')
            ))),
    array('title' => $this->Paginator->sort('objet_delib', __('Libellé de l\'acte'))),
    array('title' => $this->Paginator->sort('Deliberation.date_acte',
            ($this->action == 'autreActesEnvoyes'?__('Date de décision'):__('Date de séance')
            ))), 
    array('title' => $this->Paginator->sort('num_pref', 'Classification')), 
    array('title' => 'Annexe(s)'),
    array('title' => 'Statut TDT'),
    array('title' => 'Courriers Ministériels'),
        );
        
echo $this->Bs->table($title, array('hover', 'striped'));
foreach ($deliberations as $deliberation) {
 
    if (!empty($deliberation['Deliberation']['tdt_ar_annexes_status']) 
            && $deliberation['Deliberation']['tdt_ar_annexes_status']=='danger') {
        echo $this->Bs->lineColor($deliberation['Deliberation']['tdt_ar_annexes_status']);
    }
        
    
    echo $this->Bs->cell($this->Html->link($deliberation['Deliberation']['id'], array('action'=>'view', $deliberation['Deliberation']['id'])));

    echo $this->Bs->cell($this->Html->link($deliberation['Deliberation']['num_delib'], array('action'=>'getTampon', $deliberation['Deliberation']['id'])));
    echo $this->Bs->cell($deliberation['Deliberation']['objet_delib']);
    $date='';
    if ($this->action == 'autreActesEnvoyes')
        $date= $this->Form2->ukToFrenchDateWithHour($deliberation['Deliberation']['date_acte']);
    else
         foreach ($deliberation['listeSeances'] as $seance)
            $date=  $seance['libelle'] . (isset($seance['date']) && !empty($seance['date']) ? ' : ' . $this->Html2->ukToFrenchDateWithHour($seance['date']) : '');
    
    
    echo $this->Bs->cell($date);
    
    echo $this->Bs->cell($deliberation['Deliberation']['num_pref']);
    
    $annexes='';
    if(!empty($deliberation['Annex'])){
    //$annexes=$this->Bs->link('Annexes'.'<span class="badge">'.count($deliberation['Annex']).'</span>', array(), array('escape'=>false));
    $annexes.='<div class="dropdown">
        <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
          Liste des annexes';
    if (!empty($deliberation['Deliberation']['tdt_ar_annexes_status'])) {
            $annexes.=' <span class="label label-' . $deliberation['Deliberation']['tdt_ar_annexes_status'] . ' label-as-badge"'
                    . 'title="'.$deliberation['Deliberation']['tdt_ar_annexes_status_libelle'].'"'
                    . '>' . count($deliberation['Annex']). '</span>';
        }
        else
        $annexes.=' <span class="label label-info label-as-badge">'.count($deliberation['Annex']).'</span>';
              
          $annexes.=' <span class="caret"></span>
        </button>
        <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">';
              foreach ($deliberation['Annex'] as $annexe) {
              $annexes.='<li role="presentation">' .
              $this->Bs->btn($annexe['filename_pdf'], array('controller' => 'annexes', 'action' => 'download', $annexe['id']), array('icon' => 'glyphicon glyphicon-download-alt',
                  'role' => 'menuitem',
                  'tabindex' => -1
              ))
              . '</li>';
          }

          echo '</ul>
      </div>';
    }
    echo $this->Bs->cell($annexes);
    
    $etat='';
    if (isset($deliberation['Deliberation']['code_retour'])) {
    switch ($deliberation['Deliberation']['code_retour']) {
        case 4:
            $etat = $this->Html->link("Acquittement reçu le " . $deliberation['Deliberation']['tdt_dateAR'], array('action'=>'getBordereauTdt', $deliberation['Deliberation']['id']), array('title'=>'Télécharger le bordereau d\'acquittement de transaction'));
            break;
        case 3:
            $etat =  'Transmis';
            break;
        case 2:
            $etat =  'En attente de transmission';
            break;
        case 1:
            $etat =  'Posté';
            break;
        default:
            break;
    }
}else{
    if (!empty($deliberation['Deliberation']['tdt_dateAR'])){
        $etat =  $this->Html->link("Acquittement reçu le " . $deliberation['Deliberation']['tdt_dateAR'], array('action'=>'getBordereauTdt', $deliberation['Deliberation']['id']), array('title'=>'Télécharger le bordereau d\'acquittement de transaction'));
    }else{
        $etat =  'En attente de réception';
    }
}
                
    echo $this->Bs->cell($etat);
    
    $messages='';
     if (!empty($deliberation['TdtMessage'])) {
        foreach ($deliberation['TdtMessage'] as $message) {
            $reponse=false;
            switch ($message['tdt_type']) {
                case 2:
                    $libelle = "Courrier simple"; 
                    $reponse=true;
                    break;
                case 3:
                    $libelle = "Demande de pièces complémentaires"; 
                    $reponse=true;
                    break;
                case 4:
                    $libelle = "Lettre d'observation"; 
                    break;
                case 5:
                    $libelle = "Déféré au tribunal administratif"; 
                    break;
                default:
                    break;
            }
            if (!empty($libelle)){
                $messages .=  '<div style="white-space: nowrap;">';
                $messages .=  $this->Html->link($libelle.' <i class="fa fa-download"></i>', array('action'=>'downloadTdtMessage', $message['tdt_id']), array('escape'=>false,'title'=> 'Télécharger le document')) ;
                if(empty($message['Reponse']) && $reponse && empty($message['parent_id'])) {
                    $messages .= ' ';
                    //Gestion des réponses Pastell
                    if(!empty($deliberation['Deliberation']['pastell_id']) && $tdt=='PASTELL'){
                    $coll=$this->Session->read('user.Collectivite');   
                    $messages .= $this->Html->link('<i class="fa fa-envelope-o"></i>', $tdt_host.'/document/detail.php?id_d='.$deliberation['Deliberation']['pastell_id'].'&id_e='.$coll['Collectivite']['id_entity'], array('escape'=>false, 'target' => '_blank','title'=> 'Répondre'));
                    }
                    //Gestion des réponses S2low
                    if(!empty($deliberation['Deliberation']['tdt_id']) && $tdt=='S2LOW' ) 
                    $messages .=  $this->Html->link('<i class="fa fa-envelope-o"></i>', $tdt_host.'/modules/actes/actes_transac_show.php?id=' . $message['tdt_id'], array('escape'=>false, 'target' => '_blank','title'=> 'Répondre'));
                }
                else
                foreach ($message['Reponse'] as $reponse) {
                    $messages .=  '<br /><i class="fa fa-long-arrow-right" style="padding-left:10px;"></i> ';
                    $messages .=  $this->Html->link(' Réponse envoyée <i class="fa fa-download"></i>', array('action'=>'downloadTdtMessage', $reponse['tdt_id']), array('escape'=>false,'title'=> 'Télécharger la réponse envoyée')) ;
                }
                $messages .=  '</div>';
            }
        }                
    }                    
    echo $this->Bs->cell($messages);
            
}
echo $this->Bs->endTable().
        $this->Paginator->numbers(array(
    'before' => '<ul class="pagination">',
    'separator' => '',
   'currentClass' => 'active',
    'currentTag' => 'a',
    'tag' => 'li',
    'after' => '</ul><br />'
));