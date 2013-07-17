<?php
App::uses('ComponentCollection', 'Controller');
App::uses('PdfComponent', 'Controller/Component');
App::uses('Component', 'Controller');
App::uses('Folder', 'Utility');
App::uses('File', 'Utility');


    class PatchShell extends AppShell{
        
        var $uses = array ('Annex');
        
         public function main() {
                $this->out('Hello world.');
        }
    
    
        public function Version_4101to4102 () {
            $i=0;
            $this->out("Patch Processing...\n");
            $collection = new ComponentCollection();
            $this->Pdf = new PdfComponent($collection);
             
            $annexes = $this->Annex->find('all',
                        array('fields'=>array('id','filename','filename_pdf','data_pdf'),
                                'recursive' => -1));
            
            $this->out("[1]Migration des pdf en odt");
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
                if(!$this->Annex->save($newAnnexe['Annex']))
                   $this->out('<error>Erreur sur la sauvegarde : '.$annexe['id']."<error>\n");
               else
                    $this->out('Sauvegarde Terminée : '.$annexe['Annex']['id']."\n");
            }
            
            $this->out("[1]Migration des pdf en odt Terminée");
            $this->out('[FIN] Nombre de migration odt effectuée : '.$i);
            $this->out('********************************************************');
            $this->out("patch complete\n");
        }
        
    }
?>
