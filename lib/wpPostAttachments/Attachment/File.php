<?php

namespace wpPostAttachments\Attachment;

class File extends Attachment
{
    protected $_file_id;

    public function get_type()
    {
        return 'file';
    }

    public function set_file_id($file_id)
    {
        if ($file_id instanceof \WP_Post) {
            $file_id = $file_id->ID;
        }
        $this->_file_id = (int) $file_id;
    }

    public function get_file_id()
    {
        return $this->_file_id;
    }

    public function to_array()
    {
        $array = parent::to_array();
        $array['file_id'] = $this->get_file_id();
        return $array;
    }

    public function get_thumb_url($size = null)
    {
        if (($thumb_url = parent::get_thumb_url($size)) !== false) {
            return $thumb_url;
        }
        // try to get thumbnail directly from the referenced file
        $img = wp_get_attachment_image_src((int) $this->get_file_id(), $size);
        if ($img) {
            // [0 => url, 1 => width, 2 => height]
            return $img[0];
        }
        return false;
    }
}