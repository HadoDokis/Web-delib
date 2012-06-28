<?php
/*
 * Created on 31 juil. 07
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

class FckHelper extends Helper { 

    var $helpers = Array('Html', 'Javascript'); 

    function load($id) { 
        $did = ''; 
        foreach (explode('.', $id) as $v) { 
            $did .= ucfirst($v); 
        }  

        $code = "CKEDITOR.replace( '".$did."' );"; 
        return $this->Javascript->codeBlock($code);  
    } 
} 

?>
