<?php

namespace wpLinksets\Link;

class File extends Post
{
    const TYPE = 'file';

    /**
     * @param int $id
     */
    public function __construct($id)
    {
        parent::__construct($id);
        if ($this->_post->post_type !== 'attachment') {
            throw new \InvalidArgumentException(sprintf('Invalid file ID (%d)', $this->_post->ID));
        }
    }

    public function get_url()
    {
        return wp_get_attachment_url($this->_post->ID);
    }

    /**
     * @param null $size
     * @return string|false
     */
    public function get_thumb_url($size = null)
    {
        if (($thumb_url = parent::get_thumb_url($size)) !== false) {
            return $thumb_url;
        }
        // try to get thumbnail directly from the referenced file
        $img = wp_get_attachment_image_src($this->_post->ID, $size);
        if ($img) {
            // [0 => url, 1 => width, 2 => height]
            return $img[0];
        }
        return false;
    }
}