<?php
    ////////////////////////
    // Configuration GEDOOo
    ////////////////////////
    define ('GENERER_DOC_SIMPLE', false);
    define ('USE_GEDOOO', true);
    define("GEDOOO_WSDL",  "http://gedooo.local:8080/axis2/services/OfficeService?wsdl");

    ////////////////////////
    // Configuration S2LOW
    ////////////////////////
    define('PEM', '/var/www/webdelib/app/config/client.pem');
    define('PASSWORD', 'demo_u');
    define('HOST', 'demo-s2low.extranet.adullact.org');
    define('SSLKEY', '/var/www/webdelib/app/config/key.pem');
    define('CA_PATH', '/var/www/webdelib/app/config/');
    define('FILE_CLASS', '/var/www/webdelib/app/webroot/files/actes/classification.xml');

    ////////////////////////
    // Configuration du LDAP 
    ////////////////////////
    define ('LDAP_HOST', 'hostname');
    define ('PORT',      '389');
    define ('BASE_DN',   'dc=domaine,dc=com');
    define ('MANAGER',   'cn=manager,dc=domaine,dc=com');
    define ('LDAP_PASS', '');
    define ('MAIL', 'mail');
    define ('COMMON_NAME', 'cn');
    define ('UNIQUE_ID', 'uid');
    define ('PASSWORD_USER', 'userpassword');
?>
