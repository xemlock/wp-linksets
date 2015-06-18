<?php

class wpPostAttachments
{
    protected $_post_types = array();

    /**
     * Register attachments to be available for given post type
     * @param string $post_type
     */
    public function register($post_type)
    {
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        add_action('save_post', array($this, 'save_post'));
        add_action('the_post', array($this, 'the_post'));
    }

    public function add_meta_boxes()
    {
        foreach ($this->_post_types as $post_type) {
            add_meta_box('links', 'Odnośniki', array($this, 'render_metabox'), $post_type, 'normal', 'default', null);
        }
    }

    public function save_post($post_id)
    {
        $post = get_post($post_id);

        if ($post && in_array($post->post_type, $this->_post_types) && isset($_REQUEST['post_attachments'])) {

            $linkset = array();
            foreach ((array) $_REQUEST['linkset'] as $link) {
                $link = (array) $link;
                $linkset[] = array(
                    'url'   => (string) @$link['url'],
                    'title' => (string) @$link['title'],
                    'thumb' => (int) @$link['thumb'],
                );
            }
            update_post_meta($post_id, '_linkset', self::serialize($linkset));
        }
    }

    public function the_post(WP_Post $post)
    {
        if (in_array($post->post_type, self::$post_types)) {
            $post->attachments = self::get_linkset($post);
        }
    }

    public function render_metabox(WP_Post $post)
    {
    }

    /**
     * @var wpPostAttachments
     */
    protected static $_instance;

    /**
     * Retrieves the globally accesible plugin instance.
     *
     * @return wpPostAttachments
     */
    public static function getInstance()
    {
        if (empty(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
}
