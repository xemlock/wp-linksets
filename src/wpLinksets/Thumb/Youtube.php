<?php

namespace wpLinksets\Thumb;

/**
 * @property-read string $youtube_id
 */
class Youtube extends BaseThumb
{
    /**
     * @var string
     */
    protected $_video_id;

    /**
     * @param string $video_id
     */
    public function __construct($video_id)
    {
        $this->_video_id = trim($video_id);
    }

    /**
     * Get YouTube Video ID of this thumbnail
     *
     * @return string
     */
    public function get_video_id()
    {
        return $this->_video_id;
    }

    /**
     * {@inheritDoc}
     *
     * @param string $size OPTIONAL
     * @return string
     */
    public function get_url($size = null)
    {
        // thumbnail sizes:
        // - default (120x90px)
        // - mqdefault (320x180px)
        // - hqdefault (480x360px)
        // - sddefault (640x480px)
        // - maxresdefault (1920x1080px)
        // The higher resolution thumbnails are not guaranteed to exist
        $video_id = $this->_video_id;
        $thumb_url = sprintf('https://img.youtube.com/vi/%s/hqdefault.jpg', urlencode($video_id));

        return $thumb_url;
    }

    /**
     * {@inheritDoc}
     *
     * @return int
     */
    public function get_width()
    {
        return 480;
    }

    /**
     * {@inheritDoc}
     *
     * @return int
     */
    public function get_height()
    {
        return 360;
    }
}