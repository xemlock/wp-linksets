<?php

class wpPostAttachments_Image
{
    protected $_data;

    public function __get($key)
    {
        return isset($this->_data[$key]) ? $this->_data[$key] : null;
    }
}
