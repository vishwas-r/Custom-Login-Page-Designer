<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @since      1.0.0
 * @package    Custom_Login_Page_Designer
 * @subpackage Custom_Login_Page_Designer/public
 */

namespace CLPD\Public;

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class to handle public-specific functionality of the plugin.
 */
class Public_Class {

    /**
     * The loader that's responsible for maintaining and registering all hooks
     *
     * @var \CLPD\Loader
     */
    private $loader;

    /**
     * The plugin settings
     *
     * @var array
     */
    private $settings;

    /**
     * Initialize the class and set its properties.
     *
     * @param \CLPD\Loader $loader The loader object.
     */
    public function __construct($loader) {
        $this->loader = $loader;
        $this->settings = get_option('clpd_settings', array());

        $this->register_hooks();
    }

    /**
     * Register the hooks related to public functionality
     */
    private function register_hooks() {
        // No specific hooks for public-facing functionality yet
        // Most of the public functionality is handled by the Customizer class
    }
}