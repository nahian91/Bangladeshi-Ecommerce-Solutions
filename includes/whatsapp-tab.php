<?php
if (!defined('ABSPATH')) exit;

/**
 * ===============================
 * Admin Settings Tab
 * ===============================
 */
function bes_whatsapp_tab() {
    $defaults = [
        'enabled' => 0, // Disabled by default
        'number' => '',
        'button_text' => 'Order via WhatsApp',
        'message' => 'Hello, I would like to order this product: {product}',
        'show_single' => 1,
        'show_archive' => 1,
        'color' => '#25D366',
    ];

    $saved = get_option('bes_whatsapp_settings', []);
    $settings = wp_parse_args($saved, $defaults);

    echo '<h2>BES WhatsApp Order Settings</h2>';
    echo '<p>Enable WhatsApp order button and customize its behavior and appearance.</p>';

    echo '<form method="post">';
    wp_nonce_field('bes_whatsapp_save','bes_whatsapp_nonce');

    echo '<div style="background:#fff; padding:20px; border-radius:8px; box-shadow:0 2px 10px rgba(0,0,0,0.05); max-width:600px;">';

    // Enable
    echo '<p><label><input type="checkbox" name="bes_whatsapp_settings[enabled]" value="1" '.checked($settings['enabled'],1,false).' /> Enable WhatsApp Button</label></p>';

    // Phone Number
    echo '<p><label>Phone Number:<br><input type="text" name="bes_whatsapp_settings[number]" value="'.esc_attr($settings['number']).'" placeholder="+8801XXXXXXXXX" style="width:100%; padding:8px; border-radius:4px; border:1px solid #ccc;"></label></p>';

    // Button Text
    echo '<p><label>Button Text:<br><input type="text" name="bes_whatsapp_settings[button_text]" value="'.esc_attr($settings['button_text']).'" style="width:100%; padding:8px; border-radius:4px; border:1px solid #ccc;"></label></p>';

    // Message Template
    echo '<p><label>Message Template:<br><textarea name="bes_whatsapp_settings[message]" rows="3" style="width:100%; padding:8px; border-radius:4px; border:1px solid #ccc;">'.esc_textarea($settings['message']).'</textarea></label><br><small>Use <code>{product}</code> to include product name.</small></p>';

    // Show Options
    echo '<p><label><input type="checkbox" name="bes_whatsapp_settings[show_single]" value="1" '.checked($settings['show_single'],1,false).' /> Show on Single Product Page</label></p>';
    echo '<p><label><input type="checkbox" name="bes_whatsapp_settings[show_archive]" value="1" '.checked($settings['show_archive'],1,false).' /> Show on Shop / Archive Pages</label></p>';

    // Button Color
    echo '<p><label>Button Color:<br><input type="color" name="bes_whatsapp_settings[color]" value="'.esc_attr($settings['color']).'" style="width:100px; height:40px; border:none;"></label></p>';

    echo '</div>';

    submit_button('Save WhatsApp Settings');
    echo '</form>';
}

/**
 * ===============================
 * Save Settings
 * ===============================
 */
add_action('admin_init', function(){
    if(isset($_POST['bes_whatsapp_settings']) && check_admin_referer('bes_whatsapp_save','bes_whatsapp_nonce')){
        $data = $_POST['bes_whatsapp_settings'];
        $settings = [];
        $settings['enabled'] = !empty($data['enabled']) ? 1 : 0;
        $settings['number'] = sanitize_text_field($data['number'] ?? '');
        $settings['button_text'] = sanitize_text_field($data['button_text'] ?? 'Order via WhatsApp');
        $settings['message'] = sanitize_textarea_field($data['message'] ?? '');
        $settings['show_single'] = !empty($data['show_single']) ? 1 : 0;
        $settings['show_archive'] = !empty($data['show_archive']) ? 1 : 0;
        $settings['color'] = sanitize_text_field($data['color'] ?? '#25D366');

        update_option('bes_whatsapp_settings', $settings);
    }
});

/**
 * ===============================
 * Frontend WhatsApp Button
 * ===============================
 */
function bes_whatsapp_button_output(){
    $settings = get_option('bes_whatsapp_settings', []);
    if(empty($settings['enabled']) || empty($settings['number'])) return;

    global $product;
    if(!$product) return;

    $product_name = $product->get_name();
    $message = rawurlencode(str_replace('{product}', $product_name, $settings['message']));
    $phone = preg_replace('/\D/', '', $settings['number']);
    $color = esc_attr($settings['color']);
    $text = esc_html($settings['button_text']);

    echo '<a href="https://wa.me/'.$phone.'?text='.$message.'" target="_blank" class="bes-whatsapp-btn" style="display:inline-block; padding:10px 20px; background:'.$color.'; color:#fff; border-radius:6px; text-decoration:none; font-weight:bold; margin-top:10px;">'.$text.'</a>';
}

// Single product page
add_action('woocommerce_after_add_to_cart_button', function(){
    $settings = get_option('bes_whatsapp_settings', []);
    if(!empty($settings['show_single'])){
        bes_whatsapp_button_output();
    }
});

// Archive/shop page
add_action('woocommerce_after_shop_loop_item', function(){
    $settings = get_option('bes_whatsapp_settings', []);
    if(!empty($settings['show_archive'])){
        bes_whatsapp_button_output();
    }
});
