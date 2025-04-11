<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @since      1.0.0
 * @package    CLPD51
 * @subpackage CLPD51/admin
 */
namespace CLPD\Admin;
// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    exit;
}
/**
 * Class to handle admin-specific functionality of the plugin.
 */
class Admin {
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
     * Register the hooks related to admin functionality
     */
    private function register_hooks() {
        $this->loader->add_action('admin_menu', $this, 'add_menu_page');
        $this->loader->add_action('admin_menu', $this, 'add_options_page');
        $this->loader->add_action('admin_enqueue_scripts', $this, 'clpd_enqueue_admin_scripts');
        $this->loader->add_filter('plugin_action_links_' . CLPD_PLUGIN_BASENAME, $this, 'add_plugin_action_links');
    }
    
    /**
     * Add top-level menu page to admin menu
     */
    public function add_menu_page() {
        add_menu_page(
            __('Custom Login Page Designer', 'custom-login-page-designer'),
            __('Login Page Designer', 'custom-login-page-designer'),
            'manage_options',
            'custom-login-page-designer',
            array($this, 'render_options_page'),
            'dashicons-admin-customizer',
            60
        );
    }
    
    /**
     * Add options page to admin menu (kept for backward compatibility)
     */
    public function add_options_page() {
        add_options_page(
            __('Custom Login Page Designer', 'custom-login-page-designer'),
            __('Login Page Designer', 'custom-login-page-designer'),
            'manage_options',
            'custom-login-page-designer-settings',
            array($this, 'render_options_page')
        );
    }
    
    /**
     * Render options page
     */
    public function render_options_page() {
        // Check user capabilities
        if (!current_user_can('manage_options')) {
            return;
        }
        // Save settings if form submitted
        if (isset($_POST['clpd_settings_submit']) && check_admin_referer('clpd_settings_nonce', 'clpd_settings_nonce')) {
            update_option('clpd_settings', $this->process_settings($_POST));
            $this->settings = get_option('clpd_settings', array());
            echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__('Settings saved successfully!', 'custom-login-page-designer') . '</p></div>';
        }
        include CLPD_PLUGIN_PATH . 'admin/partials/clpd-admin-display.php';
    }
    /**
     * Process settings from form submission
     *
     * @param array $post_data Form data from $_POST.
     * @return array Processed settings.
     */
    private function process_settings($post_data) {
        $settings = array();
    
        $fields = array(
            'design_template'         => 'sanitize_text_field',
            'background_type'         => 'sanitize_text_field',
            'background_color'        => 'sanitize_hex_color',
            'background_gradient_start' => 'sanitize_hex_color',
            'background_gradient_end' => 'sanitize_hex_color',
            'background_image'        => 'esc_url_raw',
            'logo_image'              => 'esc_url_raw',
            'logo_image_width'        => 'absint',
            'logo_image_height'       => 'absint',
            'logo_image_radius'       => 'absint',
            'text_color'              => 'sanitize_hex_color',
            'text_font_family'        => 'sanitize_text_field',
            'button_text_color'       => 'sanitize_hex_color',
            'button_bg_color'         => 'sanitize_hex_color',
            'button_hover_bg_color'   => 'sanitize_hex_color',
            'button_font_family'      => 'sanitize_text_field'
        );
    
        foreach ($fields as $field => $sanitize_callback) {
            if (isset($post_data[$field])) {
                $settings[$field] = call_user_func($sanitize_callback, $post_data[$field]);
            }
        }
    
        return $settings;
    }
    
