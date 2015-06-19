<?php

namespace wpPostAttachments\Attachment;

class File extends Base
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
}