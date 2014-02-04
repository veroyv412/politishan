<?php
// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../../application'));

defined('APPLICATION_ROOT')
    || define('APPLICATION_ROOT', realpath(dirname(__FILE__) . '/../..'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'development'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));

/** Zend_Application */
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);


/*
 * CALLING from request
// bootstrap and retrive the frontController resource
$front = $application->getBootstrap()
      ->bootstrap('frontController')
      ->getResource('frontController');

$request = new Zend_Controller_Request_Simple ('update-kloutscore', 'process', 'default');      

$loader = Zend_Loader_Autoloader::getInstance();
// we need this custom namespace to load our custom class
$loader->registerNamespace('Custom_');

// set front controller options to make everything operational from CLI
$front->setRequest($request)
   ->setResponse(new Zend_Controller_Response_Cli())
   ->setRouter(new Custom_Controller_Router_Cli())
   ->throwExceptions(true);

// lets bootstrap our application and enjoy!
$application->bootstrap()
   ->run();
   */

/**
 * CALLING as an Static Class
 */
$application->bootstrap();
