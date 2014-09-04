<?php
/* Configuration du debug */
Configure::write('debug', 1);
/* URL de Webdelib (pour mails) */
Configure::write('WEBDELIB_URL', 'http://webdelib.x.x.org');
//Router::fullBaseUrl
Configure::write('Config.language', 'fra');

/* Configuration session default 600 */
Configure::write('Session.timeout', 600);
Configure::write('Cakeflow.optimisation', true);

/* Options d'affichage */
Configure::write('AFFICHE_HIERARCHIE_SERVICE', true);
Configure::write('AFFICHE_CONVOCS_ANONYME', true);
Configure::write('CONVOCS_MODIFIABLES', false);
Configure::write('NOT_UNIQUE_CIRCUIT', true);
Configure::write('DELIBERATIONS_MULTIPLES', false); //Mode délibérations multiples
Configure::write('INIT_SEQ', false); //Initialiser le numéro de séquence
Configure::write('LIMIT', 5); //limitation du nombre de projets par banette
Configure::write('PROTOCOLE_DL', 'vnd.sun.star.webdav');

/* Configuration pour la conversion de format des documents */
Configure::write('CONVERSION_TYPE', 'CLOUDOOO');
Configure::write('CLOUDOOO_HOST', '192.168.2.35');
Configure::write('CLOUDOOO_PORT', '8012');

/* Configuration pour la manipulation des pdf */
Configure::write('PDFTK_EXEC', '/usr/bin/pdftk');
Configure::write('PDFINFO_EXEC', '/usr/bin/pdfinfo');

/* Configuration GhostScript */
// Chemin vers l'executable
Configure::write('GS_EXEC', '/usr/bin/gs');
// Résolution d'image (pixels) : Agit sur la rapidité de génération des annexes (Conversion pdf et doc en image)
Configure::write('GS_RESOLUTION', '150');

/* Configuration mail */
Configure::write('MAIL_FROM', 'Webdelib v4.2 <webdelib@adullact.org>');
Configure::write('SMTP_USE', false);
Configure::write('SMTP_PORT', '');
Configure::write('SMTP_TIMEOUT', '');
Configure::write('SMTP_HOST', '');
Configure::write('SMTP_USERNAME', '');
Configure::write('SMTP_PASSWORD', '');
Configure::write('SMTP_CLIENT', '');

/* Configuration GEDOOo */
//Configure::write('Gedooo.wsdl', 'http://192.168.2.185:8080/GEDOOo/OfficeService?wsdl');
Configure::write('Gedooo.wsdl', 'http://192.168.2.35:8980/ODFgedooo/OfficeService?wsdl');
Configure::write('Gedooo.method', 'GedoooClassic');

/**
 * Parapheur (Signature électronique, délégation)
 */
/* Activer le parapheur */
Configure::write('USE_PARAPHEUR', true);
/* Service utilisé pour le parapheur ('PASTELL', 'IPARAPHEUR', ...) */
Configure::write('PARAPHEUR', 'IPARAPHEUR');

/**
 * TDT (Tiers de télétransmission)
 */
/* Activer le TDT (tiers de télétransmission) */
Configure::write('USE_TDT', true);
/* Service utilisé pour le TDT ('PASTELL', 'S2LOW', ...) */
Configure::write('TDT', 'S2LOW');

/**
 * SAE (Service d'archivage électronique)
 */
/* Activer le SAE */
Configure::write('USE_SAE', false);
/* Service utilisé pour le TDT ('PASTELL', 'ASALAE', ...) */
Configure::write('SAE', '');

/**
 * GED (Gestion électronique documentaire)
 */
/* Activer la GED */
Configure::write('USE_GED', true);
/* Service utilisé pour la GED ('PASTELL' ou 'GED') */
Configure::write('GED', 'CMIS');
Configure::write('GED_XML_VERSION', '3');

/**
 * Configuration des connecteurs
 */
/* Configuration de PASTELL */
Configure::write('USE_PASTELL', false);
Configure::write('PASTELL_HOST', 'https://pastell.test.adullact.org');
Configure::write('PASTELL_TYPE', 'actes-generique');
Configure::write('PASTELL_PARAPHEUR_TYPE', 'Actes');
Configure::write('PASTELL_LOGIN', 'webdelib');
Configure::write('PASTELL_PWD', 'webdelib');
Configure::write('PASTELL_CACERT', APP . 'Config/cert_pastell/ac.pem');
Configure::write('PASTELL_CLIENTCERT', APP . 'Config/cert_pastell/cert.pem');
Configure::write('PASTELL_CERTPWD', '');

