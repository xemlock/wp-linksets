<?php

class wpPostAttachments
{
    const POST_META_KEY = '_post_attachments';
    const REQUEST_KEY = 'post_attachments';

    protected $_post_types = array();

    public function __construct()
    {
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        add_action('save_post', array($this, 'save_post'));
        add_action('the_post', array($this, 'the_post'));
    }

    /**
     * Enables post attachment functionality for the given post type(s)
     *
     * @param string|array $post_type
     */
    public function enable($post_type)
    {
        $post_types = (array) $post_type;
        foreach ($post_types as $post_type) {
            $this->_post_types[] = (string) $post_type;
        }
    }

    /**
     * @internal
     */
    public function add_meta_boxes()
    {
        foreach ($this->_post_types as $post_type) {
            add_meta_box('post_attachments', 'Post attachments', array($this, 'render_metabox'), $post_type, 'normal', 'default', null);
        }
    }

    public function render_metabox(WP_Post $post)
    {
        echo 123;
    }

    /**
     * @param $post_id
     * @internal
     */
    public function save_post($post_id)
    {
        $post = get_post($post_id);

        if ($post && in_array($post->post_type, $this->_post_types) && isset($_REQUEST[self::REQUEST_KEY])) {

            $attachments = array();
            foreach ((array) $_REQUEST[self::REQUEST_KEY] as $data) {
                if (isset($data['type']) && ($attachment = $this->create_attachment($data['type']))) {
                    $attachments[] = $attachment->to_array();
                }
            }
            update_post_meta($post_id, self::POST_META_KEY, wp_json_encode($attachments));
        }
    }

    /**
     * @param WP_Post $post
     * @internal
     */
    public function the_post(WP_Post $post)
    {
        if (in_array($post->post_type, $this->_post_types)) {
            $post->post_attachments = $this->get_post_attachments($post->ID);
        }
    }

    /**
     * @param int $post_id
     * @return array
     */
    public function get_post_attachments($post_id)
    {
        $meta = get_post_meta((int) $post_id, self::POST_META_KEY, true);
    }

    /**
     * @param $type
     * @return wpPostAttachments_Attachment|null
     */
    public function create_attachment($type, array $data = null)
    {
        switch ($type) {
            case 'link':
                break;

            case 'file':
                break;

            case 'audio':
                break;

            case 'youtube':
                break;

            default:
                return null;
        }

        $data = array(
            'url'         => (string) @$data['url'],
            'author'      => (string) @$data['author'],
            'date'        => (int) strtotime(@$data['date']),
            'title'       => (string) @$data['title'],
            'description' => (string) @$data['description'],
            'thumbnail'   => (int) @$data['thumbnail'],
        );
    }

    /**
     * @var wpPostAttachments
     */
    protected static $_instance;

    /**
     * Retrieves the globally accessible plugin instance.
     *
     * @return wpPostAttachments
     */
    public static function get_instance()
    {
        if (empty(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
}
