<?php

/**
 * Code source de la classe TdtMessage.
 *
 * PHP 5.3
 *
 * @package app.Model.TdtMessage
 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
 */

/**
 * Classe TdtMessage.
 *
 * @package app.Model.TdtMessage
 *
 */
class TdtMessage extends AppModel {

    public $useTable = "tdt_messages";
    public $belongsTo = array(
        'Deliberation' => array(
            'foreignKey' => 'delib_id',
        )
    );
    public $hasMany = array(
        'Reponse' => array(
            'className' => 'TdtMessage',
            'foreignKey' => 'parent_id',
            'order' => 'tdt_id ASC',
            'dependent' => true),
    );
    
    function RecupMessagePdfFromTar($data){
        //Vérification si les données ne sont pas déjà du pdf (Message reponse Pastell)
        $infos=AppTools::FileMime($data);
        if($infos['mimetype']==='application/pdf')
            return array('filename'=> 'Reponse.pdf', 'content'=>$data);
        
        $folder = new Folder(AppTools::newTmpDir(TMP . 'files' . DS . 'Tdt'), true, 0777);
        $fileTgz = new File($folder->path . DS . 'WD_TDT_DOC.tgz', true, 0777);
        $fileTgz->write($data);
        $phar = new PharData($fileTgz->pwd());
        $phar->extractTo($folder->path); 

        $files = $folder->find('.*\.pdf', true);
        foreach ($files as $file) {
            $file = new File($folder->pwd() . DS . $file);
            $content = $file->read();
            $name = $file->name;
        }
        $folder->delete();
        
        return array('filename'=> $name, 'content'=>$content);
    }
}
