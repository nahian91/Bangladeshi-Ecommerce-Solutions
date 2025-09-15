<?php
if (!defined('ABSPATH')) exit;

function bes_whatsapp_tab() {
    $saved_settings = get_option('bes_whatsapp_settings');
    $settings = wp_parse_args($saved_settings, [
        'enabled' => true,
        'number'  => '',
        'message' => 'Hello, I would like to order this product: {product}',
    ]);

    echo '<h2>BES WhatsApp Order Settings</h2>';
    echo '<p>Enable a WhatsApp order button and customize the message template.</p>';

    echo '<div class="bes-whatsapp-card">';

    // Enable toggle
    echo '<div class="bes-whatsapp-field">';
    echo '<label class="bes-switch">';
    echo '<input type="checkbox" name="bes_whatsapp_settings[enabled]" '.checked($settings['enabled'], true, false).'>';
    echo '<span class="bes-slider round"></span> Enable WhatsApp Button';
    echo '</label>';
    echo '</div>';

    // Phone Number
    echo '<div class="bes-whatsapp-field">';
    echo '<label>Phone Number:</label><br>';
    echo '<input type="text" name="bes_whatsapp_settings[number]" value="'.esc_attr($settings['number']).'" placeholder="+8801XXXXXXXXX" class="bes-input">';
    echo '</div>';

    // Message Template
    echo '<div class="bes-whatsapp-field">';
    echo '<label>Message Template:</label><br>';
    echo '<textarea name="bes_whatsapp_settings[message]" class="bes-textarea" rows="3">'.esc_textarea($settings['message']).'</textarea>';
    echo '<div class="bes-whatsapp-preview">Preview: <strong>'.esc_html($settings['message']).'</strong></div>';
    echo '<p class="bes-helper">Use <code>{product}</code> to include the product name.</p>';
    echo '</div>';

    echo '</div>';

    submit_button('Save WhatsApp Settings');
}

// -------------------- Frontend WhatsApp Button --------------------
add_action('woocommerce_after_add_to_cart_button', 'bes_add_whatsapp_button_single');
add_action('woocommerce_after_shop_loop_item', 'bes_add_whatsapp_button_archive');

function bes_add_whatsapp_button_single(){
    bes_whatsapp_button();
}

function bes_add_whatsapp_button_archive(){
    bes_whatsapp_button();
}

function bes_whatsapp_button(){
    $settings = get_option('bes_whatsapp_settings');
    if(empty($settings['enabled']) || empty($settings['number'])) return;

    global $product;
    $product_name = $product->get_name();
    $message = rawurlencode(str_replace('{product}', $product_name, $settings['message']));
    $phone = preg_replace('/\D/', '', $settings['number']);

    echo '<a href="https://wa.me/'.$phone.'?text='.$message.'" target="_blank" class="bes-whatsapp-btn">Order via WhatsApp</a>';
}
