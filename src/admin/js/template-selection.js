jQuery(document).ready(function($) {
    // Template settings object from PHP localized script
    const templateSettings = clpdData.templateSettings;
    
    // Template selection handler
    $('.clpd-template-select').on('change', function() {
        const selectedTemplate = $(this).data('template');
        
        if (templateSettings[selectedTemplate]) {
            const settings = templateSettings[selectedTemplate];
            
            // Update background type and show/hide relevant options
            $('#background_type').val(settings.background_type).trigger('change');
            
            // Update color pickers
            $('#background_color').val(settings.background_color).trigger('change');
            $('#background_gradient_start').val(settings.background_gradient_start).trigger('change');
            $('#background_gradient_end').val(settings.background_gradient_end).trigger('change');
            
            // Update logo dimensions
            $('#logo_image_width').val(settings.logo_image_width);
            $('#logo_image_height').val(settings.logo_image_height);
            $('#logo_image_radius').val(settings.logo_image_radius);
            
            // Update text settings
            $('#text_color').val(settings.text_color).trigger('change');
            $('#text_font_family').val(settings.text_font_family);
            
            // Update button settings
            $('#button_text_color').val(settings.button_text_color).trigger('change');
            $('#button_bg_color').val(settings.button_bg_color).trigger('change');
            $('#button_hover_bg_color').val(settings.button_hover_bg_color).trigger('change');
            $('#button_font_family').val(settings.button_font_family);
        }
    });
    
    // Background type change handler to show/hide relevant options
    $('#background_type').on('change', function() {
        const type = $(this).val();
        $('.clpd-background-option').hide();
        $('.clpd-background-' + type).show();
    });
    
    // Media uploader for background and logo images
    $('.clpd-image-upload-button').on('click', function(e) {
        e.preventDefault();
        
        const button = $(this);
        const targetField = button.data('target');
        
        const mediaUploader = wp.media({
            title: targetField === 'logo_image' ? clpdData.logoText : clpdData.bgImageText,
            button: {
                text: clpdData.useImageText
            },
            multiple: false
        });
        
        mediaUploader.on('select', function() {
            const attachment = mediaUploader.state().get('selection').first().toJSON();
            $('#' + targetField).val(attachment.url);
            button.closest('.clpd-image-uploader').find('.clpd-image-preview')
                .html('<img src="' + attachment.url + '" alt="Preview">');
            
            // Show remove button
            if (button.siblings('.clpd-image-remove-button').length === 0) {
                button.after('<button type="button" class="button clpd-image-remove-button" data-target="' + targetField + '">' + 
                    clpdData.removeImageText + '</button>');
            } else {
                button.siblings('.clpd-image-remove-button').show();
            }
        });
        
        mediaUploader.open();
    });
    
    // Remove image handler
    $(document).on('click', '.clpd-image-remove-button', function() {
        const button = $(this);
        const targetField = button.data('target');
        
        $('#' + targetField).val('');
        button.closest('.clpd-image-uploader').find('.clpd-image-preview').empty();
        button.hide();
    });
});