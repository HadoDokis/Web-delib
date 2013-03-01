<?php

    class PdfComponent extends Component {

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

        function toOdt($filename){
            $outputDir =  tempdir();
            $GS_EXEC = Configure::read('GS_EXEC');
            $GS_OPTS = ' -dNOPAUSE -dBATCH -sDEVICE=pngalpha -r150 -sPAPERSIZE=a4 -sOutputFile="'.$outputDir.'/%d.png"';

            $output = array();
            require_once(ROOT.DS.APP_DIR.DS.'Vendor'.DS.'odtphp'.DS.'odf.php');
            $doc = new Odf(ROOT.DS.APP_DIR.DS.'Vendor'.DS.'odtphp'.DS.'modele.odt');

            $commande = "$GS_EXEC  $GS_OPTS $filename" ;
            exec($commande, $output, $return_value);
            $nbPages = count(scandir($outputDir)) -1;
            $article = $doc->setSegment('articles');
            for($i=1; $i<$nbPages; $i++) {
                if (file_exists($outputDir."/$i.png")){
                    $infos = getimagesize ($outputDir."/$i.png");
                    if (intval($infos[0]) == 1754) {
                        $source = imagecreatefrompng($outputDir."/$i.png");
                        $rotate = imagerotate($source, 90, 0);
                        imagepng( $rotate, $outputDir."/$i.png");
                        imagedestroy($source);
                        imagedestroy($rotate);
                    }
                    $article->setImage('image', $outputDir."/$i.png", $i -1);
                    
                    $article->merge();
                }
            }
            $doc->mergeSegment($article);
            $doc->saveToDisk($outputDir."/result.odt");
            return file_get_contents($outputDir."/result.odt");
        }
    }
?>
