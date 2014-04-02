<?php
// Parametres du service
    define ('HOST_AH', 'http://horo.dell.adullact.org');
    define ('DEBUG', true);
    define ('FILE_LOG', '/tmp/opensign.log');
    define ('OPENSSL_PATH', '/usr/bin/');
    define ('CONFIG_OPENSSL', '/etc/ssl/liberhorodatage.cnf');

//Parametres du certificat
    define ('CACERT',         '/etc/ssl/tmp_ca/certs/root_ca.crt');
    define ('PUBLIC_PEM',     '/etc/ssl/tmp_ca/certs/ts.crt');
    define ('KEY_PEM',        '/etc/ssl/tmp_ca/keys/ts_pkey.pem');

    ini_set('soap.wsdl_cache_enabled', 0);
    ini_set('default_socket_timeout', 180);
?>
