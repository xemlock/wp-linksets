<?php

namespace wpLinksets\Link;

/**
 * Class Youtube
 *
 * @property string $video_id
 * @property-read string $embed_url
 */
class Youtube extends BaseLink
{
    const TYPE = 'youtube';

    const VIDEO_ID_REGEX = '[a-zA-Z0-9_-]{11}';

    /**
     * Youtube Video ID
     * @var string
     */
    protected $_video_id;

    /**
     * @param string $video_id OPTIONAL
     */
    public function __construct($video_id = null)
    {
        if ($video_id !== null) {
            $this->set_video_id($video_id);
        }
    }

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
        $this->_video_id = self::extract_video_id($video_id);
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
        return sprintf('https://www.youtube.com/watch?v=%s', urlencode($this->_video_id));
    }

    public function get_embed_url()
    {
        return sprintf('https://www.youtube.com/embed/%s', urlencode($this->_video_id));
    }

    public function get_thumb($size = null)
    {
        if (($thumb = parent::get_thumb($size)) !== null) {
            return $thumb;
        }

        if ($this->_video_id) {
            return new \wpLinksets\Thumb\Youtube($this->_video_id);
        }

        return null;
    }

    /**
     * @return array
     */
    public function to_array()
    {
        $array = parent::to_array();
        $array['video_id'] = $this->_video_id;
        return $array;
    }

    /**
     * @param array $data
     * @return Youtube
     */
    public static function from_array(array $data)
    {
        /** @var Youtube $link */
        $link = new static();
        $link->set_from_array($data);
        return $link;
    }

    /**
     * @param string $video_id
     * @return string
     */
    public static function extract_video_id($video_id)
    {
        $video_id = trim($video_id);

        if (!preg_match('/^' . self::VIDEO_ID_REGEX . '$/', $video_id)) {
            // Try to extract Video ID from the assumed URL
            // www.youtube.com/watch?v=VIDEO_ID
            // www.youtube.com/v/VIDEO_ID
            // www.youtube.com/embed/VIDEO_ID
            // youtu.be/VIDEO_ID

            $patterns = array(
                '/[?&]v=(?P<id>' . self::VIDEO_ID_REGEX . ')/',         // regular
                '/\/(v|embed)\/(?P<id>' . self::VIDEO_ID_REGEX . ')/',  // embed
                '/youtu\.be\/(?P<id>' . self::VIDEO_ID_REGEX . ')/',    // shortened
            );

            foreach ($patterns as $pattern) {
                if (preg_match($pattern, $video_id, $match)) {
                    $video_id = $match['id'];
                    break;
                }
            }
        }

        return $video_id;
    }
}