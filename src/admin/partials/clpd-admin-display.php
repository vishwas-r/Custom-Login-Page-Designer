<?php
/**
 * The admin options page display
 *
 * @since      1.0.0
 * @package    CLPD51
 * @subpackage CLPD51/admin/partials
 */
// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    exit;
}
// Get current settings
$settings = $this->settings;
// Get design templates
$design_templates = array(
    'minimal-white' => __('Minimal White', 'custom-login-page-designer'),
    'corporate-professional' => __('Corporate Professional', 'custom-login-page-designer'),
    'modern-tech' => __('Modern Tech', 'custom-login-page-designer'),
    'glassmorphism' => __('Glassmorphism', 'custom-login-page-designer'),
    'dark-gradient' => __('Dark Gradient', 'custom-login-page-designer'),
    'nature-inspired' => __('Nature Inspired', 'custom-login-page-designer'),
    'neomorphic-modern' => __('Neomorphic Modern', 'custom-login-page-designer'),
    'blueprint-professional' => __('Blueprint Professional', 'custom-login-page-designer'),
);
// Get font families
$font_families = array(
    'Arial, sans-serif' => __('Arial', 'custom-login-page-designer'),
    'Helvetica, sans-serif' => __('Helvetica', 'custom-login-page-designer'),
    'Georgia, serif' => __('Georgia', 'custom-login-page-designer'),
    'Tahoma, sans-serif' => __('Tahoma', 'custom-login-page-designer'),
    'Verdana, sans-serif' => __('Verdana', 'custom-login-page-designer'),
    'Times New Roman, serif' => __('Times New Roman', 'custom-login-page-designer'),
);

// Template settings
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
);

// Get the selected template
$design_template = isset($settings['design_template']) ? $settings['design_template'] : 'minimal-white';

// Use template settings if available, otherwise use defaults
$current_template = isset($clpd_template_settings[$design_template]) ? $clpd_template_settings[$design_template] : $clpd_template_settings['minimal-white'];

// Get settings or use defaults from the template
$background_type = isset($settings['background_type']) ? $settings['background_type'] : $current_template['background_type'];
$background_color = isset($settings['background_color']) ? $settings['background_color'] : $current_template['background_color'];
$background_gradient_start = isset($settings['background_gradient_start']) ? $settings['background_gradient_start'] : $current_template['background_gradient_start'];
$background_gradient_end = isset($settings['background_gradient_end']) ? $settings['background_gradient_end'] : $current_template['background_gradient_end'];
$background_image = isset($settings['background_image']) ? $settings['background_image'] : $current_template['background_image'];
$logo_image = isset($settings['logo_image']) ? $settings['logo_image'] : '';
$logo_image_width = isset($settings['logo_image_width']) ? $settings['logo_image_width'] : $current_template['logo_image_width'];
$logo_image_height = isset($settings['logo_image_height']) ? $settings['logo_image_height'] : $current_template['logo_image_height'];
$logo_image_radius = isset($settings['logo_image_radius']) ? $settings['logo_image_radius'] : $current_template['logo_image_radius'];
$text_color = isset($settings['text_color']) ? $settings['text_color'] : $current_template['text_color'];
$text_font_family = isset($settings['text_font_family']) ? $settings['text_font_family'] : $current_template['text_font_family'];
$button_text_color = isset($settings['button_text_color']) ? $settings['button_text_color'] : $current_template['button_text_color'];
$button_bg_color = isset($settings['button_bg_color']) ? $settings['button_bg_color'] : $current_template['button_bg_color'];
$button_hover_bg_color = isset($settings['button_hover_bg_color']) ? $settings['button_hover_bg_color'] : $current_template['button_hover_bg_color'];
$button_font_family = isset($settings['button_font_family']) ? $settings['button_font_family'] : $current_template['button_font_family'];

// Default settings for restore functionality
$default_settings = $clpd_template_settings['minimal-white'];
$default_settings['design_template'] = 'minimal-white';
$default_settings['logo_image'] = '';

