<?php

$lock = '../app.lock';

if (file_exists($lock)) {
    header('HTTP/1.0 403 Forbidden');
    exit('Application not avaliable.');
}

use Symfony\Component\ClassLoader\ApcClassLoader;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Debug\Debug;

if (isset($_SERVER['HTTP_CLIENT_IP'])
    || isset($_SERVER['HTTP_X_FORWARDED_FOR'])
    || !(in_array(@$_SERVER['REMOTE_ADDR'], array('127.0.0.1', 'fe80::1', '::1')) || php_sapi_name() === 'cli-server')
) {
    header('HTTP/1.0 403 Forbidden');
    exit('You are not allowed to access this file. Check '.basename(__FILE__).' for more information.');
}

$requireAppAutoload = __DIR__.'/../app/autoload.php';
$requireAppKernel = __DIR__.'/../app/AppKernel.php';
$requireAppCache = __DIR__.'/../app/bootstrap.php.cache';

if (isset($_SERVER['HTTP_ENVIRONMENT_MOD'])) {
    switch ($_SERVER['HTTP_ENVIRONMENT_MOD']) {
        case 'dev':
            $loader = require_once $requireAppAutoload;
            Debug::enable();
            require_once $requireAppKernel;
            $kernel = new AppKernel('dev', true);
        break;
        case 'prod':
            $loader = require_once $requireAppCache;
            $apcLoader = new ApcClassLoader(sha1(__FILE__), $loader);
            $loader->unregister();
            $apcLoader->register(true);
            require_once $requireAppKernel;
            $kernel = new AppKernel('prod', true);
            $kernel->loadClassCache();
        break;
        case 'pre_prod':
            $loader = require_once $requireAppCache;
            $apcLoader = new ApcClassLoader(sha1(__FILE__), $loader);
            $loader->unregister();
            $apcLoader->register(true);
            require_once $requireAppKernel;
            $kernel = new AppKernel('pre_prod', false);
            $kernel->loadClassCache();
        break;
        case 'debug':
            $loader = require_once $requireAppAutoload;
            Debug::enable();
            require_once $requireAppKernel;
            $kernel = new AppKernel('debug', false);
        break; 
    }
} else {
    $loader = require_once $requireAppCache;
    $apcLoader = new ApcClassLoader(sha1(__FILE__), $loader);
    $loader->unregister();
    $apcLoader->register(true);
    require_once $requireAppKernel;
    $kernel = new AppKernel('prod', true);
    $kernel->loadClassCache();
} 

$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
