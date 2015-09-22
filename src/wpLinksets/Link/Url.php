<?php

namespace wpLinksets\Link;

/**
 * Class Url
 *
 * @property string $url
 */
class Url extends BaseLink
{
    const TYPE = 'url';

    protected $_url;

    public function get_type()
    {
        return static::TYPE;
    }

    public function set_url($url)
    {
        $url = trim($url);
        if (strlen($url) && !preg_match('#(https?|ftp)://#', $url)) {
            $url = 'http://' . $url;
        }
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
     * @return Url
     */
    public static function from_array(array $data)
    {
        /** @var Url $link */
        $link = new static();
        $link->set_from_array($data);
        return $link;
    }
}