<?php

if (!defined('WP_LINKSETS_FUNCTIONS')) {
    define('WP_LINKSETS_FUNCTIONS', __FILE__);

/**
 * Get linkset for the given post
 *
 * @param int|WP_Post $post
 * @return \wpLinksets\Linkset
 */
function get_post_linkset($post = null)
{
    return \wpLinksets\Plugin::get_instance()->get_linkset($post);
}

} // WP_LINKSETS_FUNCTIONS
