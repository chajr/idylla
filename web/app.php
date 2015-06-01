<?php
use Symfony\Component\ClassLoader\ApcClassLoader;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Debug\Debug;

if (file_exists(__DIR__ . '/../app.lock')) {
    header('HTTP/1.0 403 Forbidden');
    exit('Application not available.');
}

if (isset($_SERVER['HTTP_CLIENT_IP'])
    || isset($_SERVER['HTTP_X_FORWARDED_FOR'])
    || !(
        in_array(@$_SERVER['REMOTE_ADDR'], ['127.0.0.1', 'fe80::1', '::1'])
        || php_sapi_name() === 'cli-server'
    )
) {
    header('HTTP/1.0 403 Forbidden');
    exit('You are not allowed to access this file. Check ' . basename(__FILE__) . ' for more information.');
}

$requireAppAutoload = __DIR__ . '/../app/autoload.php';
$requireAppKernel   = __DIR__ . '/../app/AppKernel.php';
$requireAppCache    = __DIR__ . '/../app/bootstrap.php.cache';
$environments       = require_once __DIR__ . '/../app/config/environments.php';
$environment        = 'prod';

define('LOCATOR',   __DIR__ . '/..'); 
define('VAR',   LOCATOR.'/var'); 
define('CONFIGURE', LOCATOR.'/web'); 

if (isset($_SERVER['HTTP_ENVIRONMENT_MOD'])) {
    $environment = $_SERVER['HTTP_ENVIRONMENT_MOD'];
}

switch ($environment) {
    case $environments['dev']:
    case $environments['debug']:
        require_once $requireAppAutoload;
        Debug::enable();
        require_once $requireAppKernel;
        $kernel = new AppKernel($environment, true);
        break;

    case $environments['pre_prod']:
    case $environments['prod']:
    default:
        $loader     = require_once $requireAppCache;
        $apcLoader  = new ApcClassLoader(sha1(__FILE__), $loader);
        $loader->unregister();
        $apcLoader->register(true);
        $kernel = new AppKernel($environment, false);
        $kernel->loadClassCache();
        break;
}

$request    = Request::createFromGlobals();
$response   = $kernel->handle($request);

$response->send();
$kernel->terminate($request, $response);
