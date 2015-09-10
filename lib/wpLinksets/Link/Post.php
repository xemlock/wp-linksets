<?php

namespace wpLinksets\Link;

class Post extends BaseLink
{
    const TYPE = 'post';

    /**
     * @var \WP_Post
     */
    protected $_post;

    /**
     * @param int $id
     * @throws \InvalidArgumentException
     */
    public function __construct($id)
    {
        if (is_int($id) || ctype_digit($id)) {
            /** @var \WP_Post $post */
            $post = get_post($id);
        }
        if (empty($post)) {
            throw new \InvalidArgumentException(sprintf('Invalid post ID (%s)', $id));
        }
        $this->_post = $post;
    }

    /**
     * @return string
     */
    public function get_type()
    {
        return static::TYPE;
    }

    /**
     * @return string
     */
    public function get_url()
    {
        return get_permalink($this->_post);
    }

    /**
     * @return \WP_Post
     */
    public function get_post()
    {
        return $this->_post;
    }

    /**
     * @return int
     */
    public function get_post_id()
    {
        return (int) $this->get_post()->ID;
    }

    /**
     * @return array
     */
    public function to_array()
    {
        $array = parent::to_array();
        $array['id'] = $this->get_post_id();
        return $array;
    }

    /**
     * @param array $data
     * @return Post
     */
    public static function from_array(array $data)
    {
        $id = isset($data['id']) ? $data['id'] : null;
        $link = new static($id);
        return $link;
    }
}