<?php

namespace wpLinksets;

/**
 * A proxy class that provides object oriented interface to attachment post
 *
 * @property-read string $url
 * @property-read string $path
 * @property-read int $size
 * @property-read string $mime_type
 */
class File
{
    /**
     * @var \WP_Post
     */
    protected $_post;

    /**
     * @param int|\WP_Post $post
     * @throws \InvalidArgumentException
     */
    public function __construct($post)
    {
        $post = get_post($post);
        if (!$post instanceof \WP_Post) {
            throw new \InvalidArgumentException('Invalid post ID');
        }
        /** @var \WP_Post $post */
        if ($post->post_type !== 'attachment') {
            throw new \InvalidArgumentException('Post is not an attachment');
        }
        $this->_post = $post;
    }

    /**
     * Get post instance
     *
     * @return \WP_Post
     */
    public function get_post()
    {
        return $this->_post;
    }

    /**
     * Get file path
     *
     * @return string
     */
    public function get_path()
    {
        return get_attached_file($this->_post->ID);
    }

    /**
     * Get file size
     *
     * @return int
     */
    public function get_size()
    {
        return filesize($this->get_path());
    }

    /**
     * Get file MIME type
     *
     * @return string
     */
    public function get_mime_type()
    {
        return $this->_post->post_mime_type;
    }

    /**
     * Get file URL
     *
     * @return string
     */
    public function get_url()
    {
        return wp_get_attachment_url($this->_post->ID);
    }

    /**
     * Prepare file data for JavaScript
     *
     * @return array
     */
    public function get_js_data()
    {
        return wp_prepare_attachment_for_js($this->_post);
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
     * Proxy to {@link get_path()}
     *
     * @return string
     */
    public function __toString()
    {
        return $this->get_path();
    }
}