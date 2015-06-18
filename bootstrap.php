<?php

defined('ABSPATH') || die();

if (file_exists($autoload = dirname(__FILE__) . '/vendor/autoload.php')) {
    require_once $autoload;
}

wpPostAttachments::get_instance();
