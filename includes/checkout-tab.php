<?php
if (!defined('ABSPATH')) exit;

function bcaw_checkout_tab() {

    // Get options with defaults
    $checkout = get_option('bcaw_checkout_fields', []);
    if (!is_array($checkout)) $checkout = [];

    $titles = wp_parse_args(get_option('bcaw_checkout_titles', []), [
        'page_title'        => __('Checkout', 'banglacommerce-all-in-one-woocommerce'),
        'billing_title'     => __('Billing Details', 'banglacommerce-all-in-one-woocommerce'),
        'shipping_title'    => __('Shipping Details', 'banglacommerce-all-in-one-woocommerce'),
        'order_notes_title' => __('Order Notes', 'banglacommerce-all-in-one-woocommerce'),
        'place_order_button'=> __('Place Order', 'banglacommerce-all-in-one-woocommerce'),
    ]);

    $additional = wp_parse_args(get_option('bcaw_checkout_additional', []), [
        'show_terms'          => true,
        'terms_text'          => __('I have read and agree to the terms and conditions.', 'banglacommerce-all-in-one-woocommerce'),
        'show_login_reminder' => true,
        'custom_css'          => ''
    ]);

    settings_fields('bcaw_checkout_group');
    do_settings_sections('bcaw_checkout_group');
    ?>

    <h2><?php echo esc_html__('Checkout Fields & Titles', 'banglacommerce-all-in-one-woocommerce'); ?></h2>
    <p><?php echo esc_html__('Manage fields, titles, order, and additional checkout settings.', 'banglacommerce-all-in-one-woocommerce'); ?></p>

    <div class="bcaw-checkout-wrapper">

        <!-- Fields Section -->
        <div class="bcaw-section">
            <h3><?php echo esc_html__('Fields Settings', 'banglacommerce-all-in-one-woocommerce'); ?></h3>
            <p><?php echo esc_html__('Click a card to expand. Drag to reorder fields. You can disable or rename them.', 'banglacommerce-all-in-one-woocommerce'); ?></p>

            <div id="bcaw-checkout-fields">
                <?php foreach ($checkout as $group_name => $group_fields) : 
                    if (!is_array($group_fields)) continue;
                ?>
                    <div class="bcaw-field-card">
                        <div class="bcaw-card-header"><strong><?php echo esc_html($group_name); ?></strong> <span class="dashicons dashicons-arrow-down"></span></div>
                        <div class="bcaw-card-body">
                            <ul class="bcaw-fields-group" data-group="<?php echo esc_attr($group_name); ?>">
                                <?php foreach ($group_fields as $key => $field) :
                                    $enabled     = $field['enabled'] ?? true;
                                    $label       = $field['label'] ?? '';
                                    $required    = $field['required'] ?? true;
                                    $placeholder = $field['placeholder'] ?? '';
                                    $description = $field['description'] ?? '';
                                ?>
                                    <li class="bcaw-field-item">
                                        <label class="bcaw-switch">
                                            <input type="checkbox" name="bcaw_checkout_fields[<?php echo esc_attr($group_name); ?>][<?php echo esc_attr($key); ?>][enabled]" <?php checked($enabled,true); ?>>
                                            <span class="bcaw-slider round"></span>
                                        </label>
                                        <input type="text" name="bcaw_checkout_fields[<?php echo esc_attr($group_name); ?>][<?php echo esc_attr($key); ?>][label]" value="<?php echo esc_attr($label); ?>" placeholder="<?php echo esc_attr__('Label', 'banglacommerce-all-in-one-woocommerce'); ?>" style="flex:1; margin-right:5px;">
                                        <label>
                                            <input type="checkbox" name="bcaw_checkout_fields[<?php echo esc_attr($group_name); ?>][<?php echo esc_attr($key); ?>][required]" <?php checked($required,true); ?>> <?php echo esc_html__('Required', 'banglacommerce-all-in-one-woocommerce'); ?>
                                        </label>
                                        <input type="text" name="bcaw_checkout_fields[<?php echo esc_attr($group_name); ?>][<?php echo esc_attr($key); ?>][placeholder]" value="<?php echo esc_attr($placeholder); ?>" placeholder="<?php echo esc_attr__('Placeholder', 'banglacommerce-all-in-one-woocommerce'); ?>">
                                        <input type="text" name="bcaw_checkout_fields[<?php echo esc_attr($group_name); ?>][<?php echo esc_attr($key); ?>][description]" value="<?php echo esc_attr($description); ?>" placeholder="<?php echo esc_attr__('Description', 'banglacommerce-all-in-one-woocommerce'); ?>">
                                        <span class="dashicons dashicons-move"></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Titles Section -->
        <div class="bcaw-section">
            <h3><?php echo esc_html__('Checkout Page Titles', 'banglacommerce-all-in-one-woocommerce'); ?></h3>
            <div class="bcaw-title-field">
                <?php foreach ($titles as $key => $value) :
                    $label = ucwords(str_replace('_',' ',$key));
                ?>
                    <label><?php echo esc_html__($label, 'banglacommerce-all-in-one-woocommerce'); ?>:
                        <input type="text" name="bcaw_checkout_titles[<?php echo esc_attr($key); ?>]" value="<?php echo esc_attr($value); ?>" class="bcaw-title-input" data-preview="<?php echo esc_attr($key); ?>">
                    </label><br>
                <?php endforeach; ?>

                <div class="bcaw-preview">
                    <p><strong><?php echo esc_html__('Live Preview:', 'banglacommerce-all-in-one-woocommerce'); ?></strong></p>
                    <h2 id="bcaw-preview_page_title"><?php echo esc_html($titles['page_title']); ?></h2>
                    <h3 id="bcaw-preview_billing_title"><?php echo esc_html($titles['billing_title']); ?></h3>
                    <h3 id="bcaw-preview_shipping_title"><?php echo esc_html($titles['shipping_title']); ?></h3>
                    <h3 id="bcaw-preview_order_notes_title"><?php echo esc_html($titles['order_notes_title']); ?></h3>
                    <button class="button button-primary" id="bcaw-preview_place_order"><?php echo esc_html($titles['place_order_button']); ?></button>
                </div>
            </div>
        </div>

        <!-- Additional Settings Section -->
        <div class="bcaw-section">
            <h3><?php echo esc_html__('Additional Settings', 'banglacommerce-all-in-one-woocommerce'); ?></h3>
            <label class="bcaw-switch">
                <input type="checkbox" name="bcaw_checkout_additional[show_terms]" <?php checked($additional['show_terms'],true); ?>>
                <span class="bcaw-slider round"></span> <?php echo esc_html__('Show Terms & Conditions', 'banglacommerce-all-in-one-woocommerce'); ?>
            </label><br>
            <textarea name="bcaw_checkout_additional[terms_text]" placeholder="<?php echo esc_attr__('Terms & Conditions text', 'banglacommerce-all-in-one-woocommerce'); ?>" style="width:100%;height:60px;"><?php echo esc_textarea($additional['terms_text']); ?></textarea><br>

            <label class="bcaw-switch">
                <input type="checkbox" name="bcaw_checkout_additional[show_login_reminder]" <?php checked($additional['show_login_reminder'],true); ?>>
                <span class="bcaw-slider round"></span> <?php echo esc_html__('Show Login Reminder', 'banglacommerce-all-in-one-woocommerce'); ?>
            </label><br>

            <label><?php echo esc_html__('Custom CSS (Optional):', 'banglacommerce-all-in-one-woocommerce'); ?></label><br>
            <textarea name="bcaw_checkout_additional[custom_css]" placeholder="<?php echo esc_attr__('Custom CSS', 'banglacommerce-all-in-one-woocommerce'); ?>" style="width:100%;height:80px;"><?php echo esc_textarea($additional['custom_css']); ?></textarea>
        </div>

        <?php submit_button(esc_html__('Save Checkout Settings', 'banglacommerce-all-in-one-woocommerce')); ?>

    </div>

    <style>
        .bcaw-checkout-wrapper { display:flex; flex-direction:column; gap:20px; }
        .bcaw-section { padding:15px; border:1px solid #ddd; border-radius:5px; background:#fff; }
        .bcaw-field-card { margin-bottom:10px; border:1px solid #ccc; border-radius:4px; }
        .bcaw-card-header { padding:5px 10px; background:#f7f7f7; cursor:pointer; display:flex; justify-content:space-between; align-items:center; }
        .bcaw-card-body { padding:10px; display:none; }
        .bcaw-field-item { display:flex; align-items:center; gap:5px; margin-bottom:5px; }
        .bcaw-field-item input[type="text"] { padding:3px; }
        .bcaw-fields-group { list-style:none; margin:0; padding:0; }
        .bcaw-switch { position: relative; display: inline-block; width:40px; height:20px; }
        .bcaw-switch input { display:none; }
        .bcaw-slider { position:absolute; cursor:pointer; top:0; left:0; right:0; bottom:0; background:#ccc; transition:.4s; border-radius:20px; }
        .bcaw-slider:before { position:absolute; content:""; height:16px; width:16px; left:2px; bottom:2px; background:white; transition:.4s; border-radius:50%; }
        .bcaw-switch input:checked + .bcaw-slider { background:#2196F3; }
        .bcaw-switch input:checked + .bcaw-slider:before { transform:translateX(20px); }
    </style>

    <script>
        jQuery(document).ready(function($){
            $('.bcaw-card-header').on('click', function(){
                $(this).next('.bcaw-card-body').slideToggle();
            });
            $('.bcaw-title-input').on('input', function(){
                let preview_id = $(this).data('preview');
                $('#bcaw-preview_' + preview_id).text($(this).val());
            });
            if($.fn.sortable){
                $('.bcaw-fields-group').sortable({ placeholder:"ui-state-highlight", axis:"y" });
            }
        });
    </script>

<?php
}
?>
