<?php

// On prend du premier caractere de l'url jusqu'au premier .
// comme nom de la base de données.
//
//ex. : http://collectivite.webdelib.demonstrations.adullact.org
// 		DATABASE = collectivite
//

define ('DATABASE', substr($_SERVER['SERVER_NAME'], 0, strpos ($_SERVER['SERVER_NAME'], '.')));

class DATABASE_CONFIG {

	var $default = array(
		'driver' => 'mysql',
		'connect' => 'mysql_connect',
		'host' => 'localhost',
		'login' => 'root',
		'password' => '',
//		'database' => DATABASE,
		'database' => 'webdelib',
		'prefix' => ''
	);
}

?>