<?php

/**
 * Plugin Name: WP Post Attachments
 * Plugin URI: http://github.com/xemlock/wp-post-attachments
 * Description:
 * Author: xemlock <xemlock@gmail.com>
 * Author URI: http://xemlock.pl
 * Version: 0.1.0
 * License: GPLv2 or later
 */

defined('ABSPATH') || die();

if (file_exists($autoload = __DIR__ . '/vendor/autoload.php')) {
    require_once $autoload;
}

wpPostAttachments\Plugin::get_instance()->init();
