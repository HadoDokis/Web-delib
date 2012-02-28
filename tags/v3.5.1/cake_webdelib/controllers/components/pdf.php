<?php

    class PdfComponent extends Object {

	function concatener($document_path, $annexes=array()) {
	    $output = array();
            if (empty( $annexes))
                return true;
            $pdftk_exec = Configure::read('PDFTK_EXEC');
	    if (!file_exists( $pdftk_exec)){
                $this->log("L'executable pdftk n'existe pas");
                return false;
	    }
	    $annexes_path = implode(" ", $annexes);
            $doc_orig = $document_path.'-orig';
            rename($document_path, $doc_orig);
            $commande = "$pdftk_exec $doc_orig  $annexes_path output $document_path";
	    exec($commande, $output, $return_value);
              
            return ($return_value ==0);
        }  
    }
?>