// Handle restore default settings
if (isset($_POST['clpd_restore_defaults']) && check_admin_referer('clpd_settings_nonce', 'clpd_settings_nonce')) {
    update_option('clpd_settings', $default_settings);
    
    // Show admin notice
    add_action('admin_notices', function() {
        echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__('Settings restored to defaults.', 'custom-login-page-designer') . '</p></div>';
    });
    
    // Refresh the settings
    $settings = $default_settings;
    $design_template = $default_settings['design_template'];
    $background_type = $default_settings['background_type'];
    $background_color = $default_settings['background_color'];
    $background_gradient_start = $default_settings['background_gradient_start'];
    $background_gradient_end = $default_settings['background_gradient_end'];
    $background_image = $default_settings['background_image'];
    $logo_image = $default_settings['logo_image'];
    $logo_image_width = $default_settings['logo_image_width'];
    $logo_image_height = $default_settings['logo_image_height'];
    $logo_image_radius = $default_settings['logo_image_radius'];
    $text_color = $default_settings['text_color'];
    $text_font_family = $default_settings['text_font_family'];
    $button_text_color = $default_settings['button_text_color'];
    $button_bg_color = $default_settings['button_bg_color'];
    $button_hover_bg_color = $default_settings['button_hover_bg_color'];
    $button_font_family = $default_settings['button_font_family'];
}
?>
<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    
    <div class="clpd-admin-container">
        <div class="clpd-admin-main">
            <form method="post" action="">
                <?php wp_nonce_field('clpd_settings_nonce', 'clpd_settings_nonce'); ?>
                
                <div class="clpd-admin-section">
                    <h2><?php esc_html_e('Design Template', 'custom-login-page-designer'); ?></h2>
                    <table class="form-table">
                        <tr>
                            <th scope="row">
                                <label for="design_template"><?php esc_html_e('Select Template', 'custom-login-page-designer'); ?></label>
                            </th>
                            <td>
                                <div class="clpd-template-selector">
                                    <?php foreach ($design_templates as $value => $label) : ?>
                                        <label class="clpd-template-option <?php echo ($value === $design_template) ? 'selected' : ''; ?>">
                                            <input type="radio" name="design_template" value="<?php echo esc_attr($value); ?>" 
                                                   <?php checked($design_template, $value); ?>
                                                   class="clpd-template-select"
                                                   data-template="<?php echo esc_attr($value); ?>">
                                            <div class="clpd-template-preview">
                                                <?php 
                                                $template_img_id = attachment_url_to_postid(CLPD_PLUGIN_URL . 'admin/images/' . $value . '-preview.png');
                                                if ($template_img_id) {
                                                    echo wp_get_attachment_image($template_img_id, 'medium', false, array('alt' => esc_attr($label)));
                                                } else {
                                                    // Fallback if the image isn't in the media library
                                                    $img_url = CLPD_PLUGIN_URL . 'admin/images/' . $value . '-preview.png';
                                                    echo '<img src="' . esc_url($img_url) . '" alt="' . esc_attr($label) . '">';
                                                }
                                                ?>
                                            </div>
                                            <span><?php echo esc_html($label); ?></span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
                
                <div class="clpd-admin-section">
                    <h2><?php esc_html_e('Background Settings', 'custom-login-page-designer'); ?></h2>
                    <table class="form-table">
                        <tr>
                            <th scope="row">
                                <label for="background_type"><?php esc_html_e('Background Type', 'custom-login-page-designer'); ?></label>
                            </th>
                            <td>
                                <select name="background_type" id="background_type" class="clpd-background-type-select">
                                    <option value="color" <?php selected($background_type, 'color'); ?>><?php esc_html_e('Solid Color', 'custom-login-page-designer'); ?></option>
                                    <option value="gradient" <?php selected($background_type, 'gradient'); ?>><?php esc_html_e('Gradient', 'custom-login-page-designer'); ?></option>
                                    <option value="image" <?php selected($background_type, 'image'); ?>><?php esc_html_e('Image', 'custom-login-page-designer'); ?></option>
                                </select>
                            </td>
                        </tr>
                        
                        <tr class="clpd-background-option clpd-background-color" <?php echo ($background_type !== 'color') ? 'style="display:none;"' : ''; ?>>
                            <th scope="row">
                                <label for="background_color"><?php esc_html_e('Background Color', 'custom-login-page-designer'); ?></label>
                            </th>
                            <td>
                                <input type="text" name="background_color" id="background_color" class="clpd-color-picker" value="<?php echo esc_attr($background_color); ?>">
                            </td>
                        </tr>
                        
                        <tr class="clpd-background-option clpd-background-gradient" <?php echo ($background_type !== 'gradient') ? 'style="display:none;"' : ''; ?>>
                            <th scope="row">
                                <label for="background_gradient_start"><?php esc_html_e('Gradient Start Color', 'custom-login-page-designer'); ?></label>
                            </th>
                            <td>
                                <input type="text" name="background_gradient_start" id="background_gradient_start" class="clpd-color-picker" value="<?php echo esc_attr($background_gradient_start); ?>">
                            </td>
                        </tr>
                        
                        <tr class="clpd-background-option clpd-background-gradient" <?php echo ($background_type !== 'gradient') ? 'style="display:none;"' : ''; ?>>
                            <th scope="row">
                                <label for="background_gradient_end"><?php esc_html_e('Gradient End Color', 'custom-login-page-designer'); ?></label>
                            </th>
                            <td>
                                <input type="text" name="background_gradient_end" id="background_gradient_end" class="clpd-color-picker" value="<?php echo esc_attr($background_gradient_end); ?>">
                            </td>
                        </tr>
                        
                        <tr class="clpd-background-option clpd-background-image" <?php echo ($background_type !== 'image') ? 'style="display:none;"' : ''; ?>>
                            <th scope="row">
                                <label for="background_image"><?php esc_html_e('Background Image', 'custom-login-page-designer'); ?></label>
                            </th>
                            <td>
                                <div class="clpd-image-uploader">
                                    <div class="clpd-image-preview">
                                        <?php if (!empty($background_image)) : 
                                            $bg_img_id = attachment_url_to_postid($background_image);
                                            if ($bg_img_id) {
                                                echo wp_get_attachment_image($bg_img_id, 'thumbnail', false, array('alt' => esc_attr__('Background Preview', 'custom-login-page-designer')));
                                            } else {
                                                echo '<img src="' . esc_url($background_image) . '" alt="' . esc_attr__('Background Preview', 'custom-login-page-designer') . '">';
                                            }
                                        endif; ?>
                                    </div>
                                    <input type="hidden" name="background_image" id="background_image" value="<?php echo esc_attr($background_image); ?>">
                                    <button type="button" class="button clpd-image-upload-button" data-target="background_image">
                                        <?php esc_html_e('Select Image', 'custom-login-page-designer'); ?>
                                    </button>
                                    <?php if (!empty($background_image)) : ?>
                                        <button type="button" class="button clpd-image-remove-button" data-target="background_image">
                                            <?php esc_html_e('Remove Image', 'custom-login-page-designer'); ?>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
                
                <div class="clpd-admin-section">
                    <h2><?php esc_html_e('Logo Settings', 'custom-login-page-designer'); ?></h2>
                    <table class="form-table">
                        <tr>
                            <th scope="row">
                                <label for="logo_image"><?php esc_html_e('Logo Image', 'custom-login-page-designer'); ?></label>
                            </th>
                            <td>
                                <div class="clpd-image-uploader">
                                    <div class="clpd-image-preview">
                                        <?php if (!empty($logo_image)) : 
                                            $logo_img_id = attachment_url_to_postid($logo_image);
                                            if ($logo_img_id) {
                                                echo wp_get_attachment_image($logo_img_id, 'thumbnail', false, array('alt' => esc_attr__('Logo Preview', 'custom-login-page-designer')));
                                            } else {
                                                echo '<img src="' . esc_url($logo_image) . '" alt="' . esc_attr__('Logo Preview', 'custom-login-page-designer') . '">';
                                            }
                                        endif; ?>
                                    </div>
                                    <input type="hidden" name="logo_image" id="logo_image" value="<?php echo esc_attr($logo_image); ?>">
                                    <button type="button" class="button clpd-image-upload-button" data-target="logo_image">
                                        <?php esc_html_e('Select Logo', 'custom-login-page-designer'); ?>
                                    </button>
                                    <?php if (!empty($logo_image)) : ?>
                                        <button type="button" class="button clpd-image-remove-button" data-target="logo_image">
                                            <?php esc_html_e('Remove Logo', 'custom-login-page-designer'); ?>
                                        </button>
                                    <?php endif; ?>
                                </div>
                                <p class="description"><?php esc_html_e('Upload a logo to replace the WordPress logo on the login page.', 'custom-login-page-designer'); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="logo_image_width"><?php esc_html_e('Logo Image Width', 'custom-login-page-designer'); ?></label>
                            </th>
                            <td>
                                <input type="text" name="logo_image_width" id="logo_image_width" class="clpd-logo-image" value="<?php echo esc_attr($logo_image_width); ?>">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="logo_image_height"><?php esc_html_e('Logo Image Height', 'custom-login-page-designer'); ?></label>
                            </th>
                            <td>
                                <input type="text" name="logo_image_height" id="logo_image_height" class="clpd-logo-image" value="<?php echo esc_attr($logo_image_height); ?>">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="logo_image_radius"><?php esc_html_e('Logo Image Border Radius', 'custom-login-page-designer'); ?></label>
                            </th>
                            <td>
                                <input type="text" name="logo_image_radius" id="logo_image_radius" class="clpd-logo-image" value="<?php echo esc_attr($logo_image_radius); ?>">
                            </td>
                        </tr>
                    </table>
                </div>
                
                <div class="clpd-admin-section">
                    <h2><?php esc_html_e('Text Settings', 'custom-login-page-designer'); ?></h2>
                    <table class="form-table">
                        <tr>
                            <th scope="row">
                                <label for="text_color"><?php esc_html_e('Text Color', 'custom-login-page-designer'); ?></label>
                            </th>
                            <td>
                                <input type="text" name="text_color" id="text_color" class="clpd-color-picker" value="<?php echo esc_attr($text_color); ?>">
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="text_font_family"><?php esc_html_e('Font Family', 'custom-login-page-designer'); ?></label>
                            </th>
                            <td>
                                <select name="text_font_family" id="text_font_family">
                                    <?php foreach ($font_families as $value => $label) : ?>
                                        <option value="<?php echo esc_attr($value); ?>" <?php selected($text_font_family, $value); ?>><?php echo esc_html($label); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                        </tr>
                    </table>
                </div>
                
                <div class="clpd-admin-section">
                    <h2><?php esc_html_e('Button Settings', 'custom-login-page-designer'); ?></h2>
                    <table class="form-table">
                        <tr>
                            <th scope="row">
                                <label for="button_text_color"><?php esc_html_e('Button Text Color', 'custom-login-page-designer'); ?></label>
                            </th>
                            <td>
                                <input type="text" name="button_text_color" id="button_text_color" class="clpd-color-picker" value="<?php echo esc_attr($button_text_color); ?>">
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="button_bg_color"><?php esc_html_e('Button Background Color', 'custom-login-page-designer'); ?></label>
                            </th>
                            <td>
                                <input type="text" name="button_bg_color" id="button_bg_color" class="clpd-color-picker" value="<?php echo esc_attr($button_bg_color); ?>">
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="button_hover_bg_color"><?php esc_html_e('Button Hover Background Color', 'custom-login-page-designer'); ?></label>
                            </th>
                            <td>
                                <input type="text" name="button_hover_bg_color" id="button_hover_bg_color" class="clpd-color-picker" value="<?php echo esc_attr($button_hover_bg_color); ?>">
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="button_font_family"><?php esc_html_e('Button Font Family', 'custom-login-page-designer'); ?></label>
                            </th>
                            <td>
                                <select name="button_font_family" id="button_font_family">
                                    <?php foreach ($font_families as $value => $label) : ?>
                                        <option value="<?php echo esc_attr($value); ?>" <?php selected($button_font_family, $value); ?>><?php echo esc_html($label); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                        </tr>
                    </table>
                </div>
                
                <div class="clpd-submit-buttons">
                    <p class="submit">
                        <input type="submit" name="clpd_settings_submit" class="button button-primary" value="<?php esc_html_e('Save Settings', 'custom-login-page-designer'); ?>">
                        <input type="submit" name="clpd_restore_defaults" class="button" value="<?php esc_html_e('Restore Default Settings', 'custom-login-page-designer'); ?>" onclick="return confirm('<?php esc_html_e('Are you sure you want to restore all settings to default values?', 'custom-login-page-designer'); ?>')">
                        <!-- <a href="<?php //echo esc_url(wp_login_url() . '?preview=1'); ?>" target="_blank" class="button"><?php //esc_html_e('Preview Login Page', 'custom-login-page-designer'); ?></a> -->
                    </p>
                </div>
            </form>
        </div>
        
        <div class="clpd-admin-sidebar">
            <div class="clpd-sidebar-widget">
                <h3><?php esc_html_e('About This Plugin', 'custom-login-page-designer'); ?></h3>
                <p><?php esc_html_e('Custom Login Page Designer allows you to customize your WordPress login page with predefined designs and various customization options.', 'custom-login-page-designer'); ?></p>
            </div>
            
            <div class="clpd-sidebar-widget">
                <h3><?php esc_html_e('Need Help?', 'custom-login-page-designer'); ?></h3>
                <ul>
                    <li><a href="#"><?php esc_html_e('Documentation', 'custom-login-page-designer'); ?></a></li>
                    <li><a href="#"><?php esc_html_e('Support', 'custom-login-page-designer'); ?></a></li>
                    <li><a href="#"><?php esc_html_e('Rate this Plugin', 'custom-login-page-designer'); ?></a></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="footer">
        Made with  <span class="heart">❤️</span>  by <a href="https://vishwas.me" target="_blank">Vishwas R</a>
    </div>
</div>