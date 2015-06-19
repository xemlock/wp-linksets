<?php

namespace wpPostAttachments\Attachment;

abstract class Attachment
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
     * @param array $data
     */
    public function __construct(array $data = null)
    {
        if ($data) {
            $this->set_from_array($data);
        }
    }

    /**
     * @return string
     */
    abstract public function get_type();

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
        $date = is_int($date) || is_float($date) ? $date : strtotime($date);
        $this->_date = date('Y-m-d H:i:s', $date);
    }

    /**
     * @return string
     */
    public function get_date()
    {
        if ($this->_date === null) {
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
     * @param int|WP_Post $image
     */
    public function set_thumb_id($thumb_id)
    {
        if ($thumb_id instanceof \WP_Post) {
            $thumb_id = $thumb_id->ID;
        }
        $this->_thumb_id = (int) $thumb_id;
    }

    /**
     * @return int|null
     */
    public function get_thumb_id()
    {
        return $this->_thumb_id;
    }

    /**
     * The result depends on the value of the {@link get_thumb_id()} method
     *
     * @param  string|array $size
     * @return string|false
     */
    public function get_thumb_url($size = null)
    {
        $img = wp_get_attachment_image_src((int) $this->get_thumb_id(), $size);
        if ($img) {
            // [0 => url, 1 => width, 2 => height]
            return $img[0];
        }
        return false;
    }

    /**
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
     * @return array
     */
    public function to_array()
    {
        return array(
            'type'        => $this->get_type(),
            'title'       => $this->get_title(),
            'description' => $this->get_description(),
            'date'        => $this->get_date(),
            'author_id'   => $this->get_author_id(),
            'thumb_id'    => $this->get_image(),
        );
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
}