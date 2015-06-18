<?php

class wpPostAttachments_Proxy_Image extends wpPostAttachments_Proxy_Abstract
{
    /**
     * @var string|false
     */
    protected $_url;

    /**
     * Retrieves post object this proxy is bound to
     *
     * @return WP_Post|null
     */
    public function get_record()
    {
        return get_post($this->_id);
    }

    /**
     * @return string|false
     */
    public function get_url()
    {
        if ($this->_url === null) {
            $this->_url = wp_get_attachment_url($this->_id);
        }
        return $this->_url;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->get_url();
    }
}
