<?php

namespace wpLinksets\Link;

class Youtube extends BaseLink
{
    const TYPE = 'youtube';

    /**
     * Youtube Video ID
     * @var string
     */
    protected $_video_id;

    /**
     * @return string
     */
    public function get_type()
    {
        return static::TYPE;
    }

    /**
     * @param string $video_id
     */
    public function set_video_id($video_id)
    {
        $this->_video_id = (string) $video_id;
    }

    /**
     * @return string
     */
    public function get_video_id()
    {
        return $this->_video_id;
    }

    /**
     * @return string
     */
    public function get_url()
    {
        $video_id = $this->get_video_id();
        return sprintf('http://www.youtube.com/watch?v=%s', urlencode($video_id));
    }

    /**
     * @return string
     */
    public function get_thumb_url($size = null)
    {
        $thumb_url = parent::get_thumb_url($size);
        if ($thumb_url === false) {
            // sizes:
            // - default (120x90px)
            // - mqdefault (320x180px)
            // - hqdefault (480x360px)
            // - sddefault (640x480px)
            $video_id = $this->get_video_id();
            $thumb_url = sprintf('http://img.youtube.com/vi/%s/hqdefault.jpg', urlencode($video_id));
        }
        return $thumb_url;
    }

    /**
     * @return array
     */
    public function to_array()
    {
        $array = parent::to_array();
        $array['video_id'] = $this->get_video_id();
        return $array;
    }

    /**
     * @param array $data
     * @return Youtube
     */
    public static function from_array(array $data)
    {
        $link = new static();
        if (isset($data['video_id'])) {
            $link->set_video_id($data['video_id']);
        }
        return $link;
    }
}