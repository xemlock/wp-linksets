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
     * @param null $size
     * @return string|false
     */
    public function get_thumb_url($size = null)
    {
        // try to retrieve thumbnail for this link, if that fails use
        // post's thumbnail image
        if (($thumb_url = parent::get_thumb_url($size)) !== false) {
            return $thumb_url;
        }
        $post_thumbnail_id = get_post_thumbnail_id($this->_post->ID);
        $img = wp_get_attachment_image_src($post_thumbnail_id, $size);
        if ($img) {
            // [0 => url, 1 => width, 2 => height]
            return $img[0];
        }
        return false;
    }

    /**
     * @return \WP_Post
     */
    public function get_post()
    {
        return $this->_post;
    }

    /**
     * @return array
     */
    public function to_array()
    {
        $array = parent::to_array();
        $array['id'] = $this->_post->ID;
        return $array;
    }

    /**
     * @param array $data
     * @return Post
     */
    public static function from_array(array $data)
    {
        $id = isset($data['id']) ? $data['id'] : null;
        /** @var Post $link */
        $link = new static($id);
        $link->set_from_array($data);
        return $link;
    }
}