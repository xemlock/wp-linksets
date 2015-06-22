<?php

defined('ABSPATH') || die();

if (file_exists($autoload = __DIR__ . '/vendor/autoload.php')) {
    require_once $autoload;
}

wpPostAttachments\Plugin::get_instance()->init();
