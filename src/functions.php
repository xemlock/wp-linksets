<?php

function get_post_linkset($post_id = null)
{
    return \wpLinksets\Plugin::get_instance()->get_linkset($post_id);
}

