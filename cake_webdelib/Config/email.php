<?php
/**
 *
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Config
 * @since         CakePHP(tm) v 2.0.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

/**
 * This is email configuration file.
 *
 * Utilisé pour l'envoi d'email
 * (Par les modèles seulement dans webdelib 4.2)
 *
 */
class EmailConfig {

    public $default = array(
        'transport' => 'Mail',
        'emailFormat' => 'both',
        'charset' => 'utf-8',
        'headerCharset' => 'utf-8',
    );

    public $smtp = array(
        'transport' => 'Smtp',
        'port' => 25,
        'timeout' => 30,
        'client' => null,
        'log' => false,
        'emailFormat' => 'both',
        'charset' => 'utf-8',
        'headerCharset' => 'utf-8',
    );

    public function __construct() {
        $from = Configure::read('MAIL_FROM');
        if(!filter_var($from, FILTER_VALIDATE_EMAIL)){
            $syntaxe='#(.*)\s<([\w.-]+@[\w.-]+\.[a-zA-Z]{2,6})>#';
            $froms=array();
            if (preg_match($syntaxe,$from,$froms)){
                foreach ($froms as $from){
                    if(filter_var($from, FILTER_VALIDATE_EMAIL)){
                        if ($from == $froms[2])
                            $from = array($from => $froms[1]);
                        break;
                    }
                }
            }
        }
        if (!Configure::read('SMTP_USE')){
            //Protocole Mail standard
            $this->default['from'] = $from;
        }else{
            $this->smtp['from'] = $from;
            $this->smtp['port'] = Configure::read('SMTP_PORT');
            $this->smtp['timeout'] = Configure::read('SMTP_TIMEOUT');
            $this->smtp['host'] = Configure::read('SMTP_HOST');
            $this->smtp['username'] = Configure::read('SMTP_USERNAME');
            $this->smtp['password'] = Configure::read('SMTP_PASSWORD');
            $this->smtp['client'] = Configure::read('SMTP_CLIENT');
        }
    }
}