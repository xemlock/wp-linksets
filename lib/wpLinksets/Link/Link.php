<?php

namespace wpLinksets\Link;

class Link extends BaseLink
{
    const TYPE = 'link';

    protected $_url;

    public function get_type()
    {
        return static::TYPE;
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

    /**
     * @param array $data
     * @return Link
     */
    public static function from_array(array $data)
    {
        $link = new static();
        if (isset($data['url'])) {
            $link->set_url($data['url']);
        }
        return $link;
    }
}