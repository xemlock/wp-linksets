<?php

namespace wpLinksets\Link;

/**
 * Class File
 *
 * @property-read string $path
 */
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

    /**
     * Get file path
     *
     * @return string
     */
    public function get_path()
    {
        return get_attached_file($this->_post->ID);
    }

    /**
     * @return string
     */
    public function get_url()
    {
        return wp_get_attachment_url($this->_post->ID);
    }

    /**
     * {@inheritDoc}
     *
     * @param mixed $size OPTIONAL
     * @return \wpLinksets\Thumb\BaseThumb|null
     */
    public function get_thumb($size = null)
    {
        if (($thumb = parent::get_thumb($size)) !== null) {
            return $thumb;
        }

        // try to get thumbnail directly from the file - this will only
        // succeed if file is an image
        try {
            return new \wpLinksets\Thumb\Thumb($this->_post->ID, $size);
        } catch (\Exception $e) {
        }

        return null;
    }

    /**
     * {@inheritDoc}
     *
     * @return array
     */
    public function get_js_data()
    {
        $data = parent::get_js_data();
        $data['file'] = wp_prepare_attachment_for_js($this->_post);
        return $data;
    }
}