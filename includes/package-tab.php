<?php
if (!defined('ABSPATH')) exit;

function bes_package_tab() {
    $defaults = [
        'company_name' => '',
        'logo' => '',
        'address' => '',
        'phone' => '',
        'email' => '',
        'footer_text' => '',
        'terms' => '',
        'fields_order' => ['logo','company_name','address','phone','email','footer_text','terms'],
    ];

    $settings = wp_parse_args(get_option('bes_package_settings', []), $defaults);
    ?>

    <div class="wrap">
        <h1><?php _e('Package / Invoice Settings','bes'); ?></h1>
        <p><?php _e('Customize your invoice fields below.','bes'); ?></p>

        <div id="bes-fields-container" style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-top:20px;">
            <?php foreach ($settings['fields_order'] as $field) : ?>
                <div class="bes-field-item" data-field="<?php echo esc_attr($field); ?>" style="background:#fff;border:1px solid #ddd;border-radius:8px;padding:15px;box-shadow:0 2px 6px rgba(0,0,0,0.05);">
                    <?php switch ($field) :
                        case 'company_name': ?>
                            <label for="bes_company_name"><?php _e('Company Name','bes'); ?></label>
                            <input type="text" id="bes_company_name" name="bes_package_settings[company_name]" value="<?php echo esc_attr($settings['company_name']); ?>" style="width:100%;padding:8px;border:1px solid #ccc;border-radius:4px;">
                        <?php break;

                        case 'logo': ?>
                            <label for="bes_package_logo"><?php _e('Logo','bes'); ?></label><br>
                            <input type="text" id="bes_package_logo" name="bes_package_settings[logo]" value="<?php echo esc_attr($settings['logo']); ?>" style="width:calc(100% - 90px);padding:8px;border:1px solid #ccc;border-radius:4px;display:inline-block;">
                            <button class="button" id="bes_upload_logo" style="margin-left:5px;"><?php _e('Upload','bes'); ?></button>
                            <?php if ($settings['logo']) : ?>
                                <img src="<?php echo esc_url($settings['logo']); ?>" style="max-width:100px;margin-top:10px;border-radius:4px;">
                            <?php endif; ?>
                        <?php break;

                        case 'address': ?>
                            <label for="bes_address"><?php _e('Address','bes'); ?></label>
                            <textarea id="bes_address" name="bes_package_settings[address]" style="width:100%;padding:8px;border:1px solid #ccc;border-radius:4px;"><?php echo esc_textarea($settings['address']); ?></textarea>
                        <?php break;

                        case 'phone': ?>
                            <label for="bes_phone"><?php _e('Phone','bes'); ?></label>
                            <input type="text" id="bes_phone" name="bes_package_settings[phone]" value="<?php echo esc_attr($settings['phone']); ?>" style="width:100%;padding:8px;border:1px solid #ccc;border-radius:4px;">
                        <?php break;

                        case 'email': ?>
                            <label for="bes_email"><?php _e('Email','bes'); ?></label>
                            <input type="text" id="bes_email" name="bes_package_settings[email]" value="<?php echo esc_attr($settings['email']); ?>" style="width:100%;padding:8px;border:1px solid #ccc;border-radius:4px;">
                        <?php break;

                        case 'footer_text': ?>
                            <label for="bes_footer_text"><?php _e('Footer Text','bes'); ?></label>
                            <textarea id="bes_footer_text" name="bes_package_settings[footer_text]" style="width:100%;padding:8px;border:1px solid #ccc;border-radius:4px;"><?php echo esc_textarea($settings['footer_text']); ?></textarea>
                        <?php break;

                        case 'terms': ?>
                            <label for="bes_terms"><?php _e('Terms & Conditions','bes'); ?></label>
                            <textarea id="bes_terms" name="bes_package_settings[terms]" style="width:100%;padding:8px;border:1px solid #ccc;border-radius:4px;"><?php echo esc_textarea($settings['terms']); ?></textarea>
                        <?php break;

                    endswitch; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <br>
        <?php submit_button(__('Save Package/Invoice Settings','bes')); ?>
    </div>

<?php
}