/* Configuration Webservice du i-Parapheur */
Configure::write('USE_IPARAPHEUR', true);
Configure::write('IPARAPHEUR_HOST', 'https://parapheur.demonstrations.adullact.org');
Configure::write('IPARAPHEUR_LOGIN', 'webdelib');
Configure::write('IPARAPHEUR_PWD', 'webdelib123');
Configure::write('IPARAPHEUR_TYPE', 'Actes');
Configure::write('IPARAPHEUR_VISIBILITY', 'PUBLIC');
Configure::write('IPARAPHEUR_WSDL', 'ws-iparapheur?wsdl');
Configure::write('IPARAPHEUR_CACERT', APP . 'Config/cert_parapheur/ac.pem');
Configure::write('IPARAPHEUR_CLIENTCERT', APP . 'Config/cert_parapheur/cert.pem');
Configure::write('IPARAPHEUR_CERTPWD', 'demo-u');

/* Configuration S2LOW */
Configure::write('USE_S2LOW', true);
Configure::write('S2LOW_HOST', 'https://demo-s2low11.extranet.adullact.org');
Configure::write('S2LOW_PEM', APP . 'Config/cert_s2low/client.pem');
Configure::write('S2LOW_SSLKEY', APP . 'Config/cert_s2low/key.pem');
Configure::write('S2LOW_CAPATH', APP . 'Config/cert_s2low/');
Configure::write('S2LOW_CLASSIFICATION', APP . 'webroot/files/actes/classification.xml');
Configure::write('S2LOW_CERTPWD', 'demo-u');
Configure::write('S2LOW_USEPROXY', false);
Configure::write('S2LOW_PROXYHOST', '');
Configure::write('S2LOW_MAILSEC', true);
Configure::write('S2LOW_MAILSECPWD', '');

/* Configuration CONNECTEUR CMIS GED */
Configure::write('CMIS_HOST', 'http://cmis.test.adullact.org/alfresco/service/api/cmis');
Configure::write('CMIS_LOGIN', 'splaza');
Configure::write('CMIS_PWD', 'G3,V5e9');
Configure::write('CMIS_REPO', '/Sites/Web-delib');

/* Configuration pour Idélibre */
Configure::write('USE_IDELIBRE', false);
Configure::write('IDELIBRE_HOST', '');
Configure::write('IDELIBRE_LOGIN', '');
Configure::write('IDELIBRE_PWD', '');
Configure::write('IDELIBRE_CONN', ''); //Collectivité
//Certificats pour Idélibre
Configure::write('IDELIBRE_USE_CERT', false);
Configure::write('IDELIBRE_CAPATH', APP . 'Config/cert_idelibre/');
Configure::write('IDELIBRE_CA', APP . 'Config/cert_idelibre/ca.pem');
Configure::write('IDELIBRE_KEY', APP . 'Config/cert_idelibre/key.pem');
Configure::write('IDELIBRE_CERT', APP . 'Config/cert_idelibre/cert.pem');
Configure::write('IDELIBRE_CERTPWD', '');

/* Configuration ASAL@AE */
Configure::write('USE_ASALAE', false);
Configure::write('ASALAE_HOST', '');
Configure::write('ASALAE_LOGIN', '');
Configure::write('ASALAE_PWD', '');
Configure::write('ASALAE_WSDL', '');
Configure::write('ASALAE_SIREN_ARCHIVE', '');
Configure::write('ASALAE_NUMERO_AGREMENT', '');

/**
 * Configuration des annuaires (OpenLDAP ou Active Directory)
 */
Configure::write('USE_LDAP', false);
Configure::write('LDAP', 'OPENLDAP');
Configure::write('LDAP_HOST', '');
Configure::write('LDAP_PORT', '');
Configure::write('LDAP_LOGIN', '');
Configure::write('LDAP_PASSWD', '');
Configure::write('LDAP_UID', '');
Configure::write('LDAP_BASE_DN', '');
Configure::write('LDAP_ACCOUNT_SUFFIX', '');
Configure::write('LDAP_DN', '');
