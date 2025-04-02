<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @since      1.0.0
 * @package    Custom_Login_Page_Designer
 * @subpackage Custom_Login_Page_Designer/admin
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
        $this->loader->add_action('admin_menu', $this, 'add_options_page');
        $this->loader->add_action('admin_enqueue_scripts', $this, 'enqueue_scripts');
        $this->loader->add_filter('plugin_action_links_' . CLPD_PLUGIN_BASENAME, $this, 'add_plugin_action_links');
    }

    /**
     * Add options page to admin menu
     */
    public function add_options_page() {
        add_options_page(
            __('Custom Login Page Designer', 'custom-login-page-designer'),
            __('Login Page Designer', 'custom-login-page-designer'),
            'manage_options',
            'custom-login-page-designer',
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
            // echo '<div class="notice notice-success is-dismissible"><p>' . __('Settings saved successfully!', 'custom-login-page-designer') . '</p></div>';
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
            'design_template',
            'background_type',
            'background_color',
            'background_gradient_start',
            'background_gradient_end',
            'background_image',
            'logo_image',
            'logo_image_width',
            'logo_image_height',
            'logo_image_radius',
            'text_color',
            'text_font_family',
            'button_text_color',
            'button_bg_color',
            'button_hover_bg_color',
            'button_font_family'
        );

        foreach ($fields as $field) {
            if (isset($post_data[$field])) {
                $settings[$field] = $post_data[$field];
            }
        }

        return $settings;
    }

    /**
     * Enqueue admin scripts and styles
     *
     * @param string $hook Current admin page.
     */
    public function enqueue_scripts($hook) {
        if ($hook !== 'settings_page_custom-login-page-designer') {
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
            esc_url(admin_url('options-general.php?page=custom-login-page-designer')),
            __('Settings', 'custom-login-page-designer')
        );
        
        array_unshift($links, $settings_link);
        
        return $links;
    }
}