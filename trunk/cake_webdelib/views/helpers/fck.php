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
		$url = 'http://'.$_SERVER['HTTP_HOST'].$this->base.'/js/';


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
} 
?>
