<?php
if (!defined('ABSPATH')) exit;

function bes_whatsapp_tab(){
    // Get saved settings or defaults
    $saved_settings = get_option('bes_whatsapp_settings');
    $settings = wp_parse_args($saved_settings, [
        'enabled' => true,
        'number' => '',
        'message' => 'Hello, I would like to order this product: {product}',
    ]);

    echo '<h2>WhatsApp Order Settings</h2>';
    echo '<p>Enable a WhatsApp order button and customize the message template.</p>';

    echo '<div class="bes-whatsapp-card" style="border:1px solid #ddd; padding:15px; max-width:600px; border-radius:5px; background:#fafafa;">';

    // Enable Toggle
    echo '<div class="bes-whatsapp-field" style="margin-bottom:15px;">';
    echo '<label class="switch">';
    echo '<input type="checkbox" name="bes_whatsapp_settings[enabled]" '.checked($settings['enabled'], true, false).'>';
    echo '<span class="slider round"></span> Enable WhatsApp Button';
    echo '</label>';
    echo '</div>';

    // Phone Number
    echo '<div class="bes-whatsapp-field" style="margin-bottom:15px;">';
    echo '<label>Phone Number:</label><br>';
    echo '<input type="text" name="bes_whatsapp_settings[number]" value="'.esc_attr($settings['number']).'" placeholder="e.g. +8801XXXXXXXXX" style="width:300px;padding:5px;">';
    echo '</div>';

    // Message Template
    echo '<div class="bes-whatsapp-field" style="margin-bottom:15px;">';
    echo '<label>Message Template:</label><br>';
    echo '<textarea name="bes_whatsapp_settings[message]" rows="3" style="width:100%; max-width:500px; padding:5px;">'.esc_textarea($settings['message']).'</textarea>';
    echo '<div class="bes-whatsapp-preview" style="margin-top:5px; font-size:13px; color:#555;">Preview: <strong>'.esc_html($settings['message']).'</strong></div>';
    echo '<p style="font-size:12px; color:#777;">Use <code>{product}</code> to include the product name.</p>';
    echo '</div>';

    echo '</div>';

    submit_button('Save WhatsApp Settings');
    ?>

    <style>
        .switch { position: relative; display: inline-block; width:40px; height:20px; }
        .switch input { display:none; }
        .slider { position:absolute; cursor:pointer; top:0; left:0; right:0; bottom:0; background:#ccc; transition:.4s; border-radius:20px; }
        .slider:before { position:absolute; content:""; height:16px; width:16px; left:2px; bottom:2px; background:white; transition:.4s; border-radius:50%; }
        input:checked + .slider { background:#25D366; }
        input:checked + .slider:before { transform:translateX(20px); }
    </style>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script>
        jQuery(document).ready(function($){
            $('textarea[name="bes_whatsapp_settings[message]"]').on('input', function(){
                var val = $(this).val();
                $('.bes-whatsapp-preview strong').text(val);
            });
        });
    </script>
    <?php
}

// Add WhatsApp Button under Add to Cart on Single Product
add_action('woocommerce_after_add_to_cart_button', function(){
    $settings = get_option('bes_whatsapp_settings');
    if(empty($settings['enabled']) || empty($settings['number'])) return;

    global $product;
    $product_name = $product->get_name();
    $message = rawurlencode(str_replace('{product}', $product_name, $settings['message']));
    $phone = preg_replace('/\D/', '', $settings['number']);

    echo '<a href="https://wa.me/'.$phone.'?text='.$message.'" target="_blank" class="button" style="background:#25D366; border-color:#25D366; color:#fff; margin-top:5px;">Order via WhatsApp</a>';
});

// Add WhatsApp Button on Shop/Archive Pages
add_action('woocommerce_after_shop_loop_item', function(){
    $settings = get_option('bes_whatsapp_settings');
    if(empty($settings['enabled']) || empty($settings['number'])) return;

    global $product;
    $product_name = $product->get_name();
    $message = rawurlencode(str_replace('{product}', $product_name, $settings['message']));
    $phone = preg_replace('/\D/', '', $settings['number']);

    echo '<a href="https://wa.me/'.$phone.'?text='.$message.'" target="_blank" class="button" style="background:#25D366; border-color:#25D366; color:#fff; margin-top:5px; display:inline-block;">Order via WhatsApp</a>';
});
