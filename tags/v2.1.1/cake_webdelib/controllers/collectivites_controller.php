<?php
/*
 * Created on 24 sept. 07
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 class CollectivitesController extends AppController {
	var $uses = array( 'Collectivite', 'User');

	// Gestion des droits
	var $aucunDroit = array('synchronize', 'setMails');
	var $commeDroit = array('edit'=>'Collectivites:index', 'setLogo'=>'Collectivites:index');

	function index() {
		$this->Seance->recursive = 0;
		$this->set('collectivite', $this->Collectivite->findAll());
                $this->set('logo_path',   'http://'.$_SERVER['HTTP_HOST'].$this->base."/files/image/logo.jpg");
	}

 	function edit($id = null) {
		if (empty($this->data)) {
			$this->data = $this->Collectivite->read(null, $id);
		}
		else {
			if(!empty($this->params['form']))
				$this->Collectivite->save($this->data);
			$this->redirect('/collectivites');
		}
 	}

 	function setLogo($id = null) {
 		if (empty($this->data)) {
			$this->data = $this->Collectivite->read(null, $id);
		}
		else {
		    $type_file = $this->data['Image']['logo']['type'];
            if( !strstr($type_file, 'jpg') && !strstr($type_file, 'jpeg') ){
                //exit("Le fichier n'est pas une image au format jpg/jpeg");
                $this->Session->setFlash("Le fichier n'est pas une image au format jpg/jpeg");
    		}else {
			$name_file = 'logo.jpg';
			$pos =  strrpos ( getcwd(), 'webroot');
			$path = substr(getcwd(), 0, $pos);
			$content_dir = $path.'webroot/files/image/';
			$tmp_file =  $this->data['Image']['logo']['tmp_name'];

    		if( !move_uploaded_file($tmp_file, 	$content_dir.$name_file) )
       		    exit("Impossible de copier le fichier dans $content_dir");
			$this->redirect('/collectivites');
		}}
 	}


        function setMails($id = null) {
            $path = CONFIG_PATH.'emails/';
            $this->set('email_path', $path);

            if (!empty($this->data)) {
                $cpt = 1;
                foreach ($this->data['Mail'] as $mail){
                    if ($cpt == 1) {
                         if ($mail['name']=='') continue;
                         $name_file = 'refus';
                         $tmp_file =  $mail['tmp_name'];
                         if( !move_uploaded_file($tmp_file, $path.$name_file) )
                             exit("Impossible de copier le fichier dans $content_dir");
                    }  
                    if ($cpt == 2) {
                         if ($mail['name']=='') continue;
                         $name_file = 'traiter';
                         $tmp_file =  $mail['tmp_name'];
                         if( !move_uploaded_file($tmp_file, $path.$name_file) )
                             exit("Impossible de copier le fichier dans $content_dir");
                    }  
                    if ($cpt ==  3) {
                         if ($mail['name']=='') continue;
                         $name_file = 'insertion';
                         $tmp_file =  $mail['tmp_name'];
                         if( !move_uploaded_file($tmp_file, $path.$name_file) )
                             exit("Impossible de copier le fichier dans $content_dir");
                    }  
                    if ($cpt == 4) {
                         if ($mail['name']=='') continue;
                         $name_file = 'convocation';
                         $tmp_file =  $mail['tmp_name'];
                         if( !move_uploaded_file($tmp_file, $path.$name_file) )
                             exit("Impossible de copier le fichier dans $content_dir");
                    }  
                    $cpt++;

                }
                $this->redirect('/collectivites/');
            }
        }

 	function synchronize() {
	    $ldapconn = ldap_connect(LDAP_HOST, PORT)
		    or die("Impossible de se connecter au serveur LDAP {LDAP_HOST}");
		if ($ldapconn) {
    		// bind with appropriate dn to give update access
    	    $r=ldap_bind($ldapconn, MANAGER, LDAP_PASS);
 		    if(!$r)
 		        die("ldap_bind failed<br>");

            $dn = "ou=users,dc=adullact,dc=org";
			$filter="(|(sn=*))";
			$justthese = array(MAIL, COMMON_NAME, UNIQUE_ID, PASSWORD_USER);
			$sr=ldap_search($ldapconn, $dn, $filter, $justthese);
			$users = ldap_get_entries($ldapconn, $sr);

			foreach ($users as $user) {
				if (isset( $user[UNIQUE_ID][0]))
					$login = $user[UNIQUE_ID][0];

				if (isset( $user[PASSWORD_USER][0]))
					$password = $user[PASSWORD_USER][0];
				else
					unset($password);

				if (isset($login)&& isset($password)){
					if (isset( $user[MAIL][0]))
					    $mail = $user[MAIL][0];
					else
					     $mail = "";

					if (isset( $user[COMMON_NAME][0])){
					    $cn = $user[COMMON_NAME][0];
					    $prenom = substr($cn, 0, strpos($cn, ' ') );
					    $nom    = substr($cn, strpos($cn, ' '), strlen($cn));
			    	}
			    	else
			    	    $cn = "";

			      	$data = $this->User->findAll("User.login = '$login'");
					if (empty($data)){
						$this->User->create();
					    $this->data['User']['id']='';
					    $this->data['User']['nom']=$nom;
				    	$this->data['User']['prenom']=$prenom;
				   		$this->data['User']['login']=$login;
				    	$this->data['User']['mail']=$mail;
						$pwd= base64_decode(substr($password, 5, strlen($password)));
						$mdp = unpack("H*",$pwd );
						$this->data['User']['password']= $mdp[1];
				    	$this->User->save($this->data);
					}
				}
			}
            ldap_close($ldapconn);
        } else {
            echo "Unable to connect to LDAP server";
        }
 	}
}



?>
