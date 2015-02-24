<?php
/**
 * This file is loaded automatically by the app/webroot/index.php file after core.php
 *
 * This file should load/create any application wide configuration settings, such as
 * Caching, Logging, loading additional configuration files.
 *
 * You should also use this file to include any files that provide global functions/constants
 * that your application uses.
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
 * @since         CakePHP(tm) v 0.10.8.2117
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

// Setup a 'default' cache configuration for use in the application.
Cache::config('default', array('engine' => 'File'));

/**
 * The settings below can be used to set additional paths to models, views and controllers.
 *
 * App::build(array(
 *     'Model'                     => array('/path/to/models/', '/next/path/to/models/'),
 *     'Model/Behavior'            => array('/path/to/behaviors/', '/next/path/to/behaviors/'),
 *     'Model/Datasource'          => array('/path/to/datasources/', '/next/path/to/datasources/'),
 *     'Model/Datasource/Database' => array('/path/to/databases/', '/next/path/to/database/'),
 *     'Model/Datasource/Session'  => array('/path/to/sessions/', '/next/path/to/sessions/'),
 *     'Controller'                => array('/path/to/controllers/', '/next/path/to/controllers/'),
 *     'Controller/Component'      => array('/path/to/components/', '/next/path/to/components/'),
 *     'Controller/Component/Auth' => array('/path/to/auths/', '/next/path/to/auths/'),
 *     'Controller/Component/Acl'  => array('/path/to/acls/', '/next/path/to/acls/'),
 *     'View'                      => array('/path/to/views/', '/next/path/to/views/'),
 *     'View/Helper'               => array('/path/to/helpers/', '/next/path/to/helpers/'),
 *     'Console'                   => array('/path/to/consoles/', '/next/path/to/consoles/'),
 *     'Console/Command'           => array('/path/to/commands/', '/next/path/to/commands/'),
 *     'Console/Command/Task'      => array('/path/to/tasks/', '/next/path/to/tasks/'),
 *     'Lib'                       => array('/path/to/libs/', '/next/path/to/libs/'),
 *     'Locale'                    => array('/path/to/locales/', '/next/path/to/locales/'),
 *     'Vendor'                    => array('/path/to/vendors/', '/next/path/to/vendors/'),
 *     'Plugin'                    => array('/path/to/plugins/', '/next/path/to/plugins/'),
 * ));
 *
 */

/**
 * Custom Inflector rules can be set to correctly pluralize or singularize table, model, controller names or whatever other
 * string is passed to the inflection functions
 *
 * Inflector::rules('singular', array('rules' => array(), 'irregular' => array(), 'uninflected' => array()));
 * Inflector::rules('plural', array('rules' => array(), 'irregular' => array(), 'uninflected' => array()));
 *
 */
Inflector::rules('plural', array('irregular' => array(        
                                    'acteur_service' => 'acteurs_services',
                                    'aro_aco' => 'aros_acos',
                                    'typeseance_acteur' => 'typeseances_acteurs',
                                    'typeseance_typeacteur' => 'typeseances_typeacteurs',
                                    'user_circuit' => 'users_circuits',
                                    'user_service' => 'users_services',
                                    'infosupdef'=>'infosupdefs',
                                    'infosuplistedef'=>'infosuplistedefs' 
                                )));

/**
 * Plugins need to be loaded manually, you can either load them one by one or all of them in a single call
 * Uncomment one of the lines below, as you need. Make sure you read the documentation on CakePlugin to use more
 * advanced ways of loading plugins
 *
 * CakePlugin::loadAll(); // Loads all plugins at once
 * CakePlugin::load('DebugKit'); //Loads a single plugin named DebugKit
 *
 */
