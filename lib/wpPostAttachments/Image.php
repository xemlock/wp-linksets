<?php

class wpPostAttachments_Image
{
    /**
     * @var int
     */
    protected $_id;

    /**
     * @var string|false
     */
    protected $_url;

    /**
     * @param int $id
     */
    public function __construct($id)
    {
        $this->_id = (int) $id;
    }

    /**
     * @return int
     */
    public function get_id()
    {
        return $this->_id;
    }

    /**
     * @return string|false
     */
    public function get_url()
    {
        if ($this->_url === null) {
            $this->_url = wp_get_attachment_url($this->_id);
        }
        return $this->_url;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        if (method_exists($this, $method = 'get_' . $key)) {
            return $this->$method();
        }
    }

    /**
     * @param string $key
     * @return bool
     */
    public function __isset($key)
    {
        return $this->__get($key) !== null;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->get_url();
    }
}
