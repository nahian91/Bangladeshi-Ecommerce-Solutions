<?php
if (!defined('ABSPATH')) exit;

function bes_checkout_tab() {
    $checkout = get_option('bes_checkout_fields', []);
    $titles = wp_parse_args(get_option('bes_checkout_titles', []), [
        'page_title' => 'Checkout',
        'billing_title' => 'Billing Details',
        'shipping_title' => 'Shipping Details',
        'order_notes_title' => 'Order Notes',
        'place_order_button' => 'Place Order',
    ]);
    $additional = wp_parse_args(get_option('bes_checkout_additional', []), [
        'show_terms' => true,
        'terms_text' => 'I have read and agree to the terms and conditions.',
        'show_login_reminder' => true,
        'custom_css' => ''
    ]);

    settings_fields('bes_checkout_group');
    do_settings_sections('bes_checkout_group');
    ?>

    <h2>Checkout Fields & Titles</h2>
    <p>Manage fields, titles, and checkout page settings.</p>
    <div class="bes-checkout-wrapper">

        <!-- Checkout Fields -->
        <div class="bes-section">
            <h3>Fields Settings</h3>
            <p>Click a card to expand. Drag to reorder fields.</p>
            <div id="bes-checkout-fields">
                <?php foreach ($checkout as $group_name => $group_fields) : ?>
                    <div class="bes-field-card">
                        <div class="bes-card-header"><strong><?php echo esc_html($group_name); ?></strong> <span class="dashicons dashicons-arrow-down"></span></div>
                        <div class="bes-card-body">
                            <ul class="bes-fields-group" data-group="<?php echo esc_attr($group_name); ?>">
                                <?php foreach ($group_fields as $key => $field) :
                                    $enabled = $field['enabled'] ?? true;
                                    $label = $field['label'] ?? '';
                                    $required = $field['required'] ?? true;
                                    $placeholder = $field['placeholder'] ?? '';
                                    $description = $field['description'] ?? '';
                                ?>
                                    <li class="bes-field-item">
                                        <label class="switch">
                                            <input type="checkbox" name="bes_checkout_fields[<?php echo $group_name; ?>][<?php echo $key; ?>][enabled]" <?php checked($enabled,true); ?>>
                                            <span class="slider round"></span>
                                        </label>
                                        <input type="text" name="bes_checkout_fields[<?php echo $group_name; ?>][<?php echo $key; ?>][label]" value="<?php echo esc_attr($label); ?>" placeholder="Label" style="flex:1;">
                                        <label>
                                            <input type="checkbox" name="bes_checkout_fields[<?php echo $group_name; ?>][<?php echo $key; ?>][required]" <?php checked($required,true); ?>> Required
                                        </label>
                                        <input type="text" name="bes_checkout_fields[<?php echo $group_name; ?>][<?php echo $key; ?>][placeholder]" value="<?php echo esc_attr($placeholder); ?>" placeholder="Placeholder">
                                        <input type="text" name="bes_checkout_fields[<?php echo $group_name; ?>][<?php echo $key; ?>][description]" value="<?php echo esc_attr($description); ?>" placeholder="Description">
                                        <span class="dashicons dashicons-move"></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Titles Settings -->
        <div class="bes-section">
            <h3>Checkout Page Titles</h3>
            <div class="bes-title-field">
                <?php foreach ($titles as $key => $value) :
                    $label = ucwords(str_replace('_',' ',$key));
                ?>
                    <label><?php echo $label; ?>:
                        <input type="text" name="bes_checkout_titles[<?php echo $key; ?>]" value="<?php echo esc_attr($value); ?>" class="bes-title-input" data-preview="<?php echo esc_attr($key); ?>">
                    </label><br>
                <?php endforeach; ?>

                <div class="bes-preview">
                    <p><strong>Live Preview:</strong></p>
                    <h2 id="preview_page_title"><?php echo esc_html($titles['page_title']); ?></h2>
                    <h3 id="preview_billing_title"><?php echo esc_html($titles['billing_title']); ?></h3>
                    <h3 id="preview_shipping_title"><?php echo esc_html($titles['shipping_title']); ?></h3>
                    <h3 id="preview_order_notes_title"><?php echo esc_html($titles['order_notes_title']); ?></h3>
                    <button class="button button-primary" id="preview_place_order"><?php echo esc_html($titles['place_order_button']); ?></button>
                </div>
            </div>
        </div>

        <!-- Additional Settings -->
        <div class="bes-section">
            <h3>Additional Settings</h3>
            <label class="switch">
                <input type="checkbox" name="bes_checkout_additional[show_terms]" <?php checked($additional['show_terms'],true); ?>>
                <span class="slider round"></span> Show Terms & Conditions
            </label><br>
            <textarea name="bes_checkout_additional[terms_text]" placeholder="Terms & Conditions text" style="width:100%;height:60px;"><?php echo esc_textarea($additional['terms_text']); ?></textarea><br>

            <label class="switch">
                <input type="checkbox" name="bes_checkout_additional[show_login_reminder]" <?php checked($additional['show_login_reminder'],true); ?>>
                <span class="slider round"></span> Show Login Reminder
            </label><br>

            <label>Custom CSS (Optional):</label><br>
            <textarea name="bes_checkout_additional[custom_css]" placeholder="Custom CSS" style="width:100%;height:80px;"><?php echo esc_textarea($additional['custom_css']); ?></textarea>
        </div>

        <?php submit_button('Save Checkout Settings'); ?>
    </div>
<?php
}
