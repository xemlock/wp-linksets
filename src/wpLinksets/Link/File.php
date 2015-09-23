<?php

namespace wpLinksets\Link;

/**
 * Class File
 *
 * @property-read \wpLinksets\File $file
 */
class File extends Post
{
    const TYPE = 'file';

    /**
     * @var \wpLinksets\File
     */
    protected $_file;

    /**
     * @param int $id
     */
    public function __construct($id)
    {
        $this->_file = new \wpLinksets\File($id);
        parent::__construct($this->_file->get_post());
    }

    /**
     * @return \wpLinksets\File
     */
    public function get_file()
    {
        return $this->_file;
    }

    /**
     * @return string
     */
    public function get_url()
    {
        return $this->_file->get_url();
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
        $data['file'] = $this->_file->get_js_data();
        return $data;
    }
}