<?php
set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__ . '/../vendor/');
require_once 'google-api/src/Google_Client.php';
require_once 'google-api/src/contrib/Google_CalendarService.php';


$client = new Google_Client();
$client->setApplicationName("Sync remember The Milk to Google Calendar");

// Visit https://code.google.com/apis/console?api=calendar to generate your
// client id, client secret, and to register your redirect uri.
$client->setClientId('828609736789.apps.googleusercontent.com');
$client->setClientSecret('pR0IPy8fMwryBnf0vJk_7T_o');
$client->setRedirectUri('urn:ietf:wg:oauth:2.0:oob');

$cal = new Google_CalendarService($client);

if (!isset($argv[1]))  {
    echo "Hay que indicar el token generado en el paso 2\n";
    exit();
}

echo "Usando token: {$argv[1]}\n";

try
{
    $client->setAccessToken($argv[1]);
    echo "Token válido\n";
    exit();
}
catch(Exception $e)
{
    echo $e."\n";
    echo "No válido. Ejecuta el paso 1\n";
    echo "  php gc_step_1.php\n";
}
