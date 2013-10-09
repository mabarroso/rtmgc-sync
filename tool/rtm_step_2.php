<?php
set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__ . '/php-rtm/src/');

function __autoload($nombre_clase) {
    include $nombre_clase . '.php';
}

if (!isset($argv[1]))  {
    echo "Hay que indicar el Frob generado en el paso 1\n";
    exit();
}

echo "Usando Frob: {$argv[1]}\n";

use Rtm\Rtm;

$rtm = new Rtm;
$rtm->setApiKey('4c2e8bb871b188bec50c8aae666f978f');
$rtm->setSecret('7559a26816a49809');
//$rtm->setAuthToken('c039c7db0732ef2bf5544146a32dcfc6ea9801d9');
$rtm->setFrob($argv[1]);

try
{
    // Check authentication token
    $response = $rtm->getService(Rtm::SERVICE_AUTH)->getToken();
    $rtm->setAuthToken($response->getToken());
    echo "Token: ". $response->getToken()."\n";
    echo "Ejecuta el paso 3\n";
    echo "  php rtm_step_3.php ". $response->getToken()."\n";

    $rtm->getService(Rtm::SERVICE_AUTH)->checkToken();
exit();
}
catch(Exception $e)
{
    echo $e->message."\n";
    echo "Ejecuta el paso 1\n";
    echo "  php rtm_step_1.php\n";
}
