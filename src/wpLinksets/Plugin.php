<?php

namespace wpLinksets;

class Plugin
{
    const FEATURE_KEY   = 'linksets';
    const REQUEST_KEY   = 'linkset';
    const META_KEY      = '_linkset';
    const POST_PROPERTY = 'post_linkset';

    /**
     * Factory used for link instantiation
     * @var LinkFactory
     */
    protected $_link_factory;

    /**
     * List of post types linkset feature is enabled for
     * @var array
     */
    protected $_post_types = array();

    public function __construct()
    {
        $this->_link_factory = new LinkFactory();
    }

    public function init()
    {
        add_action('init', array($this, 'on_init'));
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
            $this->_post_types[$post_type] = true;
        }
    }

    /**
     * Is post attachment functionality enabled for the given post type
     *
     * @param string $post_type
     * @return bool
     */
    public function is_enabled($post_type)
    {
        return isset($this->_post_types[$post_type]);
    }

    /**
     * Retrieves a list of post types this plugin is active for
     *
     * @return array
     */
    public function get_post_types()
    {
        return array_keys($this->_post_types);
    }

    /**
     * Initializes list of post types based on current theme settings
     */
    protected function _setup_post_types()
    {
        // retrieve post types registered via add_theme_support()
        $supported = get_theme_support(self::FEATURE_KEY);

        if ($supported) {
            // if not explicitly specified, enable support for post and page
            if ($supported === true) {
                $supported = array('post', 'page');
            }
            foreach ((array) $supported as $post_type) {
                // post_type can still be an array here
                $this->enable($post_type);
            }
        }
    }

    /**
     * Registers plugin hooks
     */
    public function on_init()
    {
        $this->_setup_post_types();
        add_action('add_meta_boxes', array($this, 'on_meta_boxes'));

        // in order to save post its content must not be considered empty
        // make sure post is not empty if attachments are provided in the request
        add_filter('wp_insert_post_empty_content', array($this, '_is_post_content_empty'));

        // these are not called if base post fields are not changed
        add_action('save_post', array($this, 'on_save_post'));

        // attachments use different hooks
        add_action('edit_attachment', array($this, 'on_save_post'));
        add_action('add_attachment', array($this, 'on_save_post'));

        add_action('the_post', array($this, 'on_the_post'));

        add_action('admin_enqueue_scripts', array($this, 'on_admin_enqueue_scripts'));

        // this will be active if Timber is present
        add_filter('get_twig', array($this, 'on_get_twig'));
    }

    public function on_admin_enqueue_scripts()
    {
        // needed for find posts div
        // wp_enqueue_script('thickbox');
        // wp_enqueue_style('thickbox');

        // findPosts
        // wp_enqueue_script('media');
        // wp_enqueue_script('wp-ajax-response');

        wp_enqueue_style('linkset', $this->get_plugin_url('assets/css/style.css'), array('dashicons', 'thickbox'));
        wp_enqueue_script('linkset', $this->get_plugin_url('assets/js/main.js'), array('media', 'wp-ajax-response', 'thickbox'));
    }

    /**
     *
     */
    public function on_meta_boxes()
    {
        foreach ($this->get_post_types() as $post_type) {
            add_meta_box('post_attachments', 'Linkset', array($this, 'render_metabox'), $post_type, 'normal', 'default', null);
        }
    }

    public function render_metabox(\WP_Post $post)
    {
        require $this->get_plugin_path() . '/views/metabox.php';
    }

    /**
     * @param int $post_id
     */
    public function on_save_post($post_id)
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

        if ($post && $this->is_enabled($post->post_type) && isset($_POST[self::REQUEST_KEY])) {
            $linkset = new Linkset();

            foreach ((array) $_POST[self::REQUEST_KEY] as $data) {
                try {
                    $link = $this->_link_factory->create_link($data);
                    $linkset->add($link);
                } catch (\Exception $e) {
                    // do nothing, or log error elsewhere
                }
            }

            // don't serialize (or JSON encode) meta explicitly, because the
            // meta value will be stripped of backslashes. That will lead to
            // corrupted data (unserializable string or corrupted UTF-8 chars)
            // $meta = serialize($linkset->to_array());
            update_post_meta($post_id, self::META_KEY, $linkset->to_array());

            $post->{self::POST_PROPERTY} = $linkset;
        }

        //echo 'isEnabled: ', (int) $this->is_enabled($post->post_type), "<br/>";
        //echo 'isSubmitted:' , (int) isset($_POST[self::REQUEST_KEY]), "<br/>";
        //echo @$meta;
        //echo '<pre>', __METHOD__, "\n", print_r($post, 1); print_r($_POST); exit;
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
     * @param \WP_Post $post
     * @internal
     */
    public function on_the_post($post)
    {
        if (!$post instanceof \WP_Post) {
            return;
        }
        if ($this->is_enabled($post->post_type)) {
            $post->{self::POST_PROPERTY} = $this->get_linkset($post->ID);
        }
    }

    /**
     * @param \Twig_Environment $twig
     */
    public function on_get_twig($twig)
    {
        if (class_exists('Twig_Environment') && $twig instanceof \Twig_Environment) {
            $func = new \Twig_SimpleFunction('get_post_linkset', array($this, 'get_linkset'));
            $twig->addFunction($func);
        }
        return $twig;
    }

    public function get_nonce()
    {
        return wp_create_nonce(basename(__FILE__));
    }

    /**
     * Prior to building linkset data is filtered by get_linkset filter.
     *
     * @param int|\WP_Post $post_id OPTIONAL
     * @return \wpLinksets\Linkset
     */
    public function get_linkset($post_id = null)
    {
        if ($post_id === null) {
            $post_id = get_post();
        }

        if (is_object($post_id) && isset($post_id->ID)) {
            $post_id = $post_id->ID;
        }

        $data = get_post_meta((int) $post_id, self::META_KEY, true);

        if (!is_array($data) && function_exists('json_decode')) {
            // backwards compatibility
            $data = (array) json_decode($data, true);
        }

        $linkset = new Linkset();

        if (is_array($data)) {
            $data = apply_filters('get_linkset', $data);

            foreach ((array) $data as $val) {
                try {
                    $link = $this->_link_factory->create_link($val);
                    $linkset->add($link);
                } catch (\Exception $e) {
                    // do nothing, or log error elsewhere
                }
            }
        }

        return $linkset;
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
     * @var \wpLinksets\Plugin
     */
    protected static $_instance;

    /**
     * Retrieves the globally accessible plugin instance
     *
     * @return \wpLinksets\Plugin
     */
    public static function get_instance()
    {
        if (empty(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Starts the global plugin instance
     */
    public static function start()
    {
        self::get_instance()->init();
    }
}
