<?php
/**
 * The settings class
 *
 * @since      1.0.0
 * @package    Custom_Login_Page_Designer
 * @subpackage Custom_Login_Page_Designer/includes
 */

namespace CLPD;

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class to handle plugin settings.
 */
class Settings {

    /**
     * The loader that's responsible for maintaining and registering all hooks
     *
     * @var Loader
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
     * @param Loader $loader The loader object.
     */
    public function __construct($loader) {
        $this->loader = $loader;
        $this->settings = get_option('clpd_settings', array());

        $this->register_hooks();
    }

    /**
     * Register the hooks related to settings
     */
    private function register_hooks() {
        $this->loader->add_action('admin_init', $this, 'register_settings');
    }

    /**
     * Register plugin settings
     */
    public function register_settings() {
        register_setting(
            'clpd_settings_group',
            'clpd_settings',
            'sanitize_settings'
        );
    }

    /**
     * Sanitize plugin settings
     *
     * @param array $input The settings input.
     * @return array Sanitized settings.
     */
    public function sanitize_settings($input) {
        $sanitized = array();

        if (isset($input['design_template'])) {
            $sanitized['design_template'] = sanitize_text_field($input['design_template']);
        }

        if (isset($input['background_type'])) {
            $sanitized['background_type'] = sanitize_text_field($input['background_type']);
        }

        if (isset($input['background_color'])) {
            $sanitized['background_color'] = sanitize_hex_color($input['background_color']);
        }

        if (isset($input['background_gradient_start'])) {
            $sanitized['background_gradient_start'] = sanitize_hex_color($input['background_gradient_start']);
        }

        if (isset($input['background_gradient_end'])) {
            $sanitized['background_gradient_end'] = sanitize_hex_color($input['background_gradient_end']);
        }

        if (isset($input['background_image'])) {
            $sanitized['background_image'] = esc_url_raw($input['background_image']);
        }

        if (isset($input['logo_image'])) {
            $sanitized['logo_image'] = esc_url_raw($input['logo_image']);
        }

        if (isset($input['logo_image_width'])) {
            $sanitized['logo_image_width'] = sanitize_text_field($input['logo_image_width']);
        }

        if (isset($input['logo_image_height'])) {
            $sanitized['logo_image_height'] = sanitize_text_field($input['logo_image_height']);
        }

        if (isset($input['logo_image_radius'])) {
            $sanitized['logo_image_radius'] = sanitize_text_field($input['logo_image_radius']);
        }
        
        if (isset($input['text_color'])) {
            $sanitized['text_color'] = sanitize_hex_color($input['text_color']);
        }

        if (isset($input['text_font_family'])) {
            $sanitized['text_font_family'] = sanitize_text_field($input['text_font_family']);
        }

        if (isset($input['button_text_color'])) {
            $sanitized['button_text_color'] = sanitize_hex_color($input['button_text_color']);
        }

        if (isset($input['button_bg_color'])) {
            $sanitized['button_bg_color'] = sanitize_hex_color($input['button_bg_color']);
        }

        if (isset($input['button_hover_bg_color'])) {
            $sanitized['button_hover_bg_color'] = sanitize_hex_color($input['button_hover_bg_color']);
        }

        if (isset($input['button_font_family'])) {
            $sanitized['button_font_family'] = sanitize_text_field($input['button_font_family']);
        }

        return $sanitized;
    }

    /**
     * Get a specific setting
     *
     * @param string $key     The setting key.
     * @param mixed  $default The default value if setting doesn't exist.
     * @return mixed
     */
    public function get_setting($key, $default = null) {
        if (isset($this->settings[$key])) {
            return $this->settings[$key];
        }
        return $default;
    }

    /**
     * Get all settings
     *
     * @return array
     */
    public function get_all_settings() {
        return $this->settings;
    }
}