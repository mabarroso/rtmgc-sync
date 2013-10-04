<?php
set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__ . '/php-rtm/src/');

function __autoload($nombre_clase) {
    include $nombre_clase . '.php';
}

if (!isset($argv[1]))  {
    echo "Hay que indicar el token generado en el paso 2\n";
    exit();
}

echo "Usando token: {$argv[1]}\n";

use Rtm\Rtm;

$rtm = new Rtm;
$rtm->setApiKey('4c2e8bb871b188bec50c8aae666f978f');
$rtm->setSecret('7559a26816a49809');
$rtm->setAuthToken($argv[1]);
//$rtm->setFrob('5cf8684f6c98149abf1ca676e852356ccd5a1e41');

try
{
    // Check authentication token
    $rtm->getService(Rtm::SERVICE_AUTH)->checkToken();
    print "El token es correcto\n";
}
catch(Exception $e)
{
    echo $e->message."\n";
    echo "Ejecuta el paso 2 con el token generado por el paso 1\n";
}
