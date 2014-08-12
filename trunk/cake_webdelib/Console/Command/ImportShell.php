<?php

class ImportShell extends AppShell {

    public $uses = array('Infosupdef','Infosuplistedef');

    public function main() {
    }
    
    
    /**
     * Options d'éxecution et validation des arguments
     *
     * @return Parser $parser
     */
    public function getOptionParser()
    {
        $parser = parent::getOptionParser();
        $parser->description(__('Commandes d\'import de webdelib.'));

        $parser->addSubcommand('infoSupListe', array(
            'help' => __('Import de donnée dans une information supplémentaire de type liste.'),
            'parser' => array(
                'options' => array(
                    'code' => array(
                        'name' => 'code',
                        'required' => true,
                        'short' => 'c',
                        'help' => 'Code de l\'information supplémentaire à importer'
                    ),
                    'file' => array(
                        'name' => 'file',
                        'required' => true,
                        'short' => 'f',
                        'help' => 'Fichier d\'import',
                    ),
                    /* NON PRIS EN COMPTE CAR ORDRE ERONNEE contrainte
                    'desactive' => array(
                        'name' => 'desactive',
                        'required' => false,
                        'short' => 'd',
                        'help' => 'Désactive l\'ancienne liste actuelle',
                    )*/
                )
            )
        ));
        
        return $parser;
    }
    
    public function infoSupListe()
    {
        $success=false;
        try{
            $this->Infosuplistedef->begin();
            $infosupdef=$this->Infosupdef->find('first',array(
                 'fields' => 'id',
                 'conditions' => array('code'=>$this->params['code']),
                'recursive'=>-1,
            ));
            if(empty($infosupdef)) throw new Exception('Code invalide');
            
            /* NON PRIS EN COMPTE CAR ORDRE ERONNEE contrainte
            if(!empty($this->params['desactive'])) {
                $this->Infosuplistedef->begin();
                $this->Infosuplistedef->updateAll(
                        array('Infosuplistedef.actif' => false, 'Infosuplistedef.ordre' => 999),
                        array('Infosuplistedef.infosupdef_id' => $infosupdef['Infosupdef']['id'])
                );
                $this->Infosuplistedef->commit();
            }*/
        
            $values = file_get_contents($this->params['file']);
            if (($handle = fopen($this->params['file'], "r")) !== FALSE)
            {
                $i = 0; 
                while (($lines = fgetcsv($handle,1000,';')) !== FALSE) {
                    if($i==0)
                    foreach( $lines as $key => $val ) {
                        $cols[$key] = trim($lines[$key]);
                    }
                    else
                    {
                        $fields=array();
                        $this->Infosuplistedef->begin();
                        foreach ($cols as $keyCol=>$name){
                            if(!empty($lines[$keyCol]))
                            $fields[$name] = trim($lines[$keyCol]);
                            else throw new Exception('Fichier invalide');
                        }
                        $fields['infosupdef_id']=$infosupdef['Infosupdef']['id'];
                        $fields['ordre']=$i; 
                        $this->Infosuplistedef->create();
                        $this->Infosuplistedef->save($fields);
                        $this->Infosuplistedef->commit();
                        $this->out("Import : " . implode($fields));
                    }
                    $i++; 
                }
            }else{
                throw new Exception('Fichier invalide');
            }
            $this->Infosuplistedef->commit();
            $success=true;
            }
        catch (Exception $e)
        {
            $this->Infosuplistedef->rollback();
            $this->out("ERREUR : " . $e->getMessage());
        }
        if (empty($success))
            $this->footer('<error>Erreur : un problème est survenu durant l\'import !!</error>');
        else
            $this->footer('<info>Import accomplis avec succès !</info>');
            
    }
 
    /**
     * Affiche un message entouré de deux barres horizontales
     * @param string $var message
     */
    public function footer($var) {
        $this->hr();
        $this->out($var);
        $this->hr();
    }

}