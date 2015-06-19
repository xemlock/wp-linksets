<?php

class wpPostAttachments
{
    const REQUEST_KEY   = 'post_attachments';
    const POST_PROPERTY = 'post_attachments';
    const POST_META_KEY = '_post_attachments';

    protected $_post_types = array();

    protected $_type_classes = array();

    public function __construct()
    {
        // register type handlers
        $this->_type_classes['link'] = 'wpPostAttachments_Attachment_Link';
        $this->_type_classes['file'] = 'wpPostAttachments_Attachment_File';
        $this->_type_classes['audio'] = 'wpPostAttachments_Attachment_Audio';
        $this->_type_classes['youtube'] = 'wpPostAttachments_Attachment_Youtube';


        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        add_action('the_post', array($this, 'the_post'));

        // in order to save post its content must not be considered empty
        // make sure post is not empty if attachments are provided in the request
        add_filter('wp_insert_post_empty_content', array($this, '_is_post_content_empty'));

        // these are not called if base post fields are not changed
        add_action('save_post', array($this, 'save_post'));

        // attachments use different hooks
        add_action('edit_attachment', array($this, 'save_post'));
        add_action('add_attachment', array($this, 'save_post'));
    }

    /**
     * Enables post attachment functionality for the given post type(s)
     *
     * This method must be explicitly called in the theme.
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
        echo '<input type="hidden" name="custom_meta_box_nonce" value="', wp_create_nonce(basename(__FILE__)), '" />';
        echo '<input type="text" name="post_attachments[]" />';
    }

    /**
     * @param $post_id
     * @internal
     */
    public function save_post($post_id)
    {
        // Don't update metadata when auto saving post
        // http://wordpress.stackexchange.com/questions/14282/custom-post-type-metabox-not-saving
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return $post_id;
        }

        // if ( !wp_verify_nonce( $_POST['blc_noncename'], plugin_basename(__FILE__) )) {
        //  return $post_id;
        // }

        $post = get_post($post_id);

        if ($post && in_array($post->post_type, $this->_post_types) && isset($_REQUEST[self::REQUEST_KEY])) {
            $attachments = array();
            foreach ((array) $_REQUEST[self::REQUEST_KEY] as $data) {
                if (isset($data['type']) && ($attachment = $this->create_attachment($data['type']))) {
                    $attachments[] = $attachment;
                }
            }
            update_post_meta($post_id, self::POST_META_KEY, wp_json_encode(
                array_map(array($this, '_attachment_to_array'), $attachments)
            ));
            $post->{self::POST_PROPERTY} = $attachments;
        }

        // echo '<pre>', __METHOD__, "\n", print_r($post, 1);exit;
    }

    /**
     * @param bool $is_empty
     * @return bool
     * @internal
     */
    public function _is_post_content_empty($is_empty)
    {
        if (isset($_REQUEST[self::REQUEST_KEY])) {
            return false;
        }
        return $is_empty;
    }

    /**
     * @param wpPostAttachments_Attachment_Abstract $attachment
     * @return array
     * @internal
     */
    public function _attachment_to_array(wpPostAttachments_Attachment_Abstract $attachment)
    {
        return $attachment->to_array();
    }

    /**
     * @param WP_Post $post
     * @internal
     */
    public function the_post(WP_Post $post)
    {
        if (in_array($post->post_type, $this->_post_types)) {
            $post->{self::POST_PROPERTY} = $this->get_post_attachments($post->ID);
        }
    }

    /**
     * @param int|WP_Post $post_id
     * @return wpPostAttachments_Attachment[]
     */
    public function get_post_attachments($post_id)
    {
        if ($post_id instanceof WP_Post) {
            $post_id = $post_id->ID;
        }

        $meta = get_post_meta((int) $post_id, self::POST_META_KEY, true);
        $data = (array) json_decode($meta, true);

        $attachments = array();
        foreach ($data as $val) {
            if (isset($val['type']) && ($attachment = $this->create_attachment($val))) {
                $attachments[] = $attachment;
            }
        }
        return $attachments;
    }

    /**
     * @param $type
     * @return wpPostAttachments_Attachment|null
     */
    public function create_attachment($type, array $data = null)
    {
        if (isset($this->_type_classes[$type])) {
            $class = $this->_type_classes[$type];
            return new $class($data);
        }
        return null;
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
