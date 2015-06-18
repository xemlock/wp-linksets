<?php

class wpPostAttachments_Proxy_User extends wpPostAttachments_Proxy_Abstract
{
    /**
     * @return WP_User|null
     */
    public function get_record()
    {
        // get_user_by() returns false when no user is found, here
        // we need to return NULL on failure
        return ($user = get_user_by('id', $this->_id)) ? $user : null;
    }
}