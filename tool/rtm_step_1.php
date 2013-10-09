<?php
set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__ . '/php-rtm/src/');

function __autoload($nombre_clase) {
    include $nombre_clase . '.php';
}

use Rtm\Rtm;

$rtm = new Rtm;
$rtm->setApiKey('4c2e8bb871b188bec50c8aae666f978f');
$rtm->setSecret('7559a26816a49809');
//$rtm->setAuthToken('c039c7db0732ef2bf5544146a32dcfc6ea9801d9');
//$rtm->setFrob('5cf8684f6c98149abf1ca676e852356ccd5a1e41');

try
{
    // Check authentication token
    $rtm->getService(Rtm::SERVICE_AUTH)->checkToken();
}
catch(Exception $e)
{
    try
    {
        // Check authentication token
        $response = $rtm->getService(Rtm::SERVICE_AUTH)->getToken();
        $rtm->setAuthToken($response->getToken());
        echo "Token: ".$response->getToken()."\n";
        $rtm->getService(Rtm::SERVICE_AUTH)->checkToken();
exit();
    }
    catch(Exception $e)
    {
        // No permissions, let's get them
        $rtm->getService(Rtm::SERVICE_AUTH)->getFrob();
        echo "Frob: ".($rtm->getFrob())."\n";
        echo "Auth: ".$rtm->getAuthUrl(Rtm::AUTH_TYPE_DELETE)."\n";
        echo "Ejecuta el paso 2 tras ir a la web\n";
        echo "  php rtm_step_2.php ".($rtm->getFrob())."\n";
        exit();
    }
}