    /**
     * Enqueue admin scripts and styles
     *
     * @param string $hook Current admin page.
     */
    public function clpd_enqueue_admin_scripts($hook) {
        // Check if we're on either the top-level page or the settings submenu
        if ($hook !== 'toplevel_page_custom-login-page-designer' && $hook !== 'settings_page_custom-login-page-designer-settings') {
            return;
        }
        // Admin styles
        wp_enqueue_style(
            'clpd-admin-style',
            CLPD_PLUGIN_URL . 'admin/css/clpd-admin.css',
            array(),
            CLPD_VERSION
        );
        // WordPress color picker
        wp_enqueue_style('wp-color-picker');
        
        // WordPress media uploader
        wp_enqueue_media();
        // Admin scripts
        wp_enqueue_script(
            'clpd-admin-script',
            CLPD_PLUGIN_URL . 'admin/js/clpd-admin.js',
            array('jquery', 'wp-color-picker'),
            CLPD_VERSION,
            true
        );
        
        // Add your template selection JS file
        wp_enqueue_script(
            'clpd-template-selection',
            CLPD_PLUGIN_URL . 'admin/js/template-selection.js',
            array('jquery', 'wp-color-picker', 'clpd-admin-script'),
            CLPD_VERSION,
            true
        );
        
        // Get template settings for JavaScript
        global $template_settings;
        if (!isset($template_settings)) {
            // If not available globally, recreate the template settings array
            $template_settings = array(
                'minimal-white' => array(
                    'background_type' => 'color',
                    'background_color' => '#f0f0f1',
                    'background_gradient_start' => '#2271b1',
                    'background_gradient_end' => '#135e96',
                    'background_image' => '',
                    'logo_image_width' => '250px',
                    'logo_image_height' => '80px',
                    'logo_image_radius' => '0px',
                    'text_color' => '#3c434a',
                    'text_font_family' => 'Arial, sans-serif',
                    'button_text_color' => '#ffffff',
                    'button_bg_color' => '#2271b1',
                    'button_hover_bg_color' => '#135e96',
                    'button_font_family' => 'Arial, sans-serif',
                ),
                'corporate-professional' => array(
                    'background_type' => 'color',
                    'background_color' => '#f8f9fa',
                    'background_gradient_start' => '#4b7bec',
                    'background_gradient_end' => '#3867d6',
                    'background_image' => '',
                    'logo_image_width' => '200px',
                    'logo_image_height' => '70px',
                    'logo_image_radius' => '5px',
                    'text_color' => '#2d3436',
                    'text_font_family' => 'Helvetica, sans-serif',
                    'button_text_color' => '#ffffff',
                    'button_bg_color' => '#3867d6',
                    'button_hover_bg_color' => '#2d3436',
                    'button_font_family' => 'Helvetica, sans-serif',
                ),
                'modern-tech' => array(
                    'background_type' => 'gradient',
                    'background_color' => '#2d3436',
                    'background_gradient_start' => '#2d3436',
                    'background_gradient_end' => '#636e72',
                    'background_image' => '',
                    'logo_image_width' => '180px',
                    'logo_image_height' => '60px',
                    'logo_image_radius' => '0px',
                    'text_color' => '#dfe6e9',
                    'text_font_family' => 'Arial, sans-serif',
                    'button_text_color' => '#ffffff',
                    'button_bg_color' => '#0984e3',
                    'button_hover_bg_color' => '#74b9ff',
                    'button_font_family' => 'Arial, sans-serif',
                ),
                'glassmorphism' => array(
                    'background_type' => 'image',
                    'background_color' => '#ffffff',
                    'background_gradient_start' => '#ffffff',
                    'background_gradient_end' => '#f1f1f1',
                    'background_image' => '',
                    'logo_image_width' => '220px',
                    'logo_image_height' => '75px',
                    'logo_image_radius' => '10px',
                    'text_color' => '#2d3436',
                    'text_font_family' => 'Helvetica, sans-serif',
                    'button_text_color' => '#ffffff',
                    'button_bg_color' => 'rgba(45, 52, 54, 0.7)',
                    'button_hover_bg_color' => 'rgba(45, 52, 54, 0.9)',
                    'button_font_family' => 'Helvetica, sans-serif',
                ),
                'dark-gradient' => array(
                    'background_type' => 'gradient',
                    'background_color' => '#000000',
                    'background_gradient_start' => '#2d3436',
                    'background_gradient_end' => '#000000',
                    'background_image' => '',
                    'logo_image_width' => '200px',
                    'logo_image_height' => '70px',
                    'logo_image_radius' => '0px',
                    'text_color' => '#ffffff',
                    'text_font_family' => 'Arial, sans-serif',
                    'button_text_color' => '#ffffff',
                    'button_bg_color' => '#6c5ce7',
                    'button_hover_bg_color' => '#a29bfe',
                    'button_font_family' => 'Arial, sans-serif',
                ),
                'nature-inspired' => array(
                    'background_type' => 'image',
                    'background_color' => '#dfe6e9',
                    'background_gradient_start' => '#55efc4',
                    'background_gradient_end' => '#00b894',
                    'background_image' => '',
                    'logo_image_width' => '180px',
                    'logo_image_height' => '60px',
                    'logo_image_radius' => '50%',
                    'text_color' => '#2d3436',
                    'text_font_family' => 'Georgia, serif',
                    'button_text_color' => '#ffffff',
                    'button_bg_color' => '#00b894',
                    'button_hover_bg_color' => '#00cec9',
                    'button_font_family' => 'Georgia, serif',
                ),
                'neomorphic-modern' => array(
                    'background_type' => 'color',
                    'background_color' => '#e0e5ec',
                    'background_gradient_start' => '#e0e5ec',
                    'background_gradient_end' => '#e0e5ec',
                    'background_image' => '',
                    'logo_image_width' => '220px',
                    'logo_image_height' => '75px',
                    'logo_image_radius' => '15px',
                    'text_color' => '#2d3436',
                    'text_font_family' => 'Helvetica, sans-serif',
                    'button_text_color' => '#2d3436',
                    'button_bg_color' => '#e0e5ec',
                    'button_hover_bg_color' => '#d1d9e6',
                    'button_font_family' => 'Helvetica, sans-serif',
                ),
                'blueprint-professional' => array(
                    'background_type' => 'color',
                    'background_color' => '#ffffff',
                    'background_gradient_start' => '#3498db',
                    'background_gradient_end' => '#2980b9',
                    'background_image' => '',
                    'logo_image_width' => '200px',
                    'logo_image_height' => '70px',
                    'logo_image_radius' => '5px',
                    'text_color' => '#34495e',
                    'text_font_family' => 'Tahoma, sans-serif',
                    'button_text_color' => '#ffffff',
                    'button_bg_color' => '#3498db',
                    'button_hover_bg_color' => '#2980b9',
                    'button_font_family' => 'Tahoma, sans-serif',
                ),
            );
        }
        
        // Localized data for admin script
        wp_localize_script(
            'clpd-admin-script',
            'clpd_admin_vars',
            array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'preview_nonce' => wp_create_nonce('clpd_preview_nonce'),
                'i18n' => array(
                    'select_image' => __('Select Image', 'custom-login-page-designer'),
                    'use_image' => __('Use This Image', 'custom-login-page-designer'),
                    'remove_image' => __('Remove Image', 'custom-login-page-designer'),
                ),
            )
        );
        
        // Localize the template selection script with template settings
        wp_localize_script(
            'clpd-template-selection',
            'clpdData',
            array(
                'templateSettings' => $template_settings,
                'logoText' => __('Select Logo', 'custom-login-page-designer'),
                'bgImageText' => __('Select Background Image', 'custom-login-page-designer'),
                'useImageText' => __('Use this image', 'custom-login-page-designer'),
                'removeImageText' => __('Remove Image', 'custom-login-page-designer')
            )
        );
    }
    /**
     * Add plugin action links
     *
     * @param array $links Plugin action links.
     * @return array Modified plugin action links.
     */
    public function add_plugin_action_links($links) {
        $settings_link = sprintf(
            '<a href="%s">%s</a>',
            esc_url(admin_url('admin.php?page=custom-login-page-designer')),
            __('Settings', 'custom-login-page-designer')
        );
        
        array_unshift($links, $settings_link);
        
        return $links;
    }
}