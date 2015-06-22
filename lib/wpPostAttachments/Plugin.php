<?php

namespace wpPostAttachments;

class Plugin
{
    const REQUEST_KEY   = 'post_attachments';
    const POST_PROPERTY = 'post_attachments';
    const POST_META_KEY = '_post_attachments';

    /**
     * @var array
     */
    protected $_type_classes = array(
        'link'    => '\\wpPostAttachments\\Attachment\\Link',
        'file'    => '\\wpPostAttachments\\Attachment\\File',
        'audio'   => '\\wpPostAttachments\\Attachment\\Audio',
        'youtube' => '\\wpPostAttachments\\Attachment\\Youtube',
    );

    /**
     * @var array
     */
    protected $_post_types = array();

    public function init()
    {
        static $initialized = false;

        if (!$initialized) {
            add_action('add_meta_boxes', array($this, 'add_meta_boxes'));

            // in order to save post its content must not be considered empty
            // make sure post is not empty if attachments are provided in the request
            add_filter('wp_insert_post_empty_content', array($this, '_is_post_content_empty'));

            // these are not called if base post fields are not changed
            add_action('save_post', array($this, 'save_post'));

            // attachments use different hooks
            add_action('edit_attachment', array($this, 'save_post'));
            add_action('add_attachment', array($this, 'save_post'));

            add_action('the_post', array($this, 'the_post'));

            $initialized = true;
        }
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

    public function get_post_types()
    {
        return $this->_post_types;
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

    public function render_metabox(\WP_Post $post)
    {
        require $this->get_plugin_path() . '/views/metabox.php';
    }

    /**
     * @param int $post_id
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

        if ($post && in_array($post->post_type, $this->_post_types) && isset($_POST[self::REQUEST_KEY])) {
            $attachments = array();
            foreach ((array) $_POST[self::REQUEST_KEY] as $data) {
                if (($attachment = $this->create_attachment($data)) !== null) {
                    $attachments[] = $attachment;
                }
            }

            $meta = wp_json_encode(
                array_map(array($this, '_attachment_to_array'), $attachments)
            );
            update_post_meta($post_id, self::POST_META_KEY, $meta);

            $post->{self::POST_PROPERTY} = $attachments;
        }

        // echo @$meta;
        // echo '<pre>', __METHOD__, "\n", print_r($post, 1); print_r($_POST); exit;
    }

    /**
     * @param bool $is_empty
     * @return bool
     * @internal
     */
    public function _is_post_content_empty($is_empty)
    {
        if (isset($_POST[self::REQUEST_KEY])) {
            return false;
        }
        return $is_empty;
    }

    /**
     * @param \wpPostAttachments\Attachment\Attachment $attachment
     * @return array
     * @internal
     */
    public function _attachment_to_array(\wpPostAttachments\Attachment\Attachment $attachment)
    {
        return $attachment->to_array();
    }

    /**
     * @param \WP_Post $post
     * @internal
     */
    public function the_post(\WP_Post $post)
    {
        if (in_array($post->post_type, $this->_post_types)) {
            $post->{self::POST_PROPERTY} = $this->get_post_attachments($post->ID);
        }
    }

    /**
     * @param int|WP_Post $post_id OPTIONAL
     * @return \wpPostAttachments\Attachment\Attachment[]
     */
    public function get_post_attachments($post_id = null)
    {
        if ($post_id === null) {
            $post_id = get_post();
        }

        if ($post_id instanceof \WP_Post) {
            $post_id = $post_id->ID;
        }

        $meta = get_post_meta((int) $post_id, self::POST_META_KEY, true);
        $data = (array) json_decode($meta, true);

        $attachments = array();
        foreach ($data as $val) {
            if (($attachment = $this->create_attachment($val)) !== null) {
                $attachments[] = $attachment;
            }
        }
        return $attachments;
    }

    /**
     * @param string|array $type
     * @return \wpPostAttachments\Attachment\Attachment|null
     */
    public function create_attachment($type)
    {
        if (is_array($type)) {
            $data = $type;
            $type = (string) $data['type'];
        } else {
            $data = null;
            $type = (string) $type;
        }

        if (isset($this->_type_classes[$type])) {
            $class = $this->_type_classes[$type];
            /** @var \wpPostAttachments\Attachment\Attachment $attachment */
            $attachment = new $class();
            if ($data) {
                $attachment->set_from_array($data);
            }
            return $attachment;
        }

        return null;
    }

    /**
     * @param string $path OPTIONAL
     * @return string
     * @throws Exception
     */
    public function get_plugin_url($path = null)
    {
        static $plugin_url;
        if ($plugin_url === null) {
            // get url corresponding to wp-content directory
            $content_url = content_url();

            // get wp-content dir name (defaults to wp-content)
            $content_dir = basename($content_url);

            // find the last occurrence of wp-content in the path of the
            // directory of the plugin - we require that it resides inside
            // wp-content subtree
            $dir = wp_normalize_path($this->get_plugin_path());
            if (($pos = strrpos($dir, '/' . $content_dir . '/')) !== false) {
                // replace path to wp-content with the wp-content url
                $plugin_url = $content_url . substr($dir, $pos + strlen($content_dir) + 1);
            } else {
                throw new \RuntimeException('Unable to determine plugin url');
            }
        }
        if ($path === null) {
            $path = $plugin_url;
        } else {
            $path = $plugin_url . '/' . ltrim($path, '/');
        }
        return $path;
    }

    /**
     * @param  string $path OPTIONAL
     * @return string
     */
    public function get_plugin_path($path = null)
    {
        static $plugin_dir;
        if ($plugin_dir === null) {
            $plugin_dir = realpath(__DIR__ . '/../..');
        }
        if ($path === null) {
            $path = $plugin_dir;
        } else {
            $path = $plugin_dir . DIRECTORY_SEPARATOR . ltrim($path, '/\\');
        }
        return $path;
    }


    /**
     * @var \wpPostAttachments\Plugin
     */
    protected static $_instance;

    /**
     * Retrieves the globally accessible plugin instance.
     *
     * @return \wpPostAttachments\Plugin
     */
    public static function get_instance()
    {
        if (empty(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
}
