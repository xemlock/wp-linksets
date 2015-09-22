<?php

namespace wpLinksets\Link;

/**
 * Class Post
 *
 * @property-read \WP_Post $post
 */
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
     * @return array
     */
    public function to_array()
    {
        $array = parent::to_array();
        $array['id'] = $this->_post->ID;
        return $array;
    }

    /**
     * {@inheritDoc}
     *
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

    /**
     * {@inheritDoc}
     *
     * @param mixed $size OPTIONAL
     * @return \wpLinksets\Thumb\BaseThumb|null
     */
    public function get_thumb($size = null)
    {
        // try to load thumbnail from explicitly set thumbnail ID
        if (($thumb = parent::get_thumb($size)) !== null) {
            return $thumb;
        }

        $post_thumbnail_id = get_post_thumbnail_id($this->_post->ID);
        if ($post_thumbnail_id) {
            try {
                return new \wpLinksets\Thumb\Thumb($post_thumbnail_id, $size);
            } catch (\Exception $e) {
            }
        }

        return null;
    }
}