<?php

namespace wpLinksets\Thumb;

/**
 * Image attachment based thumbnail
 *
 * @property-read int $id
 */
class Thumb extends BaseThumb
{
    /**
     * @var int
     */
    protected $_id;

    /**
     * @var string
     */
    protected $_url;

    /**
     * @var int
     */
    protected $_width;

    /**
     * @var int
     */
    protected $_height;

    /**
     * @param int $id
     * @param string $size OPTIONAL
     * @throws \InvalidArgumentException
     */
    public function __construct($id, $size = null)
    {
        $img = wp_get_attachment_image_src($id, $size);

        if (empty($img)) {
            throw new \InvalidArgumentException('Invalid file ID');
        }

        $this->_id = (int) $id;
        $this->_url = $img[0];
        $this->_width = $img[1];
        $this->_height = $img[2];
    }

    /**
     * Get thumbnail URL
     *
     * @return string
     */
    public function get_url()
    {
        return $this->_url;
    }

    /**
     * Get thumbnail width
     *
     * @return int
     */
    public function get_width()
    {
        return $this->_width;
    }

    /**
     * Get thumbnail height
     *
     * @return int
     */
    public function get_height()
    {
        return $this->_height;
    }
}