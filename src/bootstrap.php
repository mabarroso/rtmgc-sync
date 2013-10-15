<?php
/**
 * bootstrap.php
 * Bootstrapping application
 *
 * PHP version 5.3
 *
 * @category  RTMGC
 * @package   Sync
 * @author    mabarroso <mabarroso@mabarroso.com>
 * @copyright 2013 mabarroso.com
 * @license   Apache 2 License http://www.apache.org/licenses/LICENSE-2.0.html
 * @version   GIT: $Id$
 * @link      http://www.mabarroso.com
 * @since     File available since Release 0.1
 */

/**
 * [__autoload description]
 *
 * @param [type] $class_name [description]
 *
 * @return none
 */
function __autoload($class_name)
{
    include $class_name . '.php';
}

date_default_timezone_set('Europe/Madrid');

$php_env = getenv('PHP_ENV');

if (empty($php_env)) {
    $php_env = 'development';
    putenv("PHP_ENV={$php_env}");
}

// Checking composer autoload.
require_once __DIR__ . '/../vendor/autoload.php';

// Setting include path
set_include_path(
    '.' .
    PATH_SEPARATOR . __DIR__ . '/../vendor/' .
    PATH_SEPARATOR . get_include_path()
);

// Initial configuration
if (getenv('PHP_ENV') != 'production') {
    ini_set('display_errors', 'stderr');
    error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE); //error_reporting(E_ALL || ~E_STRICT);
} else {
    error_reporting(0);
}

require_once 'config.php';
