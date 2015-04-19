<?php

$lock = 'app_lock.php';

if(file_exists($lock)){
 exit('Aplikacja niedostępna.');
}

use Symfony\Component\ClassLoader\ApcClassLoader;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Debug\Debug;

// Enable APC for autoloading to improve performance.
// You should change the ApcClassLoader first argument to a unique prefix
// in order to prevent cache key conflicts with other applications
// also using APC.
/*
$apcLoader = new ApcClassLoader(sha1(__FILE__), $loader);
$loader->unregister();
$apcLoader->register(true);
*/
// This check prevents access to debug front controllers that are deployed by accident to production servers.
// Feel free to remove this, extend it, or make something more sophisticated.
if (isset($_SERVER['HTTP_CLIENT_IP'])
    || isset($_SERVER['HTTP_X_FORWARDED_FOR'])
    || !(in_array(@$_SERVER['REMOTE_ADDR'], array('127.0.0.1', 'fe80::1', '::1')) || php_sapi_name() === 'cli-server')
) {
    header('HTTP/1.0 403 Forbidden');
    exit('You are not allowed to access this file. Check '.basename(__FILE__).' for more information.');
}
if(isset($_SERVER['HTTP_ENVIRONMENT_MOD'])){
 switch($_SERVER['HTTP_ENVIRONMENT_MOD']){
  case 'dev':
   $loader = require_once __DIR__.'/../app/autoload.php';
   Debug::enable();
   require_once __DIR__.'/../app/AppKernel.php';
   $kernel = new AppKernel('dev', true);
  break;
  case 'prod':
   $loader = require_once __DIR__.'/../app/bootstrap.php.cache';
   require_once __DIR__.'/../app/AppKernel.php';
   $kernel = new AppKernel('prod', true);
   $kernel->loadClassCache();
  break;
  case 'pre_prod':
   $loader = require_once __DIR__.'/../app/bootstrap.php.cache';
   require_once __DIR__.'/../app/AppKernel.php';
   $kernel = new AppKernel('pre_prod', false);
   $kernel->loadClassCache();
  break;
  case 'debug':
   $loader = require_once __DIR__.'/../app/autoload.php';
   Debug::enable();
   require_once __DIR__.'/../app/AppKernel.php';
   $kernel = new AppKernel('debug', false);
  break; 
 }
}else{
 $loader = require_once __DIR__.'/../app/bootstrap.php.cache';
 require_once __DIR__.'/../app/AppKernel.php';
 $kernel = new AppKernel('prod', true);
 $kernel->loadClassCache();
} 

// When using the HttpCache, you need to call the method in your front controller instead of relying on the configuration parameter
//Request::enableHttpMethodParameterOverride();
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
