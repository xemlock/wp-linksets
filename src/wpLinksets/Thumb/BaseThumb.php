<?php

namespace wpLinksets\Thumb;

/**
 * @property-read string $url
 * @property-read int $width
 * @property-read int $height
 * @property-read string $orientation
 */
abstract class BaseThumb
{
    const ORIENTATION_LANDSCAPE = 'landscape';

    const ORIENTATION_PORTRAIT  = 'portrait';

    /**
     * Get thumbnail URL
     *
     * @return string
     */
    abstract public function get_url();

    /**
     * Get thumbnail width
     *
     * @return int
     */
    abstract public function get_width();

    /**
     * Get thumbnail height
     *
     * @return int
     */
    abstract public function get_height();

    /**
     * Get thumbnail orientation - landscape or portrait
     *
     * @return string
     */
    public function get_orientation()
    {
        if ($this->get_width() >= $this->get_height()) {
            return self::ORIENTATION_LANDSCAPE;
        }
        return self::ORIENTATION_PORTRAIT;
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
     * Proxy to {@link get_url()}
     *
     * @return string
     */
    public function __toString()
    {
        return $this->get_url();
    }
}