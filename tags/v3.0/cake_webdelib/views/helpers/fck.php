<?php
/*
 * Created on 31 juil. 07
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
class FckHelper extends Helper
{
    function load($id, $toolbar = 'Default') {
        $did = '';
        foreach (explode('[', str_replace(']','', $id)) as $v) {
            $did .= ucfirst($v);
        }
	$url = 'http://'.$_SERVER['HTTP_HOST'].$this->base.'/js/fckeditor/';
        return <<<FCK_CODE
             <script type="text/javascript">
             fckLoader_$did = function () {
                 var bFCKeditor_$did = new FCKeditor('$id');
                 bFCKeditor_$did.BasePath = '$url';
                 bFCKeditor_$did.ToolbarSet = '$toolbar';
                 bFCKeditor_$did.ReplaceTextarea();
             }
             fckLoader_$did();
             </script>
FCK_CODE;
    }

   function fileBrowserInput($fieldName, $htmlAttributes = array(), $return = false) {
        $output = $this->input($fieldName, $htmlAttributes, $return);
        if (!isset($htmlAttributes['id']))
           $htmlAttributes['id'] = $this->model.Inflector::camelize($this->field);
        $output .= '<script type="text/javascript">';
        $output .= "//<![CDATA[\n";
        $output .= "function openFileBrowser(id){\n";
        $output .= "var fck = new FCKeditor(id);\n";
        $output .= "fck.BasePath = '".$this->webroot."js/fckeditor/'\n";
        $output .= "var url = fck.BasePath + 'editor/filemanager/browser/default/browser.html?Type=Image&Connector=connectors/php/connector.php';\n";
        $output .= "var sOptions = 'toolbar=no,status=no,resizable=yes,dependent=yes,scrollbars=yes';\n";
        $output .= "sOptions += ',width=640';\n";
        $output .= "sOptions += ',height=480';\n";
        $output .= "window.SetUrl = function(fileUrl){\n";
        $output .= "document.getElementById('".$htmlAttributes['id']."').value = fileUrl;\n";
      //  $output .= "\$(id).value = fileUrl;\n";
        $output .= "}\n";
        $output .= "var oWindow = window.open( url, 'FCKBrowseWindow', sOptions ) ;\n";
        $output .= "}\n";
        $output .= "//]]>\n";
        $output .= '</script>';
        $output .= '<a href="#" onclick="openFileBrowser(\''.$htmlAttributes['id'].'\'); return false;">select an image...</a>';
        return $output;
    }

} 
?>
