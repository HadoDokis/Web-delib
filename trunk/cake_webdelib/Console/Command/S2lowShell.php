<?php

class S2lowShell extends AppShell {

    public $uses = array('Acteurseance');

    public function main() {
        App::uses('AppShell', 'Console/Command');
        App::uses('ComponentCollection', 'Controller');
        App::uses('S2lowComponent', 'Controller/Component');
        
        //Si service désactivé ==> quitter
        if (!Configure::read('USE_S2LOW')) exit("Service S2LOW désactivé");
        
        $collection = new ComponentCollection();
        $this->S2low =new S2lowComponent($collection);

        $mails = $this->Acteurseance->find('all', array('conditions' => array('date_envoi !=' => null,
                                                                              'date_reception' => null),
                                                        'recursive'  => -1));
     
        foreach($mails as $mail) {
            $this->Acteurseance->id = $mail['Acteurseance']['id'];
            $mail_id  = $mail['Acteurseance']['mail_id'];
            $infos = $this->S2low->checkMail($mail_id);
            $debut = strpos($infos, 'mailTo:t:');
            $tmp = substr($infos, $debut+ strlen('mailTo:t:'), strlen($infos));
            $fin   = strpos($tmp, '==message==');
            $info = trim(substr($tmp, 0, $fin)); 
            if ($debut === false) 
                continue;
            else {
                 $this->Acteurseance->saveField('date_reception', $info);
            }
        }
        
    }
}


?>
