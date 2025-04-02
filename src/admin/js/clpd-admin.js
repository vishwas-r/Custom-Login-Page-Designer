/**
 * Custom Login Page Designer Admin JavaScript
 *
 * @package    Custom_Login_Page_Designer
 * @subpackage Custom_Login_Page_Designer/admin/js
 */

(function($) {
    'use strict';

    $(document).ready(function() {
        // Initialize color pickers
        $('.clpd-color-picker').wpColorPicker();
        
        // Background type switcher
        $('.clpd-background-type-select').on('change', function() {
            var selectedType = $(this).val();
            $('.clpd-background-option').hide();
            $('.clpd-background-' + selectedType).show();
        });
        
        // Media uploader for background image
        $('.clpd-image-upload-button').on('click', function(e) {
            e.preventDefault();
            
            var button = $(this);
            var targetId = button.data('target');
            var imagePreviewContainer = button.siblings('.clpd-image-preview');
            var hiddenInput = $('#' + targetId);
            
            // Create media frame
            var frame = wp.media({
                title: clpd_admin_vars.i18n.select_image,
                multiple: false,
                library: {
                    type: 'image'
                },
                button: {
                    text: clpd_admin_vars.i18n.use_image
                }
            });
            
            // Handle selection
            frame.on('select', function() {
                var attachment = frame.state().get('selection').first().toJSON();
                
                // Update hidden input with image URL
                hiddenInput.val(attachment.url);
                
                // Update preview
                imagePreviewContainer.html('<img src="' + attachment.url + '" alt="Preview">');
                
                // Show remove button if it doesn't exist
                if (button.siblings('.clpd-image-remove-button').length === 0) {
                    button.after('<button type="button" class="button clpd-image-remove-button" data-target="' + targetId + '">' + clpd_admin_vars.i18n.remove_image + '</button>');
                }
            });
            
            // Open media frame
            frame.open();
        });
        
        // Remove image button
        $(document).on('click', '.clpd-image-remove-button', function(e) {
            e.preventDefault();
            
            var button = $(this);
            var targetId = button.data('target');
            var hiddenInput = $('#' + targetId);
            
            // Clear input and preview
            hiddenInput.val('');
            button.siblings('.clpd-image-preview').empty();
            
            // Remove this button
            button.remove();
        });
        
        // Template selector
        $('.clpd-template-option input').on('change', function() {
            $('.clpd-template-option').removeClass('selected');
            $(this).closest('.clpd-template-option').addClass('selected');
        });
    });
    
})(jQuery);