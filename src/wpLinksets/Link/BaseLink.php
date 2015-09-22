<?php

namespace wpLinksets\Link;

/**
 * Class BaseLink
 *
 * @property string $type
 * @property string $url
 * @property string $title
 * @property string $description
 * @property string $date
 * @property int $author_id
 * @property int $thumb_id
 * @property-read \wpLinksets\Thumb\BaseThumb|null $thumb
 */
abstract class BaseLink
{
    /**
     * @var string
     */
    protected $_title;

    /**
     * @var string
     */
    protected $_description;

    /**
     * @var string
     */
    protected $_date;

    /**
     * @var int
     */
    protected $_author_id;

    /**
     * @var int
     */
    protected $_thumb_id;

    /**
     * @return string
     */
    abstract public function get_type();

    /**
     * @return string
     */
    abstract public function get_url();

    /**
     * @param string $title
     */
    public function set_title($title)
    {
        $this->_title = (string) $title;
    }

    /**
     * @return string
     */
    public function get_title()
    {
        return (string) $this->_title;
    }

    /**
     * @param string $description
     */
    public function set_description($description)
    {
        $this->_description = (string) $description;
    }

    /**
     * @return string
     */
    public function get_description()
    {
        return (string) $this->_description;
    }

    /**
     * @param string|int $date
     */
    public function set_date($date)
    {
        $time = is_int($date) || is_float($date) || ctype_digit($date) ? $date : strtotime($date);
        $time = $time === false ? time() : $time;
        $this->_date = date('Y-m-d H:i:s', $time);
    }

    /**
     * @return string
     */
    public function get_date()
    {
        if (trim($this->_date) === '') {
            $this->set_date(time());
        }
        return $this->_date;
    }

    /**
     * @param int|\WP_User $user
     */
    public function set_author_id($author_id)
    {
        if ($author_id instanceof \WP_User) {
            $author_id = $author_id->ID;
        }
        $this->_author_id = (int) $author_id;
    }

    /**
     * @return int
     */
    public function get_author_id()
    {
        if ($this->_author_id === null) {
            $this->set_author_id(get_current_user_id());
        }
        return $this->_author_id;
    }

    /**
     * Set thumbnail id. This effectively clears currently present
     * thumbnail instance.
     *
     * @param int|\WP_Post $image
     */
    public function set_thumb_id($thumb_id)
    {
        if ($thumb_id instanceof \WP_Post) {
            $thumb_id = $thumb_id->ID;
        }
        $this->_thumb_id = (int) $thumb_id;
        $this->_thumb = null;
    }

    /**
     * @return int|null
     */
    public function get_thumb_id()
    {
        return $this->_thumb_id;
    }

    /**
     * Get a thumbnail for this link
     *
     * @param mixed $size OPTIONAL
     * @return \wpLinksets\Thumb\BaseThumb|null
     */
    public function get_thumb($size = null)
    {
        // if thumbnail ID has been explicitly set, try to load it
        if ($this->_thumb_id) {
            try {
                return new \wpLinksets\Thumb\Thumb($this->_thumb_id, $size);
            } catch (\Exception $e) {
            }
        }
        return null;
    }

    /**
     * The result depends on the value of the {@link get_thumb_id()} method
     *
     * @param  string|array $size
     * @return string|false
     */
    public function get_thumb_url($size = null)
    {
        return ($thumb = $this->get_thumb($size)) ? $thumb->get_url() : false;
    }

    /**
     * Set link properties from an array
     *
     * @param array $data
     */
    public function set_from_array(array $data)
    {
        foreach ($data as $key => $value) {
            if (method_exists($this, $method = 'set_' . $key)) {
                $this->$method($value);
            }
        }
    }

    /**
     * Get data to be used when serializing this object in the database
     *
     * @return array
     */
    public function to_array()
    {
        return array(
            'type'        => $this->get_type(),
            'url'         => $this->get_url(),
            'title'       => $this->get_title(),
            'description' => $this->get_description(),
            'date'        => $this->get_date(),
            'author_id'   => $this->get_author_id(),
            'thumb_id'    => $this->get_thumb_id(),
        );
    }

    /**
     * Returns an array of data for this object to be used in the JavaScript
     *
     * @return array
     */
    public function get_js_data()
    {
        $data = $this->to_array();
        $data['thumb_url'] = $this->get_thumb_url('thumbnail');
        return $data;
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
     * @param mixed $value
     */
    public function __set($key, $value)
    {
        if (method_exists($this, $method = 'set_' . $key)) {
            $this->$method($value);
            return;
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
     * Create a link instance using data from an array
     *
     * This is a factory method and must be implemented in the subclasses.
     *
     * @param array $data
     * @return BaseLink
     */
    public static function from_array(array $data)
    {
        throw new \BadMethodCallException('This method must be implemented by subclasses');
    }
}