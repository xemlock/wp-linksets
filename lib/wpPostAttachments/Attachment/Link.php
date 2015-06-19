<?php

namespace wpPostAttachments\Attachment;

class Link extends Attachment
{
    protected $_url;

    public function get_type()
    {
        return 'link';
    }

    public function set_url($url)
    {
        $this->_url = $url;
    }

    public function get_url()
    {
        return $this->_url;
    }

    public function to_array()
    {
        $array = parent::to_array();
        $array['url'] = $this->get_url();
        return $array;
    }
}