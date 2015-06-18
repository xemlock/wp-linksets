<?php

abstract class wpPostAttachments_Attachment
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
     * @var DateTime
     */
    protected $_date;

    /**
     * @var wpPostAttachments_Proxy_User
     */
    protected $_author;

    /**
     * @var wpPostAttachments_Proxy_Image
     */
    protected $_image;

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
     * @param int|string|DateTime $date
     */
    public function set_date($date)
    {
        if (!$date instanceof DateTime) {
            if (is_int($date) || is_float($date) || ctype_digit($date)) {
                $date = '@' . $date;
            }
            $date = new DateTime($date);
        }
        $this->_date = $date;
    }

    /**
     * @return DateTime
     */
    public function get_date()
    {
        if (empty($this->_date)) {
            $this->_date = new DateTime();
        }
        return $this->_date;
    }

    /**
     * @param int|wpPostAttachments_Proxy_User $user
     */
    public function set_author($author)
    {
        if (!$author instanceof wpPostAttachments_Proxy_User) {
            $author = new wpPostAttachments_Proxy_User((int) $author);
        }
        $this->_author = $author;
    }

    /**
     * @return wpPostAttachments_Proxy_User
     */
    public function get_author()
    {
        if (empty($this->_author)) {
            $this->set_author(get_current_user_id());
        }
        return $this->_author;
    }

    /**
     * @param int|wpPostAttachments_Proxy_Image $image
     */
    public function set_image($image)
    {
        if (!$image instanceof wpPostAttachments_Proxy_Image) {
            $image = new wpPostAttachments_Proxy_Image((int) $image);
        }
        $this->_image = $image;
    }

    /**
     * @return wpPostAttachments_Proxy_Image|null
     */
    public function get_image()
    {
        return $this->_image;
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
