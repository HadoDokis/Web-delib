<?php
$titre =  $this->Bs->tag('h3', $titreVue.'('.$nbProjets.')');
$affichage = $this->element('9cases',array('projets'=>$this->data,
    'traitement_lot'=> isset($traitement_lot)?$traitement_lot:null)
    ); 
//echo $this->Bs->modal($titre,$affichage);
//echo $titre;
//echo $affichage;
$id = 'view';
$out = '<div class="modal-dialog" style="width:101%">';
// Content
$out .= '<div class="modal-content">';
$out .= '<div class="modal-header">';
$out .= '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>';
$out .= '<h4 class="modal-title" id="' . $id . 'Label">' . $titre . '</h4>';
$out .= '</div>';
$out .= '<div class="modal-body">';
$out .= $this->Bs->btn('Télécharger', 
                array('controller' => 'deliberations', 'action' => 'downloadTableauBordCsv',$projets), 
                array('type' => 'default', 
                    'icon' => 'glyphicon glyphicon-download', 
                    'title' => 'télécharger la liste présente au format csv',
                    'size' => 'lg',
                    ));
$out .= $affichage;
$out .= '</div>';
$out .= '</div>';
// End Content
$out .= '</div>';
echo $out;