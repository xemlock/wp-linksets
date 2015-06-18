<?php

abstract class wpPostAttachments_Proxy_Abstract
{
    /**
     * @var int
     */
    protected $_id;

    /**
     * Constructor
     *
     * @param int $id
     */
    public function __construct($id)
    {
        $this->_id = (int) $id;
    }

    /**
     * Retrieves record's ID this proxy is bound to
     *
     * @return int
     */
    public function get_id()
    {
        return $this->_id;
    }

    /**
     * Retrieves record this proxy is bound to
     *
     * @return object|null
     */
    abstract public function get_record();

    /**
     * Tests if this proxy references an existing record
     *
     * @return bool
     */
    public function is_valid()
    {
        return $this->get_record() !== null;
    }

    /**
     * Retrieves property from the record this proxy is bound to
     *
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        if (method_exists($this, $method = 'get_' . $key)) {
            return $this->$method();
        }
        return ($record = $this->get_record()) && isset($record->$key) ? $record->$key : null;
    }

    /**
     * Tests for existence of a property
     *
     * @param string $key
     * @return bool
     */
    public function __isset($key)
    {
        return $this->__get($key) !== null;
    }
}