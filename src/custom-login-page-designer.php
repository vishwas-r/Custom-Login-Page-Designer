<?php
/**
 * Plugin Name: Custom Login Page Designer
 * Plugin URI: https://vishwas.me
 * Description: Custom Login Page Designer allows you to customize your WordPress login page with predefined designs and various customization options.
 * Version: 1.1.0
 * Author: Vishwas R
 * Author URI: https://vishwas.me/
 * Text Domain: custom-login-page-designer
 * License: GPL v3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Main plugin class
 */
class CLPD51 {

    /**
     * Plugin version
     *
     * @var string
     */
    const VERSION = '1.1.0';

    /**
     * Plugin singleton instance
     *
     * @var CLPD51
     */
    private static $instance = null;

    /**
     * Plugin directory path
     *
     * @var string
     */
    private $plugin_path;

    /**
     * Plugin directory URL
     *
     * @var string
     */
    private $plugin_url;

    /**
     * Get singleton instance
     *
     * @return CLPD51
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct() {
        $this->plugin_path = plugin_dir_path(__FILE__);
        $this->plugin_url = plugin_dir_url(__FILE__);

        $this->define_constants();
        $this->load_dependencies();
        $this->init_hooks();
    }

    /**
     * Define plugin constants
     */
    private function define_constants() {
        define('CLPD_VERSION', self::VERSION);
        define('CLPD_PLUGIN_PATH', $this->plugin_path);
        define('CLPD_PLUGIN_URL', $this->plugin_url);
        define('CLPD_PLUGIN_BASENAME', plugin_basename(__FILE__));
    }

    /**
     * Load plugin dependencies
     */
    private function load_dependencies() {
        // Core classes
        require_once CLPD_PLUGIN_PATH . 'includes/class-clpd-loader.php';
        require_once CLPD_PLUGIN_PATH . 'includes/class-clpd-settings.php';
        require_once CLPD_PLUGIN_PATH . 'includes/class-clpd-customizer.php';
        require_once CLPD_PLUGIN_PATH . 'admin/class-clpd-admin.php';
        require_once CLPD_PLUGIN_PATH . 'public/class-clpd-public.php';
    }

    /**
     * Initialize plugin hooks
     */
    private function init_hooks() {
        // Activation and deactivation hooks
        register_activation_hook(__FILE__, array($this, 'activate'));
        
        // Initialize components
        add_action('plugins_loaded', array($this, 'init_components'));
    }

    /**
     * Initialize plugin components
     */
    public function init_components() {
        $loader = new CLPD\Loader();
        $settings = new CLPD\Settings($loader);
        $customizer = new CLPD\Customizer($loader);
        $admin = new CLPD\Admin\Admin($loader);
        $public = new CLPD\Public\Public_Class($loader);

        $loader->run();
    }

    /**
     * Plugin activation
     */
    public function activate() {
        // Set default options if not already set
        if (!get_option('clpd_settings')) {
            $default_settings = array(
                'design_template' => 'minimal-white',
                'background_type' => 'color',
                'background_color' => '#f0f0f1',
                'background_gradient_start' => '#2271b1',
                'background_gradient_end' => '#135e96',
                'background_image' => '',
                'logo_image' => '',
                'logo_image_width' => '250px',
                'logo_image_height' => '80px',
                'logo_image_radius' => '0px',
                'text_color' => '#3c434a',
                'text_font_family' => 'sans-serif',
                'button_text_color' => '#ffffff',
                'button_bg_color' => '#2271b1',
                'button_hover_bg_color' => '#135e96',
                'button_font_family' => 'sans-serif',
            );
            update_option('clpd_settings', $default_settings);
        }
    }

    /**
     * Get plugin directory path
     *
     * @return string
     */
    public function get_plugin_path() {
        return $this->plugin_path;
    }

    /**
     * Get plugin directory URL
     *
     * @return string
     */
    public function get_plugin_url() {
        return $this->plugin_url;
    }
}

// Initialize the plugin
function clpd_page_designer() {
    return CLPD51::get_instance();
}

// Get the plugin running
clpd_page_designer();