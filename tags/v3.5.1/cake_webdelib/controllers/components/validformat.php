<?php
class ValidformatComponent extends Object {

/**
 *
 */
function check($file, $format) {
    $tmp_name = $file['tmp_name'];
    $cmd = "file --mime -b  $tmp_name";
    $result = trim(shell_exec($cmd));

    $this->log($result);
    $this->log($format);

    switch ($format) {
    case 'odt':
        return ($result == 'application/vnd.oasis.opendocument.text');
        break;
    case 'pdf':
        return ($result == 'application/pdf');
        break;
    case 'jpeg':
        return ($result == 'image/jpeg');
        break;
    case 'png': 
        return ($result == 'image/png');
        break;
    default:
         return false;
    }
     
    return false;
}




}?>
