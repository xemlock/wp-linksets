<?php

error_reporting(E_ALL | E_STRICT);

// find WordPress install dir
$dir = dirname(__FILE__);
while ($parent = realpath($dir . '/..')) {
    if (file_exists($path = $parent . '/wp-load.php')) {
        $wp_load = $path;
        break;
    }
    $dir = $parent;
}
if (empty($wp_load)) {
    die('Unable to find WordPress loading script');
}
require_once $wp_load;

// find autoload.php moving upwards, so that tests can be executed
// even if the library itself lies in the vendor/ directory of another
// project

$dir = dirname(__FILE__);
$autoload = null;

while ($parent = realpath($dir . '/..')) {
    if (file_exists($path = $parent . '/vendor/autoload.php')) {
        $autoload = $path;
        break;
    }
    $dir = $parent;
}
if (empty($autoload)) {
    die('Unable to find autoload.php');
}

require_once $autoload;
