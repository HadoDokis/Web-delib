<?php
App::uses('Folder', 'Utility');
App::uses('File', 'Utility');
App::uses('AppTools', 'Lib');

/**
 * Class ConversionComponent
 */
class ConversionComponent extends Component {

    function __construct() {

    }

    /**
     * Conversion de format du fichie $fileUri vers le format $format
     * @param $pathFile
     * @param $dataExtention
     * @param $dataSortieExtention
     * @return array|bool|string tableau de réponse composé comme suit :
     *      'resultat' => boolean
     *      'info' => string
     *      'convertedFileUri' => nom et chemin du fichier converti
     */
    function convertirFichier($pathFile, $dataExtention, $dataSortieExtention) {

        return $this->_convertir(file_get_contents($pathFile), $dataExtention, $dataSortieExtention);
    }

    /**
     * Conversion de format du fichie $fileUri vers le format $format
     * @param $sData
     * @param $dataExtention
     * @param $dataSortieExtention
     * @return array|bool|string tableau de réponse composé comme suit :
     *      'resultat' => boolean
     *      'info' => string
     *      'convertedFileUri' => nom et chemin du fichier converti
     */
    function convertirFlux($sData, $dataExtention, $dataSortieExtention) {

        return $this->_convertir($sData, $dataExtention, $dataSortieExtention);
    }

    /**
     * @param $data
     * @param $dataExtention
     * @param $dataSortieExtention
     * @return array|bool|string
     */
    function _convertir($data, $dataExtention, $dataSortieExtention) {

        require_once 'XML/RPC2/Client.php';

        // initialisations
        $ret = array();
        $convertorType = Configure::read('CONVERSION_TYPE');

        if (empty($convertorType)) {
            $ret['resultat'] = false;
            $ret['info'] = __('Type du programme de conversion non déclaré dans le fichier de configuration de Webdelib', true);
            return $ret;
        }

        $options = array(
            'uglyStructHack' => true
        );

        $url = 'http://' . Configure::read('CLOUDOOO_HOST') . ':' . Configure::read('CLOUDOOO_PORT');
        $client = XML_RPC2_Client::create($url, $options);
        try {
            $result = $client->convertFile(base64_encode($data), $dataExtention, $dataSortieExtention, false, true);
            return base64_decode($result);
        } catch (XML_RPC2_FaultException $e) {
            $this->log('Exception #' . $e->getFaultCode() . ' : ' . $e->getFaultString(), 'debug');
            return false;
        }
    }

    /**
     * @param $model
     * @param $id
     * @param $field
     * @param $content
     * @return string
     */
    function odt2txt($model, $id, $field, $content) {
        $output = array();
        $odt2txt_exec = Configure::read('odt2txt_EXEC');

        $dir = TMP . "/$model" . '_' . "$id/";
        $odtFile = $dir . "$field" . '_' . "$id";
        if (!file_exists($dir))
            mkdir($dir);
        file_put_contents($odtFile . ".odt", $content);
        $commande = "$odt2txt_exec $odtFile" . 'odt';
        exec($commande, $output, $return_value);
        return (file_get_contents("$odtFile.txt"));

    }

    /**
     * @param $document_path
     * @param array $annexes
     * @return bool
     */
    function concatener($document_path, $annexes = array()) {
        $output = array();
        if (empty($annexes))
            return true;
        $pdftk_exec = Configure::read('PDFTK_EXEC');
        if (!file_exists($pdftk_exec)) {
            return false;
        }
        $annexes_path = implode(" ", $annexes);
        $doc_orig = $document_path . '-orig';
        rename($document_path, $doc_orig);
        $commande = "$pdftk_exec $doc_orig  $annexes_path output $document_path";
        exec($commande, $output, $return_value);

        return ($return_value == 0);
    }

    /**
     * @param $sData
     * @param $stypeMime
     * @return mixed
     */
    function toOdt($sData, $stypeMime) {

        $DOC_TYPE = Configure::read('DOC_TYPE');

        $folder = new Folder(AppTools::newTmpDir(TMP . 'files' . DS . 'conversion' . DS), true, 0777);
        //Si le fichier n'est pas un pdf on le converti en pdf
        if ($DOC_TYPE[$stypeMime]['extension'] != 'pdf')
            $sDataPdf = $this->convertirFlux($sData, $DOC_TYPE[$stypeMime]['extension'], 'pdf');

        $file = new File($folder->pwd() . DS . '_origine.pdf', true, 0777);
        if (!empty($DOC_TYPE[$stypeMime]['extension']) && $DOC_TYPE[$stypeMime]['extension'] == 'pdf') {
            if (is_file($sData))
                $file->append(file_get_contents($sData));
            else
                $file->append($sData);

        } else $file->append($sDataPdf);

        $return = $this->_PdftoOdt($folder, $file);
        $folder->delete();

        return $return;
    }

