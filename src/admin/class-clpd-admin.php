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

        // Custom highlights to keep parent menu active when grouped
        add_filter('parent_file', array($this, 'xboard_keep_menu_open'), 999);
        add_filter('submenu_file', array($this, 'xboard_highlight_submenu'), 999, 2);
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
        $active_plugins = apply_filters('xboard_active_plugins', []);
        $is_grouped = count($active_plugins) > 1;

        if ($is_grouped) {
            $parent_slug = apply_filters('xboard_parent_slug', 'xboard');
            $parent_title = apply_filters('xboard_parent_title', 'XBoard');

            // Register parent menu exactly once
            if (empty($GLOBALS['xboard_parent_registered'])) {
                add_menu_page(
                    $parent_title,
                    $parent_title,
                    'manage_options',
                    $parent_slug,
                    'xboard_render_dashboard',
                    'dashicons-admin-tools',
                    3 // Position right after Dashboard
                );
                add_submenu_page(
                    $parent_slug,
                    esc_html__('Dashboard', 'custom-login-page-designer'),
                    esc_html__('Dashboard', 'custom-login-page-designer'),
                    'manage_options',
                    $parent_slug,
                    'xboard_render_dashboard'
                );
                $GLOBALS['xboard_parent_registered'] = true;
            }

            // Register Custom Login Page Designer under XBoard
            add_submenu_page(
                $parent_slug,
                __('Login Page Designer', 'custom-login-page-designer'),
                __('Login Page Designer', 'custom-login-page-designer'),
                'manage_options',
                'custom-login-page-designer',
                array($this, 'render_options_page')
            );
        } else {
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
        // Check if we're on either the top-level page or the settings submenu, or the grouped XBoard subpage
        if ($hook !== 'toplevel_page_custom-login-page-designer' && $hook !== 'settings_page_custom-login-page-designer-settings' && $hook !== 'xboard_page_custom-login-page-designer') {
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
        global $clpd_template_settings;
        if (!isset($clpd_template_settings)) {
            // If not available globally, recreate the template settings array
            $clpd_template_settings = array(
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
                'vintage-paper' => array(
                    'background_type' => 'color',
                    'background_color' => '#f5f0e1',
                    'background_gradient_start' => '#d9c5a0',
                    'background_gradient_end' => '#f5f0e1',
                    'background_image' => '',
                    'logo_image_width' => '210px',
                    'logo_image_height' => '75px',
                    'logo_image_radius' => '8px',
                    'text_color' => '#523a28',
                    'text_font_family' => 'Georgia, serif',
                    'button_text_color' => '#ffffff',
                    'button_bg_color' => '#8b5a2b',
                    'button_hover_bg_color' => '#6b4423',
                    'button_font_family' => 'Georgia, serif',
                ),
                'cosmic-night' => array(
                    'background_type' => 'gradient',
                    'background_color' => '#0f0f1f',
                    'background_gradient_start' => '#0f0f1f',
                    'background_gradient_end' => '#202060',
                    'background_image' => '',
                    'logo_image_width' => '220px',
                    'logo_image_height' => '80px',
                    'logo_image_radius' => '12px',
                    'text_color' => '#e0e0ff',
                    'text_font_family' => 'Roboto, sans-serif',
                    'button_text_color' => '#ffffff',
                    'button_bg_color' => '#9d4edd',
                    'button_hover_bg_color' => '#c77dff',
                    'button_font_family' => 'Roboto, sans-serif',
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
                'templateSettings' => $clpd_template_settings,
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
        array_unshift($links, $settings_link);
        
        return $links;
    }

    public function xboard_keep_menu_open($parent_file) {
        if (isset($_GET['page'])) {
            $page = sanitize_text_field(wp_unslash($_GET['page']));
            $clpd_pages = array('custom-login-page-designer', 'custom-login-page-designer-settings');
            if (in_array($page, $clpd_pages, true)) {
                return 'xboard';
            }
        }
        return $parent_file;
    }

    public function xboard_highlight_submenu($submenu_file, $parent_file) {
        if ($parent_file === 'xboard' && isset($_GET['page'])) {
            $page = sanitize_text_field(wp_unslash($_GET['page']));
            if ($page === 'custom-login-page-designer' || $page === 'custom-login-page-designer-settings') {
                return 'custom-login-page-designer';
            }
        }
        return $submenu_file;
    }
}

namespace {
    /**
     * Helper to fetch Custom Login Page Designer stats for the unified dashboard
     */
    if (!function_exists('xboard_get_clpd_stats')) {
        function xboard_get_clpd_stats() {
            $settings = get_option('clpd_settings', array());
            $template = isset($settings['design_template']) ? $settings['design_template'] : 'minimal-white';
            
            // Map slug to human readable name
            $templates = array(
                'minimal-white' => 'Minimal White',
                'corporate-professional' => 'Corporate Professional',
                'modern-tech' => 'Modern Tech',
                'glassmorphism' => 'Glassmorphism',
                'dark-gradient' => 'Dark Gradient',
                'nature-inspired' => 'Nature Inspired',
                'neomorphic-modern' => 'Neomorphic Modern',
                'blueprint-professional' => 'Blueprint Professional',
                'vintage-paper' => 'Vintage Paper',
                'cosmic-night' => 'Cosmic Night',
            );
            
            $template_name = isset($templates[$template]) ? $templates[$template] : $template;
            
            return array(
                'active_template' => $template_name,
                'bg_type' => isset($settings['background_type']) ? ucfirst($settings['background_type']) : 'Color',
            );
        }
    }

    /**
     * Render the unified XBoard Dashboard page (duplicate-safe)
     */
    if (!function_exists('xboard_render_dashboard')) {
        function xboard_render_dashboard() {
            // Stats for License MasterX
            $lmx_stats = [];
            if (class_exists('LicenseMasterX51_Admin')) {
                $lmx_admin = new LicenseMasterX51_Admin();
                $lmx_stats = $lmx_admin->get_quick_stats();
            }

            // Stats for File Download ManagerX
            $fdmx_stats = [];
            if (function_exists('xboard_get_fdmx_stats')) {
                $fdmx_stats = xboard_get_fdmx_stats();
            }

            // Stats for Post DuplicateX
            $postdx_stats = [];
            if (function_exists('xboard_get_postdx_stats')) {
                $postdx_stats = xboard_get_postdx_stats();
            }

            // Stats for Custom Login Page Designer
            $clpd_stats = [];
            if (function_exists('xboard_get_clpd_stats')) {
                $clpd_stats = xboard_get_clpd_stats();
            }

            $active_plugins = apply_filters('xboard_active_plugins', []);
            ?>
            <div style="font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,'Helvetica Neue',Arial,sans-serif;background:#f6f8fa;min-height:100vh;margin:0 0 0 -20px;padding:0;max-width:100%;box-sizing:border-box;">

                <!-- ── Banner Header ── -->
                <div style="background:linear-gradient(135deg,#2563ec 0%,#3b82f6 50%,#8b5cf6 100%);color:#fff;padding:30px 40px;box-shadow:0 4px 20px rgba(0,0,0,0.1);">
                    <div style="display:flex;justify-content:space-between;align-items:center;max-width:1400px;margin:0 auto;">
                        <div style="display:flex;flex-direction:column;">
                            <h1 style="font-size:32px;font-weight:700;margin:0;color:#fff;display:flex;align-items:center;gap:12px;">
                                <span class="dashicons dashicons-admin-tools" style="font-size:36px;width:36px;height:36px;"></span>
                                XBoard
                                <span style="background:rgba(255,255,255,0.2);padding:4px 12px;border-radius:9999px;font-weight:500;font-size:11px;vertical-align:middle;backdrop-filter:blur(8px);">Unified Suite</span>
                            </h1>
                            <p style="font-size:16px;opacity:0.9;margin:8px 0 0 0;font-weight:400;">Centralized control center for AlertX developer plugins.</p>
                        </div>
                    </div>
                </div>

                <!-- ── Horizontal Nav Bar ── -->
                <nav class="xboard-nav">
                    <ul>
                        <li class="active">
                            <a href="<?php echo esc_url(admin_url('admin.php?page=xboard')); ?>">
                                <span class="dashicons dashicons-dashboard"></span>
                                <?php esc_html_e('Dashboard', 'custom-login-page-designer'); ?>
                            </a>
                        </li>
                        <?php if (!empty($lmx_stats)) : ?>
                        <li>
                            <a href="<?php echo esc_url(admin_url('admin.php?page=licensemasterx51')); ?>">
                                <span class="dashicons dashicons-admin-network"></span>
                                <?php esc_html_e('License MasterX', 'custom-login-page-designer'); ?>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if (!empty($fdmx_stats)) : ?>
                        <li>
                            <a href="<?php echo esc_url(admin_url('admin.php?page=file-download-managerx')); ?>">
                                <span class="dashicons dashicons-download"></span>
                                <?php esc_html_e('File Download ManagerX', 'custom-login-page-designer'); ?>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if (!empty($postdx_stats)) : ?>
                        <li>
                            <a href="<?php echo esc_url(admin_url('admin.php?page=post-duplicatex')); ?>">
                                <span class="dashicons dashicons-admin-page"></span>
                                <?php esc_html_e('Post DuplicateX', 'custom-login-page-designer'); ?>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if (!empty($clpd_stats)) : ?>
                        <li>
                            <a href="<?php echo esc_url(admin_url('admin.php?page=custom-login-page-designer')); ?>">
                                <span class="dashicons dashicons-admin-customizer"></span>
                                <?php esc_html_e('Login Page Designer', 'custom-login-page-designer'); ?>
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </nav>

                <!-- ── Plugin Cards Grid ── -->
                <div style="max-width:1400px;margin:0 auto;padding:28px 40px;box-sizing:border-box;">
                    <style>
                        .xboard-nav { background:white; border-radius:0 0 12px 12px; border:1px solid #e5e7eb; padding:12px 40px; margin-bottom:30px; margin-top:-15px; box-shadow:0 1px 3px 0 rgba(0,0,0,0.05); }
                        .xboard-nav ul { list-style:none; margin:0 auto; padding:0; display:flex; gap:8px; flex-wrap:wrap; max-width:1400px; }
                        .xboard-nav li { margin:0; }
                        .xboard-nav a { color:#4b5563; text-decoration:none; font-weight:600; font-size:14px; padding:10px 18px; border-radius:8px; display:inline-flex; align-items:center; gap:8px; transition:all 0.2s ease; border:1px solid transparent; }
                        .xboard-nav a:hover { background:#f9fafb; color:#111827; transform:translateY(-1px); }
                        .xboard-nav li.active a { background:#f5f3ff; color:#7c3aed; border-color:#ddd6fe; box-shadow:0 2px 4px rgba(124,58,237,0.05); }
                        .xboard-nav a .dashicons { font-size:18px; width:18px; height:18px; color:inherit; }
                        .xboard-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(360px,1fr)); gap:24px; }
                        .xboard-card { background:#fff; border-radius:12px; border:1px solid #e2e8f0; box-shadow:0 4px 6px -1px rgba(0,0,0,.05),0 2px 4px -1px rgba(0,0,0,.03); transition:all .3s cubic-bezier(.4,0,.2,1); overflow:hidden; display:flex; flex-direction:column; }
                        .xboard-card:hover { transform:translateY(-4px); box-shadow:0 20px 25px -5px rgba(0,0,0,.1),0 10px 10px -5px rgba(0,0,0,.04); border-color:#cbd5e1; }
                        .xboard-card-header { display:flex; align-items:center; gap:12px; padding:24px; border-bottom:1px solid #f1f5f9; background:#f8fafc; }
                        .xboard-card-header .dashicons { font-size:24px; width:24px; height:24px; color:#475569; }
                        .xboard-card-header h2 { font-size:18px; font-weight:600; margin:0; color:#1e293b; }
                        .xboard-card-body { padding:24px; flex:1; display:flex; flex-direction:column; justify-content:space-between; }
                        .xboard-stat-row { display:flex; gap:16px; margin-bottom:20px; }
                        .xboard-stat { flex:1; background:#f8fafc; padding:16px; border-radius:8px; border:1px solid #e2e8f0; }
                        .xboard-number { font-size:24px; font-weight:700; color:#0f172a; margin-bottom:4px; }
                        .xboard-label { font-size:11px; color:#64748b; font-weight:600; text-transform:uppercase; letter-spacing:.05em; }
                        .xboard-top-file { font-size:13px; color:#475569; margin:0 0 20px 0; background:#f1f5f9; padding:10px 14px; border-radius:6px; }
                        .xboard-top-file code { font-family:monospace; color:#0f172a; font-size:12px; }
                        .xboard-quick-links { display:flex; flex-wrap:wrap; gap:10px; margin-top:auto; padding-top:15px; border-top:1px solid #f1f5f9; }
                        .xboard-btn { display:inline-flex; align-items:center; background:#fff; border:1px solid #cbd5e1; color:#334155; padding:8px 16px; font-size:13px; font-weight:600; border-radius:6px; text-decoration:none; transition:all .2s; }
                        .xboard-btn:hover { background:#f8fafc; border-color:#94a3b8; color:#0f172a; }
                    </style>
                    <div class="xboard-grid">

                        <!-- License MasterX Card -->
                        <?php if (!empty($lmx_stats)) : ?>
                        <div class="xboard-card">
                            <div class="xboard-card-header">
                                <span class="dashicons dashicons-admin-network"></span>
                                <h2>License MasterX</h2>
                            </div>
                            <div class="xboard-card-body">
                                <div>
                                    <div class="xboard-stat-row">
                                        <div class="xboard-stat">
                                            <div class="xboard-number"><?php echo number_format_i18n($lmx_stats['total_keys']); ?></div>
                                            <div class="xboard-label">License Keys</div>
                                        </div>
                                        <div class="xboard-stat">
                                            <div class="xboard-number"><?php echo number_format_i18n($lmx_stats['active_activations']); ?></div>
                                            <div class="xboard-label">Activations</div>
                                        </div>
                                    </div>
                                    <div class="xboard-stat-row">
                                        <div class="xboard-stat">
                                            <div class="xboard-number"><?php echo number_format_i18n($lmx_stats['total_combined_customers']); ?></div>
                                            <div class="xboard-label">Customers</div>
                                        </div>
                                        <div class="xboard-stat" style="opacity:.8;">
                                            <div class="xboard-number" style="font-size:20px;"><?php echo number_format_i18n($lmx_stats['blocked_ips']); ?></div>
                                            <div class="xboard-label">Blocked IPs</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="xboard-quick-links">
                                    <a href="<?php echo esc_url(admin_url('admin.php?page=licensemasterx51')); ?>" class="xboard-btn">License Settings</a>
                                    <a href="<?php echo esc_url(admin_url('admin.php?page=licensemasterx51-serial-keys')); ?>" class="xboard-btn">Serial Keys</a>
                                    <a href="<?php echo esc_url(admin_url('admin.php?page=licensemasterx51-store-entitlements')); ?>" class="xboard-btn">Store Customers</a>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- File Download ManagerX Card -->
                        <?php if (!empty($fdmx_stats)) : ?>
                        <div class="xboard-card">
                            <div class="xboard-card-header">
                                <span class="dashicons dashicons-download"></span>
                                <h2>File Download ManagerX</h2>
                            </div>
                            <div class="xboard-card-body">
                                <div>
                                    <div class="xboard-stat-row">
                                        <div class="xboard-stat">
                                            <div class="xboard-number"><?php echo number_format_i18n($fdmx_stats['total_files']); ?></div>
                                            <div class="xboard-label">Managed Files</div>
                                        </div>
                                        <div class="xboard-stat">
                                            <div class="xboard-number"><?php echo number_format_i18n($fdmx_stats['total_downloads']); ?></div>
                                            <div class="xboard-label">Downloads</div>
                                        </div>
                                    </div>
                                    <div class="xboard-top-file">
                                        <strong>Top File:</strong> <code><?php echo esc_html($fdmx_stats['top_file']); ?></code>
                                    </div>
                                </div>
                                <div class="xboard-quick-links">
                                    <a href="<?php echo esc_url(admin_url('admin.php?page=file-download-managerx')); ?>" class="xboard-btn">File Manager</a>
                                    <a href="<?php echo esc_url(admin_url('admin.php?page=file-download-managerx-stats')); ?>" class="xboard-btn">Download Stats</a>
                                    <a href="<?php echo esc_url(admin_url('admin.php?page=file-download-managerx-settings')); ?>" class="xboard-btn">Settings</a>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Post DuplicateX Card -->
                        <?php if (!empty($postdx_stats)) : ?>
                        <div class="xboard-card">
                            <div class="xboard-card-header">
                                <span class="dashicons dashicons-admin-page"></span>
                                <h2>Post DuplicateX</h2>
                            </div>
                            <div class="xboard-card-body">
                                <div>
                                    <div class="xboard-stat-row">
                                        <div class="xboard-stat">
                                            <div class="xboard-number"><?php echo number_format_i18n($postdx_stats['enabled_post_types']); ?></div>
                                            <div class="xboard-label">Enabled Post Types</div>
                                        </div>
                                    </div>
                                    <div class="xboard-top-file">
                                        <strong>Types:</strong> <code><?php echo esc_html($postdx_stats['post_types_list']); ?></code>
                                    </div>
                                </div>
                                <div class="xboard-quick-links">
                                    <a href="<?php echo esc_url(admin_url('admin.php?page=post-duplicatex')); ?>" class="xboard-btn">Settings</a>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Custom Login Page Designer Card -->
                        <?php if (!empty($clpd_stats)) : ?>
                        <div class="xboard-card">
                            <div class="xboard-card-header">
                                <span class="dashicons dashicons-admin-customizer"></span>
                                <h2>Login Page Designer</h2>
                            </div>
                            <div class="xboard-card-body">
                                <div>
                                    <div class="xboard-stat-row">
                                        <div class="xboard-stat">
                                            <div class="xboard-number" style="font-size:18px;word-break:break-word;"><?php echo esc_html($clpd_stats['active_template']); ?></div>
                                            <div class="xboard-label">Active Template</div>
                                        </div>
                                        <div class="xboard-stat">
                                            <div class="xboard-number" style="font-size:18px;"><?php echo esc_html($clpd_stats['bg_type']); ?></div>
                                            <div class="xboard-label">Background</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="xboard-quick-links">
                                    <a href="<?php echo esc_url(admin_url('admin.php?page=custom-login-page-designer')); ?>" class="xboard-btn">Customize Login Page</a>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
            <?php
        }
    }
}