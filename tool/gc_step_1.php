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

$authUrl = $client->createAuthUrl();

echo "Auth: ".$authUrl."\n";
echo "Ejecuta el paso 2 tras ir a la web\n";
echo "  php gc_step_2.php CODIGO_DADO_POR_GOOGLE_EN_LA_URL_ANTERIOR\n";

