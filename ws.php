<?php

use Zend\Loader\StandardAutoloader;
use Zend\Json\Server\Server;


require_once dirname(__DIR__) . '/ZF2/library/Zend/Loader/StandardAutoloader.php';
$loader = new StandardAutoloader(array('autoregister_zf' => true));
$loader->register();

//fin - carga automatica de Zend Framework

//cargamos archivos de clases a utilizar
require_once 'Departamentos_fn.php';

$server = new Server();
$server->setClass('Departamentos');

if ('GET' == $_SERVER['REQUEST_METHOD']) {
    // Indicate the URL endpoint, and the JSON-RPC version used:
    $server->setTarget('/json-rpc.php')
           ->setEnvelope(Zend\Json\Server\Smd::ENV_JSONRPC_2);

    // Grab the SMD
    $smd = $server->getServiceMap();

    // Return the SMD to the client
    header('Content-Type: application/json');
    echo $smd;
    return;
}

$server->handle();
?>