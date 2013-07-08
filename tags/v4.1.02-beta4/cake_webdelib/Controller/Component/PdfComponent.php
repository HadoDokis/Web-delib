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

            //Preparation de la commande ghostscript
            $GS_EXEC =  Configure::read('GS_EXEC');
            $GS_OPTS = ' -dNOPAUSE -dBATCH -sDEVICE=pngalpha -r150 -sPAPERSIZE=a4 -sOutputFile="'.$outputDir.'/%d.png"';

            $output = array();

            $commande = "$GS_EXEC  $GS_OPTS $filename" ;
            //generation des images
            exec($commande, $output, $return_value);
            //génération du fichier ODT
            $this->generateOdtFileWithImages($outputDir);

            return file_get_contents($outputDir."/result.odt");
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

        function generateOdtFileWithImages($outputDir) {
            $document = new ZipArchive();
            $document->open($outputDir."/result.odt", ZIPARCHIVE::OVERWRITE);
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

            $nbPages = count(scandir($outputDir)) -1;
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
                    $XMLP = $doc->createElement('text:p');
                    $XMLP->setAttribute('text:style-name', 'P1');

                    $XMLFrame = $doc->createElement('draw:frame');
                    $XMLFrame->setAttribute('draw:style-name', 'fr1');
                    $XMLFrame->setAttribute('draw:name', "$i");
                    $XMLFrame->setAttribute('text:anchor-type', 'char');
                    $XMLFrame->setAttribute('svg:width', '21cm');
                    $XMLFrame->setAttribute('svg:height', '29.7cm');
                    $XMLFrame->setAttribute('draw:z-index', "$i");

                    $XMLImage = $doc->createElement('draw:image');
                    $XMLImage->setAttribute('xlink:href', "Pictures/$i.png");
                    $XMLImage->setAttribute('xlink:type', 'simple');
                    $XMLImage->setAttribute('xlink:show', 'embed');
                    $XMLImage->setAttribute('xlink:actuate', 'onLoad');

                    $XMLFrame->appendChild($XMLImage);
                    $XMLP->appendChild($XMLFrame);
                    $XMLText->appendChild($XMLP);

                    $XMLImageEntry = $manifest->createElement('manifest:file-entry');
                    $XMLImageEntry->setAttribute('manifest:media-type', '');
                    $XMLImageEntry->setAttribute('manifest:full-path', "Pictures/$i.png");
                    $XMLManifest->appendChild($XMLImageEntry);

                    $document->addFile(($outputDir."/$i.png"),"Pictures/$i.png");
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
            $XMLStyleIChild->setAttribute('style:run-through', 'background');
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

            $XMLStyleI->appendChild($XMLStyleIChild);
            $XMLAutomaticStyles->appendChild($XMLStyleI);

            return $XMLAutomaticStyles;
        }
    }
?>