CakePlugin::loadAll(array(
		array('bootstrap' => true),
 		'DebugKit' => array('routes' => false, 'bootstrap' => false),
                'AclManager' => array('routes' => false, 'bootstrap' => true),
                'Cakeflow' => array('routes' => false, 'bootstrap' => true),
                'Appchecks' => array('routes' => false, 'bootstrap' => false),
                'AclExtras' => array('routes' => false, 'bootstrap' => false),
                'Bootstrap3' => array('routes' => false, 'bootstrap' => false)
 	));

/**
 * You can attach event listeners to the request lifecycle as Dispatcher Filter. By default CakePHP bundles two filters:
 *
 * - AssetDispatcher filter will serve your asset files (css, images, js, etc) from your themes and plugins
 * - CacheDispatcher filter will read the Cache.check configure variable and try to serve cached content generated from controllers
 *
 * Feel free to remove or add filters as you see fit for your application. A few examples:
 *
 * Configure::write('Dispatcher.filters', array(
 *		'MyCacheFilter', //  will use MyCacheFilter class from the Routing/Filter package in your app.
 *		'MyCacheFilter' => array('prefix' => 'my_cache_'), //  will use MyCacheFilter class from the Routing/Filter package in your app with settings array.
 *		'MyPlugin.MyFilter', // will use MyFilter class from the Routing/Filter package in MyPlugin plugin.
 *		array('callable' => $aFunction, 'on' => 'before', 'priority' => 9), // A valid PHP callback type to be called on beforeDispatch
 *		array('callable' => $anotherMethod, 'on' => 'after'), // A valid PHP callback type to be called on afterDispatch
 *
 * ));
 */
Configure::write('Dispatcher.filters', array(
	'AssetDispatcher',
	'CacheDispatcher'
));

/**
 * Configures default file logging options
 */
App::uses('CakeLog', 'Log');
CakeLog::config('debug', array(
	'engine' => 'File',
	'types' => array('notice', 'info', 'debug'),
	'file' => 'debug',
));
CakeLog::config('error', array(
	'engine' => 'File',
	'types' => array('warning', 'error', 'critical', 'alert', 'emergency'),
	'file' => 'error',
));

// Charger l'autoload de composer.
require APP . 'Vendor/autoload.php';

// Retire et réajoute l'autoloader de CakePHP puisque Composer pense que
// c'est le plus important.
// See http://goo.gl/kKVJO7
spl_autoload_unregister(array('App', 'load'));
spl_autoload_register(array('App', 'load'), true, true);

Configure::write('I18n.preferApp', true);

Configure::write('AclManager.ignorePlugins', array('AclExtras','AclManager','Gedooo','DebugKit','Cakeflow','Appchecks','AuthManager'));
Configure::write('AclManager.ignoreActions', array('isAuthorized'));
Configure::write('AclManager.ignoreControllers', array(
    'Annexes',
    'Commentaires',
    'Webdav',
    'Pages',
    'Infosups',
    'Historiques',
    'Nature',
    'ModelOdtValidator.Modeltemplates',
    )
);
Configure::write('AclManager.models', array('Profil', 'User','Typeacte'));
Configure::write('AclManager.aros', array('Profil', 'User'));

require_once('webdelib.inc');
require_once('formats.inc');
if (file_exists(APP.DS.'Config'.DS.'pastell.inc'))
    include_once('pastell.inc');

//appVersion
$versionFile = file(APP . DS . 'VERSION.txt');
define('VERSION', trim(array_pop($versionFile)));

//dbVersion
//include_once(ROOT . DS . APP_DIR . DS . 'Plugin' . DS . 'Cakeflow' . DS . 'Config' . DS . 'cakeflow.conf.php');

// TODO:
//Configure::load('webdelib');
//Configure::load('formats');
//
//if (file_exists(APP.DS.'Config'.DS.'pastell.inc'))
//    Configure::load('pastell');
//

//dbVersion
//include_once(ROOT . DS . APP_DIR . DS . 'Plugin' . DS . 'Cakeflow' . DS . 'Config' . DS . 'cakeflow.conf.php');


//FIXME Pour la date de séance
//setlocale(LC_ALL, 'fr_FR.utf8');