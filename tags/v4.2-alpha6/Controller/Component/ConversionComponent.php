<?php

App::uses('Folder', 'Utility');
App::uses('File', 'Utility');
App::uses('AppTools', 'Lib');


class ConversionComponent extends Component {

    function __construct() {

    }
    /**
     * conversion de format du fichie $fileUri vers le format $format
     * @return array tableau de réponse composé comme suit :
     * 	'resultat' => boolean
     *  'info' => string
     * 	'convertedFileUri' => nom et chemin du fichier converti
     */
    function convertirFichier($pathFile, $dataExtention, $dataSortieExtention) {
            
        return $this->_convertir(file_get_contents($pathFile), $dataExtention , $dataSortieExtention);
    }

    /**
     * conversion de format du fichie $fileUri vers le format $format
     * @return array tableau de réponse composé comme suit :
     * 	'resultat' => boolean
     *  'info' => string
     * 	'convertedFileUri' => nom et chemin du fichier converti
     */
    function convertirFlux($sData, $dataExtention, $dataSortieExtention) {
       
        return $this->_convertir($sData, $dataExtention , $dataSortieExtention);
    }
    
    function _convertir($data, $dataExtention , $dataSortieExtention) {
          
        require_once 'XML/RPC2/Client.php';
        
        // initialisations
        $ret = array();
        $result = array();
        $convertorType = Configure::read('CONVERSION_TYPE');

        if (empty($convertorType)) {
                $ret['resultat'] = false;
                $ret['info'] = __('Type du programme de conversion non déclaré dans le fichier de configuration de Webdelib', true);
                return $ret;
        }

        $options = array(
            'uglyStructHack' => true
        );

        $url = 'http://'.Configure::read('CLOUDOOO_HOST') . ':' . Configure::read('CLOUDOOO_PORT');
        $client = XML_RPC2_Client::create($url, $options);
        try {
            $result = $client->convertFile(base64_encode($data), $dataExtention, $dataSortieExtention, false, true);
        } catch (XML_RPC2_FaultException $e) {
            $this->log('Exception #' . $e->getFaultCode() . ' : ' . $e->getFaultString(), 'debug');
            return false;
        }
        
        return base64_decode($result);
    }

    function odt2txt($model, $id, $field, $content) {
        $output = array();
        $odt2txt_exec = Configure::read('odt2txt_EXEC');

        $dir=  TMP."/$model".'_'."$id/";
        $odtFile =  $dir."$field".'_'."$id";
        if (!file_exists($dir))
            mkdir($dir);
        file_put_contents($odtFile.".odt", $content);
        $commande = "$odt2txt_exec $odtFile".'odt';
        exec($commande, $output, $return_value);
        return  (file_get_contents( "$odtFile.txt"));

    }

    function concatener($document_path, $annexes=array()) {
            $output = array();
            if (empty( $annexes))
                return true;
            $pdftk_exec = Configure::read('PDFTK_EXEC');
            if (!file_exists( $pdftk_exec)){
                return false;
            }
            $annexes_path = implode(" ", $annexes);
            $doc_orig = $document_path.'-orig';
            rename($document_path, $doc_orig);
            $commande = "$pdftk_exec $doc_orig  $annexes_path output $document_path";
            exec($commande, $output, $return_value);
              
            return ($return_value ==0);
        }  

        function toOdt($sData, $stypeMime){
            
            $DOC_TYPE = Configure::read('DOC_TYPE');
            
            $folder= new Folder(AppTools::newTmpDir(TMP.'files'.DS.'conversion'.DS), true, 0777);
            //Si le fichier n'est pas un pdf on le converti en pdf
            //if($DOC_TYPE[$stypeMime]['extension']!='pdf')
            $sDataPdf=$this->convertirFlux($sData, $DOC_TYPE[$stypeMime]['extension'], 'pdf');
           
            $file = new File($folder->pwd().DS.'_origine.pdf', true, 0777);
            if(!empty($DOC_TYPE[$stypeMime]['extension']) && $DOC_TYPE[$stypeMime]['extension']=='pdf')  { 
                if(is_file($sData))
                    $file->append(file_get_contents($sData));
                else 
                    $file->append($sData);
                    
            } else $file->append($sDataPdf);
            
            $return=$this->_PdftoOdt($folder, $file);
            //$folder->delete();
            
            return $return;
        }
        
