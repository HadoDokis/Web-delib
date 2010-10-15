<?php
class NaturesController extends AppController {

    var $name = 'Natures';
    var $uses = array('Nature', 'Ado');
    var $aucunDroit;
     
/*    
    var $scaffold;
  
    function add() {
        if (!empty($this->data)) 
            if ($this->Nature->save($this->data)) {
                $ado = new Ado();
                $ado->create(); 
                $ado->save(array( 'model'=>'Nature',
                                  'foreign_key'=>$this->Nature->id,
                                  'parent_id'=>0,
                                  'alias'=>'Nature:'.$this->data['Nature']['libelle']));   

                $this->Session->setFlash('La nature a &eacute;t&eacute; sauvegard&eacute;');
                $this->redirect('/natures/index');
            } else {
                $this->Session->setFlash('Veuillez corriger les erreurs ci-dessous.');
            }

    }
 */       

}
?>
