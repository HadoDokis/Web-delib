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

        $url = 'http://'.Configure::read('CLOUDOOO_HOST') . ":" . Configure::read('CLOUDOOO_PORT');
        $client = XML_RPC2_Client::create($url, $options);
        try {
            $result = $client->convertFile(base64_encode($data), "$dataExtention", "$dataSortieExtention");
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
        $odtFile =  $dir."$field".'_'."$delib_id";
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
            
            $files = $folder->find('.*\.pdf');
            $i=0;
            foreach ($files as $file) {
                 $file = new File($folder->pwd() . DS . $file);
                 
                 $command=$PDFINFO_EXEC.' '.$file->pwd(). '|grep "Page.*size:"';
                 $retour=shell_exec($command);
                 $aInfo=explode(' ',$retour);
                 $aFormat=array();
                 foreach ($aInfo as $info)
                     if(is_numeric($info))$aFormat[]=$info;
                 if($aFormat[0] > $aFormat[1])
                     $orientaion='portrait'; 
                     else $orientaion='landscape'; 
                     
                $pageParam[$i]=array('path'=>$folder->pwd().DS.$i.'.png',
                                     'name'=>$i.'.png',
                                        'orientation'=>$orientaion);
                 
                $imagick = new Imagick();
                $imagick->setResolution( $GS_RESOLUTION, $GS_RESOLUTION); 
                $imagick->readImage($file->pwd().'['.$i.']');
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
            
            $file = new File($folder->pwd()."/result.odt");
            $return=$file->read();
            $file->close();

            return $return;
        }
        
        function _generateManifest($manifest) {
            $XMLManifest = $manifest->createElement('manifest:manifest');
            $XMLManifest->setAttribute('xmlns:manifest', 'urn:oasis:names:tc:opendocument:xmlns:manifest:1.0');

            $XMLFileEntry = $manifest->createElement('manifest:file-entry');
            $XMLFileEntry->setAttribute('manifest:media-type', 'application/vnd.oasis.opendocument.text');
            $XMLFileEntry->setAttribute('manifest:full-path', '/');
            $XMLManifest->appendChild($XMLFileEntry);

            $XMLFileEntry = $manifest->createElement('manifest:file-entry');
            $XMLFileEntry->setAttribute('manifest:media-type', 'text/xml');
            $XMLFileEntry->setAttribute('manifest:full-path', 'content.xml');
            $XMLManifest->appendChild($XMLFileEntry);

            return $XMLManifest;
        }
        
        function generateOdtFileWithImages(&$folder, $aPagePng) {
            $document = new ZipArchive();
            $document->open($folder->pwd()."/result.odt", ZIPARCHIVE::OVERWRITE);
            $document->addEmptyDir('Pictures');

            $manifest = new DOMDocument('1.0', 'utf-8');
            $XMLManifest = $this->_generateManifest($manifest);
            $manifest->appendChild($XMLManifest);

            $doc = new DOMDocument('1.0', 'utf-8');

            $XMLDocumentContent = $this->_generateDefaultHeaders($doc);
            $doc->appendChild($XMLDocumentContent);

            $XMLAutomaticStyles = $this->_generateDefaultStyle($doc);
            $XMLDocumentContent->appendChild($XMLAutomaticStyles);

            $XMLBody = $doc->createElement('office:body');
            $XMLDocumentContent->appendChild($XMLBody);

            $XMLText = $doc->createElement('office:text');
            $XMLText->setAttribute('text:use-soft-page-breaks', 'true');
            $XMLBody->appendChild($XMLText);

            foreach($aPagePng as $keyPage=>$page) {
                if (file_exists($page['path'])){
                    $infos = getimagesize ($page['path']);
                    if (intval($infos[0]) == 1754) {
                        $source = imagecreatefrompng($folder->pwd().$page['name']);
                        $rotate = imagerotate($source, 90, 0);
                        imagepng( $rotate, $page['path']);
                        imagedestroy($source);
                        imagedestroy($rotate);
                    }
                    $XMLP = $doc->createElement('text:p');
                    $XMLP->setAttribute('text:style-name', 'P1');

                    $XMLFrame = $doc->createElement('draw:frame');
                    $XMLFrame->setAttribute('draw:style-name', 'fr1');
                    $XMLFrame->setAttribute('draw:name', $keyPage);
                    $XMLFrame->setAttribute('text:anchor-type', 'char');
                    $XMLFrame->setAttribute('svg:width', '21cm');
                    $XMLFrame->setAttribute('svg:height', '29.7cm');
                    $XMLFrame->setAttribute('draw:z-index', $keyPage);

                    $XMLImage = $doc->createElement('draw:image');
                    $XMLImage->setAttribute('xlink:href', 'Pictures/'.$page['name']);
                    $XMLImage->setAttribute('xlink:type', 'simple');
                    $XMLImage->setAttribute('xlink:show', 'embed');
                    $XMLImage->setAttribute('xlink:actuate', 'onLoad');

                    $XMLFrame->appendChild($XMLImage);
                    $XMLP->appendChild($XMLFrame);
                    $XMLText->appendChild($XMLP);

                    $XMLImageEntry = $manifest->createElement('manifest:file-entry');
                    $XMLImageEntry->setAttribute('manifest:media-type', '');
                    $XMLImageEntry->setAttribute('manifest:full-path', 'Pictures/'.$page['name']);
                    $XMLManifest->appendChild($XMLImageEntry);

                    $document->addFile($page['path'],'Pictures/'.$page['name']);
                }
            }

            $document->addFromString('META-INF/manifest.xml', $manifest->saveXML());
            $document->addFromString('content.xml', $doc->saveXML());
            
        }

        function _generateDefaultHeaders($doc) {
            $XMLDocumentContent = $doc->createElement('office:document-content');
            $XMLDocumentContent->setAttribute('xmlns:office', 'urn:oasis:names:tc:opendocument:xmlns:office:1.0');
            $XMLDocumentContent->setAttribute('xmlns:text', 'urn:oasis:names:tc:opendocument:xmlns:text:1.0');
            $XMLDocumentContent->setAttribute('xmlns:fo', 'urn:oasis:names:tc:opendocument:xmlns:xsl-fo-compatible:1.0');
            $XMLDocumentContent->setAttribute('xmlns:style', 'urn:oasis:names:tc:opendocument:xmlns:style:1.0');
            $XMLDocumentContent->setAttribute('xmlns:draw', 'urn:oasis:names:tc:opendocument:xmlns:drawing:1.0');
            $XMLDocumentContent->setAttribute('xmlns:xlink', 'http://www.w3.org/1999/xlink');
            $XMLDocumentContent->setAttribute('xmlns:svg', 'urn:oasis:names:tc:opendocument:xmlns:svg-compatible:1.0');
            $XMLDocumentContent->setAttribute('office:version', '1.2');
            return $XMLDocumentContent;
        }

        function _generateDefaultStyle($doc) {
            $XMLAutomaticStyles = $doc->createElement('office:automatic-styles');
            $XMLStyleP = $doc->createElement('style:style');
            $XMLStyleP->setAttribute('style:name', 'P1');
            $XMLStyleP->setAttribute('style:family', 'paragraph');
            $XMLStyleP->setAttribute('style:parent-style-name', 'Standard');
            $XMLStylePChild = $doc->createElement('style:paragraph-properties');
            $XMLStylePChild->setAttribute('fo:break-before', 'page');

            $XMLStyleP->appendChild($XMLStylePChild);
            $XMLAutomaticStyles->appendChild($XMLStyleP);

            $XMLStyleI = $doc->createElement('style:style');
            $XMLStyleI->setAttribute('style:name', 'fr1');
            $XMLStyleI->setAttribute('style:family', 'graphic');
            $XMLStyleI->setAttribute('style:parent-style-name', 'Graphics');
            $XMLStyleIChild = $doc->createElement('style:graphic-properties');
            $XMLStyleIChild->setAttribute('style:run-through', 'foreground');
            $XMLStyleIChild->setAttribute('style:wrap', 'run-through');
            $XMLStyleIChild->setAttribute('style:number-wrapped-paragraphs', 'no-limit');
            $XMLStyleIChild->setAttribute('style:vertical-pos', 'top');
            $XMLStyleIChild->setAttribute('style:vertical-rel', 'page');
            $XMLStyleIChild->setAttribute('style:horizontal-pos', 'center');
            $XMLStyleIChild->setAttribute('style:horizontal-rel', 'page');
            $XMLStyleIChild->setAttribute('style:mirror', 'none');
            $XMLStyleIChild->setAttribute('fo:clip', 'rect(0cm, 0cm, 0cm, 0cm)');
            $XMLStyleIChild->setAttribute('draw:luminance', '0%');
            $XMLStyleIChild->setAttribute('draw:contrast', '0%');
            $XMLStyleIChild->setAttribute('draw:red', '0%');
            $XMLStyleIChild->setAttribute('draw:green', '0%');
            $XMLStyleIChild->setAttribute('draw:blue', '0%');
            $XMLStyleIChild->setAttribute('draw:gamma', '0%');
            $XMLStyleIChild->setAttribute('draw:color-inversion', 'false');
            $XMLStyleIChild->setAttribute('draw:image-opacity', '100%');
            $XMLStyleIChild->setAttribute('draw:color-mode', 'standard');
            $XMLStyleIChild->setAttribute('style:flow-with-text', 'false');
            $XMLStyleIChild->setAttribute('style:number-wrapped-paragraphs', '1');

            $XMLStyleI->appendChild($XMLStyleIChild);
            $XMLAutomaticStyles->appendChild($XMLStyleI);

            return $XMLAutomaticStyles;
        }

}?>
