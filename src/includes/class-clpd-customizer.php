<?php
/**
 * The customizer class
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
 * Class to handle login page customization.
 */
class Customizer {

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
     * Register the hooks related to customization
     */
    private function register_hooks() {
        $this->loader->add_action('login_enqueue_scripts', $this, 'enqueue_styles');
        // $this->loader->add_action('login_head', $this, 'output_custom_styles');
        $this->loader->add_filter('login_headerurl', $this, 'custom_login_header_url');
        $this->loader->add_filter('login_headertext', $this, 'custom_login_header_text');
    }

    /**
     * Enqueue login page styles
     */
    public function enqueue_styles() {
        $design_template = isset($this->settings['design_template']) ? $this->settings['design_template'] : 'minimal-white';
        
        // Enqueue the base styles
        wp_enqueue_style(
            'clpd-base-style',
            CLPD_PLUGIN_URL . 'public/css/clpd-base.css',
            array(),
            CLPD_VERSION
        );
        
        // Enqueue the selected design template
        wp_enqueue_style(
            'clpd-design-template',
            CLPD_PLUGIN_URL . 'public/css/' . $design_template . '.css',
            array('clpd-base-style'),
            CLPD_VERSION
        );

        $custom_css = $this->get_custom_styles();
        wp_add_inline_style( 'clpd-design-template', $custom_css );
    }

    /**
     * Output custom styles for the login page
     */
    public function get_custom_styles() {
        $background_type = isset($this->settings['background_type']) ? $this->settings['background_type'] : 'color';
        $background_color = isset($this->settings['background_color']) ? $this->settings['background_color'] : '#f0f0f1';
        $background_gradient_start = isset($this->settings['background_gradient_start']) ? $this->settings['background_gradient_start'] : '#2271b1';
        $background_gradient_end = isset($this->settings['background_gradient_end']) ? $this->settings['background_gradient_end'] : '#135e96';
        $background_image = isset($this->settings['background_image']) ? $this->settings['background_image'] : '';
        $logo_image = isset($this->settings['logo_image']) ? $this->settings['logo_image'] : '';
        $logo_image_width = isset($this->settings['logo_image_width']) ? $this->settings['logo_image_width'] : '250px';
        $logo_image_height = isset($this->settings['logo_image_height']) ? $this->settings['logo_image_height'] : '80px';
        $logo_image_radius = isset($this->settings['logo_image_radius']) ? $this->settings['logo_image_radius'] : '0px';
        $text_color = isset($this->settings['text_color']) ? $this->settings['text_color'] : '#3c434a';
        $text_font_family = isset($this->settings['text_font_family']) ? $this->settings['text_font_family'] : 'sans-serif';
        $button_text_color = isset($this->settings['button_text_color']) ? $this->settings['button_text_color'] : '#ffffff';
        $button_bg_color = isset($this->settings['button_bg_color']) ? $this->settings['button_bg_color'] : '#2271b1';
        $button_hover_bg_color = isset($this->settings['button_hover_bg_color']) ? $this->settings['button_hover_bg_color'] : '#135e96';
        $button_font_family = isset($this->settings['button_font_family']) ? $this->settings['button_font_family'] : 'sans-serif';
        
        $custom_css = '';
        
        // Background styles
        $custom_css .= 'body.login {';
        if ($background_type === 'color') {
            $custom_css .= 'background-color: ' . $background_color . ';';
        } elseif ($background_type === 'gradient') {
            $custom_css .= 'background: linear-gradient(to bottom, ' . $background_gradient_start . ', ' . $background_gradient_end . ');';
        } elseif ($background_type === 'image' && !empty($background_image)) {
            $custom_css .= 'background-image: url(' . $background_image . ');';
            $custom_css .= 'background-size: cover;';
            $custom_css .= 'background-position: center center;';
        }
        $custom_css .= '}';
        
        // Text styles
        $custom_css .= 'body.login, body.login label, body.login #login_error, body.login .message, body.login .success {';
        $custom_css .= 'color: ' . $text_color . ' !important;';
        $custom_css .= 'font-family: ' . $text_font_family . ';';
        $custom_css .= '}';
        
        // Button styles
        $custom_css .= 'body.login .button-primary {';
        $custom_css .= 'background: ' . $button_bg_color . ' !important;';
        $custom_css .= 'color: ' . $button_text_color . ' !important;';
        $custom_css .= 'font-family: ' . $button_font_family . ';';
        $custom_css .= 'border-color: ' . $button_bg_color . ' !important;';
        $custom_css .= '}';
        
        // Button hover styles
        $custom_css .= 'body.login .button-primary:hover {';
        $custom_css .= 'background: ' . $button_hover_bg_color . ' !important;';
        $custom_css .= 'border-color: ' . $button_hover_bg_color . ' !important;';
        $custom_css .= '}';
        
        // Logo styles
        if (!empty($logo_image)) {
            $custom_css .= 'body.login h1 a {';
            $custom_css .= 'background-image: url(' . $logo_image . ');';
            $custom_css .= 'background-size: contain;';
            $custom_css .= 'width: ' . $logo_image_width . ' !important;';
            $custom_css .= 'height: ' . $logo_image_height . ' !important;';
            $custom_css .= 'border-radius: ' . $logo_image_radius . ' !important;';
            $custom_css .= '}';
        }
        
        return $custom_css;
    }

    /**
     * Change the login logo URL
     *
     * @return string
     */
    public function custom_login_header_url() {
        return home_url('/');
    }

    /**
     * Change the login logo text
     *
     * @return string
     */
    public function custom_login_header_text() {
        return get_bloginfo('name');
    }
}