    /**
     * @param $folder
     * @param $fileOrigine
     * @return mixed
     * @throws InternalErrorException
     */
    function _PdftoOdt(&$folder, &$fileOrigine) {

        //Preparation de la commande ghostscript
        $PDFTK_EXEC = Configure::read('PDFTK_EXEC');
        $PDFINFO_EXEC = Configure::read('PDFINFO_EXEC');
        $GS_RESOLUTION = Configure::read('GS_RESOLUTION');

        $NbrPage=trim(shell_exec($PDFINFO_EXEC.' '.$fileOrigine->pwd().' | grep -a Pages: | sed -e "s/ *Pages: *//g"'));
        
        if($NbrPage>0){
            for ($i = 1;  $i <= $NbrPage ; $i++) {
               $pageName='page_'.  sprintf('%04d', $i);
               shell_exec($PDFTK_EXEC.' '. $fileOrigine->pwd() .' cat '.$i.' output '.$folder->pwd() . DS . $pageName.'.pdf-orign 2>&1');
               shell_exec($PDFTK_EXEC.' '. $folder->pwd() . DS . $pageName .'.pdf-orign dump_data output '.$folder->pwd() . DS . $pageName.'.txt 2>&1');
               shell_exec($PDFTK_EXEC.' '. $folder->pwd() . DS . $pageName.'.pdf-orign update_info '.$folder->pwd() . DS . $pageName.'.txt output '.$folder->pwd() . DS . $pageName.'.pdf 2>&1');
            }
        }else
            return ''; //GESTION DES ERREURS A FAIRE
        
        $fileOrigine->delete();
        $files = $folder->find('.*\.pdf', true);
        
        $i = 1;
        foreach ($files as $file) {
            $file = new File($folder->pwd() . DS . $file);

            $imagick = new Imagick();
            $imagick->setResolution($GS_RESOLUTION, $GS_RESOLUTION);
            $imagick->readImage($file->pwd() . '[0]');
            $imagick->setImageFormat('png');
            $imagick->writeImage($folder->pwd() . DS . $i . '.png');
            
            if ($imagick->getImageHeight() > $imagick->getImageWidth())
                $orientaion = 'portrait';
            else $orientaion = 'landscape';
            
            $pageParam[$i] = array('path' => $folder->pwd() . DS . $i . '.png',
                'name' => $i . '.png',
                'orientation' => $orientaion);
            
            $this->log('page '.$i.'| orientation='. $orientaion, 'debug');
            $imagick->clear();
            $file->close();
            
            $i++;
        }

        //génération du fichier ODT
        if (empty($pageParam))
            throw new InternalErrorException('Impossible de convertir le fichier : paramètres manquants');

        $this->generateOdtFileWithImages($folder, $pageParam);

        $file = new File($folder->pwd() . DS . 'result.odt');
        $return = $file->read();
        $file->close();

        return $return;
    }
    
    /**
     * @param $folder
     * @param $fileOrigine
     * @return mixed
     * @throws InternalErrorException
     */
    function _PdftoOdtGs(&$folder, &$fileOrigine) {

        //Preparation de la commande ghostscript
        $PDFINFO_EXEC = Configure::read('PDFINFO_EXEC');
        $GS_RESOLUTION = Configure::read('GS_RESOLUTION');

        $NbrPage=trim(shell_exec($PDFINFO_EXEC.' '.$fileOrigine->pwd().' | grep Pages: | sed -e "s/ *Pages: *//g"'));
        
        for ($i = 0;  $i < $NbrPage ; $i++) {
            $imagick = new Imagick();
            $imagick->setResolution($GS_RESOLUTION, $GS_RESOLUTION);
            $imagick->readImage($fileOrigine->pwd() . '['.$i.']');
            $imagick->setImageFormat('png');
            $imagick->writeImage($folder->pwd() . DS . $i . '.png');
            
            if ($imagick->getImageHeight() > $imagick->getImageWidth())
                $orientaion = 'portrait';
            else $orientaion = 'landscape';
            
            $pageParam[$i] = array('path' => $folder->pwd() . DS . $i . '.png',
                'name' => $i . '.png',
                'orientation' => $orientaion);
            
            $this->log('page '.($i+1).'| orientation='. $orientaion, 'debug');
            $imagick->clear();
        }

        //génération du fichier ODT
        if (empty($pageParam))
            throw new InternalErrorException('Impossible de convertir le fichier : paramètres manquants');

        $this->generateOdtFileWithImages($folder, $pageParam);

        $file = new File($folder->pwd() . DS . 'result.odt');
        $return = $file->read();
        $file->close();

        return $return;
    }

    /**
     * @param $folder
     * @param $aPagePng
     */
    function generateOdtFileWithImages(&$folder, $aPagePng) {

        App::import('Vendor', 'phpodt/phpodt');
        $odt = ODT::getInstance(true, $folder->pwd() . DS . 'result.odt');
        $pageStyleP = new PageStyle('myPageStylePortrait', 'Standard');
        $pageStyleP->setOrientation(StyleConstants::PORTRAIT);
        $pageStyleP->setHorizontalMargin('0cm', '0cm');
        $pageStyleP->setVerticalMargin('0cm', '0cm');
        $pStyleP = new ParagraphStyle('myPStyleP', 'Standard');
        $pStyleP->setBreakAfter(StyleConstants::PAGE);
        $pageStyleL = new PageStyle('myPageStyleLandscape', 'Landscape');
        $pageStyleL->setOrientation(StyleConstants::LANDSCAPE);
        $pageStyleL->setHorizontalMargin('0cm', '0cm');
        $pageStyleL->setVerticalMargin('0cm', '0cm');
        $pStyleL = new ParagraphStyle('myPStyleL', 'Landscape');

        $pStyleL->setBreakBefore(StyleConstants::PAGE);


        foreach ($aPagePng as $keyPage => $page) {

            if ($page['orientation'] == 'landscape') {
                $p = new Paragraph($pStyleL);
                $p->addImage($page['path'], '29.7cm', '21cm', true, $page['name'], 'paragraph');
            } else {
                $p = new Paragraph($pStyleP);
                $p->addImage($page['path'], '21cm', '29.7cm', true, $page['name'], 'paragraph');
            }
        }
        $odt->output();
    }
}