        function _PdftoOdt(&$folder, &$fileOrigine){
            
            //Preparation de la commande ghostscript
            $PDFTK_EXEC = Configure::read('PDFTK_EXEC');
            $PDFINFO_EXEC = Configure::read('PDFINFO_EXEC');
            $GS_RESOLUTION =  Configure::read('GS_RESOLUTION');

            shell_exec($PDFTK_EXEC.' '.$fileOrigine->pwd().' burst output '.$folder->pwd().'/page_%04d.pdf');
            $fileOrigine->delete();
            
            $files = $folder->find('.*\.pdf', true);
            $i=0;
            foreach ($files as $file) {
                 $file = new File($folder->pwd() . DS . $file);
                 
                 $command=$PDFINFO_EXEC.' '.$file->pwd(). '|grep "Page.*size:"';
                 $retour=shell_exec($command);
                 $aInfo=explode(' ',$retour);
                 $aFormat=array();
                 foreach ($aInfo as $info)
                     if(is_numeric($info))$aFormat[]=$info;
                 if($aFormat[0] < $aFormat[1])
                     $orientaion='portrait'; 
                     else $orientaion='landscape'; 
                     
                $pageParam[$i]=array('path'=>$folder->pwd().DS.$i.'.png',
                                     'name'=>$i.'.png',
                                        'orientation'=>$orientaion);
                 
                $imagick = new Imagick();
                $imagick->readImage($file->pwd().'[0]');
                $imagick->setImageUnits(imagick::RESOLUTION_PIXELSPERINCH);
                $imagick->setResolution( $GS_RESOLUTION, $GS_RESOLUTION); 
                $imagick->setImageFormat('png');
                $imagick->setImageCompression(Imagick::COMPRESSION_UNDEFINED);
                $imagick->setImageCompressionQuality(0);
                //$imagick->setImageCompression(true);
                //$imagick->setCompression(Imagick::COMPRESSION_BZIP); 
                //$imagick->setImageCompressionQuality(80); 
                $imagick->writeImage($folder->pwd().DS.$i.'.png'); 
                $im = imagecreatefrompng($folder->pwd().DS.$i.'.png');
                imagepng($im,$folder->pwd().DS.$i.'.png',9);
                $file->close();
                $i++;
             }
             
            $files = $folder->find('.*\.png');
            //génération du fichier ODT
            $this->generateOdtFileWithImages($folder,$pageParam);
           
            $file = new File($folder->pwd().DS.'result.odt');
            $return=$file->read();
            $file->close();

            return $return;
        }
        
        function generateOdtFileWithImages(&$folder, $aPagePng) {
            
            //App::import('phpodt/phpodt');
            require_once(APP.DS.'Vendor'.DS.'phpodt'.DS.'phpodt.php');
            $odt = ODT::getInstance();
            $pageStyleP = new PageStyle('myPageStylePortrait','Standard');
            $pageStyleP->setOrientation(StyleConstants::PORTRAIT);
            $pageStyleP->setHorizontalMargin('0cm', '0cm');
            $pageStyleP->setVerticalMargin('0cm', '0cm');
            $pStyleP = new ParagraphStyle('myPStyleP', 'Standard');
            $pStyleP->setBreakAfter(StyleConstants::PAGE);
            $pageStyleL = new PageStyle('myPageStyleLandscape','Landscape');
            $pageStyleL->setOrientation(StyleConstants::LANDSCAPE);
            $pageStyleL->setHorizontalMargin('0cm', '0cm');
            $pageStyleL->setVerticalMargin('0cm', '0cm');
            $pStyleL = new ParagraphStyle('myPStyleL', 'Landscape');
            $pStyleL->setBreakBefore(StyleConstants::PAGE);
            
            
            foreach($aPagePng as $keyPage=>$page) {
                
            if($page['orientation']=='landscape'){
                $p = new Paragraph($pStyleL);
            }
            else{
                $p = new Paragraph($pStyleP);
            }
                
                $p->addImage($page['path'], '100%', '100%');
            }
            $odt->output($folder->pwd().DS.'result.odt');
        }
}
?>