<?php
if (!defined('ABSPATH')) exit;

function bes_package_tab() {
    // Get saved settings or use defaults
    $defaults = [
        'company_name' => '',
        'logo' => '',
        'address' => '',
        'phone' => '',
        'email' => '',
        'footer_text' => '',
        'template' => 'simple',
        'color' => '#4caf50',
        'terms' => '',
        'fields_order' => ['logo','company_name','address','phone','email','footer_text'],
    ];

    $settings = wp_parse_args(get_option('bes_package_settings', []), $defaults);

    echo '<h2>Package/Invoice Settings</h2>';
    echo '<p>Customize your invoice templates and reorder fields.</p>';

    // Fields container
    echo '<div id="bes-fields-container" style="max-width:600px;">';

    foreach ($settings['fields_order'] as $field) {
        echo '<div class="bes-field-item" data-field="'.$field.'" style="border:1px solid #ddd;padding:10px;margin-bottom:5px;background:#fafafa;cursor:move;">';
        switch ($field) {
            case 'company_name':
                echo '<label>Company Name:<br><input type="text" name="bes_package_settings[company_name]" value="'.esc_attr($settings['company_name']).'" style="width:100%;padding:5px;"></label>';
                break;
            case 'logo':
                echo '<label>Logo:<br>';
                echo '<input type="text" id="bes_package_logo" name="bes_package_settings[logo]" value="'.esc_attr($settings['logo']).'" style="width:80%;padding:5px;"> ';
                echo '<button class="button" id="bes_upload_logo">Upload</button>';
                echo '</label>';
                if($settings['logo']) echo '<img src="'.esc_url($settings['logo']).'" id="bes_logo_preview" style="max-width:120px;margin-top:5px;">';
                break;
            case 'address':
                echo '<label>Address:<br><textarea name="bes_package_settings[address]" style="width:100%;padding:5px;">'.esc_textarea($settings['address']).'</textarea></label>';
                break;
            case 'phone':
                echo '<label>Phone:<br><input type="text" name="bes_package_settings[phone]" value="'.esc_attr($settings['phone']).'" style="width:100%;padding:5px;"></label>';
                break;
            case 'email':
                echo '<label>Email:<br><input type="text" name="bes_package_settings[email]" value="'.esc_attr($settings['email']).'" style="width:100%;padding:5px;"></label>';
                break;
            case 'footer_text':
                echo '<label>Footer Text:<br><textarea name="bes_package_settings[footer_text]" style="width:100%;padding:5px;">'.esc_textarea($settings['footer_text']).'</textarea></label>';
                break;
        }
        echo '</div>';
    }
    echo '</div><br>';

    // Template selector
    $templates = ['simple'=>'Simple','detailed'=>'Detailed','compact'=>'Compact'];
    echo '<label>Invoice Template:<br><select name="bes_package_settings[template]" style="padding:5px;">';
    foreach($templates as $key=>$label){
        echo '<option value="'.esc_attr($key).'" '.selected($settings['template'],$key,false).'>'.$label.'</option>';
    }
    echo '</select></label><br><br>';

    // Color Picker
    echo '<label>Primary Color:<br><input type="text" name="bes_package_settings[color]" value="'.esc_attr($settings['color']).'" class="bes-color-picker" style="width:100px;padding:5px;"></label><br><br>';

    // Terms
    echo '<label>Terms & Conditions:<br><textarea name="bes_package_settings[terms]" style="width:100%;height:80px;padding:5px;">'.esc_textarea($settings['terms']).'</textarea></label><br><br>';

    // Live preview
    echo '<h3>Live Preview:</h3>';
    echo '<div id="bes-preview" style="border:1px solid #ddd;padding:15px;background:#fafafa;max-width:600px;">';
    if($settings['logo']) echo '<img src="'.esc_url($settings['logo']).'" style="max-width:120px;"><br>';
    echo '<h2 style="color:'.esc_attr($settings['color']).'">'.esc_html($settings['company_name']).'</h2>';
    echo '<p>'.nl2br(esc_html($settings['address'])).'</p>';
    echo '<p>Phone: '.esc_html($settings['phone']).' | Email: '.esc_html($settings['email']).'</p>';
    echo '<hr><p style="font-size:12px;">'.esc_html($settings['footer_text']).'</p>';
    echo '</div>';

    submit_button('Save Package/Invoice Settings');

    ?>
    <script>
    jQuery(document).ready(function($){
        $('#bes-fields-container').sortable({ placeholder: "ui-state-highlight" });

        // Logo upload
        var file_frame;
        $('#bes_upload_logo').on('click', function(e){
            e.preventDefault();
            if(file_frame){ file_frame.open(); return; }
            file_frame = wp.media.frames.file_frame = wp.media({
                title: 'Select or Upload Logo',
                button: { text: 'Use this logo' },
                multiple: false
            });
            file_frame.on('select', function(){
                var attachment = file_frame.state().get('selection').first().toJSON();
                $('#bes_package_logo').val(attachment.url);
                $('#bes_logo_preview').attr('src', attachment.url);
            });
            file_frame.open();
        });

        // Color Picker
        $('.bes-color-picker').wpColorPicker();

        // Live preview
        $('#bes-fields-container input, #bes-fields-container textarea, #bes-fields-container select').on('input change', function(){
            var logo = $('#bes_package_logo').val();
            $('#bes-preview').find('img').attr('src', logo);
            $('#bes-preview h2').text($('input[name="bes_package_settings[company_name]"]').val());
            $('#bes-preview p').first().html($('textarea[name="bes_package_settings[address]"]').val().replace(/\n/g,'<br>'));
            $('#bes-preview p').eq(1).text('Phone: '+$('input[name="bes_package_settings[phone]"]').val()+' | Email: '+$('input[name="bes_package_settings[email]"]').val());
            $('#bes-preview hr').next('p').text($('textarea[name="bes_package_settings[footer_text]"]').val());
        });
    });
    </script>
    <style>
        .bes-field-item { border:1px solid #ddd; padding:10px; margin-bottom:5px; background:#fafafa; cursor:move; }
    </style>
    <?php
}

// Enqueue scripts
add_action('admin_enqueue_scripts', function($hook){
    if(strpos($hook,'bes-settings')!==false){
        wp_enqueue_media();
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');
        wp_enqueue_script('jquery-ui-sortable');
    }
});
