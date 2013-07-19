<?php
App::uses('ComponentCollection', 'Controller');
App::uses('Component', 'Controller');
App::uses('Folder', 'Utility');
App::uses('File', 'Utility');


    class PatchShell extends AppShell{
        
        public $tasks = array('Tdt');
        public $uses = array ('Annex','Deliberation');
        
         public function main() {
                $this->out('Webdelib.');
        }
    
        /* Mise à jour de la version 4.1.01 à la version 4.1.02
         * Génération des annexes en odt valide, Mise à jour de classification, 
         * Changement du num préfecture
         */
        public function Version_4101to4102 () {
            $success=true;
            $this->out("Patch Processing...\n");
            $collection = new ComponentCollection();
            $action=0;
            
            //Génération des annexes en odt valide
            if(isset($this->args[0]) && $this->args[0]=='PDFtoODT' || !isset($this->args[0]))
            {
                $action++;
                App::uses('PdfComponent', 'Controller/Component');
                $this->out((isset($this->args[0])?'':'[1]')."Migration des pdf en odt...");
                $this->Pdf = new PdfComponent($collection);

                $annexes = $this->Annex->find('all',
                            array('fields'=>array('id','filename','filename_pdf','data_pdf'),
                                    'recursive' => -1));

                $i=0;
                foreach ($annexes as $annexe) {
                    if ( strpos($annexe['Annex']['filename'], 'odt') === false )
                            continue;

                    $this->out('Generation '.$annexe['Annex']['id'].'...');

                    $i++;
                    $newAnnexe['Annex']['id']=$annexe['Annex']['id'];
                    $outputDir =  tempdir();
                    $folder = new Folder($outputDir);
                    $file = new File($outputDir.$annexe['Annex']['id'].'pdf', false);
                    $file->write($annexe['Annex']['data_pdf']);
                    $newAnnexe['Annex']['data'] =   $this->Pdf->toOdt($file->pwd());
                    $newAnnexe['Annex']['filetype'] = 'application/pdf';
                    $newAnnexe['Annex']['size'] = $file->size();

                    $file->delete();
                    $file->close();
                    $folder->delete();
                   $success = $this->Annex->save($newAnnexe['Annex']) & $success;
                   if(!$success)
                       $this->out('<error>Erreur sur la sauvegarde : '.$annexe['id']."<error>\n");
                   else
                        $this->out('Sauvegarde Terminée : '.$annexe['Annex']['id']."\n");
                }

                $this->out((isset($this->args[0])?'':'[1]').'Migration des pdf en odt Terminée ('.$i.' modifications');
            }
            
            //Mise à jour de la classification
            if(isset($this->args[0]) && $this->args[0]=='classification' || !isset($this->args[0]))
            {
                $action++;
                $this->out((isset($this->args[0])?'':'[2]').'Mise à jour classification...');
                $success = $this->Tdt->classification() & $success;
                if($success)
                $this->out((isset($this->args[0])?'':'[2]').'Mise à jour classification Terminée');
                else
                    $this->out('<error>Erreur</error> : Mise à jour classification !!');
            }
            
            //Mise à jour num préfecture
            if(isset($this->args[0]) && $this->args[0]=='num_pref' || !isset($this->args[0]))
            {
                $action++;
                $this->out((isset($this->args[0])?'':'[3]').'Mise à jour numéro Préfecture...');
                App::uses('DeliberationsController', 'Controller');
                $this->Delib = new DeliberationsController($collection);
                $deliberations = $this->Deliberation->find('all',
                            array('fields'=>array('id','num_pref'),
                                    'recursive' => -1));      
                
                foreach($deliberations as $deliberation) 
                {
                    $this->out('Migration deliberaion : '.$deliberation['Deliberation']['id'].'...');
                   $num_pref=strstr($deliberation['Deliberation']['num_pref'], ' - ',true);
                    if(isset($num_pref) && !empty($num_pref)){
                        $this->Deliberation->id=$deliberation['Deliberation']['id'];
                        $success = $this->Deliberation->saveField('num_pref',$num_pref)& $success;
                        if(!$success)
                           $this->out("<error>Erreur</error> sur la sauvegarde : ".$deliberation['Deliberation']['id']."\n");
                       else
                            $this->out('Sauvegarde Terminée : '.$deliberation['Deliberation']['id']."\n");
                    }else
                        $this->out('Rien à faire : '.$deliberation['Deliberation']['id']."\n");
                }
                
                if($success)
                $this->out((isset($this->args[0])?'':'[3]').'Mise à jour numéro Préfecture Terminée');
                else
                    $this->out('<error>Erreur</error> : Mise à jour numéro Préfecture !!');
            }
            
            if($action===0){
                $success=false;
                $this->out('<question>Commande inconnu !!</question>');
            }
            if($success)
                $this->footer('<info>patch complete<info>');
                else
                    $this->footer('<warning>patch incomplete !!</warning>');
        }
        
         public function footer($var) {
            $this->hr();
            $this->out($var);
            $this->hr();
        }
        
    }
?>
